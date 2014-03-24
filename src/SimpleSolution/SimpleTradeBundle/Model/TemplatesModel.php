<?php

namespace SimpleSolution\SimpleTradeBundle\Model;

use SimpleSolution\SimpleTradeBundle\Entity\Templates;
use Doctrine\ORM\EntityManager;

Class TemplatesModel
{
    protected $em;
    protected $entityName;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->entityName = 'SimpleSolution\SimpleTradeBundle\Entity\Templates';
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

    public function findByName($name)
    {
        $object = $this->em->getRepository($this->entityName)->findOneBy(array( 'name' => $name ));
        return $object;
    }

}
