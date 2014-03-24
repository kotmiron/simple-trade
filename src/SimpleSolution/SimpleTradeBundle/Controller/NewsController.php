<?php

namespace SimpleSolution\SimpleTradeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SimpleSolution\SimpleTradeBundle\Annotations\Permissions;
use SimpleSolution\SimpleTradeBundle\Model\NewsModel;
use SimpleSolution\SimpleTradeBundle\Model\NewsStatusesModel;
use SimpleSolution\SimpleTradeBundle\Model\CompaniesModel;
use SimpleSolution\SimpleTradeBundle\Model\SrosModel;
use SimpleSolution\SimpleTradeBundle\Form\news\NewsAddForm;
use SimpleSolution\SimpleTradeBundle\Form\news\NewsChangeForm;
use SimpleSolution\SimpleTradeBundle\Consts\Constants;

class NewsController extends Controller
{
    private $controllerName = "News";

    /**
     * @Route("/news", name="news")
     * @Permissions(perm="/news")
     * @Template()
     */
    public function newsAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $newsModel = new NewsModel($em, $session, $acl);

        $news = $newsModel->findAll();

        $permissions = array( );
        $names = array(
            Constants::NEWS_PERFORMER => 'Строительная компания',
            Constants::NEWS_CUSTOMER => 'Заказчик',
            Constants::NEWS_SRO => 'СРО',
            Constants::NEWS_ANONYMOUS => 'Публичная' );

        foreach( $news as $item ) {
            $permissions[ $item->getId() ] = array( 'Модератор' );
            foreach( $names as $key => $name ) {
                if (($item->getPermission() & $key) == $key) {
                    array_push($permissions[ $item->getId() ], $name);
                }
            }
            $permissions[ $item->getId() ] = implode(',', $permissions[ $item->getId() ]);
        }

        return $this->render('SimpleTradeBundle:News:index.html.twig', array(
                'news' => $news,
                'user' => $this->get('security.context')->getToken()->getUser(),
                'permissions' => $permissions
            ));
    }

    /**
     * @Route("/news/customer", name="news_customer")
     * @Permissions(perm="/news/customer")
     * @Template()
     */
    public function newsCustomerAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $newsModel = new NewsModel($em, $session, $acl);

        $news = $newsModel->findAllByPermission(Constants::NEWS_CUSTOMER);
        return $this->render('SimpleTradeBundle:News:index_customer.html.twig', array(
                'news' => $news,
                'user' => $this->get('security.context')->getToken()->getUser()
            ));
    }

    /**
     * @Route("/news/performer", name="news_performer")
     * @Permissions(perm="/news/performer")
     * @Template()
     */
    public function newsPerformerAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $newsModel = new NewsModel($em, $session, $acl);

        $news = $newsModel->findAllByPermission(Constants::NEWS_PERFORMER);
        return $this->render('SimpleTradeBundle:News:index_performer.html.twig', array(
                'news' => $news,
                'user' => $this->get('security.context')->getToken()->getUser()
            ));
    }

    /**
     * @Route("/news/sro", name="news_sro")
     * @Permissions(perm="/news/sro")
     * @Template()
     */
    public function newsSroAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $newsModel = new NewsModel($em, $session, $acl);

        $news = $newsModel->findAllByPermission(Constants::NEWS_SRO);
        return $this->render('SimpleTradeBundle:News:index_sro.html.twig', array(
                'news' => $news,
                'user' => $this->get('security.context')->getToken()->getUser()
            ));
    }

    /**
     * @Route("/news/add")
     * @Permissions(perm="/news/add")
     * @Template()
     */
    public function newsAddAction()
    {
        $request = $this->get('request');
        $em = $this->get('doctrine.orm.entity_manager');

        $form = $this->createForm(new NewsAddForm());
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {

                $session = $this->get('session');
                $acl = $this->get('acl');

                $formData = $form->getData();

                $em->getConnection()->beginTransaction();
                try {

                    /**
                     * Create news
                     */
                    $emailRepeat = $formData[ 'emailRepeat' ];

                    $permission = 0;
                    $status = null;
                    $companiesModel = new CompaniesModel($em, $session, $acl);
                    $srosModel = new SrosModel($em, $session, $acl);
                    foreach( $formData[ 'show' ] as $type ) {
                        $companies = null;
                        switch( $type ) {
                            case 0://Подрядчик
                                $permission |= Constants::NEWS_PERFORMER;
                                if ($emailRepeat) {
                                    $companies = $companiesModel->findAllByType('PERFORMER');
                                }
                                break;
                            case 1://Заказчик
                                $permission |= Constants::NEWS_CUSTOMER;
                                if ($emailRepeat) {
                                    $companies = $companiesModel->findAllByType('CUSTOMER');
                                }
                                break;
                            case 2://СРО
                                $permission |= Constants::NEWS_SRO;
                                if ($emailRepeat) {
                                    $companies = $srosModel->findAll();
                                }
                                break;
                            case 3://Публичная
//                                $newsStatusesModel = new NewsStatusesModel($em);
//                                $prepublicStatus = $newsStatusesModel->findOneByName('PRE_PUBLIC');
//                                $status = $prepublicStatus;
                                $permission |= Constants::NEWS_ANONYMOUS;
                                break;
                        }
                        if ($companies) {
                            foreach( $companies as $company ) {
                                $this->get('mail')->sendByEmail(Constants::MAIL_2COMPANY_NEW_NEWS, $company->getContent()->getEmail(), array( 'company' => $company, 'data' => $formData, ));
                            }
                        }
                    }

                    $newsModel = new NewsModel($em, $session, $acl);
                    $newsModel->create(array( 'title' => $formData[ 'title' ],
                        'text' => $formData[ 'text' ] ), array(
                        'status' => $status,
                        'permission' => $permission ));

                    $em->getConnection()->commit();
                } catch( Exception $e ) {
                    $em->getConnection()->rollback();
                    $em->close();
                    throw $e;
                }

                $session->getFlashBag()->add('notice', 'Новость успешно создана.');

                return $this->redirect($this->generateUrl('news'));
            }
        }
        return $this->render('SimpleTradeBundle:News:add.html.twig', array(
                'form' => $form->createView()
            ));
    }

    /**
     * @Route("/news/change/{id}")
     * @Permissions(perm="/news/change")
     * @Template()
     */
    public function newsChangeAction($id)
    {
        if (!is_numeric($id)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $request = $this->get('request');
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $newsModel = new NewsModel($em, $session, $acl);
        $news = $newsModel->findByPK($id);
        if (null === $news) {
            return $this->redirect($this->generateUrl('login'));
        }

        $permissions = array(
            Constants::NEWS_PERFORMER => 0,
            Constants::NEWS_CUSTOMER => 1,
            Constants::NEWS_SRO => 2,
            Constants::NEWS_ANONYMOUS => 3 );
        $selected = array( );
        foreach( $permissions as $key => $perm ) {
            if (($news->getPermission() & $key) == $key) {
                array_push($selected, $perm);
            }
        }
        $content = $news->getContent();
        $form = $this->createForm(new NewsChangeForm($selected), $content);
        if ($request->isMethod('POST')) {
            $form = $this->createForm(new NewsChangeForm($selected));
            $form->bind($request);

            if ($form->isValid()) {

                $formData = $form->getData();

                $em->getConnection()->beginTransaction();
                try {

//                    $newsStatusesModel = new NewsStatusesModel($em);
//                    $prepublicStatus = $newsStatusesModel->findOneByName('PRE_PUBLIC');
                    /**
                     * change news
                     */
                    $emailRepeat = $formData[ 'emailRepeat' ];

                    $permission = 0;
                    $status = null;
                    $companiesModel = new CompaniesModel($em, $session, $acl);
                    $srosModel = new SrosModel($em, $session, $acl);
                    foreach( $formData[ 'show' ] as $type ) {
                        $companies = null;
                        switch( $type ) {
                            case 0://Подрядчик
                                $permission |= Constants::NEWS_PERFORMER;
                                if ($emailRepeat) {
                                    $companies = $companiesModel->findAllByType('PERFORMER');
                                }
                                break;
                            case 1://Заказчик
                                $permission |= Constants::NEWS_CUSTOMER;
                                if ($emailRepeat) {
                                    $companies = $companiesModel->findAllByType('CUSTOMER');
                                }
                                break;
                            case 2://СРО
                                $permission |= Constants::NEWS_SRO;
                                if ($emailRepeat) {
                                    $companies = $srosModel->findAll();
                                }
                                break;
                            case 3://Публичная
                                $permission |= Constants::NEWS_ANONYMOUS;
                                break;
                        }
                        if ($companies) {
                            foreach( $companies as $company ) {
                                $this->get('mail')->sendByEmail(Constants::MAIL_2COMPANY_NEW_NEWS, $company->getContent()->getEmail(), array( 'company' => $company, 'data' => $formData, ));
                            }
                        }
                    }

                    $newsModel = new NewsModel($em, $session, $acl);
                    $newsModel->update($id, array( 'title' => $formData[ 'title' ],
                        'text' => $formData[ 'text' ] ), array(
                        'status' => $status,
                        'permission' => $permission ));

                    $em->getConnection()->commit();
                } catch( Exception $e ) {
                    $em->getConnection()->rollback();
                    $em->close();
                    throw $e;
                }

                $session->getFlashBag()->add('notice', 'Новость успешно изменена.');

                return $this->redirect($this->generateUrl('news'));
            }
        }
        return $this->render('SimpleTradeBundle:News:change.html.twig', array(
                'form' => $form->createView()
            ));
    }

    /**
     * @Route("/news/remove/{id}")
     * @Permissions(perm="/news/remove")
     * @Template()
     */
    public function newsRemoveAction($id)
    {
        if (!is_numeric($id)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $newsModel = new NewsModel($em, $session, $acl);

        $news = $newsModel->findByPK($id);
        if (null === $news) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em->getConnection()->beginTransaction();
        try {
            $newsModel = new NewsModel($em, $session, $acl);

            $newsModel->removeByPK($id);
            $em->getConnection()->commit();
        } catch( Exception $e ) {
            $em->getConnection()->rollback();
            $em->close();
            throw $e;
        }


        return $this->redirect($this->generateUrl('news'));
    }

    /**
     * @Route("/news/publish/{id}")
     * @Permissions(perm="/news/publish")
     * @Template()
     */
    /* public function newsPublishAction($id)
      {
      if (!is_numeric($id)) {
      return $this->redirect($this->generateUrl('login'));
      }

      $em = $this->get('doctrine.orm.entity_manager');
      $session = $this->get('session');
      $acl = $this->get('acl');

      $newsModel = new NewsModel($em, $session, $acl);

      $news = $newsModel->findByPK($id);
      if (null === $news) {
      return $this->redirect($this->generateUrl('login'));
      }

      $newsStatusesModel = new NewsStatusesModel($em);
      $publicStatus = $newsStatusesModel->findOneByName('PUBLIC');

      $content = $news->getContent()->getAllFieldsAsArray();

      $newsModel->update($id, $content, array( 'status' => $publicStatus )
      );
      $session->getFlashBag()->add('notice', 'Новость успешно опубликована');

      return $this->redirect($this->generateUrl('news'));
      } */

    /**
     * @Route("/news/depublish/{id}")
     * @Permissions(perm="/news/depublish")
     * @Template()
     */
    /* public function newsDepublishAction($id)
      {
      if (!is_numeric($id)) {
      return $this->redirect($this->generateUrl('login'));
      }

      $em = $this->get('doctrine.orm.entity_manager');
      $session = $this->get('session');
      $acl = $this->get('acl');

      $newsModel = new NewsModel($em, $session, $acl);

      $news = $newsModel->findByPK($id);
      if (null === $news) {
      return $this->redirect($this->generateUrl('login'));
      }

      $newsStatusesModel = new NewsStatusesModel($em);
      $notpublicStatus = $newsStatusesModel->findOneByName('NOT_PUBLIC');

      $content = $news->getContent()->getAllFieldsAsArray();

      $newsModel->update($id, $content, array( 'status' => $notpublicStatus )
      );
      $session->getFlashBag()->add('notice', 'Новость успешно убрана из опубликованных');

      return $this->redirect($this->generateUrl('news'));
      } */
}
