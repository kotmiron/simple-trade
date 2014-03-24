<?php

namespace SimpleSolution\SimpleTradeBundle\Model;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

Class CompaniesModel extends \History_version
{
    public function __construct(EntityManager $em, Session $session, $acl)
    {
        parent::__construct($em, $session, 'SimpleSolution\SimpleTradeBundle\Entity\Companies', $acl);
    }

    public function findAllBySroId($id)
    {
        $objects = $this->em->getRepository('SimpleSolution\SimpleTradeBundle\Entity\SrosCompanies')->findBy(array('sro' => $id));
        $out = array();
        foreach($objects as $object)
        {
            array_push($out, $object->getCompany());
        }
        return $out;
    }

    public function findAllByUserId($id)
    {
        $objects = $this->em->getRepository('SimpleSolution\SimpleTradeBundle\Entity\UsersCompanies')->findBy(array('user' => $id));
        $out = array();
        foreach($objects as $object)
        {
            array_push($out, $object->getCompany());
        }
        return $out;
    }

    public function findAllByStatus($name)
    {
        $status = $this->em->getRepository('SimpleSolution\SimpleTradeBundle\Entity\CompaniesStatuses')->findOneBy(array('name' => $name));
        return $this->em->getRepository($this->entityName)->findBy(array('status' => $status->getId()));
    }

    public function findAllByType($name)
    {
        $type = $this->em->getRepository('SimpleSolution\SimpleTradeBundle\Entity\CompaniesTypes')->findOneBy(array('name' => $name));
        return $this->em->getRepository($this->entityName)->findBy(array('type' => $type->getId()));
    }
}
