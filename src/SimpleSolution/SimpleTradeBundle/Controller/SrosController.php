<?php

namespace SimpleSolution\SimpleTradeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SimpleSolution\SimpleTradeBundle\Annotations\Permissions;
use SimpleSolution\SimpleTradeBundle\Entity\Users;
use SimpleSolution\SimpleTradeBundle\Model\SrosModel;
use SimpleSolution\SimpleTradeBundle\Model\UsersModel;
use SimpleSolution\SimpleTradeBundle\Model\CompaniesModel;
use SimpleSolution\SimpleTradeBundle\Model\RolesModel;
use SimpleSolution\SimpleTradeBundle\Model\RegionsModel;
use SimpleSolution\SimpleTradeBundle\Model\UsersRolesModel;
use SimpleSolution\SimpleTradeBundle\Model\UsersSrosModel;
use SimpleSolution\SimpleTradeBundle\Model\CompaniesStatusesModel;
use SimpleSolution\SimpleTradeBundle\Model\CompaniesContentModel;
use SimpleSolution\SimpleTradeBundle\Model\AccountsModel;
use SimpleSolution\SimpleTradeBundle\Model\DocumentsLinksModel;
use SimpleSolution\SimpleTradeBundle\Model\DocumentsModel;
use SimpleSolution\SimpleTradeBundle\Model\UsersCompaniesModel;
use SimpleSolution\SimpleTradeBundle\Model\SrosTypesModel;
use SimpleSolution\SimpleTradeBundle\Model\RequestsModel;
use SimpleSolution\SimpleTradeBundle\Model\RequestsStatusesModel;
use SimpleSolution\SimpleTradeBundle\Model\RequestsTypesModel;
use SimpleSolution\SimpleTradeBundle\Model\RequestsSkillsModel;
use SimpleSolution\SimpleTradeBundle\Model\RequestsBlockModel;
use SimpleSolution\SimpleTradeBundle\Model\CompaniesSkillsModel;
use SimpleSolution\SimpleTradeBundle\Model\SrosCompaniesModel;
use SimpleSolution\SimpleTradeBundle\Model\SkillsModel;
use SimpleSolution\SimpleTradeBundle\Form\sros\SrosCreateForm;
use SimpleSolution\SimpleTradeBundle\Form\sros\CompaniesBlockForm;
use SimpleSolution\SimpleTradeBundle\Form\users\UsersAddForm;
use SimpleSolution\SimpleTradeBundle\Form\cabinet\CabinetCompaniesChangeForm;
use SimpleSolution\SimpleTradeBundle\Form\cabinet\CabinetCompaniesChangeSkillsForm;

class SrosController extends Controller
{
    private $controllerName = 'Sros';

    /**
     * @Route("/sros", name="sros")
     * @Permissions(perm="/sros")
     * @Template()
     */
    public function srosAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');
        $srosModel = new SrosModel($em, $session, $acl);

        $sros = $srosModel->findAll();
        return $this->render('SimpleTradeBundle:Sros:index.html.twig', array(
                'sros' => $sros,
                'user' => $this->get('security.context')->getToken()->getUser()
            ));
    }

    /**
     * @Route("/sros/users/{sroId}", name="sros_users")
     * @Permissions(perm="/sros/users")
     * @Template()
     */
    public function usersAction($sroId)
    {
        if (!is_numeric($sroId)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $factory = $this->get('security.encoder_factory');
        $acl = $this->get('acl');
        $usersModel = new UsersModel($em, $session, $factory);
        $srosModel = new SrosModel($em, $session, $acl);

        $sro = $srosModel->findByPK($sroId);
        if (!isset($sro)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $users = $usersModel->findAllBySroId($sroId);
        return $this->render('SimpleTradeBundle:Sros:users.html.twig', array(
                'users' => $users,
                'sro' => $sro,
                'user' => $this->get('security.context')->getToken()->getUser()
            ));
    }

    /**
     * @Route("/sros/companies", name="sros_companies")
     * @Permissions(perm="/sros/companies")
     * @Template()
     */
    public function companiesAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');
        $companiesModel = new CompaniesModel($em, $session, $acl);
        $usersSrosModel = new UsersSrosModel($em, $session);

        $userCurrent = $this->get('security.context')->getToken()->getUser();
        $usersSros = $usersSrosModel->findAllByUserId($userCurrent->getId());
        $sroCompanies = array( );
        foreach( $usersSros as $usersSro ) {
            $sroId = $usersSro->getSro()->getId();
            $sroCompanies[ $sroId ] = $companiesModel->findAllBySroId($sroId);
        }

        return $this->render('SimpleTradeBundle:Sros:companies.html.twig', array(
                'sroCompanies' => $sroCompanies,
                'user' => $userCurrent
            ));
    }

    /**
     * @Route("/sros/companies/change/{companyId}")
     * @Permissions(perm="/sros/companies/change")
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
        $form = $this->createForm(new CabinetCompaniesChangeForm($regions, $content->getRegion()->getId()), $content);
        if ($request->isMethod('POST')) {
            $form = $this->createForm(new CabinetCompaniesChangeForm($regions));
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

                return $this->redirect($this->generateUrl('sros_companies'));
            }
        }
        return $this->render('SimpleTradeBundle:Sros:change_companies.html.twig', array(
                'form' => $form->createView(),
                'company' => $company,
                'files' => $files
            ));
    }

    /**
     * @Route("/sros/companies/block/{companyId}")
     * @Permissions(perm="/sros/companies/block")
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

        $requestsModel = new RequestsModel($em, $session, $acl);
        if ($requestsModel->checkByCompanyIdStatusType($company->getId(), 'NEW', 'BLOCK')) {
            $session->getFlashBag()->add('notice', 'У вас уже подана заявка, не рассмотренная администратором');

            return $this->redirect($this->generateUrl('sros_companies'));
        }

        $form = $this->createForm(new CompaniesBlockForm());
        if ($request->isMethod('POST')) {
            $form = $this->createForm(new CompaniesBlockForm());
            $form->bind($request);
            if ($form->isValid()) {
                $formData = $form->getData();
                $em->getConnection()->beginTransaction();
                try {
                    $requestsStatusesModel = new RequestsStatusesModel($em);
                    $newStatus = $requestsStatusesModel->findOneByName('NEW');

                    $requestsTypesModel = new RequestsTypesModel($em);
                    $blockType = $requestsTypesModel->findOneByName('BLOCK');

                    $request = $requestsModel->create(array( ), array(
                        'company' => $company,
                        'status' => $newStatus,
                        'type' => $blockType ));

                    $userCurrent = $this->get('security.context')->getToken()->getUser();

                    $usersSrosModel = new UsersSrosModel($em, $session);
                    $usersSro = $usersSrosModel->findOneByUserId($userCurrent->getId());
                    $sro = $usersSro->getSro();

                    $requestsBlockModel = new RequestsBlockModel($em);
                    $requestsBlockModel->create(array(
                        'comment' => $formData[ 'comment' ],
                        'request' => $request,
                        'sro' => $sro
                    ));
                    $em->getConnection()->commit();
                } catch( Exception $e ) {
                    $em->getConnection()->rollback();
                    $em->close();
                    throw $e;
                }
                $session->getFlashBag()->add('notice', 'Заявка на блокировку компании успешно отправлена');

                return $this->redirect($this->generateUrl('sros_companies'));
            }
        }
        return $this->render('SimpleTradeBundle:Sros:block_companies.html.twig', array(
                'form' => $form->createView(),
                'company' => $company
            ));
    }

    /**
     * @Route("/sros/companies/active/{companyId}")
     * @Permissions(perm="/sros/companies/active")
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
        if (!isset($company)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $activeStatus = $companiesStatusesModel->findOneByName('ACTIVE');
        $content = $company->getContent()->getAllFieldsAsArray();

        $companiesModel->update($companyId, $content, array( 'status' => $activeStatus, 'type' => $company->getType(), 'penalty' => $company->getPenalty() ));

        $session->getFlashBag()->add('notice', 'Компания успешно разблокирована');

        return $this->redirect($this->generateUrl('sros_companies'));
    }

    /**
     * @Route("/sros/users/add/{sroId}")
     * @Permissions(perm="/sros/users/add")
     * @Template()
     */
    public function usersCreateAction($sroId)
    {
        if (!is_numeric($sroId)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $request = $this->get('request');
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $factory = $this->get('security.encoder_factory');
        $acl = $this->get('acl');

        $srosModel = new SrosModel($em, $session, $acl);
        $sro = $srosModel->findByPK($sroId);
        if (!isset($sro)) return $this->redirect($this->generateUrl('login'));

        $rolesModel = new RolesModel($em);
        $permissions = $rolesModel->findAllAsArray();

        $regionsModel = new RegionsModel($em);
        $regions = $regionsModel->findAll();

        $user = new Users();
        $form = $this->createForm(new UsersAddForm($permissions, $regions));
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $usersModel = new UsersModel($em, $session, $factory);
                $usersRolesModel = new UsersRolesModel($em, $session);
                $usersSrosModel = new UsersSrosModel($em, $session);
//                $accountsModel = new AccountsModel($em, $session, $acl);

                $formData = $form->getData();
                $em->getConnection()->beginTransaction();
                try {
                    $region = $regionsModel->findByPK($formData[ 'regionId' ]);

                    $user = $usersModel->create(array( 'login' => $formData[ 'login' ],
                        'name' => $formData[ 'name' ],
                        'password' => $formData[ 'password' ],
                        'email' => $formData[ 'email' ],
                        'region' => $region ));
                    $userId = $user->getId();
                    $roles = $formData[ 'permissions' ];
                    foreach( $roles as $role ) {
                        $usersRolesModel->create(array( 'user' => $userId,
                            'role' => $role ));
                    }
                    $usersSrosModel->createFromObjects(array( 'user' => $user,
                        'sro' => $sro ));

//                    $accountsModel->create(array( 'account' => 500,
//                        'tariff' => null ), array( 'company' => null ));

                    $em->getConnection()->commit();
                } catch( Exception $e ) {
                    $em->getConnection()->rollback();
                    $em->close();
                    throw $e;
                }

                $session->getFlashBag()->add('notice', 'Пользователь успешно создан');

                return $this->redirect($this->generateUrl('sros_users', array( 'sroId' => $sroId )));
            }
        }
        return $this->render('SimpleTradeBundle:Sros:create_user.html.twig', array(
                'form' => $form->createView(),
                'sro' => $sro
            ));
    }

    /**
     * @Route("/sros/users/remove/{id}")
     * @Permissions(perm="/sros/users/remove")
     * @Template()
     */
    public function usersRemoveAction($id)
    {
        if (!is_numeric($id)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $factory = $this->get('security.encoder_factory');
        $usersModel = new UsersModel($em, $session, $factory);
        $usersRolesModel = new UsersRolesModel($em, $session);
        $usersSrosModel = new UsersSrosModel($em, $session);

        $user = $usersModel->findByPK($id);
        if (!isset($user)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $sro = $usersSrosModel->findOneByUserId($id)->getSro();
        $usersRolesModel->removeByUserId($id);
        $usersSrosModel->removeByUserId($id);

        if (count($usersSrosModel->findAllBySroId($id)) == 0) {
            $usersModel->removeByPK($id);
        }
        return $this->redirect($this->generateUrl('sros_users', array( 'sroId' => $sro->getId() )));
    }

    /**
     * @Route("/sros/add")
     * @Permissions(perm="/sros/add")
     * @Template()
     */
    public function srosAddAction()
    {
        $request = $this->get('request');
        $em = $this->get('doctrine.orm.entity_manager');

        $regionsModel = new RegionsModel($em);
        $regions = $regionsModel->findAll();

        $srosTypesModel = new SrosTypesModel($em);
        $srosTypes = $srosTypesModel->findAll();

        $form = $this->createForm(new SrosCreateForm($regions, $srosTypes));

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {

                $session = $this->get('session');
                $acl = $this->get('acl');
                $factory = $this->get('security.encoder_factory');

                $formData = $form->getData();

                $em->getConnection()->beginTransaction();
                try {
                    /**
                     * Create SRO
                     */
                    $type = $srosTypesModel->findByPK($formData[ 'srosType' ]);

                    $srosModel = new SrosModel($em, $session, $acl);
                    $sro = $srosModel->create($formData, array( 'type' => $type ));

                    /**
                     * Create SRO User
                     */
                    $region = $regionsModel->findByPK($formData[ 'region' ]);

                    $usersModel = new UsersModel($em, $session, $factory);
                    $a = array(
                        'login' => $formData[ 'login' ],
                        'name' => $formData[ 'name' ],
                        'password' => $formData[ 'password' ],
                        'email' => $formData[ 'email' ],
                        'region' => $region
                    );
                    $user = $usersModel->create($a);

                    /**
                     * Create account for user
                     */
//                    $accountsModel = new AccountsModel($em, $session, $acl);
//                    $accountsModel->create(array( 'account' => 500,
//                        'tariff' => null ), array( 'user' => $user ));
                    /**
                     * Create SRO-User link
                     */
                    $usersSrosModel = new UsersSrosModel($em, $session);
                    $usersSrosModel->createFromObjects(array( 'user' => $user,
                        'sro' => $sro ));

                    /**
                     * Take SRO role
                     */
                    $rolesModel = new RolesModel($em);
                    $sroRole = $rolesModel->findOneByName('SRO');

                    /**
                     * Set SRO role to user
                     */
                    $usersRolesModel = new UsersRolesModel($em, $session);
                    $usersRolesModel->createFromObjects(array( 'user' => $user, 'role' => $sroRole ));

                    $em->getConnection()->commit();
                } catch( Exception $e ) {
                    $em->getConnection()->rollback();
                    $em->close();
                    throw $e;
                }

                $session->getFlashBag()->add('notice', 'СРО успешно зарегистрирована');

                return $this->redirect($this->generateUrl('sros'));
            }
        }

        return $this->render('SimpleTradeBundle:Sros:create.html.twig', array(
                'form' => $form->createView(),
            ));
    }

    /**
     * @Route("/sros/remove/{id}")
     * @Permissions(perm="/sros/remove")
     * @Template()
     */
    public function srosRemoveAction($id)
    {
        if (!is_numeric($id)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $srosModel = new SrosModel($em, $session, $acl);

        $sros = $srosModel->findByPK($id);
        if (null === $sros) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em->getConnection()->beginTransaction();
        try {
            $usersSrosModel = new UsersSrosModel($em, $session);
            $usersSrosModel->removeBySroId($id);

            $srosModel->removeByPK($id);
            $em->getConnection()->commit();
        } catch( Exception $e ) {
            $em->getConnection()->rollback();
            $em->close();
            throw $e;
        }


        return $this->redirect($this->generateUrl('sros'));
    }

    /**
     * @Route("/sros/companies/change/skills/{companyId}")
     * @Permissions(perm="/sros/companies/change/skills")
     * @Template()
     */
    public function companiesChangeSkillsAction($companyId)
    {
        $request = $this->get('request');
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $companiesModel = new CompaniesModel($em, $session, $acl);
        $company = $companiesModel->findByPK($companyId);
        if (!isset($company)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $requestsModel = new RequestsModel($em, $session, $acl);
        if ($requestsModel->checkByCompanyIdStatusType($company->getId(), 'NEW', 'SKILLS')) {
            $session->getFlashBag()->add('notice', 'У вас уже подана заявка, не рассмотренная администратором');

            return $this->redirect($this->generateUrl('sros_companies'));
        }

        $documentsLinksModel = new DocumentsLinksModel($em, $session);
        $files = $documentsLinksModel->findAllByOwner($company->getId(), 'COMPANY');

        $companiesSkillsModel = new CompaniesSkillsModel($em, $session);
        $skillsArray = $companiesSkillsModel->findSkillsByCompanyIdAsArrayOfIds($company->getId());

        $skillsSelected = implode(',', array_keys($skillsArray));
        $attrsSelected = implode(',', $skillsArray);

        $srosCompaniesModel = new SrosCompaniesModel($em, $session);
        $srosTypes = $srosCompaniesModel->findSrosTypesByCompanyId($company->getId());

        $skillsModel = new SkillsModel($em);
        $skills = $skillsModel->findAllBySrosTypesAsArray($srosTypes);

        $form = $this->createForm(new CabinetCompaniesChangeSkillsForm($skillsSelected, $attrsSelected));
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $formData = $form->getData();
                $em->getConnection()->beginTransaction();
                try {

                    $requestsStatusesModel = new RequestsStatusesModel($em);
                    $requestsTypesModel = new RequestsTypesModel($em);

                    $newStatus = $requestsStatusesModel->findOneByName('NEW');

                    $skillsType = $requestsTypesModel->findOneByName('SKILLS');

                    $request = $requestsModel->create(array( ), array(
                        'company' => $company,
                        'status' => $newStatus,
                        'type' => $skillsType ));

                    $requestsSkillsModel = new RequestsSkillsModel($em);

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
                $session->getFlashBag()->add('notice', 'Заявка отправлена администратору');

                return $this->redirect($this->generateUrl('sros_companies'));
            }
        }
        return $this->render('SimpleTradeBundle:Sros:change_skills.html.twig', array(
                'form' => $form->createView(),
                'company' => $company,
                'files' => $files,
                'skills' => $skills
            ));
    }

}
