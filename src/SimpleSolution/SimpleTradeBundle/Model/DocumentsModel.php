<?php

namespace SimpleSolution\SimpleTradeBundle\Model;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

Class DocumentsModel extends \History_version
{

    public function __construct(EntityManager $em, Session $session, $acl)
    {
        parent::__construct($em, $session, 'SimpleSolution\SimpleTradeBundle\Entity\Documents', $acl);
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

    public function removeByPK($id)
    {
        $object = $this->em->find($this->entityName, $id);

        $transaction = new \History_transaction($this->em, $this->session);
        $transaction->createTransaction('remove', $this->entityName, $object);
        $this->em->remove($object);
        $this->em->flush();
    }

}
