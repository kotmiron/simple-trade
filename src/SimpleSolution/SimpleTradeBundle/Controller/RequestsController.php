<?php

namespace SimpleSolution\SimpleTradeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SimpleSolution\SimpleTradeBundle\Annotations\Permissions;
use SimpleSolution\SimpleTradeBundle\Model\CompaniesModel;
use SimpleSolution\SimpleTradeBundle\Model\AuctionsModel;
use SimpleSolution\SimpleTradeBundle\Model\UsersCompaniesModel;
use SimpleSolution\SimpleTradeBundle\Model\DocumentsLinksModel;
use SimpleSolution\SimpleTradeBundle\Model\CompaniesSkillsModel;
use SimpleSolution\SimpleTradeBundle\Model\RegionsModel;
use SimpleSolution\SimpleTradeBundle\Model\SkillsModel;
use SimpleSolution\SimpleTradeBundle\Model\UsersModel;
use SimpleSolution\SimpleTradeBundle\Model\UsersSrosModel;
use SimpleSolution\SimpleTradeBundle\Model\DocumentsModel;
use SimpleSolution\SimpleTradeBundle\Model\CompaniesStatusesModel;
use SimpleSolution\SimpleTradeBundle\Model\SrosCompaniesModel;
use SimpleSolution\SimpleTradeBundle\Model\RequestsModel;
use SimpleSolution\SimpleTradeBundle\Model\RequestsSkillsModel;
use SimpleSolution\SimpleTradeBundle\Model\RequestsStatusesModel;
use SimpleSolution\SimpleTradeBundle\Model\RequestsTypesModel;
use SimpleSolution\SimpleTradeBundle\Model\RequestsBlockModel;
use SimpleSolution\SimpleTradeBundle\Model\RequestsSrosModel;
use SimpleSolution\SimpleTradeBundle\Model\RequestsComplaintsModel;
use SimpleSolution\SimpleTradeBundle\Model\UsersRolesModel;
use SimpleSolution\SimpleTradeBundle\Model\TariffsModel;
use SimpleSolution\SimpleTradeBundle\Model\AccountsModel;
use SimpleSolution\SimpleTradeBundle\Form\requests\RequestRegisterChangeForm;
use SimpleSolution\SimpleTradeBundle\Form\requests\RequestRegisterApproveForm;
use SimpleSolution\SimpleTradeBundle\Form\requests\RequestSkillsChangeForm;
use SimpleSolution\SimpleTradeBundle\Form\requests\CompaniesBlockForm;
use SimpleSolution\SimpleTradeBundle\Form\requests\ComplaintsAddForm;
use SimpleSolution\SimpleTradeBundle\Consts\Constants;

class RequestsController extends Controller
{

    /**
     * @Route("/requests", name="requests")
     * @Permissions(perm="/requests")
     * @Template()
     */
    public function indexAction()
    {
        return $this->render('SimpleTradeBundle:Requests:index.html.twig', array(
                'user' => $this->get('security.context')->getToken()->getUser()
            ));
    }

    /**
     * @Route("/requests/register", name="requests_register")
     * @Permissions(perm="/requests/register")
     * @Template()
     */
    public function registerAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');
        $companiesModel = new CompaniesModel($em, $session, $acl);

        $companies = $companiesModel->findAllByStatus('PRE_REGISTRATION');
        return $this->render('SimpleTradeBundle:Requests:register_index.html.twig', array(
                'companies' => $companies,
                'user' => $this->get('security.context')->getToken()->getUser()
            ));
    }

    /**
     * @Route("/requests/register/show/{companyId}", name="requests_register_show")
     * @Permissions(perm="/requests/register/show")
     * @Template()
     */
    public function registerShowAction($companyId)
    {
        if (!is_numeric($companyId)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $companiesModel = new CompaniesModel($em, $session, $acl);
        $company = $companiesModel->findByPK($companyId);
        if (!isset($company)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $documentsLinksModel = new DocumentsLinksModel($em, $session);
        $files = $documentsLinksModel->findAllByOwner($companyId, 'COMPANY');

        $companiesSkillsModel = new CompaniesSkillsModel($em, $session);
        $skills = implode(',', $companiesSkillsModel->findSkillsByCompanyIdAsArray($company->getId()));

        return $this->render('SimpleTradeBundle:Requests:register_show.html.twig', array(
                'user' => $this->get('security.context')->getToken()->getUser(),
                'company' => $company,
                'files' => $files,
                'skills' => $skills
            ));
    }

    /**
     * @Route("/requests/register/change/{companyId}")
     * @Permissions(perm="/requests/register/change")
     * @Template()
     */
    public function registerChangeAction($companyId)
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
        if (!isset($company)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $documentsLinksModel = new DocumentsLinksModel($em, $session);
        $files = $documentsLinksModel->findAllByOwner($companyId, 'COMPANY');

        $companiesSkillsModel = new CompaniesSkillsModel($em, $session);
        $skillsArray = $companiesSkillsModel->findSkillsByCompanyIdAsArrayOfIds($company->getId());

        $skillsSelected = implode(',', array_keys($skillsArray));
        $attrsSelected = implode(',', $skillsArray);

        $srosCompaniesModel = new SrosCompaniesModel($em, $session);
        $srosTypes = $srosCompaniesModel->findSrosTypesByCompanyId($companyId);

        $skillsModel = new SkillsModel($em);
        $skills = $skillsModel->findAllBySrosTypesAsArray($srosTypes);

        $regionsModel = new RegionsModel($em);
        $regions = $regionsModel->findAll();

        $content = $company->getContent();
        $form = $this->createForm(new RequestRegisterChangeForm($regions, $content->getRegion()->getId(), $skillsSelected, $attrsSelected), $content);
        if ($request->isMethod('POST')) {
            $form = $this->createForm(new RequestRegisterChangeForm($regions));
            $form->bind($request);

            if ($form->isValid()) {
                $formData = $form->getData();
                $em->getConnection()->beginTransaction();

                try {
                    $region = $regionsModel->findByPK($formData[ 'region_id' ]);
                    $fields = array(
                        'title' => $formData[ 'title' ],
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

                    $userCurrent = $this->get('security.context')->getToken()->getUser();

                    $usersModel = new UsersModel($em, $session, $factory);
                    $usersModel->update(array( 'id' => $userCurrent->getId(),
                        'region' => $region ));

                    $companiesSkillsModel->removeByCompanyId($company->getId());

                    $attrsBySkill = array( );
                    $attrs = explode(',', $formData[ 'attributes' ]);
                    foreach( $attrs as $attr ) {
                        list($id, $main, $dangerous) = explode('|', $attr);
                        $attrsBySkill[ $id ][ 'main' ] = $main;
                        $attrsBySkill[ $id ][ 'dangerous' ] = $dangerous;
                    }

                    $skills = explode(',', $formData[ 'skills' ]);

                    foreach( $skills as $skill ) {
                        $companiesSkillsModel->create(array( 'company' => $company->getId(),
                            'skill' => $skill,
                            'isMain' => $attrsBySkill[ $skill ][ 'main' ],
                            'isDangerous' => $attrsBySkill[ $skill ][ 'dangerous' ]
                        ));
                    }

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

                return $this->redirect($this->generateUrl('requests_register_show', array( 'companyId' => $companyId )));
            }
        }

        return $this->render('SimpleTradeBundle:Requests:register_change.html.twig', array(
                'user' => $this->get('security.context')->getToken()->getUser(),
                'form' => $form->createView(),
                'company' => $company,
                'files' => $files,
                'skills' => $skills
            ));
    }

    /**
     * @Route("/requests/skills", name="requests_skills")
     * @Permissions(perm="/requests/skills")
     * @Template()
     */
    public function skillsAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $requestsModel = new RequestsModel($em, $session, $acl);
        $requests = $requestsModel->findAllByType('SKILLS');

        return $this->render('SimpleTradeBundle:Requests:skills_index.html.twig', array(
                'requests' => $requests,
                'user' => $this->get('security.context')->getToken()->getUser()
            ));
    }

    /**
     * @Route("/requests/skills/show/{requestId}", name="requests_skills_show")
     * @Permissions(perm="/requests/skills/show")
     * @Template()
     */
    public function skillsShowAction($requestId)
    {
        if (!is_numeric($requestId)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $requestsModel = new RequestsModel($em, $session, $acl);
        $request = $requestsModel->findByPK($requestId);
        if (!isset($request)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $company = $request->getCompany();

        $documentsLinksModel = new DocumentsLinksModel($em, $session);
        $files = $documentsLinksModel->findAllByOwner($company->getId(), 'COMPANY');

        $requestsSkillsModel = new RequestsSkillsModel($em, $session);
        $skills = implode(',', $requestsSkillsModel->findSkillsByRequestIdAsArray($requestId));

        return $this->render('SimpleTradeBundle:Requests:skills_show.html.twig', array(
                'user' => $this->get('security.context')->getToken()->getUser(),
                'company' => $company,
                'request' => $request,
                'files' => $files,
                'skills' => $skills
            ));
    }

    /**
     * @Route("/requests/skills/change/{requestId}")
     * @Permissions(perm="/requests/skills/change")
     * @Template()
     */
    public function skillsChangeAction($requestId)
    {
        if (!is_numeric($requestId)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $req = $this->get('request');
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $requestsModel = new RequestsModel($em, $session, $acl);
        $request = $requestsModel->findByPK($requestId);
        if (!isset($request)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $company = $request->getCompany();

        $documentsLinksModel = new DocumentsLinksModel($em, $session);
        $files = $documentsLinksModel->findAllByOwner($company->getId(), 'COMPANY');

        $requestsSkillsModel = new RequestsSkillsModel($em, $session);
        $skillsArray = $requestsSkillsModel->findSkillsByRequestIdAsArrayOfIds($requestId);

        $skillsSelected = implode(',', array_keys($skillsArray));
        $attrsSelected = implode(',', $skillsArray);

        $srosCompaniesModel = new SrosCompaniesModel($em, $session);
        $srosTypes = $srosCompaniesModel->findSrosTypesByCompanyId($company->getId());

        $skillsModel = new SkillsModel($em);
        $skills = $skillsModel->findAllBySrosTypesAsArray($srosTypes);

        $form = $this->createForm(new RequestSkillsChangeForm($skillsSelected, $attrsSelected));
        if ($req->isMethod('POST')) {
            $form->bind($req);

            if ($form->isValid()) {
                $formData = $form->getData();
                $em->getConnection()->beginTransaction();

                try {

                    $requestsSkillsModel->removeByRequestId($requestId);

                    $attrsBySkill = array( );
                    $attrs = explode(',', $formData[ 'attributes' ]);
                    foreach( $attrs as $attr ) {
                        list($id, $main, $dangerous) = explode('|', $attr);
                        $attrsBySkill[ $id ][ 'main' ] = $main;
                        $attrsBySkill[ $id ][ 'dangerous' ] = $dangerous;
                    }

                    $skills = explode(',', $formData[ 'skills' ]);

                    foreach( $skills as $skill ) {
                        $requestsSkillsModel->create(array(
                            'request' => $request->getId(),
                            'skill' => $skill,
                            'isMain' => $attrsBySkill[ $skill ][ 'main' ],
                            'isDangerous' => $attrsBySkill[ $skill ][ 'dangerous' ]
                        ));
                    }

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
                $session->getFlashBag()->add('notice', 'Заявка успешно изменена');

                return $this->redirect($this->generateUrl('requests_skills_show', array( 'requestId' => $requestId )));
            }
        }

        return $this->render('SimpleTradeBundle:Requests:skills_change.html.twig', array(
                'user' => $this->get('security.context')->getToken()->getUser(),
                'form' => $form->createView(),
                'company' => $company,
                'request' => $request,
                'files' => $files,
                'skills' => $skills
            ));
    }

    /**
     * @Route("/requests/block", name="requests_block")
     * @Permissions(perm="/requests/block")
     * @Template()
     */
    public function blockAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $requestsModel = new RequestsModel($em, $session, $acl);
        $requests = $requestsModel->findAllByType('BLOCK');

        return $this->render('SimpleTradeBundle:Requests:block_index.html.twig', array(
                'requests' => $requests,
                'user' => $this->get('security.context')->getToken()->getUser()
            ));
    }

    /**
     * @Route("/requests/block/show/{requestId}", name="requests_block_show")
     * @Permissions(perm="/requests/block/show")
     * @Template()
     */
    public function blockShowAction($requestId)
    {
        if (!is_numeric($requestId)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $requestsModel = new RequestsModel($em, $session, $acl);
        $request = $requestsModel->findByPK($requestId);
        if (!isset($request)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $company = $request->getCompany();

        $documentsLinksModel = new DocumentsLinksModel($em, $session);
        $files = $documentsLinksModel->findAllByOwner($company->getId(), 'COMPANY');

        $companiesSkillsModel = new CompaniesSkillsModel($em, $session);
        $skills = implode(',', $companiesSkillsModel->findSkillsByCompanyIdAsArray($company->getId()));

        $requestsBlockModel = new RequestsBlockModel($em, $session);
        $requestBlock = $requestsBlockModel->findOneByRequestId($requestId);

        return $this->render('SimpleTradeBundle:Requests:block_show.html.twig', array(
                'user' => $this->get('security.context')->getToken()->getUser(),
                'company' => $company,
                'request' => $request,
                'files' => $files,
                'skills' => $skills,
                'requestBlock' => $requestBlock
            ));
    }

    /**
     * @Route("/requests/sros", name="requests_sros")
     * @Permissions(perm="/requests/sros")
     * @Template()
     */
    public function srosAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $requestsModel = new RequestsModel($em, $session, $acl);
        $requests = $requestsModel->findAllByType('ENTER_SRO');

        return $this->render('SimpleTradeBundle:Requests:sros_index.html.twig', array(
                'requests' => $requests,
                'user' => $this->get('security.context')->getToken()->getUser()
            ));
    }

    /**
     * @Route("/requests/sros/show/{requestId}", name="requests_sros_show")
     * @Permissions(perm="/requests/sros/show")
     * @Template()
     */
    public function srosShowAction($requestId)
    {
        if (!is_numeric($requestId)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $requestsModel = new RequestsModel($em, $session, $acl);
        $request = $requestsModel->findByPK($requestId);
        if (!isset($request)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $company = $request->getCompany();

        $documentsLinksModel = new DocumentsLinksModel($em, $session);
        $files = $documentsLinksModel->findAllByOwner($company->getId(), 'COMPANY');

        $companiesSkillsModel = new CompaniesSkillsModel($em, $session);
        $skills = implode(', ', $companiesSkillsModel->findSkillsByCompanyIdAsArray($company->getId()));

        $requestsSrosModel = new RequestsSrosModel($em, $session);
        $requestSros = $requestsSrosModel->findOneByRequestId($requestId);

        $skillsModel = new SkillsModel($em);
        $sroSkillsArray = $skillsModel->findAllBySrosTypesAsArray(array( $requestSros->getSro()->getType() ));
        $sroSkillsArray = array_pop($sroSkillsArray);
        $sroSkills = implode(', ', $sroSkillsArray[ 'skills' ]);

        return $this->render('SimpleTradeBundle:Requests:sros_show.html.twig', array(
                'user' => $this->get('security.context')->getToken()->getUser(),
                'company' => $company,
                'request' => $request,
                'files' => $files,
                'skills' => $skills,
                'requestSros' => $requestSros,
                'sroSkills' => $sroSkills
            ));
    }

    /**
     * @Route("/requests/complaints", name="requests_complaints")
     * @Permissions(perm="/requests/complaints")
     * @Template()
     */
    public function complaintsAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $requestsModel = new RequestsModel($em, $session, $acl);
        $requests = $requestsModel->findAllByType('COMPLAINT');

        return $this->render('SimpleTradeBundle:Requests:complaints_index.html.twig', array(
                'requests' => $requests,
                'user' => $this->get('security.context')->getToken()->getUser()
            ));
    }

    /**
     * @Route("/requests/complaints/show/{requestId}", name="requests_complaints_show")
     * @Permissions(perm="/requests/complaints/show")
     * @Template()
     */
    public function complaintsShowAction($requestId)
    {
        if (!is_numeric($requestId)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $requestsModel = new RequestsModel($em, $session, $acl);
        $request = $requestsModel->findByPK($requestId);
        if (!isset($request)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $documentsLinksModel = new DocumentsLinksModel($em, $session);
        $files = $documentsLinksModel->findAllByOwner($request->getId(), 'REQUEST');

        $requestsComplaintsModel = new RequestsComplaintsModel($em, $session);
        $requestComplaints = $requestsComplaintsModel->findOneByRequestId($requestId);

        $company = $requestComplaints->getCompany();

        return $this->render('SimpleTradeBundle:Requests:complaints_show.html.twig', array(
                'user' => $this->get('security.context')->getToken()->getUser(),
                'company' => $company,
                'request' => $request,
                'files' => $files,
                'requestComplaints' => $requestComplaints
            ));
    }

    /**
     * @Route("/requests/auctions", name="requests_auctions")
     * @Permissions(perm="/requests/auctions")
     * @Template()
     */
    public function auctionsAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $auctionsModel = new AuctionsModel($em, $session, $acl);

        $userCurrent = $this->get('security.context')->getToken()->getUser();
        $auctions = $auctionsModel->findByStatusesWithContent(array( 'PRE_PUBLIC' ));

        return $this->render('SimpleTradeBundle:Requests:auctions_index.html.twig', array(
                'auctions' => $auctions,
                'user' => $userCurrent
            ));
    }

    /**
     * @Route("/requests/register/approve/{companyId}")
     * @Permissions(perm="/requests/register/approve")
     * @Template()
     */
    public function registerApproveAction($companyId)
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

        $form = $this->createForm(new RequestRegisterApproveForm());
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $formData = $form->getData();
                $em->getConnection()->beginTransaction();
                try {
                    $documentsModel = new DocumentsModel($em, $session, $acl);
                    $documentsLinksModel = new DocumentsLinksModel($em, $session);
                    foreach( $formData[ 'files' ] as $value ) {
                        $document = $documentsModel->create(array( 'file' => $value->getFile(),
                            'title' => $value->getTitle(),
                            ), array( 'isActive' => 1 ));

                        $documentsLinksModel->create(array( 'document' => $document,
                            'owner' => 'COMPANY',
                            'ownerId' => $company->getId(),
                        ));
                    }

                    $companiesStatusesModel = new CompaniesStatusesModel($em);
                    $activeStatus = $companiesStatusesModel->findOneByName('ACTIVE');
                    $content = $company->getContent()->getAllFieldsAsArray();

                    switch( $company->getType()->getName() ) {
                        case 'CUSTOMER':
                            $companiesModel->update($companyId, $content, array( 'status' => $activeStatus, 'type' => $company->getType(), 'penalty' => $company->getPenalty() ));
                            break;
                        case 'PERFORMER':
                            $srosCompaniesModel = new SrosCompaniesModel($em, $session);
                            $srosCompanies = $srosCompaniesModel->findAllByCompanyIdAndStatus($company->getId(), 'PRE_REGISTRATION');

                            foreach( $srosCompanies as $srosCompany ) {
                                $srosCompaniesModel->updateFromObjects($srosCompany->getId(), array(
                                    'comment' => '',
                                    'sro' => $srosCompany->getSro(),
                                    'company' => $srosCompany->getCompany(),
                                    'status' => $activeStatus
                                ));
                            }

                            $companiesModel->update($companyId, $content, array( 'status' => $activeStatus, 'type' => $company->getType(), 'penalty' => $company->getPenalty() ));
                            break;
                    }

                    $usersCompaniesModel = new UsersCompaniesModel($em, $session);
                    $usersRolesModel = new UsersRolesModel($em, $session);
                    $users = $usersCompaniesModel->findAllUsersByCompanyId($companyId);
                    foreach( $users as $user ) {
                        $usersRolesModel->approve($user);
                    }

                    $em->getConnection()->commit();
                } catch( Exception $e ) {
                    $em->getConnection()->rollback();
                    $em->close();
                    throw $e;
                }

                $this->get('mail')->sendByUser(Constants::MAIL_2USER_REGISTRATION_CONFIRMATION, $this->get('users')->findAllByCompanyIdAndRole($company->getId(), Constants::ROLE_COMPANY_ADMIN), array( 'company' => $company, ));

                $session->getFlashBag()->add('notice', 'Регистрация компании успешно подтверждена');

                return $this->redirect($this->generateUrl('requests_register'));
            }
        }
        return $this->render('SimpleTradeBundle:Requests:register_approve.html.twig', array(
                'form' => $form->createView(),
                'company' => $company
            ));
    }

    /**
     * @Route("/requests/skills/approve/{requestId}")
     * @Permissions(perm="/requests/skills/approve")
     * @Template()
     */
    public function skillsApproveAction($requestId)
    {
        if (!is_numeric($requestId)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $requestsModel = new RequestsModel($em, $session, $acl);
        $request = $requestsModel->findByPK($requestId);
        if (!isset($request)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $company = $request->getCompany();

        $requestsSkillsModel = new RequestsSkillsModel($em, $session);

        $em->getConnection()->beginTransaction();
        try {
            $requestsStatusesModel = new RequestsStatusesModel($em);
            $approvedStatus = $requestsStatusesModel->findOneByName('APPROVED');

            $requestsModel->update($requestId, array( ), array(
                'company' => $company,
                'status' => $approvedStatus,
                'type' => $request->getType() ));

            $companiesSkillsModel = new CompaniesSkillsModel($em, $session);
            $companiesSkillsModel->removeByCompanyId($company->getId());

            $skills = $requestsSkillsModel->findAllSkillsByRequestId($requestId);

            foreach( $skills as $skill ) {
                $companiesSkillsModel->create(array( 'company' => $company->getId(),
                    'skill' => $skill->getSkill()->getId(),
                    'isMain' => $skill->getIsMain(),
                    'isDangerous' => $skill->getIsDangerous()
                ));
            }

            $em->getConnection()->commit();
        } catch( Exception $e ) {
            $em->getConnection()->rollback();
            $em->close();
            throw $e;
        }

        /* Отправляем подрядчику письмо о повышении квалификации
         *
         */
        $this->get('mail')->sendByUser(Constants::MAIL_2CONTRACTOR_QUALIFICATION_INCREASE, $this->get('users')->findAllByCompanyIdAndRole($company->getId(), Constants::ROLE_COMPANY_ADMIN), array( 'company' => $company, ));

        /**
         * Отправляем СРО письмо о повышении квалификации
         * Отправляем только тем СРО, которые есть в запросе
         *
         */
        $emails = array( );
        foreach( $requestsSkillsModel->findAllSROByRequestIdAndCompanyId($requestId, $company->getId()) as $sro ) {
            $emails[ ] = $sro->getContent()->getEmail();
        }
        $this->get('mail')->sendByEmail(Constants::MAIL_2SRO_QUALIFICATION_INCREASE, $emails, array( 'company' => $company ));

        $session->getFlashBag()->add('notice', 'Повышение квалификации успешно подтверждена');

        return $this->redirect($this->generateUrl('requests_skills'));
    }

    /**
     * @Route("/requests/block/approve/{requestId}")
     * @Permissions(perm="/requests/block/approve")
     * @Template()
     */
    public function requestsBlockApproveAction($requestId)
    {
        if (!is_numeric($requestId)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $request = $this->get('request');
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $requestsModel = new RequestsModel($em, $session, $acl);
        $req = $requestsModel->findByPK($requestId);
        if (!isset($req)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $company = $req->getCompany();

        $content = $company->getContent()->getAllFieldsAsArray();

        $form = $this->createForm(new CompaniesBlockForm(), $content);
        if ($request->isMethod('POST')) {
            $form = $this->createForm(new CompaniesBlockForm());
            $form->bind($request);
            if ($form->isValid()) {
                $formData = $form->getData();
                $em->getConnection()->beginTransaction();
                try {
                    $requestsStatusesModel = new RequestsStatusesModel($em);
                    $approvedStatus = $requestsStatusesModel->findOneByName('APPROVED');

                    $requestsModel->update($requestId, array( ), array(
                        'company' => $company,
                        'status' => $approvedStatus,
                        'type' => $req->getType() ));

                    $content[ 'comment' ] = $formData[ 'comment' ];
                    $companiesModel = new CompaniesModel($em, $session, $acl);

                    $companiesStatusesModel = new CompaniesStatusesModel($em);
                    $blockedStatus = $companiesStatusesModel->findOneByName('BLOCKED');

                    switch( $company->getType()->getName() ) {
                        case 'CUSTOMER':
                            $companiesModel->update($company->getId(), $content, array(
                                'status' => $blockedStatus,
                                'type' => $company->getType(),
                                'penalty' => $company->getPenalty() ));
                            break;
                        case 'PERFORMER':
                            $requestsBlockModel = new RequestsBlockModel($em, $session);
                            $requestBlock = $requestsBlockModel->findOneByRequestId($requestId);

                            $sro = $requestBlock->getSro();
                            $srosCompaniesModel = new SrosCompaniesModel($em, $session);
                            $srosCompany = $srosCompaniesModel->findOneByCompanyIdAndSroId($company->getId(), $sro->getId());
                            $srosCompaniesModel->updateFromObjects($srosCompany->getId(), array(
                                'comment' => $formData[ 'comment' ],
                                'sro' => $sro,
                                'company' => $company,
                                'status' => $blockedStatus
                            ));

                            $srosCompanies = $srosCompaniesModel->findAllByCompanyIdAndStatus($company->getId(), 'ACTIVE');
                            if (count($srosCompanies) == 0) {
                                $companiesModel->update($company->getId(), $content, array(
                                    'status' => $blockedStatus,
                                    'type' => $company->getType(),
                                    'penalty' => $company->getPenalty() ));
                            }
                            break;
                    }

                    $em->getConnection()->commit();
                } catch( Exception $e ) {
                    $em->getConnection()->rollback();
                    $em->close();
                    throw $e;
                }
                $session->getFlashBag()->add('notice', 'Блокировка компании успешно подтверждена');

                /* @TODO отправка писем
                 * Система отправляет Подрядчику уведомление о блокировке его компании на площадке
                 */

                return $this->redirect($this->generateUrl('requests_block'));
            }
        }
        return $this->render('SimpleTradeBundle:Requests:block_approve.html.twig', array(
                'form' => $form->createView(),
                'company' => $company
            ));
    }

    /**
     * @Route("/requests/sros/approve/{requestId}")
     * @Permissions(perm="/requests/sros/approve")
     * @Template()
     */
    public function srosApproveAction($requestId)
    {
        if (!is_numeric($requestId)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $requestsModel = new RequestsModel($em, $session, $acl);
        $request = $requestsModel->findByPK($requestId);
        if (!isset($request)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $company = $request->getCompany();

        $requestsSrosModel = new RequestsSrosModel($em, $session);
        $requestSros = $requestsSrosModel->findOneByRequestId($requestId);

        $em->getConnection()->beginTransaction();
        try {
            $requestsStatusesModel = new RequestsStatusesModel($em);
            $approvedStatus = $requestsStatusesModel->findOneByName('APPROVED');

            $requestsModel->update($requestId, array( ), array(
                'company' => $company,
                'status' => $approvedStatus,
                'type' => $request->getType() ));

            $srosCompaniesModel = new SrosCompaniesModel($em, $session);
            $srosCompaniesModel->create(array( 'sro' => $requestSros->getSro()->getId(),
                'company' => $company->getId(),
                'status' => 'ACTIVE' ));

            $em->getConnection()->commit();
        } catch( Exception $e ) {
            $em->getConnection()->rollback();
            $em->close();
            throw $e;
        }


        /* @TODO
         * Отправить письма, я так думаю, админу компании-подрядчика и в СРО
         */

        $session->getFlashBag()->add('notice', 'Вступление в СРО успешно подтверждено');

        return $this->redirect($this->generateUrl('requests_sros'));
    }

    /**
     * @Route("/requests/complaints/approve/{requestId}")
     * @Permissions(perm="/requests/complaints/approve")
     * @Template()
     */
    public function complaintsApproveAction($requestId)
    {
        if (!is_numeric($requestId)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $requestsModel = new RequestsModel($em, $session, $acl);
        $request = $requestsModel->findByPK($requestId);
        if (!isset($request)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $company = $request->getCompany();

        $requestsComplaintsModel = new RequestsComplaintsModel($em, $session);
        $requestComplaints = $requestsComplaintsModel->findOneByRequestId($requestId);

        $em->getConnection()->beginTransaction();
        try {
            $requestsStatusesModel = new RequestsStatusesModel($em);
            $approvedStatus = $requestsStatusesModel->findOneByName('APPROVED');

            $requestsModel->update($requestId, array( ), array(
                'company' => $company,
                'status' => $approvedStatus,
                'type' => $request->getType() ));

            $em->getConnection()->commit();
        } catch( Exception $e ) {
            $em->getConnection()->rollback();
            $em->close();
            throw $e;
        }

        /* @TODO
         * Отправить уведомление, наверно, что жалоба рассмотрена
         */

        $session->getFlashBag()->add('notice', 'Жалоба успешно рассмотрена');

        return $this->redirect($this->generateUrl('requests_complaints'));
    }

    /**
     * @Route("/requests/company", name="requests_company")
     * @Permissions(perm="/requests/company")
     * @Template()
     */
    public function companyAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $usersCompaniesModel = new UsersCompaniesModel($em, $session);

        $userCurrent = $this->get('security.context')->getToken()->getUser();
        $usersCompany = $usersCompaniesModel->findOneByUserId($userCurrent->getId());
        $company = $usersCompany->getCompany();

        $requestsModel = new RequestsModel($em, $session, $acl);
        $requests = $requestsModel->findAllByCompanyId($company->getId());

        return $this->render('SimpleTradeBundle:Requests:company_requests.html.twig', array(
                'user' => $this->get('security.context')->getToken()->getUser(),
                'company' => $company,
                'requests' => $requests
            ));
    }

    /**
     * @Route("/requests/sro", name="requests_sro")
     * @Permissions(perm="/requests/sro")
     * @Template()
     */
    public function sroAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $srosCompaniesModel = new SrosCompaniesModel($em, $session);
        $usersSrosModel = new UsersSrosModel($em, $session);

        $userCurrent = $this->get('security.context')->getToken()->getUser();
        $usersSro = $usersSrosModel->findOneByUserId($userCurrent->getId());
        $sro = $usersSro->getSro();

        $companiesIds = $srosCompaniesModel->findAllBySroIdAsArray($sro->getId());

        $requestsModel = new RequestsModel($em, $session, $acl);
        $requests = $requestsModel->findAllByCompaniesIds($companiesIds);

        return $this->render('SimpleTradeBundle:Requests:sro_requests.html.twig', array(
                'user' => $this->get('security.context')->getToken()->getUser(),
                'sro' => $sro,
                'requests' => $requests
            ));
    }

    /**
     * @Route("/requests/complaints/sro", name="requests_complaints_sro")
     * @Permissions(perm="/requests/complaints/sro")
     * @Template()
     */
    public function complaintsSroAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $requestsModel = new RequestsModel($em, $session, $acl);
        $requests = $requestsModel->findAllByType('COMPLAINT_SRO');

        return $this->render('SimpleTradeBundle:Requests:complaints_sro_index.html.twig', array(
                'requests' => $requests,
                'user' => $this->get('security.context')->getToken()->getUser()
            ));
    }

    /**
     * @Route("/requests/complaints/sro/show/{requestId}", name="requests_complaints_sro_show")
     * @Permissions(perm="/requests/complaints/sro/show")
     * @Template()
     */
    public function complaintsShowSroAction($requestId)
    {
        if (!is_numeric($requestId)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $requestsModel = new RequestsModel($em, $session, $acl);
        $request = $requestsModel->findByPK($requestId);
        if (!isset($request)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $documentsLinksModel = new DocumentsLinksModel($em, $session);
        $files = $documentsLinksModel->findAllByOwner($request->getId(), 'REQUEST');

        $requestsComplaintsModel = new RequestsComplaintsModel($em, $session);
        $requestComplaints = $requestsComplaintsModel->findOneByRequestId($requestId);

        $company = $requestComplaints->getCompany();

        return $this->render('SimpleTradeBundle:Requests:complaints_sro_show.html.twig', array(
                'user' => $this->get('security.context')->getToken()->getUser(),
                'company' => $company,
                'request' => $request,
                'files' => $files,
                'requestComplaints' => $requestComplaints
            ));
    }

    /**
     * @Route("/requests/complaints/sro/approve/{requestId}")
     * @Permissions(perm="/requests/complaints/sro/approve")
     * @Template()
     */
    public function complaintsApproveSroAction($requestId)
    {
        if (!is_numeric($requestId)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $requestsModel = new RequestsModel($em, $session, $acl);
        $request = $requestsModel->findByPK($requestId);
        if (!isset($request)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $company = $request->getCompany();

        $requestsComplaintsModel = new RequestsComplaintsModel($em, $session);
        $requestComplaints = $requestsComplaintsModel->findOneByRequestId($requestId);

        $em->getConnection()->beginTransaction();
        try {
            $requestsStatusesModel = new RequestsStatusesModel($em);
            $approvedStatus = $requestsStatusesModel->findOneByName('APPROVED');

            $requestsModel->update($requestId, array( ), array(
                'company' => $company,
                'status' => $approvedStatus,
                'type' => $request->getType() ));

            $em->getConnection()->commit();
        } catch( Exception $e ) {
            $em->getConnection()->rollback();
            $em->close();
            throw $e;
        }

        /* @TODO
         * Отправить уведомление, наверно, что жалоба рассмотрена
         */

        $session->getFlashBag()->add('notice', 'Жалоба успешно рассмотрена');

        return $this->redirect($this->generateUrl('requests_complaints_sro'));
    }

    /**
     * @Route("/requests/complaints/add/{companyId}")
     * @Permissions(perm="/requests/complaints/add")
     * @Template()
     */
    public function requestsComplaintAddAction($companyId)
    {
        if (!is_numeric($companyId)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $request = $this->get('request');
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $companiesModel = new CompaniesModel($em, $session, $acl);
        $toCompany = $companiesModel->findByPK($companyId);

        if (!isset($toCompany)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $userCurrent = $this->get('security.context')->getToken()->getUser();
        $usersCompaniesModel = new UsersCompaniesModel($em, $session);
        $usersCompany = $usersCompaniesModel->findOneByUserId($userCurrent->getId());
        $company = $usersCompany->getCompany();

        if ($toCompany === $company) {
            $session->getFlashBag()->add('notice', 'Нельзя пожаловаться на свою компанию.');

            return $this->redirect($this->generateUrl('requests_company'));
        }

        $form = $this->createForm(new ComplaintsAddForm());
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $formData = $form->getData();
                $em->getConnection()->beginTransaction();
                try {

                    $requestsStatusesModel = new RequestsStatusesModel($em);
                    $newStatus = $requestsStatusesModel->findOneByName('NEW');

                    $requestsTypesModel = new RequestsTypesModel($em);
                    $complaintType = $requestsTypesModel->findOneByName('COMPLAINT');
                    $complaintSroType = $requestsTypesModel->findOneByName('COMPLAINT_SRO');

                    $srosCompaniesModel = new SrosCompaniesModel($em, $session);
                    $requestsComplaintsModel = new RequestsComplaintsModel($em, $session);
                    $sros = $srosCompaniesModel->findAllByCompanyId($toCompany->getId());

                    $requestsModel = new RequestsModel($em, $session, $acl);
                    if (count($sros) > 0) {
                        $request = $requestsModel->create(array( ), array(
                            'company' => $company,
                            'status' => $newStatus,
                            'type' => $complaintSroType ));
                        foreach( $sros as $sro ) {
                            $requestsComplaintsModel->create(array(
                                'request' => $request,
                                'company' => $toCompany,
                                'sro' => $sro,
                                'text' => $formData[ 'text' ]
                            ));
                        }
                    } else {
                        $request = $requestsModel->create(array( ), array(
                            'company' => $company,
                            'status' => $newStatus,
                            'type' => $complaintType ));
                        $requestsComplaintsModel->create(array(
                            'request' => $request,
                            'company' => $toCompany,
                            'text' => $formData[ 'text' ]
                        ));
                    }

                    $documentsModel = new DocumentsModel($em, $session, $acl);
                    $documentsLinksModel = new DocumentsLinksModel($em, $session);
                    foreach( $formData[ 'files' ] as $value ) {
                        $document = $documentsModel->create(array( 'file' => $value->getFile(),
                            'title' => $value->getTitle(),
                            ), array( 'isActive' => 1 ));

                        $documentsLinksModel->create(array( 'document' => $document,
                            'owner' => 'REQUEST',
                            'ownerId' => $request->getId(),
                        ));
                    }

                    $em->getConnection()->commit();
                } catch( Exception $e ) {
                    $em->getConnection()->rollback();
                    $em->close();
                    throw $e;
                }
                $session->getFlashBag()->add('notice', 'Жалоба на компанию успешно создана');

                return $this->redirect($this->generateUrl('requests_company'));
            }
        }
        return $this->render('SimpleTradeBundle:Requests:add_complaint.html.twig', array(
                'form' => $form->createView(),
                'company' => $toCompany
            ));
    }

}
