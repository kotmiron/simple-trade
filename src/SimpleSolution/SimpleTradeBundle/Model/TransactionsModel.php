<?php

namespace SimpleSolution\SimpleTradeBundle\Model;
use SimpleSolution\SimpleTradeBundle\Entity\Transactions;
use Doctrine\ORM\EntityManager;

Class TransactionsModel
{
    protected $em;
    protected $entityName;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->entityName = 'SimpleSolution\SimpleTradeBundle\Entity\Transactions';
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

    public function create($entity)
    {
        $object = new Transactions();
        $object->setActionName($entity['actionName']);
        $object->setActionEntity($entity['actionEntity']);
        $object->setActionTable($entity['actionTable']);
        $object->setActionId($entity['actionId']);
        $object->setCreatedAt(new \DateTime());
        $session = $this->em->find('SimpleTradeBundle:Sessions', $entity['session']);
        $object->setSession($session);
        $this->em->persist($object);
        $this->em->flush();
        return $object;
    }

    public function removeByPK($id)
    {
        $object = $this->em->find($this->entityName, $id);
        $this->em->remove($object);
        $this->em->flush();
    }
}
?>
