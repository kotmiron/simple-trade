<?php

namespace SimpleSolution\SimpleTradeBundle\Model;

use SimpleSolution\SimpleTradeBundle\Entity\RolesPrimitives;
use Doctrine\ORM\EntityManager;

Class RolesPrimitivesModel
{
    protected $em;
    protected $entityName;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->entityName = 'SimpleSolution\SimpleTradeBundle\Entity\RolesPrimitives';
    }

    public function findAll()
    {
        $objects = $this->em->getRepository($this->entityName)->findAll();
        return $objects;
    }

    public function findAllAsArray()
    {
        $objects = $this->em->getRepository($this->entityName)->findAll();
        $out = array( );
        foreach( $objects as $object ) {
            $out[ $object->getId() ] = $object->getPath();
        }
        return $out;
    }

    public function findByPK($id)
    {
        $object = $this->em->find($this->entityName, $id);
        return $object;
    }

    public function findByPKs(array $ids)
    {
        if (count($ids) == 0) {
            return array( );
        }
        return $this->em->getRepository($this->entityName)->findBy(array( 'id' => $ids ));
    }

    public function create($entity)
    {
        $object = new RolesPrimitives();
        $object->setPath($entity[ 'path' ]);
        $this->em->persist($object);
        $this->em->flush();
    }

    public function removeByPK($id)
    {
        $object = $this->em->find($this->entityName, $id);
        $this->em->remove($object);
        $this->em->flush();
    }

}
?>
