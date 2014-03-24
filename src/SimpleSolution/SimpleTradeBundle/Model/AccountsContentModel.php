<?php

namespace SimpleSolution\SimpleTradeBundle\Model;
use SimpleSolution\SimpleTradeBundle\Entity\AccountsContent;
use Doctrine\ORM\EntityManager;

Class AccountsContentModel
{
    protected $em;
    protected $entityName;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->entityName = 'SimpleSolution\SimpleTradeBundle\Entity\AccountsContent';
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
        $object = new AccountsContent();
        $object->setComment($entity['comment']);
        $object->setAccount($entity['account']);
        $object->setChanges($entity['changes']);
        $object->setTariff($entity['tariff']);
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
