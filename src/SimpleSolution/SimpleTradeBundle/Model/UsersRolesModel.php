<?php

namespace SimpleSolution\SimpleTradeBundle\Model;
use SimpleSolution\SimpleTradeBundle\Entity\UsersRoles;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

Class UsersRolesModel
{
    protected $em;
    protected $session;
    protected $entityName;

    public function __construct(EntityManager $em, Session $session)
    {
        $this->em = $em;
        $this->session = $session;
        $this->entityName = 'SimpleSolution\SimpleTradeBundle\Entity\UsersRoles';
    }

    public function findAll()
    {
        $objects = $this->em->getRepository($this->entityName)->findAll();
        return $objects;
    }

    public function findByPK($id)
    {
        $object = $this->em->find($this->entityName, $id);
        return $object;
    }

    public function findPrimitivesByUserIdAsArray($id)
    {
        $objects = $this->em->getRepository($this->entityName)->findBy(array('user' => $id));
        $out = array();
        foreach($objects as $object)
        {
            $role = $object->getRole();
            $rolesPrimitives = $role->getRolesPrimitives();

            foreach ($rolesPrimitives as $primitive) {
                $out[$primitive->getId()] = $primitive->getPath();
            }
        }
        return $out;
    }

    public function findRolesByUserIdAsArray($id)
    {
        $objects = $this->em->getRepository($this->entityName)->findBy(array('user' => $id));
        $out = array();
        foreach($objects as $object)
        {
            $out[$object->getId()] = $object->getRole()->getTitle();
        }
        return $out;
    }

    public function findRolesNameByUserIdAsArray($id)
    {
        $objects = $this->em->getRepository($this->entityName)->findBy(array('user' => $id));
        $out = array();
        foreach($objects as $object)
        {
            $out[$object->getRole()->getId()] = $object->getRole()->getName();
        }
        return $out;
    }

    public function create($entity)
    {
        $object = new UsersRoles();

        $user = $this->em->find('SimpleSolution\SimpleTradeBundle\Entity\Users', $entity['user']);
        $object->setUser($user);
        $role = $this->em->find('SimpleSolution\SimpleTradeBundle\Entity\Roles', $entity['role']);
        $object->setRole($role);

        $this->em->persist($object);
        $this->em->flush();

        $transaction = new \History_transaction($this->em, $this->session);
        $transaction->createTransaction('create', $this->entityName, $object);
    }

    public function approve($user)
    {
        $objects = $this->em->getRepository($this->entityName)->findBy(array('user' => $user));
        foreach($objects as $object)
        {
            $roleName = str_replace('NOT_APPROVED_', '', $object->getRole()->getName());
            $role = $this->em->getRepository('SimpleSolution\SimpleTradeBundle\Entity\Roles')->findOneBy(array('name' => $roleName));
            $object->setRole($role);

            $this->em->persist($object);
            $this->em->flush();

            $transaction = new \History_transaction($this->em, $this->session);
            $transaction->createTransaction('update', $this->entityName, $object);
        }
    }

    public function createFromObjects($entity)
    {
        $object = new UsersRoles();

        $object->setUser($entity['user']);
        $object->setRole($entity['role']);

        $this->em->persist($object);
        $this->em->flush();

        $transaction = new \History_transaction($this->em, $this->session);
        $transaction->createTransaction('create', $this->entityName, $object);
    }

    public function removeByPK($id)
    {
        $object = $this->em->find($this->entityName, $id);

        $transaction = new \History_transaction($this->em, $this->session);
        $transaction->createTransaction('remove', $this->entityName, $object);
        $this->em->remove($object);
        $this->em->flush();
    }

    public function removeByUserId($id)
    {
        $objects = $this->em->getRepository($this->entityName)->findBy(array('user' => $id));
        foreach($objects as $object)
        {
            $transaction = new \History_transaction($this->em, $this->session);
            $transaction->createTransaction('remove', $this->entityName, $object);
            $this->em->remove($object);
        }
        $this->em->flush();
    }
}
