<?php

namespace SimpleSolution\SimpleTradeBundle\Model;

use SimpleSolution\SimpleTradeBundle\Entity\RobotsContent;
use Doctrine\ORM\EntityManager;

Class RobotsContentModel
{
    protected $em;
    protected $entityName;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->entityName = 'SimpleSolution\SimpleTradeBundle\Entity\RobotsContent';
    }

    public function findAll()
    {
        return $this->em->getRepository($this->entityName)->findAll();
    }

    public function findByPK($id)
    {
        return $this->em->find($this->entityName, $id);
    }

    public function create($entity)
    {
        $robotsContent = new RobotsContent();
        $robotsContent->setBidSize($entity['bid_size']);
        $robotsContent->setDeadline($entity['deadline']);
        $robotsContent->setBidTime($entity['bid_time']);

        $this->em->persist($robotsContent);
        $this->em->flush();
        return $robotsContent;
    }

    public function removeByPK($id)
    {
        $object = $this->em->find($this->entityName, $id);
        $this->em->remove($object);
        $this->em->flush();
    }
}
