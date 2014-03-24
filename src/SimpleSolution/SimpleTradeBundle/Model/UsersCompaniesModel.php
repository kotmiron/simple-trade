<?php

namespace SimpleSolution\SimpleTradeBundle\Model;
use SimpleSolution\SimpleTradeBundle\Entity\UsersCompanies;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

Class UsersCompaniesModel
{
    protected $em;
    protected $session;
    protected $entityName;

    public function __construct(EntityManager $em, Session $session)
    {
        $this->em = $em;
        $this->session = $session;
        $this->entityName = 'SimpleSolution\SimpleTradeBundle\Entity\UsersCompanies';
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

    public function findOneByUserId($id)
    {
        $object = $this->em->getRepository($this->entityName)->findOneBy(array('user' => $id));
        return $object;
    }

    public function findOneByCompanyId($id)
    {
        $object = $this->em->getRepository($this->entityName)->findOneBy(array('company' => $id));
        return $object;
    }

    public function findAllUsersByCompanyId($id)
    {
        $objects = $this->em->getRepository($this->entityName)->findBy(array('company' => $id));

        $users = array();
        foreach ($objects as $value) {
            $users[] = $value->getUser();
        }

        return $users;
    }

    public function create($entity)
    {
        $object = new UsersCompanies();

        $user = $this->em->find('SimpleSolution\SimpleTradeBundle\Entity\Users', $entity['user']);
        $object->setUser($user);
        $company = $this->em->find('SimpleSolution\SimpleTradeBundle\Entity\Companies', $entity['company']);
        $object->setCompany($company);

        $this->em->persist($object);
        $this->em->flush();

        $transaction = new \History_transaction($this->em, $this->session);
        $transaction->createTransaction('create', $this->entityName, $object);
    }

    public function createFromObjects($entity)
    {
        $object = new UsersCompanies();

        $object->setUser($entity['user']);
        $object->setCompany($entity['company']);

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

    public function removeByCompanyId($id)
    {
        $objects = $this->em->getRepository($this->entityName)->findBy(array('company' => $id));
        foreach($objects as $object)
        {
            $transaction = new \History_transaction($this->em, $this->session);
            $transaction->createTransaction('remove', $this->entityName, $object);
            $this->em->remove($object);
        }
        $this->em->flush();
    }
}
