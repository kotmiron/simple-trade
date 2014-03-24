<?php

namespace SimpleSolution\SimpleTradeBundle\Model;
use SimpleSolution\SimpleTradeBundle\Entity\RequestsSros;
use Doctrine\ORM\EntityManager;

Class RequestsSrosModel
{
    protected $em;
    protected $entityName;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->entityName = 'SimpleSolution\SimpleTradeBundle\Entity\RequestsSros';
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
            SELECT rs, sro, c, t
            FROM SimpleTradeBundle:RequestsSros rs
            JOIN rs.sro sro
            JOIN sro.type t
            JOIN sro.content c
            WHERE rs.request=:requestId"
            );
        $query->setParameter('requestId', $requestId);

        return $query->getOneOrNullResult();
    }

    public function checkByCompanyIdStatusTypeSroType($companyId, $statusName, $typeName, $sroTypeId)
    {
        $query = $this->em->createQuery("
            SELECT rs
            FROM SimpleTradeBundle:RequestsSros rs
            JOIN rs.request r
            JOIN r.status s
            JOIN r.type t
            JOIN rs.sro sro
            WHERE s.name=:statusName AND
            t.name=:typeName AND
            r.company=:companyId AND
            sro.type=:sroTypeId");
        $query->setParameter('statusName', $statusName);
        $query->setParameter('typeName', $typeName);
        $query->setParameter('companyId', $companyId);
        $query->setParameter('sroTypeId', $sroTypeId);

        $objects = $query->getResult();
        if (count($objects) != 0) {
            return true;
        } else {
            return false;
        }
    }

    public function create($entity)
    {
        $object = new RequestsSros();
        $object->setComment($entity['comment']);
        $object->setRequest($entity['request']);
        $object->setSro($entity['sro']);

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
