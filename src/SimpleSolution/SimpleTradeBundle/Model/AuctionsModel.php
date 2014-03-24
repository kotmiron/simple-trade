<?php

namespace SimpleSolution\SimpleTradeBundle\Model;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

Class AuctionsModel extends \History_version
{

    public function __construct(EntityManager $em, Session $session, $acl)
    {
        parent::__construct($em, $session, 'SimpleSolution\SimpleTradeBundle\Entity\Auctions', $acl);
    }

    public function findAllByUserId($id)
    {
        $objects = $this->em->getRepository($this->entityName)->findBy(array( 'user' => $id ));
        return $objects;
    }

    public function findAllByStatus($name)
    {
        $status = $this->em->getRepository('SimpleSolution\SimpleTradeBundle\Entity\AuctionsStatuses')->findOneBy(array('name' => $name));
        return $this->em->getRepository($this->entityName)->findBy(array('status' => $status->getId()));
    }

    public function findAllWithContent()
    {
        $query = $this->em->createQuery("
            SELECT a, c
            FROM SimpleTradeBundle:Auctions a
            JOIN a.content c");

        return $query->getResult();
    }

    public function findByStatusesWithContent(array $statuses)
    {
        $query = $this->em->createQuery("
            SELECT a, c, s
            FROM SimpleTradeBundle:Auctions a
            JOIN a.content c
            JOIN a.status s
            WHERE s.name IN (:statuses)")->setParameter('statuses', $statuses);

        return $query->getResult();
    }

    public function findByStatusesAndTypesWithContent(array $statuses, array $types)
    {
        $query = $this->em->createQuery("
            SELECT a, c, s, tf, r, curr
            FROM SimpleTradeBundle:Auctions a
            JOIN a.content c
            JOIN a.status s
            JOIN c.tradingForm tf
            JOIN c.region r
            JOIN c.currency curr
            WHERE s.name IN (:statuses)
            AND tf.name IN (:types)")
            ->setParameter('statuses', $statuses)
            ->setParameter('types', $types);

        return $query->getResult();
    }

    public function findAuction($params, $statuses = array( 'PUBLIC', 'NOT_TAKE_PLACE', 'STARTED', 'COMPLETED', 'CLOSED' ))
    {
        $conditionsArray = array( );

        if (isset($params[ 'type' ])) {
            $conditionsArray[ ] = "tf.id = :type_id";
        }
        if (isset($params[ 'title' ])) {
            $conditionsArray[ ] = "c.title LIKE :title";
        }
        if (isset($params[ 'code' ])) {
            //
        }
        // Расширенный поиск
        if (isset($params[ 'is_extended' ])) {
            if (isset($params[ 'region' ])) {
                $conditionsArray[ ] = "reg.id = :region_id";
            }
        }

        $conditionsString = '';
        if (count($conditionsArray) > 0) {
            $conditionsString = ' AND (' . implode(' AND ', $conditionsArray) . ')';
        }

        $query = $this->em->createQuery("
            SELECT a, c, s, tf
            FROM SimpleTradeBundle:Auctions a
            JOIN a.content c
            JOIN a.status s
            JOIN c.tradingForm tf
            JOIN c.region reg
            WHERE s.name IN (:statuses)" . $conditionsString)->setParameter('statuses', $statuses);

        if (isset($params[ 'type' ])) {
            $query->setParameter('type_id', $params[ 'type' ]);
        }
        if (isset($params[ 'title' ])) {
            $query->setParameter('title', '%' . $params[ 'title' ] . '%');
        }
        if (isset($params[ 'code' ])) {
            //
        }
        // Расширенный поиск
        if (isset($params[ 'is_extended' ])) {
            if (isset($params[ 'region' ])) {
                $query->setParameter('region_id', $params[ 'region' ]);
            }
        }

        return $query->getResult();
    }

    public function getAuctionsPublicDateAsArray($auctions)
    {
        if (count($auctions) == 0) return array( );

        $ids = array( );
        foreach( $auctions as $value ) {
            $ids[ ] = $value->getId();
        }

        $query = $this->em->createQuery("
            SELECT av
            FROM SimpleTradeBundle:AuctionsVersions av
            JOIN av.status s
            WHERE s.name = 'PUBLIC'
            AND av.groupId IN (:ids)")->setParameter('ids', $ids);

        $out = array( );
        foreach( $query->getResult() as $value ) {
            $out[ $value->getGroupId() ] = $value->getCreatedAt();
        }

        return $out;
    }

//    public function getAuctionsCompanysAsArray($auctions)
//    {
//        if (count($auctions) == 0) return array( );
//
//        $usersId = array( );
//        foreach( $auctions as $value ) {
//            $usersId[ ] = $value->getUser()->getId();
//        }
//
//        $query = $this->em->createQuery("
//            SELECT uc, cm, cn
//            FROM SimpleTradeBundle:UsersCompanies uc
//            JOIN uc.company cm
//            JOIN cm.content cn
//            WHERE uc.user IN (:ids)")->setParameter('ids', $usersId);
//
//        $usersCompany = array( );
//        foreach( $query->getResult() as $value ) {
//            $usersCompany[ $value->getUser()->getId() ] = $value->getCompany();
//        }
//
//        $out = array( );
//        foreach( $auctions as $value ) {
//            $out[ $value->getId() ] = $usersCompany[ $value->getUser()->getId() ];
//        }
//
//        return $out;
//    }

    public function getAuctionsOffersAsArray($auctions, $user)
    {
        $objects = $this->em->getRepository($this->entityName)->findBy(array( 'user' => $id ));
    }

}
