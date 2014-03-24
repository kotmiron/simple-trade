<?php

namespace SimpleSolution\SimpleTradeBundle\Model;
use SimpleSolution\SimpleTradeBundle\Entity\UsersSros;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

Class UsersSrosModel
{
    protected $em;
    protected $session;
    protected $entityName;

    public function __construct(EntityManager $em, Session $session)
    {
        $this->em = $em;
        $this->session = $session;
        $this->entityName = 'SimpleSolution\SimpleTradeBundle\Entity\UsersSros';
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

    public function findAllBySroId($id)
    {
        $objects = $this->em->getRepository($this->entityName)->findBy(array('sro' => $id));
        return $objects;
    }

    public function findOneBySroId($id)
    {
        $object = $this->em->getRepository($this->entityName)->findOneBy(array('sro' => $id));
        return $object;
    }

    public function findOneByUserId($id)
    {
        $object = $this->em->getRepository($this->entityName)->findOneBy(array('user' => $id));
        return $object;
    }

    public function findAllByUserId($id)
    {
        $objects = $this->em->getRepository($this->entityName)->findBy(array('user' => $id));
        return $objects;
    }

    public function create($entity)
    {
        $object = new UsersSros();

        $user = $this->em->find('SimpleSolution\SimpleTradeBundle\Entity\Users', $entity['user']);
        $object->setUser($user);
        $sro = $this->em->find('SimpleSolution\SimpleTradeBundle\Entity\Sros', $entity['sro']);
        $object->setSro($sro);

        $this->em->persist($object);
        $this->em->flush();

        $transaction = new \History_transaction($this->em, $this->session);
        $transaction->createTransaction('create', $this->entityName, $object);
    }

    public function createFromObjects($entity)
    {
        $object = new UsersSros();

        $object->setUser($entity['user']);
        $object->setSro($entity['sro']);

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

    public function removeBySroId($id)
    {
        $objects = $this->em->getRepository($this->entityName)->findBy(array('sro' => $id));
        foreach($objects as $object)
        {
            $roles = $this->em->getRepository('SimpleSolution\SimpleTradeBundle\Entity\UsersRoles')->findBy(array('user' => $object->getUser()->getId()));
            foreach($roles as $role)
            {
                $transaction = new \History_transaction($this->em, $this->session);
                $transaction->createTransaction('remove', $this->entityName, $role);
                $this->em->remove($role);
            }
            $transaction = new \History_transaction($this->em, $this->session);
            $transaction->createTransaction('remove', $this->entityName, $object);
            $this->em->remove($object);
        }
        $this->em->flush();
    }
}
?>
