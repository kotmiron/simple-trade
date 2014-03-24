<?php

namespace SimpleSolution\SimpleTradeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SimpleSolution\SimpleTradeBundle\Annotations\Permissions;
use SimpleSolution\SimpleTradeBundle\Model\CompaniesModel;
use SimpleSolution\SimpleTradeBundle\Model\CompaniesContentModel;
use SimpleSolution\SimpleTradeBundle\Model\DocumentsModel;
use SimpleSolution\SimpleTradeBundle\Model\DocumentsLinksModel;
use SimpleSolution\SimpleTradeBundle\Model\DocumentsContentModel;
use SimpleSolution\SimpleTradeBundle\Model\RegionsModel;
use SimpleSolution\SimpleTradeBundle\Model\UsersCompaniesModel;
use SimpleSolution\SimpleTradeBundle\Model\UsersModel;
use SimpleSolution\SimpleTradeBundle\Model\RolesModel;
use SimpleSolution\SimpleTradeBundle\Model\SkillsModel;
use SimpleSolution\SimpleTradeBundle\Model\UsersRolesModel;
use SimpleSolution\SimpleTradeBundle\Model\SrosCompaniesModel;
use SimpleSolution\SimpleTradeBundle\Model\CompaniesSkillsModel;
use SimpleSolution\SimpleTradeBundle\Model\RequestsModel;
use SimpleSolution\SimpleTradeBundle\Model\RequestsSkillsModel;
use SimpleSolution\SimpleTradeBundle\Model\RequestsStatusesModel;
use SimpleSolution\SimpleTradeBundle\Model\RequestsSrosModel;
use SimpleSolution\SimpleTradeBundle\Model\RequestsTypesModel;
use SimpleSolution\SimpleTradeBundle\Model\SrosModel;
use SimpleSolution\SimpleTradeBundle\Form\cabinet\CabinetCompaniesChangeForm;
use SimpleSolution\SimpleTradeBundle\Form\cabinet\CabinetCompaniesChangeSkillsForm;
use SimpleSolution\SimpleTradeBundle\Form\cabinet\CabinetCompaniesDocumentsChangeForm;
use SimpleSolution\SimpleTradeBundle\Form\cabinet\UsersAddForm;
use SimpleSolution\SimpleTradeBundle\Form\users\UsersChangeForm;
use SimpleSolution\SimpleTradeBundle\Form\cabinet\CabinetFilesForm;
use SimpleSolution\SimpleTradeBundle\Form\cabinet\CabinetSrosEnterForm;

class CabinetController extends Controller
{

    /**
     * @Route("/cabinet", name="cabinet")
     * @Permissions(perm="/cabinet")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');

        $userCurrent = $this->get('security.context')->getToken()->getUser();

        $usersCompaniesModel = new UsersCompaniesModel($em, $session);
        $usersCompany = $usersCompaniesModel->findOneByUserId($userCurrent->getId());
        $company = $usersCompany->getCompany();

        return $this->render('SimpleTradeBundle:Cabinet:index.html.twig', array(
                'user' => $userCurrent,
                'company' => $company ));
    }

//  закомментировано до "второго этапа", когда юзеры-компании будет многое ко многим
//    /**
//     * @Route("/cabinet/companies", name="cabinet_companies")
//     * @Permissions(perm="/cabinet/companies")
//     * @Template()
//     */
//    public function companiesAction()
//    {
//        $request = $this->get('request');
//        $em = $this->get('doctrine.orm.entity_manager');
//        $session = $this->get('session');
//        $acl = $this->get('acl');
//
//        $companiesModel = new CompaniesModel($em, $session, $acl);
//
//        $userCurrent = $this->get('security.context')->getToken()->getUser();
//        $companies = $companiesModel->findAllByUserId($userCurrent->getId());
//
//        return $this->render('SimpleTradeBundle:Cabinet\Companies:index.html.twig',
//                array('user' => $userCurrent,
//                      'companies' => $companies
//        ));
//    }

    /**
     * @Route("/cabinet/companies/change")
     * @Permissions(perm="/cabinet/companies/change")
     * @Template()
     */
    public function companiesChangeAction()
    {
        $request = $this->get('request');
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');
        $factory = $this->get('security.encoder_factory');

        $companiesModel = new CompaniesModel($em, $session, $acl);
        $usersCompaniesModel = new UsersCompaniesModel($em, $session);
        $usersModel = new UsersModel($em, $session, $factory);

        $userCurrent = $this->get('security.context')->getToken()->getUser();
        $usersCompany = $usersCompaniesModel->findOneByUserId($userCurrent->getId());
        $company = $usersCompany->getCompany();

        $documentsLinksModel = new DocumentsLinksModel($em, $session);
        $files = $documentsLinksModel->findAllByOwner($company->getId(), 'COMPANY');

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
                $companiesContentModel = new CompaniesContentModel($em);
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

                    $usersModel->update(array( 'id' => $userCurrent->getId(),
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

                return $this->redirect($this->generateUrl('cabinet'));
            }
        }
        return $this->render('SimpleTradeBundle:Cabinet\Companies:change.html.twig', array(
                'form' => $form->createView(),
                'company' => $company,
                'files' => $files
            ));
    }

    /**
     * @Route("/cabinet/companies/change/skills")
     * @Permissions(perm="/cabinet/companies/change/skills")
     * @Template()
     */
    public function companiesChangeSkillsAction()
    {
        $request = $this->get('request');
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $usersCompaniesModel = new UsersCompaniesModel($em, $session);

        $userCurrent = $this->get('security.context')->getToken()->getUser();
        $usersCompany = $usersCompaniesModel->findOneByUserId($userCurrent->getId());
        $company = $usersCompany->getCompany();

        $requestsModel = new RequestsModel($em, $session, $acl);
        if ($requestsModel->checkByCompanyIdStatusType($company->getId(), 'NEW', 'SKILLS')) {
            $session->getFlashBag()->add('notice', 'У вас уже подана заявка, не рассмотренная администратором');

            return $this->redirect($this->generateUrl('cabinet'));
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

                return $this->redirect($this->generateUrl('cabinet'));
            }
        }
        return $this->render('SimpleTradeBundle:Cabinet\Companies:change_skills.html.twig', array(
                'form' => $form->createView(),
                'company' => $company,
                'files' => $files,
                'skills' => $skills
            ));
    }

    /**
     * @Route("/cabinet/user/change")
     * @Permissions(perm="/cabinet/user/change")
     * @Template()
     */
    public function userChangeAction()
    {
        $request = $this->get('request');
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');
        $factory = $this->get('security.encoder_factory');

        $userCurrent = $this->get('security.context')->getToken()->getUser();

        $usersModel = new UsersModel($em, $session, $factory);
        $rolesModel = new RolesModel($em);
        $usersRolesModel = new UsersRolesModel($em, $session);
        $regionsModel = new RegionsModel($em);

        $permissions = $rolesModel->findAllAsArray();
        $roles = array_keys($usersRolesModel->findRolesNameByUserIdAsArray($userCurrent->getId()));
        $regions = $regionsModel->findAll();

        $region = $userCurrent->getRegion();
        if ($region) {
            $region = $region->getId();
        } else {
            $region = null;
        }
        $form = $this->createForm(new UsersChangeForm($permissions, $regions, $roles,
                array( 'region' => $region )), $userCurrent);
        if ($request->isMethod('POST')) {
            $form = $this->createForm(new UsersChangeForm($permissions, $regions, $roles,
                array( 'region' => $region )));

            $form->bind($request);
            if ($form->isValid()) {
                $formData = $form->getData();

                $em->getConnection()->beginTransaction();
                try {
                    $region = $regionsModel->findByPK($formData[ 'regionId' ]);

                    $data = array(
                        'id' => $userCurrent->getId(),
                        'login' => $formData[ 'login' ],
                        'email' => $formData[ 'email' ],
                        'phone' => $formData[ 'phone' ],
                        'position' => $formData[ 'position' ],
                        'grounds' => $formData[ 'grounds' ],
                        'name' => $formData[ 'name' ],
                        'region' => $region
                    );
                    if ($formData[ 'password' ] != '') {
                        $data[ 'password' ] = $formData[ 'password' ];
                    }

                    $usersModel->update($data);

                    $em->getConnection()->commit();
                } catch( Exception $e ) {
                    $em->getConnection()->rollback();
                    $em->close();
                    throw $e;
                }
                $session->getFlashBag()->add('notice', 'Пользователь успешно изменен');

                return $this->redirect($this->generateUrl('cabinet'));
            }
        }
        return $this->render('SimpleTradeBundle:Cabinet\Users:change.html.twig', array(
                'form' => $form->createView(),
                'user' => $userCurrent
            ));
    }

    /**
     * @Route("/cabinet/users", name="cabinet_users")
     * @Permissions(perm="/cabinet/users")
     * @Template()
     */
    public function usersAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $factory = $this->get('security.encoder_factory');
        $acl = $this->get('acl');
        $usersModel = new UsersModel($em, $session, $factory);
        $companiesModel = new CompaniesModel($em, $session, $acl);
        $usersCompaniesModel = new UsersCompaniesModel($em, $session);

        $userCurrent = $this->get('security.context')->getToken()->getUser();
        $usersCompany = $usersCompaniesModel->findOneByUserId($userCurrent->getId());
        $company = $usersCompany->getCompany();

        $users = $usersModel->findAllByCompanyId($company->getId());
        return $this->render('SimpleTradeBundle:Cabinet\Users:index.html.twig', array(
                'users' => $users,
                'company' => $company,
                'user' => $userCurrent
            ));
    }

    /**
     * @Route("/cabinet/users/add")
     * @Permissions(perm="/cabinet/users/add")
     * @Template()
     */
    public function usersAddAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $factory = $this->get('security.encoder_factory');
        $request = $this->get('request');

        $rolesModel = new RolesModel($em);
        $usersModel = new UsersModel($em, $session, $factory);
        $usersRolesModel = new UsersRolesModel($em, $session);

        $allRoles = $rolesModel->findAllAsMultiArray();
        $userRolesPrimitives = $this->get('security.context')->getToken()->getUser()->getPathRoles();

        // Соберем роли, доступные юзеру
        $roles = array( );
        foreach( $allRoles as $key => $value ) {
            if (count(array_diff($value[ 'primitives' ], $userRolesPrimitives)) == 0) {
                // Если у юзера есть доступ ко всем урлам данной роли, то добавляем чекбокс
                $roles[ $key ] = $value[ 'title' ] . '(' . implode(' + ', $value[ 'primitives' ]) . ')';
            }
        }

        $form = $this->createForm(new usersAddForm($roles));

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $formData = $form->getData();

                $em->getConnection()->beginTransaction();
                try {
                    $user = $usersModel->create(array( 'login' => $formData[ 'login' ],
                        'name' => $formData[ 'name' ],
                        'password' => $formData[ 'password' ],
                        'email' => $formData[ 'email' ],
                        'region' => null ));

                    foreach( $formData[ 'roles' ] as $role ) {
                        $usersRolesModel->create(array(
                            'user' => $user->getId(),
                            'role' => $role ));
                    }

                    $userCurrent = $this->get('security.context')->getToken()->getUser();
                    $usersCompaniesModel = new UsersCompaniesModel($em, $session);
                    $usersCompany = $usersCompaniesModel->findOneByUserId($userCurrent->getId());
                    $company = $usersCompany->getCompany();

                    $usersCompaniesModel->createFromObjects(array( 'user' => $user,
                        'company' => $company ));

                    $em->getConnection()->commit();
                } catch( Exception $e ) {
                    $em->getConnection()->rollback();
                    $em->close();
                    throw $e;
                }
                $session->getFlashBag()->add('notice', 'Пользователь успешно создан');

                return $this->redirect($this->generateUrl('cabinet_users'));
            }
        }

        return $this->render('SimpleTradeBundle:Cabinet\Users:add.html.twig', array(
                'form' => $form->createView()
            ));
    }

    /**
     * @Route("/cabinet/companies/documents", name="companies_documents")
     * @Permissions(perm="/cabinet/companies/documents")
     * @Template()
     */
    public function companiesDocumentsAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');
        $request = $this->get('request');

        $documentsModel = new DocumentsModel($em, $session, $acl);
        $documentsLinksModel = new DocumentsLinksModel($em, $session);
        $usersCompaniesModel = new UsersCompaniesModel($em, $session);

        $userCurrent = $this->get('security.context')->getToken()->getUser();
        $usersCompany = $usersCompaniesModel->findOneByUserId($userCurrent->getId());

        $documents = $documentsLinksModel->findAllByOwner($usersCompany->getCompany()->getId(), 'COMPANY');
        $form = $this->createForm(new CabinetFilesForm());

        if ($request->isMethod('POST')) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $formData = $form->getData();
                $em->getConnection()->beginTransaction();
                try {
                    foreach( $formData[ 'files' ] as $value ) {
                        $document = $documentsModel->create(array( 'file' => $value->getFile(),
                            'title' => $value->getTitle(),
                            ), array( 'isActive' => 1 ));

                        $documentsLinksModel->create(array( 'document' => $document,
                            'owner' => 'COMPANY',
                            'ownerId' => $usersCompany->getCompany()->getId(),
                        ));
                    }
                    $em->getConnection()->commit();
                } catch( Exception $e ) {
                    $em->getConnection()->rollback();
                    $em->close();
                    throw $e;
                }
                $session->getFlashBag()->add('notice', 'Документ успешно добавлен');

                return $this->redirect($this->generateUrl('companies_documents'));
            }
        }

        return $this->render('SimpleTradeBundle:Companies:documents.html.twig', array(
                'documents' => $documents,
                'form' => $form->createView()
            ));
    }

    /**
     * @Route("/cabinet/sros", name="cabinet_sros")
     * @Permissions(perm="/cabinet/sros")
     * @Template()
     */
    public function srosAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');

        $usersCompaniesModel = new UsersCompaniesModel($em, $session);
        $userCurrent = $this->get('security.context')->getToken()->getUser();

        $usersCompanies = $usersCompaniesModel->findOneByUserId($userCurrent->getId());
        $company = $usersCompanies->getCompany();

        $srosCompaniesModel = new SrosCompaniesModel($em, $session);
        $sros = $srosCompaniesModel->findAllByCompanyId($company->getId());
        return $this->render('SimpleTradeBundle:Cabinet\Sros:index.html.twig', array(
                'user' => $userCurrent,
                'sros' => $sros
            ));
    }

    /**
     * @Route("/cabinet/sros/enter")
     * @Permissions(perm="/cabinet/sros/enter")
     * @Template()
     */
    public function srosEnterAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');
        $request = $this->get('request');

        $userCurrent = $this->get('security.context')->getToken()->getUser();
        $usersCompaniesModel = new UsersCompaniesModel($em, $session);
        $usersCompanies = $usersCompaniesModel->findOneByUserId($userCurrent->getId());
        $company = $usersCompanies->getCompany();

        $srosCompaniesModel = new SrosCompaniesModel($em, $session);
        $srosTypes = $srosCompaniesModel->findSrosTypesByCompanyId($company->getId());

        $srosModel = new SrosModel($em, $session, $acl);
        $srosArray = $srosModel->findAllExcludeTypesAsArray($srosTypes);

        $skillsModel = new SkillsModel($em);
        $skills = $skillsModel->findAllAsArray();

        $form = $this->createForm(new CabinetSrosEnterForm());

        if ($request->isMethod('POST')) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $formData = $form->getData();

                $sro = $srosModel->findByPK($formData['sro']);

                $requestsSrosModel = new RequestsSrosModel($em);
                if ($requestsSrosModel->checkByCompanyIdStatusTypeSroType($company->getId(), 'NEW', 'ENTER_SRO', $sro->getType()->getId())) {
                    $session->getFlashBag()->add('notice', 'У вас уже подана заявка на этот тип СРО, не рассмотренная администратором');

                    return $this->render('SimpleTradeBundle:Cabinet\Sros:enter.html.twig', array(
                        'user' => $userCurrent,
                        'form' => $form->createView(),
                        'skills' => $skills,
                        'sros' => $srosArray
                    ));
                }

                $em->getConnection()->beginTransaction();
                try {
                    $requestsStatusesModel = new RequestsStatusesModel($em);
                    $newStatus = $requestsStatusesModel->findOneByName('NEW');

                    $requestsTypesModel = new RequestsTypesModel($em);
                    $enterType = $requestsTypesModel->findOneByName('ENTER_SRO');

                    $requestsModel = new RequestsModel($em, $session, $acl);
                    $request = $requestsModel->create(array( ), array(
                        'company' => $company,
                        'status' => $newStatus,
                        'type' => $enterType ));

                    $requestsSrosModel->create(array(
                        'comment' => '',
                        'request' => $request,
                        'sro' => $sro
                    ));

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
                    $em->getConnection()->commit();
                } catch( Exception $e ) {
                    $em->getConnection()->rollback();
                    $em->close();
                    throw $e;
                }
                $session->getFlashBag()->add('notice', 'Заявка на вступление в СРО отправлена администратору');

                return $this->redirect($this->generateUrl('cabinet_sros'));
            }
        }

        return $this->render('SimpleTradeBundle:Cabinet\Sros:enter.html.twig', array(
                'user' => $userCurrent,
                'form' => $form->createView(),
                'skills' => $skills,
                'sros' => $srosArray
            ));
    }

    /**
     * @Route("/cabinet/sros/show/{sroId}")
     * @Permissions(perm="/cabinet/sros/show")
     * @Template()
     */
    public function srosApproveAction($sroId)
    {
        if (!is_numeric($sroId)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $srosModel = new SrosModel($em, $session, $acl);
        $sro = $srosModel->findByPK($sroId);
        if (!isset($sro)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $skillsModel = new SkillsModel($em);
        $sroSkillsArray = $skillsModel->findAllBySrosTypesAsArray(array( $sro->getType() ));
        $sroSkillsArray = array_pop($sroSkillsArray);
        $sroSkills = implode(', ', $sroSkillsArray[ 'skills' ]);

        return $this->render('SimpleTradeBundle:Cabinet\Sros:show.html.twig', array(
                'user' => $this->get('security.context')->getToken()->getUser(),
                'sro' => $sro,
                'sroSkills' => $sroSkills
            ));
    }

    /**
     * @Route("/cabinet/companies/documents/change/{documentId}")
     * @Permissions(perm="/cabinet/companies/documents/change")
     * @Template()
     */
    /* public function companiesDocumentsChangeAction($documentId)
      {
      if (!is_numeric($documentId)) {
      return $this->redirect($this->generateUrl('login'));
      }


      $request = $this->get('request');
      $em = $this->get('doctrine.orm.entity_manager');
      $session = $this->get('session');
      $acl = $this->get('acl');

      $companiesDocumentsModel = new CompaniesDocumentsModel($em, $acl);
      $document = $companiesDocumentsModel->findByPK($documentId);
      $form = $this->createForm(new CabinetCompaniesDocumentsChangeForm(), $document);

      return $this->render('SimpleTradeBundle:Cabinet\Document:change.html.twig', array(
      'form' => $form->createView(),
      ));
      } */

    /**
     * @Route("/cabinet/companies/documents/remove/{id}")
     * @Permissions(perm="/cabinet/companies/documents/remove")
     * @Template()
     */
    public function companiesDocumentsRemoveAction($id)
    {
        if (!is_numeric($id)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $documentsLinksModel = new DocumentsLinksModel($em, $session);
        $documentsModel = new DocumentsModel($em, $session, $acl);
        $documentsContentModel = new DocumentsContentModel($em);

        $document = $documentsModel->findByPK($id);
        if (null === $document) {
            return $this->redirect($this->generateUrl('login'));
        }
        $em->getConnection()->beginTransaction();
        try {

            $documentsLinksModel->removeByDocumentId($id);
            $documentsModel->removeByPK($id);
            //  $documentsContentModel->removeByPK($id);
            $em->getConnection()->commit();
        } catch( Exception $e ) {
            $em->getConnection()->rollback();
            $em->close();
            throw $e;
        }

        return $this->redirect($this->generateUrl('companies_documents'));
    }

    /**
     * @Route("/pdf")
     */
    public function pdfAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $usersCompaniesModel = new UsersCompaniesModel($em, $session);
        $companiesContentModel = new CompaniesContentModel($em);

        $userCurrent = $this->get('security.context')->getToken()->getUser();
        $company = $usersCompaniesModel->findOneByUserId($userCurrent->getId());
        $companyContent = $companiesContentModel->findByPK($company->getId());

        $html = $this->renderView('SimpleTradeBundle:Templates:pdf.html.twig', array(
            'name' => $companyContent->getName(),
            'title' => $companyContent->getTitle(),
            'inn' => $companyContent->getInn(),
            'kpp' => $companyContent->getKpp(),
            'ogrn' => $companyContent->getOgrn(),
            'email' => $companyContent->getEmail(),
            'phone' => $companyContent->getPhone(),
            ));

        return new Response(
                $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
                200,
                array(
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="file.pdf"'
                )
        );
    }

}
