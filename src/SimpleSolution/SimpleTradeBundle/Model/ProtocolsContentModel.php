<?php

namespace SimpleSolution\SimpleTradeBundle\Model;

use SimpleSolution\SimpleTradeBundle\Entity\ProtocolsContent;
use Doctrine\ORM\EntityManager;

Class ProtocolsContentModel
{
    protected $em;
    protected $entityName;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->entityName = 'SimpleSolution\SimpleTradeBundle\Entity\ProtocolsContent';
    }

    public function create($entity)
    {
        $object = new ProtocolsContent();

        $object->setPlaceView($entity['placeView']);
        $object->setDatetimeStartView($entity['datetimeStartView']);
        $object->setDatetimeEndView($entity['datetimeEndView']);
        $object->setFullName1($entity['fullName1']);
        $object->setFullName2($entity['fullName2']);
        $object->setFullName3($entity['fullName3']);
        $object->setPosition1($entity['position1']);
        $object->setPosition2($entity['position2']);
        $object->setPosition3($entity['position3']);

        $this->em->persist($object);
        $this->em->flush();

        return $object;
    }

}
