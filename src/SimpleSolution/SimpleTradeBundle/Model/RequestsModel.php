<?php

namespace SimpleSolution\SimpleTradeBundle\Model;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

Class RequestsModel extends \History_version
{

    public function __construct(EntityManager $em, Session $session, $acl)
    {
        parent::__construct($em, $session, 'SimpleSolution\SimpleTradeBundle\Entity\Requests', $acl);
    }

    public function findAllByStatus($name)
    {
        $status = $this->em->getRepository('SimpleSolution\SimpleTradeBundle\Entity\RequestsStatuses')->findOneBy(array( 'name' => $name ));
        return $this->em->getRepository($this->entityName)->findBy(array( 'status' => $status->getId() ));
    }

    public function findAllByType($name)
    {
        $query = $this->em->createQuery("
            SELECT r, c, t, s
            FROM SimpleTradeBundle:Requests r
            JOIN r.company c
            JOIN r.type t
            JOIN r.status s
            WHERE t.name = '" . $name . "'");

        return $query->getResult();
    }

    public function getCountByTypeAndStatus($type, $status)
    {
        $query = $this->em->createQuery("
            SELECT COUNT(r)
            FROM SimpleTradeBundle:Requests r
            JOIN r.type t
            JOIN r.status s
            WHERE t.name = :type AND
            s.name = :status");
        $query->setParameters(array( 'type' => $type,
            'status' => $status ));

        $out = $query->getOneOrNullResult(2);
        return array_pop($out);
    }

    public function checkByCompanyIdStatusType($id, $statusName, $typeName)
    {
        $status = $this->em->getRepository('SimpleSolution\SimpleTradeBundle\Entity\RequestsStatuses')->findOneBy(array( 'name' => $statusName ));
        $type = $this->em->getRepository('SimpleSolution\SimpleTradeBundle\Entity\RequestsTypes')->findOneBy(array( 'name' => $typeName ));

        $objects = $this->em->getRepository($this->entityName)->findOneBy(array( 'company' => $id, 'type' => $type, 'status' => $status ));
        if (count($objects) != 0) {
            return true;
        } else {
            return false;
        }
    }

    public function findAllByCompanyId($companyId)
    {
        $query = $this->em->createQuery("
            SELECT r, c, t, s
            FROM SimpleTradeBundle:Requests r
            JOIN r.company c
            JOIN r.type t
            JOIN r.status s
            WHERE c.id = " . $companyId);

        return $query->getResult();
    }

    public function findAllByCompaniesIds($companiesIds)
    {
        $query = $this->em->createQuery("
            SELECT r, c, t, s
            FROM SimpleTradeBundle:Requests r
            JOIN r.company c
            JOIN r.type t
            JOIN r.status s
            WHERE c.id in (" . implode(',', $companiesIds) . ")");

        return $query->getResult();
    }

}
