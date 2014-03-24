<?php

namespace SimpleSolution\SimpleTradeBundle\Model;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

Class OffersModel extends \History_version
{

    public function __construct(EntityManager $em, Session $session, $acl)
    {
        parent::__construct($em, $session, 'SimpleSolution\SimpleTradeBundle\Entity\Offers', $acl);
    }

    public function findAllByCompanyId($id)
    {
        $objects = $this->em->getRepository($this->entityName)->findBy(array( 'company' => $id ));
        return $objects;
    }

    public function findAllByAuctionId($id)
    {
        $query = $this->em->createQuery("
            SELECT o, s, c, cc
            FROM SimpleTradeBundle:Offers o
            JOIN o.status s
            JOIN o.company c
            JOIN c.content cc
            WHERE o.auction = :id_auction");
        $query->setParameters(array(
            'id_auction' => $id
        ));

        return $query->getResult();
    }

    public function findOneByCompanyIdAndAuctionId($companyId, $auctionId)
    {
        $query = $this->em->createQuery("
            SELECT o, s, c, cc, a
            FROM SimpleTradeBundle:Offers o
            JOIN o.status s
            JOIN o.company c
            JOIN c.content cc
            JOIN o.auction a
            WHERE o.auction = :auctionId AND
            o.company = :companyId AND
            s.name != 'REVOKED_BY_BUILDER'");
        $query->setParameters(array(
            'auctionId' => $auctionId,
            'companyId' => $companyId
        ));

        return $query->getOneOrNullResult();
    }

    public function getCountByAuctionId($id)
    {
        $query = $this->em->createQuery("
            SELECT count(o)
            FROM SimpleTradeBundle:Offers o
            WHERE o.auction = :id_auction");
        $query->setParameters(array(
            'id_auction' => $id
        ));

        $out = $query->getOneOrNullResult(2);
        return array_pop($out);
    }

    public function checkByAuctionId($id)
    {
        $objects = $this->em->getRepository($this->entityName)->findAll();
        $out = array( );
        foreach( $objects as $object ) {
            if ($object->getAuction()->getId() == $id) return true;
        }
        return false;
    }

    public function checkByCompanyIdAndAuctionId($idCompany, $idAuction)
    {
        $objects = $this->em->getRepository($this->entityName)->findBy(array(
            'company' => $idCompany,
            'auction' => $idAuction
            ));

        return !empty($objects) ? true : false;
    }

    public function checkByCompanyIdAuctionIdStatus($idCompany, $idAuction, $status)
    {
        $query = $this->em->createQuery("
            SELECT o
            FROM SimpleTradeBundle:Offers o
            JOIN o.status s
            WHERE o.company = :id_company
            AND o.auction = :id_auction
            AND s.name = :status");

        $query->setParameters(array(
            'id_company' => $idCompany,
            'id_auction' => $idAuction,
            'status' => $status
        ));

        $result = $query->getResult();

        return !empty($result) ? true : false;
    }

}
