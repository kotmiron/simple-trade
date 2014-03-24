<?php

namespace SimpleSolution\SimpleTradeBundle\Model;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

Class ClarificationsModel extends \History_version
{
    const TO_AUCTION = 'REQUEST_TO_AUCTION';
    const AUCTION_RESULTS = 'REQUEST_AUCTION_RESULTS';

    public function __construct(EntityManager $em, Session $session, $acl)
    {
        parent::__construct($em, $session, 'SimpleSolution\SimpleTradeBundle\Entity\Clarifications', $acl);
    }

    public function findByAuctionId($id, $type = self::TO_AUCTION)
    {
        $query = $this->em->createQuery("
            SELECT cl, cnt, t, fu, tu
            FROM SimpleTradeBundle:Clarifications cl
            JOIN cl.content cnt
            JOIN cl.type t
            JOIN cl.fromUser fu
            LEFT JOIN cl.toUser tu
            WHERE cl.auction = :id
            AND t.name = '" . $type . "'
            ORDER BY cl.id")->setParameter('id', $id);

        $resultClarifications = $query->getResult();

        $usersId = array( );
        $clarificationsId = array( );
        foreach( $resultClarifications as $value ) {
            $usersId[ ] = $value->getFromUser()->getId();
            $clarificationsId[ ] = $value->getId();
            if (!is_null($value->getClarification())) {
                $usersId[ ] = $value->getClarification()->getFromUser()->getId();
                $clarificationsId[ ] = $value->getClarification()->getId();
            }
        }

        if (count($usersId) == 0) return array( );

        // Все компании
        $query = $this->em->createQuery("
            SELECT uc, cm, cn
            FROM SimpleTradeBundle:UsersCompanies uc
            JOIN uc.company cm
            JOIN cm.content cn
            WHERE uc.user IN (:ids)")->setParameter('ids', $usersId);

        $usersCompanies = array( );
        foreach( $query->getResult() as $value ) {
            $usersCompanies[ $value->getUser()->getId() ] = $value->getCompany();
        }

        // Все документы
        $query = $this->em->createQuery("
            SELECT dl, d, dc
            FROM SimpleTradeBundle:DocumentsLinks dl
            JOIN dl.document d
            JOIN d.content dc
            WHERE dl.ownerId IN (:clarificationsId)
            AND dl.owner = 'CLARIFICATION'")->setParameter('clarificationsId', $clarificationsId);

        $documents = array( );
        foreach( $query->getResult() as $value ) {
            if (!isset($documents[ $value->getOwnerId() ])) $documents[ $value->getOwnerId() ] = array( );
            $documents[ $value->getOwnerId() ][ ] = $value->getDocument();
        }

        // Собираем выходной массив
        $clarifications = array( );
        foreach( $resultClarifications as $value ) {

            $clarification = null;
            if (!is_null($value->getClarification())) {
                $clarification = array(
                    'company' => isset($usersCompanies[ $value->getClarification()->getFromUser()->getId() ]) ?
                        $usersCompanies[ $value->getClarification()->getFromUser()->getId() ] :
                        null,
                    'answer' => $value->getClarification(),
                    'documents' => isset($documents[ $value->getClarification()->getId() ]) ? $documents[ $value->getClarification()->getId() ] : array( )
                );
            }

            $clarifications[ $value->getId() ] = array(
                'request' => array(
                    'company' => $usersCompanies[ $value->getFromUser()->getId() ],
                    'request' => $value,
                    'documents' => isset($documents[ $value->getId() ]) ? $documents[ $value->getId() ] : array( )
                ),
                'answer' => $clarification
            );
        }

        return $clarifications;
    }

    public function getByAuctionIdAndUserId($idAuction, $idUser, $type = self::TO_AUCTION)
    {
        $query = $this->em->createQuery("
            SELECT cl, cnt, t, fu, tu
            FROM SimpleTradeBundle:Clarifications cl
            JOIN cl.content cnt
            JOIN cl.type t
            JOIN cl.fromUser fu
            LEFT JOIN cl.toUser tu
            WHERE cl.auction = :id_auction
            AND cl.fromUser = :id_user
            AND t.name = '" . $type . "'")
            ->setParameter('id_auction', $idAuction)
            ->setParameter('id_user', $idUser);

        $clarification = $query->getOneOrNullResult();

        if (is_null($clarification)) {
            return null;
        }

        $clarificationsId = array( $clarification->getId() );
        if ($clarification->getClarification()) {
            $clarificationsId[ ] = $clarification->getClarification()->getId();
        }

        // Все документы
        $query = $this->em->createQuery("
            SELECT dl, d, dc
            FROM SimpleTradeBundle:DocumentsLinks dl
            JOIN dl.document d
            JOIN d.content dc
            WHERE dl.ownerId IN (:clarificationsId)
            AND dl.owner = 'CLARIFICATION'")->setParameter('clarificationsId', $clarificationsId);

        $documents = array( );
        foreach( $query->getResult() as $value ) {
            if (!isset($documents[ $value->getOwnerId() ])) $documents[ $value->getOwnerId() ] = array( );
            $documents[ $value->getOwnerId() ][ ] = $value->getDocument();
        }

        return array(
            'request' => array(
                'request' => $clarification,
                'documents' => isset($documents[ $clarification->getId() ]) ? $documents[ $clarification->getId() ] : array( )
            ),
            'answer' => array(
                'answer' => $clarification->getClarification(),
                'documents' => $clarification->getClarification() !== null && isset($documents[ $clarification->getClarification()->getId() ]) ?
                    $documents[ $clarification->getClarification()->getId() ] :
                    array( )
            )
        );
    }

}
