<?php

namespace SimpleSolution\SimpleTradeBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SimpleSolution\SimpleTradeBundle\Annotations\Permissions;
use SimpleSolution\SimpleTradeBundle\Model\OffersModel;
use SimpleSolution\SimpleTradeBundle\Model\RegionsModel;
use SimpleSolution\SimpleTradeBundle\Model\UsersModel;
use SimpleSolution\SimpleTradeBundle\Model\AuctionsModel;
use SimpleSolution\SimpleTradeBundle\Model\UsersCompaniesModel;
use SimpleSolution\SimpleTradeBundle\Model\OffersStatusesModel;
use SimpleSolution\SimpleTradeBundle\Model\DocumentsModel;
use SimpleSolution\SimpleTradeBundle\Model\DocumentsLinksModel;
use SimpleSolution\SimpleTradeBundle\Model\AccountsModel;
use SimpleSolution\SimpleTradeBundle\Model\AccountsTypesModel;
use SimpleSolution\SimpleTradeBundle\Model\TariffsModel;
use SimpleSolution\SimpleTradeBundle\Form\cabinet\CabinetFilesForm;
use SimpleSolution\SimpleTradeBundle\Consts\Constants;

class OffersController extends Controller
{
    private $controllerName = "Offers";

    /**
     * @Route("/offers", name="offers")
     * @Permissions(perm="/offers")
     * @Template()
     */
    public function offersAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');
        $offersModel = new OffersModel($em, $session, $acl);

        $userCurrent = $this->get('security.context')->getToken()->getUser();

        $offers = $offersModel->findAll();
        return $this->render('SimpleTradeBundle:Offers:list.html.twig', array(
                'offers' => $offers,
                'user' => $userCurrent
            ));
    }

    /**
     * @Route("/offers/company", name="offers_company")
     * @Permissions(perm="/offers/company")
     * @Template()
     */
    public function offersCompanyAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');
        $offersModel = new OffersModel($em, $session, $acl);

        $userCurrent = $this->get('security.context')->getToken()->getUser();

        $usersCompaniesModel = new UsersCompaniesModel($em, $session);
        $usersCompany = $usersCompaniesModel->findOneByUserId($userCurrent->getId());
        $offers = $offersModel->findAllByCompanyId($usersCompany->getCompany()->getId());
        return $this->render('SimpleTradeBundle:Offers:list.html.twig', array(
                'offers' => $offers,
                'user' => $userCurrent
            ));
    }

    /**
     * @Route("/offers/show/{id}", name="offers_show")
     * @Permissions(perm="/offers/show")
     * @Template()
     */
    public function offersShowAction($id)
    {
        if (!is_numeric($id)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $offersModel = new OffersModel($em, $session, $acl);
        $documentsLinksModel = new DocumentsLinksModel($em, $session);

        $offer = $offersModel->findByPK($id);
        if (!$offer) {
            $session->getFlashBag()->add('notice', 'Нет такой заявки');

            return $this->redirect($this->generateUrl('offers'));
        }

        $company = $offer->getCompany();
        $usersCompaniesModel = new UsersCompaniesModel($em, $session);
        $usersCompany = $usersCompaniesModel->findOneByCompanyId($company->getId());
        $user = $usersCompany->getUser();

        $files = $documentsLinksModel->findAllByOwner($id, 'OFFER');

        return $this->render('SimpleTradeBundle:Offers:show.html.twig', array(
                'offer' => $offer,
                'company' => $company,
                'user' => $user,
                'files' => $files
            ));
    }

    /**
     * @Route("/offers/allow/{id}", name="offers_allow")
     * @Permissions(perm="/offers/allow")
     * @Template()
     */
    public function offersAllowAction($id)
    {
        if (!is_numeric($id)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');
        $userCurrent = $this->get('security.context')->getToken()->getUser();

        $offersModel = new OffersModel($em, $session, $acl);

        $offer = $offersModel->findByPK($id);

        if (!$offer) {
            $session->getFlashBag()->add('notice', 'Нет такой заявки');
            return $this->redirect($this->generateUrl('offers'));
        }
        if ($offer->getAuction()->getUser()->getId() !== $userCurrent->getId()) {
            $session->getFlashBag()->add('notice', 'Заявка на чужой аукцион');
            return $this->redirect($this->generateUrl('offers'));
        }

        $company = $offer->getCompany();
        $auction = $offer->getAuction();

        $em->getConnection()->beginTransaction();
        try {
            $offersStatusesModel = new OffersStatusesModel($em);
            $offerOnConsiderationStatus = $offersStatusesModel->findOneByName('ADMITTED_TO_PARTICIPATION');

            $offersModel->update(
                $id, null, array( 'company' => $company, 'auction' => $auction, 'status' => $offerOnConsiderationStatus )
            );

            $em->getConnection()->commit();
        } catch( Exception $e ) {
            $em->getConnection()->rollback();
            $em->close();
            throw $e;
        }

        $usersCompaniesModel = new UsersCompaniesModel($em, $session);
        $usersCompany = $usersCompaniesModel->findOneByCompanyId($company->getId());
        $user = $usersCompany->getUser();
        /**
         * Отсылаем сообщение (пока на почту)
         *
         */
        $this->get('mail')->sendByUser(Constants::MAIL_2USER_AUCTION_ADMITTED, $user, array( 'auction' => $auction, ));

        $session->getFlashBag()->add('notice', 'Заявка допущена к аукциону!');

        return $this->redirect($this->generateUrl('trade_offers', array( 'id' => $auction->getId() )));
    }

    /**
     * @Route("/offers/reject/{id}", name="offers_reject")
     * @Permissions(perm="/offers/reject")
     * @Template()
     */
    public function offersRejectAction($id)
    {
        if (!is_numeric($id)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $request = $this->get('request');
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');
        $userCurrent = $this->get('security.context')->getToken()->getUser();

        $offersModel = new OffersModel($em, $session, $acl);

        $offer = $offersModel->findByPK($id);

        if (!$offer) {
            $session->getFlashBag()->add('notice', 'Нет такой заявки');
            return $this->redirect($this->generateUrl('offers'));
        }
        if ($offer->getAuction()->getUser()->getId() !== $userCurrent->getId()) {
            $session->getFlashBag()->add('notice', 'Заявка на чужой аукцион');
            return $this->redirect($this->generateUrl('offers'));
        }

        $company = $offer->getCompany();
        $auction = $offer->getAuction();
        
        $em->getConnection()->beginTransaction();
        try {
            $offersStatusesModel = new OffersStatusesModel($em);
            $offerOnConsiderationStatus = $offersStatusesModel->findOneByName('REJECTED');

            $offersModel->update(
                $id, null, array( 'company' => $company, 'auction' => $auction, 'status' => $offerOnConsiderationStatus )
            );

            $em->getConnection()->commit();
        } catch( Exception $e ) {
            $em->getConnection()->rollback();
            $em->close();
            throw $e;
        }

        $reason = $request->get('reason', '') == '' ? 'Не указана' : $request->get('reason');

        $usersCompaniesModel = new UsersCompaniesModel($em, $session);
        $usersCompany = $usersCompaniesModel->findOneByCompanyId($company->getId());
        $user = $usersCompany->getUser();
        /**
         * Отсылаем сообщение (пока на почту)
         */
        $this->get('mail')->sendByUser(Constants::MAIL_2USER_AUCTION_REFUSED, $user, array( 'auction' => $auction, 'reason' => $reason, ));

        $session->getFlashBag()->add('notice', 'Заявка отклонена!');

        return $this->redirect($this->generateUrl('trade_offers', array( 'id' => $auction->getId() )));
    }

    /**
     * @Route("/offers/show/{id}/file/{idFile}", name="offers_get_doc")
     * @Permissions(perm="/offers/getdoc")
     * @Template()
     */
    public function offersGetDocAction($id, $idFile)
    {
        if (!is_numeric($id)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');

        $documentsLinksModel = new DocumentsLinksModel($em, $session);

        $file = $documentsLinksModel->findDocument($idFile, $id, 'OFFER');

        if ($file) {

            $full = realpath($this->container->getParameter('uploads_documents_dir') . $file->getContent()->getFilename());

            if (is_file($full)) {
                $response = new Response();
                $response->headers->set('Content-Length', filesize($full));
                $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($full) . '"');
                $response->headers->set('Pragma', 'no-cache');
                $response->headers->set('Expires', '0');
                $response->setContent(file_get_contents($full));

                return $response;
            }
        }

        throw new \Exception('File not found.');
    }

    /**
     * @Route("/offers/add/{id}")
     * @Permissions(perm="/offers/add")
     * @Template()
     */
    public function offersAddAction($id)
    {
        if (!is_numeric($id)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $request = $this->get('request');
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');
        $userCurrent = $this->get('security.context')->getToken()->getUser();

        $auctionsModel = new AuctionsModel($em, $session, $acl);
        $auction = $auctionsModel->findByPK($id);
        if (!isset($auction)) {
            return $this->redirect($this->generateUrl('login'));
        }

        if ($auction->getStatus()->getName() != 'PUBLIC') {
            $session->getFlashBag()->add('notice', 'Аукцион не опубликован');
            return $this->redirect($this->generateUrl('offers'));
        }
        $usersCompaniesModel = new UsersCompaniesModel($em, $session);
        $company = $usersCompaniesModel->findOneByUserId($userCurrent->getId())->getCompany();

        $offersModel = new OffersModel($em, $session, $acl);
        if ($offersModel->checkByCompanyIdAndAuctionId($company->getId(), $id)) {
            $session->getFlashBag()->add('notice', 'Вы уже подали заявку на этот аукцион');
            return $this->redirect($this->generateUrl('offers'));
        }

        $form = $this->createForm(new CabinetFilesForm());
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $formData = $form->getData();
                $em->getConnection()->beginTransaction();
                try {
                    $offersStatusesModel = new OffersStatusesModel($em);
                    $offerOnConsiderationStatus = $offersStatusesModel->findOneByName('ON_CONSIDERATION');

                    $offer = $offersModel->create(
                        array( ), array( 'company' => $company, 'auction' => $auction, 'status' => $offerOnConsiderationStatus )
                    );

                    $documentsModel = new DocumentsModel($em, $session, $acl);
                    $documentsLinksModel = new DocumentsLinksModel($em, $session);

                    foreach( $formData[ 'files' ] as $value ) {
                        $document = $documentsModel->create(array( 'file' => $value->getFile(),
                            'title' => $value->getTitle(),
                            ), array( 'isActive' => 0 ));

                        $documentsLinksModel->create(array( 'document' => $document,
                            'owner' => 'OFFER',
                            'ownerId' => $offer->getId(),
                        ));
                    }

                    $tariffModel = new TariffsModel($em, $session);
                    $tariff = $tariffModel->findOneByName('OFFER_ADD');

                    $accountsTypesModel = new AccountsTypesModel($em);
                    $removeType = $accountsTypesModel->findOneByName('REMOVE');

                    $accountsModel = new AccountsModel($em, $session, $acl);
                    $account = $accountsModel->findByCompanyId($company->getId());
                    $accountsModel->update($account->getId(), array( 'account' => $account->getContent()->getAccount() - $tariff->getCost(),
                        'tariff' => $tariff,
                        'changes' => abs($tariff->getCost()),
                        'comment' => ''
                        ), array( 'company' => $company,
                        'user' => $userCurrent,
                        'type' => $removeType )
                    );

                    $em->getConnection()->commit();
                } catch( Exception $e ) {
                    $em->getConnection()->rollback();
                    $em->close();
                    throw $e;
                }

                $session->getFlashBag()->add('notice', 'Заявка успешно отправлена');

                return $this->redirect($this->generateUrl('offers_company'));
            }
        }
        return $this->render('SimpleTradeBundle:Offers:add.html.twig', array(
                'form' => $form->createView(),
                'auction' => $auction,
                'company' => $company,
                'user' => $userCurrent
            ));
    }

    /**
     * @Route("/offers/remove/{id}")
     * @Permissions(perm="/offers/remove")
     * @Template()
     */
    public function offersRemoveAction($id)
    {
        if (!is_numeric($id)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $offersModel = new OffersModel($em, $session, $acl);

        $offer = $offersModel->findByPK($id);
        if (null === $offer) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em->getConnection()->beginTransaction();
        try {
            $offersStatusesModel = new OffersStatusesModel($em);
            $offerRevokedStatus = $offersStatusesModel->findOneByName('REVOKED_BY_BUILDER');

            $auction = $offer->getAuction();
            $company = $offer->getCompany();

            $offersModel->update(
                $id, null, array( 'company' => $company, 'auction' => $auction, 'status' => $offerRevokedStatus )
            );

            $tariffModel = new TariffsModel($em, $session);
            $tariff = $tariffModel->findOneByName('OFFER_ADD');

            $userCurrent = $this->get('security.context')->getToken()->getUser();
            $usersCompaniesModel = new UsersCompaniesModel($em, $session);
            $company = $usersCompaniesModel->findOneByUserId($userCurrent->getId())->getCompany();

            $accountsTypesModel = new AccountsTypesModel($em);
            $addType = $accountsTypesModel->findOneByName('ADD');

            $accountsModel = new AccountsModel($em, $session, $acl);
            $account = $accountsModel->findByCompanyId($company->getId());
            $accountsModel->update($account->getId(), array( 'account' => $account->getContent()->getAccount() + $tariff->getCost(),
                'tariff' => $tariff,
                'changes' => abs($tariff->getCost()),
                'comment' => 'заявка снята',
                ), array( 'company' => $company,
                'user' => $userCurrent,
                'type' => $addType )
            );

            $em->getConnection()->commit();
        } catch( Exception $e ) {
            $em->getConnection()->rollback();
            $em->close();
            throw $e;
        }

        return $this->redirect($this->generateUrl('trade_offer', array('id' => $offer->getAuction()->getId())));
    }

}
