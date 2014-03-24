<?php

namespace SimpleSolution\SimpleTradeBundle\Model;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

Class ProtocolsModel extends \History_version
{
    const AUCTION_RESULTS = 'AUCTION_RESULTS';
    const REVIEW_OFFERS = 'REVIEW_OFFERS';

    public function __construct(EntityManager $em, Session $session, $acl)
    {
        parent::__construct($em, $session, 'SimpleSolution\SimpleTradeBundle\Entity\Protocols', $acl);
    }

    public function getByAuctionIdWithDocuments($id, $type)
    {
        $query = $this->em->createQuery("
            SELECT p, c, t
            FROM SimpleTradeBundle:Protocols p
            JOIN p.content c
            JOIN p.type t
            WHERE p.auction = :id
            AND t.name = :type")
            ->setParameter('id', $id)
            ->setParameter('type', $type);

        $protocol = $query->getOneOrNullResult();

        if (is_null($protocol)) {
            return null;
        }

        $query = $this->em->createQuery("
            SELECT dl, d, dc
            FROM SimpleTradeBundle:DocumentsLinks dl
            JOIN dl.document d
            JOIN d.content dc
            WHERE dl.ownerId IN (:id)
            AND dl.owner = 'PROTOCOL'")->setParameter('id', $protocol->getId());

        $documents = array( );
        foreach( $query->getResult() as $value ) {
            $documents[ ] = $value->getDocument();
        }

        return array(
            'protocol' => $protocol,
            'documents' => $documents
        );
    }

}
