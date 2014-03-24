<?php

namespace SimpleSolution\SimpleTradeBundle\Model;

use SimpleSolution\SimpleTradeBundle\Entity\RequestsComplaints;
use Doctrine\ORM\EntityManager;

Class RequestsComplaintsModel
{
    protected $em;
    protected $entityName;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->entityName = 'SimpleSolution\SimpleTradeBundle\Entity\RequestsComplaints';
    }

    public function findAll()
    {
        $objects = $this->em->getRepository($this->entityName)->findAll();
        return $objects;
    }

    public function findByPK($id)
    {
        $object = $this->em->find($this->entityName, $id);
        return $object;
    }

    public function findOneByRequestId($requestId)
    {
        $query = $this->em->createQuery("
            SELECT rc
            FROM SimpleTradeBundle:RequestsComplaints rc
            WHERE rc.request=:requestId"
        );
        $query->setParameter('requestId', $requestId);

        return $query->getOneOrNullResult();
    }

    public function create($entity)
    {
        $object = new RequestsComplaints();
        $object->setText($entity[ 'text' ]);
        if (isset($entity[ 'comment' ]))
            $object->setComment($entity[ 'comment' ]);
        $object->setRequest($entity[ 'request' ]);
        $object->setCompany($entity[ 'company' ]);
        if (isset($entity[ 'sro' ]))
            $object->setSro($entity[ 'sro' ]);

        $this->em->persist($object);
        $this->em->flush();
        return $object;
    }

    public function removeByPK($id)
    {
        $object = $this->em->find($this->entityName, $id);
        $this->em->remove($object);
        $this->em->flush();
    }

}
?>
