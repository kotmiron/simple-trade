<?php

namespace SimpleSolution\SimpleTradeBundle\Model;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

Class AccountsModel extends \History_version
{

    public function __construct(EntityManager $em, Session $session, $acl)
    {
        parent::__construct($em, $session, 'SimpleSolution\SimpleTradeBundle\Entity\Accounts', $acl);
    }

    public function findByCompanyId($id)
    {
        return $this->em->getRepository($this->entityName)->findOneBy(array( 'company' => $id ));
    }

    public function findAllAsArray()
    {
        $objects = $this->em->getRepository($this->entityName)->findAll();
        $out = array( );
        foreach( $objects as $object ) {
            $out[ $object->getId() ] = $object->getContent()->getAccount();
        }
        return $out;
    }

    public function findAllHistory()
    {
        $query = $this->em->createQuery("
            SELECT av, c, t, com, comc, u, type
            FROM SimpleTradeBundle:AccountsVersions av
            JOIN av.content c
            LEFT OUTER JOIN c.tariff t
            JOIN av.company com
            JOIN com.content comc
            JOIN av.user u
            JOIN av.type type
            ORDER BY av.groupId, av.createdAt");

        return $query->getResult();
    }

    public function findAccounts($params)
    {
        foreach( $params as $key => $param ) {
            if ($param == '') {
                unset($params[ $key ]);
            }
        }
        $sql = "SELECT av, c, t, com, comc, u, type
            FROM SimpleTradeBundle:AccountsVersions av
            JOIN av.content c
            LEFT OUTER JOIN c.tariff t
            JOIN av.company com
            JOIN com.content comc
            JOIN av.user u
            JOIN av.type type";
        $joines = array( );
        $where = array( );

        if (isset($params[ 'role' ])) {
            $joines[ ] = "JOIN com.type comtype";
            $where[ ] = "comtype.name = :role";
        }
        if (isset($params[ 'name' ])) {
            $where[ ] = "comc.title LIKE :name";
            $params[ 'name' ] = '%' . $params[ 'name' ] . '%';
        }
        if (isset($params[ 'inn' ])) {
            $where[ ] = "comc.inn = :inn";
        }
        if (isset($params[ 'region' ])) {
            $where[ ] = "comc.region = :region";
        }
        if (isset($params[ 'sro' ])) {
            $ids = array( );
            $objects = $this->em->getRepository('SimpleSolution\SimpleTradeBundle\Entity\SrosCompanies')->findBy(array( 'sro' => $params[ 'sro' ] ));
            foreach( $objects as $object ) {
                $ids[ ] = $object->getCompany()->getId();
            }
            if (count($ids) > 0) {
                $where[ ] = "av.groupId IN (" . implode(',', $ids) . ")";
            } else {
                return array( );
            }
            unset($params[ 'sro' ]);
        }
        if (isset($params[ 'sroType' ])) {
            $query = $this->em->createQuery("
            SELECT sc, s, c
            FROM SimpleTradeBundle:SrosCompanies sc
            JOIN sc.sro s
            JOIN sc.company c
            WHERE s.type = :sroType");
            $query->setParameter('sroType', $params[ 'sroType' ]);
            $objects = $query->getResult();

            $ids = array( );
            foreach( $objects as $object ) {
                $ids[ ] = $object->getCompany()->getId();
            }
            if (count($ids) > 0) {
                $where[ ] = "av.groupId IN (" . implode(',', $ids) . ")";
            } else {
                return array( );
            }
            unset($params[ 'sroType' ]);
        }
        if (isset($params[ 'startPeriod' ])) {
            $where[ ] = "av.createdAt >= :startPeriod";
        }
        if (isset($params[ 'endPeriod' ])) {
            $where[ ] = "av.createdAt <= :endPeriod";
        }

        if (count($joines) > 0) {
            $sql .= ' ' . implode(' ', $joines);
        }
        if (count($where) > 0) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ' . 'ORDER BY av.groupId, av.createdAt';

        $query = $this->em->createQuery($sql);

        foreach( $params as $key => $param ) {
            if (isset($params[ $key ])) {
                $query->setParameter($key, $param);
            }
        }

        return $query->getResult();
    }

}
