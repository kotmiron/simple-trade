<?php

namespace SimpleSolution\SimpleTradeBundle\Model;

use Doctrine\ORM\EntityManager;
use SimpleSolution\SimpleTradeBundle\Entity\ProtocolsCompanyValues;

Class ProtocolsCompanyValuesModel
{
    protected $em;
    protected $entityName;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->entityName = 'SimpleSolution\SimpleTradeBundle\Entity\ProtocolsCompanyValues';
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
        $object = $this->em->getRepository($this->entityName)->findOneBy(array( 'name' => $name ));
        return $object;
    }

    public function create($entity)
    {
        $companyValue = new ProtocolsCompanyValues();
        $companyValue->setCompany($entity['company']);
        $companyValue->setValue($entity['value']);
        $companyValue->setProtocolContent($entity['protocolContent']);
        $this->em->persist($companyValue);
        $this->em->flush();

        return $companyValue;
    }

}