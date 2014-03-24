<?php

namespace SimpleSolution\SimpleTradeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SimpleSolution\SimpleTradeBundle\Model\CompaniesModel;
use SimpleSolution\SimpleTradeBundle\Model\CompaniesStatusesModel;
use SimpleSolution\SimpleTradeBundle\Model\UsersModel;
use SimpleSolution\SimpleTradeBundle\Model\UsersCompaniesModel;
use SimpleSolution\SimpleTradeBundle\Model\AccountsModel;
use SimpleSolution\SimpleTradeBundle\Model\AccountsTypesModel;
use SimpleSolution\SimpleTradeBundle\Model\DocumentsLinksModel;
use SimpleSolution\SimpleTradeBundle\Model\DocumentsModel;
use SimpleSolution\SimpleTradeBundle\Model\RegionsModel;
use SimpleSolution\SimpleTradeBundle\Model\SrosCompaniesModel;
use SimpleSolution\SimpleTradeBundle\Annotations\Permissions;
use SimpleSolution\SimpleTradeBundle\Form\companies\AccountsAddForm;
use SimpleSolution\SimpleTradeBundle\Form\companies\CompaniesChangeForm;
use SimpleSolution\SimpleTradeBundle\Form\companies\CompaniesBlockForm;

class CompaniesController extends Controller
{
    private $controllerName = "Companies";

    /**
     * @Route("/companies", name="companies")
     * @Permissions(perm="/companies")
     * @Template()
     */
    public function companiesAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $companiesModel = new CompaniesModel($em, $session, $acl);

        $userCurrent = $this->get('security.context')->getToken()->getUser();
        $companies = $companiesModel->findAll();

        return $this->render('SimpleTradeBundle:Companies:index.html.twig', array(
                'companies' => $companies,
                'user' => $userCurrent
            ));
    }

    /**
     * @Route("/companies/managment", name="companies_managment")
     * @Permissions(perm="/companies/managment")
     * @Template()
     */
    public function companiesManagmentAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $accountsModel = new AccountsModel($em, $session, $acl);
        $accounts = $accountsModel->findAllAsArray();

        $companiesModel = new CompaniesModel($em, $session, $acl);

        $companies = $companiesModel->findAll();

        return $this->render('SimpleTradeBundle:Companies:managment.html.twig', array(
                'companies' => $companies,
                'user' => $this->get('security.context')->getToken()->getUser(),
                'accounts' => $accounts
            ));
    }

    /**
     * @Route("/companies/managment/add/{companyId}")
     * @Permissions(perm="/companies/managment/add")
     * @Template()
     */
    public function companiesManagmentAddAction($companyId)
    {
        if (!is_numeric($companyId)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $request = $this->get('request');
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $companiesModel = new CompaniesModel($em, $session, $acl);
        $company = $companiesModel->findByPK($companyId);
        if (!isset($company)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $form = $this->createForm(new AccountsAddForm());
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $formData = $form->getData();
                $em->getConnection()->beginTransaction();
                try {
                    $accountsTypesModel = new AccountsTypesModel($em);
                    $addType = $accountsTypesModel->findOneByName('ADD');
                    $removeType = $accountsTypesModel->findOneByName('REMOVE');

                    $accountsModel = new AccountsModel($em, $session, $acl);
                    $account = $accountsModel->findByCompanyId($company->getId());
                    $accountsModel->update($account->getId(), array( 'account' => $account->getContent()->getAccount() + $formData[ 'money' ],
                        'tariff' => null,
                        'changes' => abs($formData[ 'money' ]),
                        'comment' => $formData[ 'comment' ]
                        ), array( 'company' => $company,
                        'user' => $this->get('security.context')->getToken()->getUser(),
                        'type' => $formData[ 'money' ] > 0 ? $addType : $removeType )
                    );

                    $em->getConnection()->commit();
                } catch( Exception $e ) {
                    $em->getConnection()->rollback();
                    $em->close();
                    throw $e;
                }
                return $this->redirect($this->generateUrl('companies_managment'));
            }
        }
        return $this->render('SimpleTradeBundle:Companies:managment_add.html.twig', array(
                'form' => $form->createView(),
                'company' => $company
            ));
    }

    /**
     * @Route("/companies/{status}")
     * @Permissions(perm="/companies")
     * @Template()
     */
    public function companiesByStatusAction($status)
    {
        switch( $status ) {
            case 'approved':
                $name = 'ACTIVE';
                break;
            case 'rejected':
                $name = 'REJECTED';
                break;
            case 'requests':
                $name = 'PRE_REGISTRATION';
                break;
            case 'blocked':
                $name = 'BLOCKED';
                break;
            default:
                return $this->redirect($this->generateUrl('login'));
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $userCurrent = $this->get('security.context')->getToken()->getUser();

        $companiesModel = new CompaniesModel($em, $session, $acl);
        $companies = $companiesModel->findAllByStatus($name);

        return $this->render('SimpleTradeBundle:Companies:' . $status . '.html.twig', array(
                'companies' => $companies,
                'user' => $userCurrent
            ));
    }

    /**
     * @Route("/companies/change/{companyId}")
     * @Permissions(perm="/companies/change")
     * @Template()
     */
    public function companiesChangeAction($companyId)
    {
        if (!is_numeric($companyId)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $request = $this->get('request');
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');
        $factory = $this->get('security.encoder_factory');

        $companiesModel = new CompaniesModel($em, $session, $acl);
        $company = $companiesModel->findByPK($companyId);

        $documentsLinksModel = new DocumentsLinksModel($em, $session);
        $files = $documentsLinksModel->findAllByOwner($companyId, 'COMPANY');

        $regionsModel = new RegionsModel($em);
        $regions = $regionsModel->findAll();

        $content = $company->getContent();
        $form = $this->createForm(new CompaniesChangeForm($regions, $content->getRegion()->getId()), $content);
        if ($request->isMethod('POST')) {
            $form = $this->createForm(new CompaniesChangeForm($regions));
            $form->bind($request);

            if ($form->isValid()) {
                $formData = $form->getData();
                $companiesModel = new CompaniesModel($em, $session, $acl);

                $em->getConnection()->beginTransaction();
                try {
                    $region = $regionsModel->findByPK($formData[ 'region_id' ]);
                    $fields = array( 'title' => $formData[ 'title' ],
                        'name' => $formData[ 'name' ],
                        'inn' => $formData[ 'inn' ],
                        'kpp' => $formData[ 'kpp' ],
                        'ogrn' => $formData[ 'ogrn' ],
                        'userName' => $formData[ 'userName' ],
                        'email' => $formData[ 'email' ],
                        'position' => $formData[ 'position' ],
                        'grounds' => $formData[ 'grounds' ],
                        'phone' => $formData[ 'phone' ],
                        'region' => $region );

                    $companiesModel->update($company->getId(), $fields, array( 'status' => $company->getStatus(), 'type' => $company->getType(), 'penalty' => $company->getPenalty() ));

                    $usersCompaniesModel = new UsersCompaniesModel($em, $session);
                    $usersCompany = $usersCompaniesModel->findOneByCompanyId($company->getId());
                    $user = $usersCompany->getUser();

                    $usersModel = new UsersModel($em, $session, $factory);
                    $usersModel->update(array( 'id' => $user->getId(),
                        'region' => $region ));

                    $documentsModel = new DocumentsModel($em, $session, $acl);
                    foreach( $formData[ 'files' ] as $value ) {
                        $document = $documentsModel->create(array( 'file' => $value->getFile(),
                            'title' => $value->getTitle(),
                            ), array( 'isActive' => 1 ));

                        $documentsLinksModel->create(array( 'document' => $document,
                            'owner' => 'COMPANY',
                            'ownerId' => $company->getId(),
                        ));
                    }

                    $deletedFiles = explode(',', $formData[ 'deletedFiles' ]);
                    foreach( $deletedFiles as $file ) {
                        $documentsLinksModel->removeByDocumentId($file);
                    }

                    $em->getConnection()->commit();
                } catch( Exception $e ) {
                    $em->getConnection()->rollback();
                    $em->close();
                    throw $e;
                }
                $session->getFlashBag()->add('notice', 'Компания успешно изменена');

                /* @TODO
                 * Система отправляет на e-mail администратора  компании сообщение об изменении данных
                 */

                return $this->redirect($this->generateUrl('companies'));
            }
        }
        return $this->render('SimpleTradeBundle:Companies:change.html.twig', array(
                'form' => $form->createView(),
                'company' => $company,
                'files' => $files
            ));
    }

    /**
     * @Route("/companies/block/{companyId}")
     * @Permissions(perm="/companies/block")
     * @Template()
     */
    public function companiesBlockAction($companyId)
    {
        if (!is_numeric($companyId)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $request = $this->get('request');
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $companiesModel = new CompaniesModel($em, $session, $acl);
        $company = $companiesModel->findByPK($companyId);
        if (!isset($company)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $content = $company->getContent()->getAllFieldsAsArray();

        $form = $this->createForm(new CompaniesBlockForm(), $content);
        if ($request->isMethod('POST')) {
            $form = $this->createForm(new CompaniesBlockForm());
            $form->bind($request);
            if ($form->isValid()) {
                $formData = $form->getData();
                $em->getConnection()->beginTransaction();
                try {
                    $companiesStatusesModel = new CompaniesStatusesModel($em);
                    $blockedStatus = $companiesStatusesModel->findOneByName('BLOCKED');

                    $content[ 'comment' ] = $formData[ 'comment' ];
                    switch( $company->getType()->getName() ) {
                        case 'CUSTOMER':
                            $companiesModel->update($company->getId(), $content, array(
                                'status' => $blockedStatus,
                                'type' => $company->getType(),
                                'penalty' => $company->getPenalty() ));
                            break;
                        case 'PERFORMER':
                            $srosCompaniesModel = new SrosCompaniesModel($em, $session);
                            $srosCompanies = $srosCompaniesModel->findAllByCompanyIdAndStatus($company->getId(), 'ACTIVE');

                            foreach( $srosCompanies as $srosCompany ) {
                                $srosCompaniesModel->updateFromObjects($srosCompany->getId(), array(
                                    'comment' => $formData[ 'comment' ],
                                    'sro' => $srosCompany->getSro(),
                                    'company' => $srosCompany->getCompany(),
                                    'status' => $blockedStatus
                                ));
                            }

                            $companiesModel->update($company->getId(), $content, array(
                                'status' => $blockedStatus,
                                'type' => $company->getType(),
                                'penalty' => $company->getPenalty() ));
                            break;
                    }

                    $em->getConnection()->commit();
                } catch( Exception $e ) {
                    $em->getConnection()->rollback();
                    $em->close();
                    throw $e;
                }
                $session->getFlashBag()->add('notice', 'Компания успешно полностью заблокирована');

                /* @TODO отправка писем
                 * Система отправляет на e-mail администратора компании письмо с сообщением о том, что его компания в системе заблокирована.
                 */

                return $this->redirect($this->generateUrl('companies'));
            }
        }
        return $this->render('SimpleTradeBundle:Companies:block.html.twig', array(
                'form' => $form->createView(),
                'company' => $company
            ));
    }

    /**
     * @Route("/companies/active/{companyId}")
     * @Permissions(perm="/companies/active")
     * @Template()
     */
    public function companiesActiveAction($companyId)
    {
        if (!is_numeric($companyId)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $companiesModel = new CompaniesModel($em, $session, $acl);
        $companiesStatusesModel = new CompaniesStatusesModel($em);

        $company = $companiesModel->findByPK($companyId);
        if (!isset($company)) return $this->redirect($this->generateUrl('login'));

        $activeStatus = $companiesStatusesModel->findOneByName('ACTIVE');
        $content = $company->getContent()->getAllFieldsAsArray();

        switch( $company->getType()->getName() ) {
            case 'CUSTOMER':
                $companiesModel->update($company->getId(), $content, array( 'status' => $activeStatus, 'type' => $company->getType(), 'penalty' => $company->getPenalty() ));
                break;
            case 'PERFORMER':
                $srosCompaniesModel = new SrosCompaniesModel($em, $session);
                $srosCompanies = $srosCompaniesModel->findAllByCompanyIdAndStatus($company->getId(), 'BLOCKED');

                foreach( $srosCompanies as $srosCompany ) {
                    $srosCompaniesModel->updateFromObjects($srosCompany->getId(), array(
                        'comment' => '',
                        'sro' => $srosCompany->getSro(),
                        'company' => $srosCompany->getCompany(),
                        'status' => $activeStatus
                    ));
                }
                $companiesModel->update($company->getId(), $content, array( 'status' => $activeStatus, 'type' => $company->getType(), 'penalty' => $company->getPenalty() ));

                break;
        }

        $session->getFlashBag()->add('notice', 'Компания успешно полностью разблокирована');

        return $this->redirect($this->generateUrl('companies'));
    }

    /**
     * @Route("/companies/remove/{id}")
     * @Permissions(perm="/companies/remove")
     * @Template()
     */
    public function companiesRemoveAction($id)
    {
        if (!is_numeric($id)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $companiesModel = new CompaniesModel($em, $session, $acl);
        $usersCompaniesModel = new UsersCompaniesModel($em, $session);

        $company = $companiesModel->findByPK($id);
        if (null === $company) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em->getConnection()->beginTransaction();
        try {
            $usersCompaniesModel->removeByCompanyId($id);
            $companiesModel->removeByPK($id);
            $em->getConnection()->commit();
        } catch( Exception $e ) {
            $em->getConnection()->rollback();
            $em->close();
            throw $e;
        }

        return $this->redirect($this->generateUrl('companies'));
    }

}
