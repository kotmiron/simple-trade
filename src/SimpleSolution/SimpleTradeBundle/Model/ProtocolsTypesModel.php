<?php

namespace SimpleSolution\SimpleTradeBundle\Model;

use Doctrine\ORM\EntityManager;

Class ProtocolsTypesModel
{
    protected $em;
    protected $entityName;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->entityName = 'SimpleSolution\SimpleTradeBundle\Entity\ProtocolsTypes';
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

    public function findOneByName($name)
    {
        $object = $this->em->getRepository($this->entityName)->findOneBy(array('name' => $name));
        return $object;
    }
}