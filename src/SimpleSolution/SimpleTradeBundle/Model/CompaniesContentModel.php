<?php

namespace SimpleSolution\SimpleTradeBundle\Model;
use SimpleSolution\SimpleTradeBundle\Entity\CompaniesContent;
use Doctrine\ORM\EntityManager;

Class CompaniesContentModel
{
    protected $em;
    protected $entityName;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->entityName = 'SimpleSolution\SimpleTradeBundle\Entity\CompaniesContent';
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
        $object = new CompaniesContent();
        $object->setTitle($entity['title']);
        $object->setName($entity['name']);
        $object->setInn($entity['inn']);
        $object->setKpp($entity['kpp']);
        $object->setOgrn($entity['ogrn']);
        $object->setEmail($entity['email']);
        $object->setPhone($entity['phone']);
        $object->setPosition($entity['position']);
        $object->setGrounds($entity['grounds']);
        $object->setUserName($entity['userName']);
        if (isset($entity['comment']))
            $object->setComment($entity['comment']);
        $object->setRegion($entity['region']);
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
