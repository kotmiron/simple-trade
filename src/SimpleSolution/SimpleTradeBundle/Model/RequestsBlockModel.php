<?php

namespace SimpleSolution\SimpleTradeBundle\Model;
use SimpleSolution\SimpleTradeBundle\Entity\RequestsBlock;
use Doctrine\ORM\EntityManager;

Class RequestsBlockModel
{
    protected $em;
    protected $entityName;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->entityName = 'SimpleSolution\SimpleTradeBundle\Entity\RequestsBlock';
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

    public function findOneByRequestId($id)
    {
        return $this->em->getRepository($this->entityName)->findOneBy(array('request' => $id));
    }

    public function create($entity)
    {
        $object = new RequestsBlock();
        $object->setComment($entity['comment']);
        $object->setRequest($entity['request']);
        $object->setSro($entity['sro']);

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
