<?php

namespace SimpleSolution\SimpleTradeBundle\Model;

use SimpleSolution\SimpleTradeBundle\Entity\ClarificationsContent;
use Doctrine\ORM\EntityManager;

Class ClarificationsContentModel
{
    protected $em;
    protected $entityName;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->entityName = 'SimpleSolution\SimpleTradeBundle\Entity\ClarificationsContent';
    }

    public function create($entity)
    {
        $object = new ClarificationsContent();

        $object->setSubject($entity['subject']);
        $object->setBody($entity['body']);

        $this->em->persist($object);
        $this->em->flush();
        return $object;
    }

}
