<?php

namespace SimpleSolution\SimpleTradeBundle\Model;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

Class SrosModel extends \History_version
{

    public function __construct(EntityManager $em, Session $session, $acl)
    {
        parent::__construct($em, $session, 'SimpleSolution\SimpleTradeBundle\Entity\Sros', $acl);
    }

    public function findAllAsArray()
    {
        $query = $this->em->createQuery("
            SELECT s, st, c
            FROM SimpleTradeBundle:Sros s
            JOIN s.type st
            JOIN s.content c
            ");

        $out = array( );
        foreach( $query->getResult() as $sro ) {
            $type = $sro->getType();
            $title = $type->getTitle();
            if (!isset($out[ $title ])) {
                $out[ $title ] = array( );
            }

            $out[ $title ][ $sro->getId() ][ 'title' ] = $sro->getContent()->getTitle();
            $out[ $title ][ $sro->getId() ][ 'type' ] = $sro->getType()->getId();
        }

        return $out;
    }

    public function findAllExcludeTypesAsArray($types)
    {
        $query = $this->em->createQuery("
            SELECT s, st, c
            FROM SimpleTradeBundle:Sros s
            JOIN s.type st
            JOIN s.content c
            WHERE s.type not IN (:types)");
        $query->setParameter('types', $types);

        $out = array( );
        foreach( $query->getResult() as $sro ) {
            $type = $sro->getType();
            $title = $type->getTitle();
            if (!isset($out[ $title ])) {
                $out[ $title ] = array( );
            }

            $out[ $title ][ $sro->getId() ][ 'title' ] = $sro->getContent()->getTitle();
            $out[ $title ][ $sro->getId() ][ 'type' ] = $sro->getType()->getId();
        }

        return $out;
    }

    public function truncate()
    {
        $cmd = $this->em->getClassMetadata($this->entityName);
        $connection = $this->em->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->beginTransaction();
        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $q = $dbPlatform->getTruncateTableSql($cmd->getTableName());
            $connection->executeUpdate($q);
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
        } catch( \Exception $e ) {
            $connection->rollback();
            throw $e;
        }
    }
    
}
