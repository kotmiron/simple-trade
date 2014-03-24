<?php

namespace SimpleSolution\SimpleTradeBundle\Model;

use SimpleSolution\SimpleTradeBundle\Entity\Skills;
use Doctrine\ORM\EntityManager;

Class SkillsModel
{
    protected $em;
    protected $entityName;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->entityName = 'SimpleSolution\SimpleTradeBundle\Entity\Skills';
    }

    public function findAll()
    {
        $objects = $this->em->getRepository($this->entityName)->findAll();
        return $objects;
    }

    public function findAllAsArray()
    {
        $query = $this->em->createQuery("
            SELECT sp, s
            FROM SimpleTradeBundle:Skills s
            JOIN s.parent sp");

        $out = array( );
        foreach( $query->getResult() as $skill ) {
            $parent = $skill->getParent();
            $id = $parent->getId();
            $title = $parent->getTitle();
            $type = $parent->getType()->getId();
            if (!isset($out[ $id ])) {
                $out[ $id ] = array(
                    'title' => $title,
                    'type' => $type,
                    'skills' => array( )
                );
            }

            $out[ $id ][ 'skills' ][ $skill->getId() ] = $skill->getTitle();
        }

        return $out;
    }

    public function findAllBySrosTypesAsArray($types)
    {
        $query = $this->em->createQuery("
            SELECT sp, s, t
            FROM SimpleTradeBundle:Skills s
            JOIN s.parent sp
            JOIN sp.type t
            WHERE sp.type IN (:types)");
        $query->setParameter('types', $types);

        $out = array( );
        foreach( $query->getResult() as $skill ) {
            $parent = $skill->getParent();
            $id = $parent->getId();
            $title = $parent->getTitle();
            $type = $parent->getType()->getId();
            if (!isset($out[ $id ])) {
                $out[ $id ] = array(
                    'title' => $title,
                    'type' => $type,
                    'skills' => array( )
                );
            }

            $out[ $id ][ 'skills' ][ $skill->getId() ] = $skill->getTitle();
        }

        return $out;
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

    public function createByEntity($oEntity)
    {
        $this->em->persist($oEntity);
        $this->em->flush();
    }

    public function create($entity)
    {
        $object = new Roles();
        $object->setTitle($entity[ 'title' ]);
        $object->setName($entity[ 'name' ]);
        $object->setParent($entity[ 'parent' ]);
        $this->em->persist($object);
        $this->em->flush();
    }

    public function removeByPK($id)
    {
        $object = $this->em->find($this->entityName, $id);
        $this->em->remove($object);
        $this->em->flush();
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
