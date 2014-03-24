<?php

namespace SimpleSolution\SimpleTradeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SimpleSolution\SimpleTradeBundle\Annotations\Permissions;
use SimpleSolution\SimpleTradeBundle\Entity\Users;
use SimpleSolution\SimpleTradeBundle\Entity\Roles;
use SimpleSolution\SimpleTradeBundle\Model\CompaniesModel;
use SimpleSolution\SimpleTradeBundle\Model\UsersModel;
use SimpleSolution\SimpleTradeBundle\Model\RolesModel;
use SimpleSolution\SimpleTradeBundle\Model\RegionsModel;
use SimpleSolution\SimpleTradeBundle\Model\UsersRolesModel;
use SimpleSolution\SimpleTradeBundle\Model\UsersCompaniesModel;
use SimpleSolution\SimpleTradeBundle\Model\UsersSrosModel;
use SimpleSolution\SimpleTradeBundle\Model\RolesPrimitivesModel;
use SimpleSolution\SimpleTradeBundle\Model\AccountsModel;
use SimpleSolution\SimpleTradeBundle\Model\SkillsModel;
use SimpleSolution\SimpleTradeBundle\Model\UsersSkillsModel;
use SimpleSolution\SimpleTradeBundle\Form\users\AdminAddForm;
use SimpleSolution\SimpleTradeBundle\Form\users\UsersAddForm;
use SimpleSolution\SimpleTradeBundle\Form\users\UsersChangeForm;
use SimpleSolution\SimpleTradeBundle\Form\users\UsersRolesAddForm;
use SimpleSolution\SimpleTradeBundle\Form\users\SystemuserAddForm;

class UsersController extends Controller
{

    /**
     * @Route("/users", name="users")
     * @Permissions(perm="/users")
     * @Template()
     */
    public function usersAllAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $factory = $this->get('security.encoder_factory');

        $usersModel = new UsersModel($em, $session, $factory);

        $users = $usersModel->findAll();
        return $this->render('SimpleTradeBundle:Users:list.html.twig', array(
                'users' => $users
            ));
    }

    /**
     * @Route("/users/show/{id}", requirements={"id" = "\d+"}, name="users_by_companyId")
     * @Permissions(perm="/users/show")
     * @Template()
     */
    public function usersAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $factory = $this->get('security.encoder_factory');
        $acl = $this->get('acl');
        $usersModel = new UsersModel($em, $session, $factory);
        $companiesModel = new CompaniesModel($em, $session, $acl);

        $company = $companiesModel->findByPK($id);
        if (!isset($company)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $users = $usersModel->findAllByCompanyId($id);
        return $this->render('SimpleTradeBundle:Users:index.html.twig', array(
                'users' => $users,
                'company' => $company
            ));
    }

    /**
     * @Route("/users/add/{$id}", requirements={"id" = "\d+"})
     * @Permissions(perm="/users/add")
     * @Template()
     */
    public function usersAddAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $factory = $this->get('security.encoder_factory');
        $request = $this->get('request');
        $acl = $this->get('acl');
        $rolesModel = new RolesModel($em);
        $usersModel = new UsersModel($em, $session, $factory);
        $usersRolesModel = new UsersRolesModel($em, $session);
        $usersCompaniesModel = new UsersCompaniesModel($em, $session);
        $companiesModel = new CompaniesModel($em, $session, $acl);
        $accountsModel = new AccountsModel($em, $session, $acl);

        $user = new Users();
        $company = $companiesModel->findByPK($id);
        if (!isset($company)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $permissions = $rolesModel->findAllAsArray();

        $regionsModel = new RegionsModel($em);
        $regions = $regionsModel->findAll();

        $form = $this->createForm(new UsersAddForm($permissions, $regions));
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
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
                    $usersCompaniesModel->createFromObjects(array( 'user' => $user,
                        'company' => $company ));

                    $accountsModel->create(array( 'account' => 200,
                        'tariff' => null,
                        'changes' => 200,
                        'comment' => '' ), array( 'company' => $company ));

                    $em->getConnection()->commit();
                } catch( Exception $e ) {
                    $em->getConnection()->rollback();
                    $em->close();
                    throw $e;
                }

                $session->getFlashBag()->add('notice', 'Пользователь успешно создан');
                return $this->redirect($this->generateUrl('users_by_companyId', array( 'id' => $id )));
            }
        }
        return $this->render('SimpleTradeBundle:Users:add.html.twig', array(
                'form' => $form->createView(),
                'company' => $company
            ));
    }

    /**
     * @Route("/users/change/{id}", requirements={"id" = "\d+"})
     * @Permissions(perm="/users/change")
     * @Template()
     */
    public function usersChangeAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $factory = $this->get('security.encoder_factory');
        $request = $this->get('request');

        $rolesModel = new RolesModel($em);
        $usersModel = new UsersModel($em, $session, $factory);
        $usersRolesModel = new UsersRolesModel($em, $session);
        $regionsModel = new RegionsModel($em);

        $user = $usersModel->findByPK($id);
        if (!isset($user)) {
            return $this->redirect($this->generateUrl('login'));
        }
        if ($user->isGod()) {
            $session->getFlashBag()->add('notice', 'Нельзя изменить этого пользователя');
            return $this->redirect($this->generateUrl('users'));
        }

        $permissions = $rolesModel->findAllAsArray();
        $roles = array_keys($usersRolesModel->findRolesNameByUserIdAsArray($id));
        $regions = $regionsModel->findAll();

        $region = $user->getRegion();
        if ($region) {
            $region = $region->getId();
        } else {
            $region = null;
        }
        $form = $this->createForm(new UsersChangeForm($permissions, $regions, $roles,
                array( 'region' => $region )), $user);
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
                        'id' => $id,
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

                    $usersRolesModel->removeByUserId($id);
                    $roles = $formData[ 'permissions' ];
                    foreach( $roles as $role ) {
                        $usersRolesModel->create(array(
                            'user' => $id,
                            'role' => $role ));
                    }

                    $em->getConnection()->commit();
                } catch( Exception $e ) {
                    $em->getConnection()->rollback();
                    $em->close();
                    throw $e;
                }

                $session->getFlashBag()->add('notice', 'Пользователь успешно изменен');
                return $this->redirect($this->generateUrl('users'));
            }
        }
        return $this->render('SimpleTradeBundle:Users:change.html.twig', array(
                'form' => $form->createView()
            ));
    }

    /**
     * @Route("/users/block/{id}", requirements={"id" = "\d+"})
     * @Permissions(perm="/users/block")
     * @Template()
     */
    public function usersBlockAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $factory = $this->get('security.encoder_factory');

        $usersModel = new UsersModel($em, $session, $factory);
        $user = $usersModel->findByPK($id);
        if (!isset($user)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $usersCompaniesModel = new UsersCompaniesModel($em, $session);
        $usersCompany = $usersCompaniesModel->findOneByUserId($id);
        if (!isset($usersCompany)) {
            $session->getFlashBag()->add('notice', 'Пользователя нельзя заблокировать');
            return $this->redirect($this->generateUrl('users'));
        }
        $company = $usersCompany->getCompany();

        $em->getConnection()->beginTransaction();
        try {
            $usersModel->update(array( 'id' => $user->getId(),
                'isBlocked' => 1 ));

            $em->getConnection()->commit();
        } catch( Exception $e ) {
            $em->getConnection()->rollback();
            $em->close();
            throw $e;
        }

        $session->getFlashBag()->add('notice', 'Пользователь успешно заблокирован');
        return $this->redirect($this->generateUrl('users_by_companyId', array( 'id' => $company->getId() )));
    }

    /**
     * @Route("/users/unblock/{id}", requirements={"id" = "\d+"})
     * @Permissions(perm="/users/unblock")
     * @Template()
     */
    public function usersUnblockAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $factory = $this->get('security.encoder_factory');

        $usersModel = new UsersModel($em, $session, $factory);
        $user = $usersModel->findByPK($id);
        if (!isset($user)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $usersCompaniesModel = new UsersCompaniesModel($em, $session);
        $usersCompany = $usersCompaniesModel->findOneByUserId($id);
        if (!isset($usersCompany)) {
            $session->getFlashBag()->add('notice', 'Пользователя нельзя заблокировать');
            return $this->redirect($this->generateUrl('users'));
        }
        $company = $usersCompany->getCompany();

        $em->getConnection()->beginTransaction();
        try {
            $usersModel->update(array( 'id' => $user->getId(),
                'isBlocked' => 0 ));

            $em->getConnection()->commit();
        } catch( Exception $e ) {
            $em->getConnection()->rollback();
            $em->close();
            throw $e;
        }

        $session->getFlashBag()->add('notice', 'Пользователь успешно разблокирован');
        return $this->redirect($this->generateUrl('users_by_companyId', array( 'id' => $company->getId() )));
    }

    /**
     * @Route("/users/remove/{id}", requirements={"id" = "\d+"})
     * @Permissions(perm="/users/remove")
     * @Template()
     */
    public function usersRemoveAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $factory = $this->get('security.encoder_factory');
        $usersModel = new UsersModel($em, $session, $factory);
        $usersRolesModel = new UsersRolesModel($em, $session);
        $usersCompaniesModel = new UsersCompaniesModel($em, $session);
        $usersSrosModel = new UsersSrosModel($em, $session);

        $user = $usersModel->findByPK($id);
        if (!isset($user)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em->getConnection()->beginTransaction();
        try {
            $company = $usersCompaniesModel->findOneByUserId($id)->getCompany();
            $usersRolesModel->removeByUserId($id);
            $usersCompaniesModel->removeByUserId($id);
            $usersSrosModel->removeByUserId($id);
            $usersModel->removeByPK($id);
            $em->getConnection()->commit();
        } catch( Exception $e ) {
            $em->getConnection()->rollback();
            $em->close();
            throw $e;
        }

        $session->getFlashBag()->add('notice', 'Пользователь успешно удален');
        return $this->redirect($this->generateUrl('users_by_companyId', array( 'id' => $company->getId() )));
    }

    /**
     * @Route("/users/roles", name="users_roles")
     * @Permissions(perm="/users/roles")
     * @Template()
     */
    public function usersRolesAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $rolesModel = new RolesModel($em);

        $roles = $rolesModel->findAll();
        return $this->render('SimpleTradeBundle:Users\Roles:index.html.twig', array(
                'roles' => $roles,
            ));
    }

    /**
     * @Route("/users/roles/add")
     * @Permissions(perm="/users/roles/add")
     * @Template()
     */
    public function usersRolesAddAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $request = $this->get('request');

        $rolesModel = new RolesModel($em);
        $rolesPrimitivesModel = new RolesPrimitivesModel($em);

        $role = new Roles();
        $primitives = $user->getPathRoles();

        $form = $this->createForm(new UsersRolesAddForm($primitives), $role);
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $rolesPrimitives = $form->getData()->getRoles();

                if (count(array_diff($rolesPrimitives, array_keys($primitives))) > 0) {
                    throw new \Exception('There is no access to one of the roles');
                }

                $formData = $form->getData();

                $em->getConnection()->beginTransaction();
                try {
                    $rolesModel->create(array(
                        'title' => $formData->getTitle(),
                        'name' => $formData->getName(),
                        'roles_primitives' => $rolesPrimitivesModel->findByPKs($rolesPrimitives),
                    ));
                    $em->getConnection()->commit();
                } catch( Exception $e ) {
                    $em->getConnection()->rollback();
                    $em->close();
                    throw $e;
                }

                $session->getFlashBag()->add('notice', 'Роль успешно создана');
                return $this->redirect($this->generateUrl('users_roles'));
            }
        }
        return $this->render('SimpleTradeBundle:Users\Roles:add.html.twig', array(
                'form' => $form->createView(),
            ));
    }

    /**
     * @Route("/users/roles/change/{id}", requirements={"id" = "\d+"})
     * @Permissions(perm="/users/roles/change")
     * @Template()
     */
    public function usersRolesChangeAction($id)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->get('doctrine.orm.entity_manager');
        $request = $this->get('request');
        $session = $this->get('session');

        $rolesModel = new RolesModel($em);
        $rolesPrimitivesModel = new RolesPrimitivesModel($em);

        $role = $rolesModel->findByPK($id);
        if (!isset($role)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $primitives = $user->getPathRoles();
        $form = $this->createForm(new UsersRolesAddForm($primitives), $role);
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $rolesPrimitives = $form->getData()->getRoles();

                if (count(array_diff($rolesPrimitives, array_keys($primitives))) > 0) {
                    throw new \Exception('There is no access to one of the roles');
                }

                $formData = $form->getData();

                $em->getConnection()->beginTransaction();
                try {
                    $rolesModel->update($role->getId(), array(
                        'title' => $formData->getTitle(),
                        'name' => $formData->getName(),
                        'roles_primitives' => $rolesPrimitivesModel->findByPKs($rolesPrimitives),
                    ));

                    $em->getConnection()->commit();
                } catch( Exception $e ) {
                    $em->getConnection()->rollback();
                    $em->close();
                    throw $e;
                }

                $session->getFlashBag()->add('notice', 'Роль успешно изменена');
                return $this->redirect($this->generateUrl('users_roles'));
            }
        }
        return $this->render('SimpleTradeBundle:Users\Roles:change.html.twig', array(
                'form' => $form->createView(),
            ));
    }

    /**
     * @Route("/users/roles/remove/{id}", requirements={"id" = "\d+"})
     * @Permissions(perm="/users/roles/remove")
     * @Template()
     */
    public function usersRolesRemoveAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');

        $rolesModel = new RolesModel($em);
        $role = $rolesModel->findByPK($id);
        if (!isset($role)) {
            return $this->redirect($this->generateUrl('login'));
        }
        $em->getConnection()->beginTransaction();
        try {
            $rolesModel->removeByPK($id);
            $em->getConnection()->commit();
        } catch( Exception $e ) {
            $em->getConnection()->rollback();
            $em->close();
            throw $e;
        }

        $session->getFlashBag()->add('notice', 'Роль успешно удалена');
        return $this->redirect($this->generateUrl('users_roles'));
    }

    /**
     * @Route("/users/admin/add", name="users_admin_add")
     * @Permissions(perm="/users/admin/add")
     * @Template()
     */
    public function usersAdminAddAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $factory = $this->get('security.encoder_factory');
        $request = $this->get('request');

        $rolesModel = new RolesModel($em);
        $usersModel = new UsersModel($em, $session, $factory);
        $usersRolesModel = new UsersRolesModel($em, $session);

        $form = $this->createForm(new AdminAddForm($permissions, $regions));

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

                    $adminRole = $rolesModel->findOneByName('ADMIN');
                    $usersRolesModel->create(array(
                        'user' => $user->getId(),
                        'role' => $adminRole->getId() ));

                    $moderatorRole = $rolesModel->findOneByName('MODERATOR');
                    $usersRolesModel->create(array(
                        'user' => $user->getId(),
                        'role' => $moderatorRole->getId() ));
                    $em->getConnection()->commit();
                } catch( Exception $e ) {
                    $em->getConnection()->rollback();
                    $em->close();
                    throw $e;
                }

                $session->getFlashBag()->add('notice', 'Администратор успешно создан');
                return $this->redirect($this->generateUrl('users'));
            }
        }

        return $this->render('SimpleTradeBundle:Users\Admin:add.html.twig', array(
                'form' => $form->createView()
            ));
    }

    /**
     * @Route("/users/systemuser/add", name="users_systemuser_add")
     * @Permissions(perm="/users/systemuser/add")
     * @Template()
     */
    public function usersSystemuserAddAction()
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

        $form = $this->createForm(new SystemuserAddForm($roles));

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $formData = $form->getData();

                // Если с формы поступила роль, которой нет у юзера, то ошибка
                if (count(array_diff($formData[ 'roles' ], array_keys($userRolesPrimitives))) > 0) {
                    throw new \Exception('There is no access to one of the roles');
                }

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
                    $em->getConnection()->commit();
                } catch( Exception $e ) {
                    $em->getConnection()->rollback();
                    $em->close();
                    throw $e;
                }

                $session->getFlashBag()->add('notice', 'Системный пользователь успешно создан');
                return $this->redirect($this->generateUrl('users'));
            }
        }

        return $this->render('SimpleTradeBundle:Users\SystemUser:add.html.twig', array(
                'form' => $form->createView()
            ));
    }

}
