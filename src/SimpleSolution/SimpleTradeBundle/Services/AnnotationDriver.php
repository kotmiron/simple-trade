<?php

namespace SimpleSolution\SimpleTradeBundle\Services;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use SimpleSolution\SimpleTradeBundle\Annotations\Permissions;
use Symfony\Component\HttpFoundation\Response;
//use Doctrine\ORM\EntityManager;
//use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use SimpleSolution\SimpleTradeBundle\Model\UsersRolesModel;
use SimpleSolution\SimpleTradeBundle\Model\RolesPrimitivesModel;

class AnnotationDriver
{
    private $reader;

    public function __construct($reader)//, $em, Session $session)
    {
        $this->reader = $reader;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        if (!is_array($controller = $event->getController())) {
            return;
        }

        $object = new \ReflectionObject($controller[ 0 ]);
        $method = $object->getMethod($controller[ 1 ]);

        foreach( $this->reader->getMethodAnnotations($method) as $configuration ) {
            if (isset($configuration->perm)) {

                $em = $controller[ 0 ]->get('doctrine.orm.entity_manager');
                $session = $controller[ 0 ]->get('session');
                $acl = $controller[ 0 ]->get('acl');
                $user = $controller[ 0 ]->get('security.context')->getToken()->getUser();

                if ($user == 'anon.') return;

                $usersRolesModel = new UsersRolesModel($em, $session);
                $rolesPrimitivesModel = new RolesPrimitivesModel($em);

                if (!$session->get('pathRoles') && !$session->get('systemRoles')) {

                    if (!$user->isGod()) {
                        $pathRoles = $usersRolesModel->findPrimitivesByUserIdAsArray($user->getId());
                        $systemRoles = $usersRolesModel->findRolesNameByUserIdAsArray($user->getId());
                    } else {
                        $pathRoles = $rolesPrimitivesModel->findAllAsArray();
                        $systemRoles = null;
                    }

                    $session->set('systemRoles', $systemRoles);
                    $session->set('pathRoles', $pathRoles);
                } else {
                    $pathRoles = $session->get('pathRoles');
                    $systemRoles = $session->get('systemRoles');
                }

                $user->setPathRoles($pathRoles);
                $user->setSystemRoles($systemRoles);
                $user->setAcl($acl);

                if (!in_array($configuration->perm, $pathRoles)) {
                    throw new AccessDeniedException();
                }

                if ($user->isBlocked()) {
                    $controller[ 0 ]->get('security.context')->setToken(null);
                    $session->invalidate();
                    $session->getFlashBag()->add('notice', 'Пользователь заблокирован');
                    throw new AccessDeniedException();
                }
            }
        }
    }

}
