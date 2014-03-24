<?php

namespace SimpleSolution\SimpleTradeBundle\Model;
use SimpleSolution\SimpleTradeBundle\Entity\SrosCompanies;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

Class SrosCompaniesModel
{
    protected $em;
    protected $session;
    protected $entityName;

    public function __construct(EntityManager $em, Session $session)
    {
        $this->em = $em;
        $this->session = $session;
        $this->entityName = 'SimpleSolution\SimpleTradeBundle\Entity\SrosCompanies';
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

    public function findOneBySroId($id)
    {
        $object = $this->em->getRepository($this->entityName)->findOneBy(array('sro' => $id));
        return $object;
    }

    public function findAllBySroId($id)
    {
        $objects = $this->em->getRepository($this->entityName)->findBy(array('sro' => $id));
        return $objects;
    }

    public function findOneByCompanyIdAndSroId($companyId, $sroId)
    {
        $object = $this->em->getRepository($this->entityName)->findOneBy(array('sro' => $sroId, 'company' => $companyId));
        return $object;
    }

    public function findAllByCompanyId($id)
    {
        $query = $this->em->createQuery("
            SELECT sc, sro, t, c, cc
            FROM SimpleTradeBundle:SrosCompanies sc
            JOIN sc.sro sro
            JOIN sro.type t
            JOIN sc.company c
            JOIN c.content cc
            WHERE sc.company=:company_id
            ");
        $query->setParameter('company_id', $id);

        $objects = $query->getResult();

        $out = array( );
        foreach($objects as $object)
        {
            array_push($out, $object->getSRO());
        }

        return $out;
    }

    public function findAllByCompanyIdAndStatus($id, $status)
    {
        $query = $this->em->createQuery("
            SELECT sc, s, c
            FROM SimpleTradeBundle:SrosCompanies sc
            JOIN sc.status cs
            JOIN sc.sro s
            JOIN sc.company c
            WHERE sc.company=:company_id AND cs.name =:status
            ");
        $query->setParameter('company_id', $id);
        $query->setParameter('status', $status);

        return $query->getResult();
    }
    public function findAllBySroIdAsArray($id)
    {
        $objects = $this->em->getRepository($this->entityName)->findBy(array('sro' => $id));
        $out = array( );
        foreach($objects as $object)
        {
            array_push($out, $object->getCompany()->getId());
        }
        return $out;
    }

    public function findSrosTypesByCompanyId($id)
    {
        $objects = $this->em->getRepository($this->entityName)->findBy(array('company' => $id));
        $out = array( );
        foreach($objects as $object)
        {
            array_push($out, $object->getSro()->getType()->getId());
        }

        return array_unique($out);
    }

    public function create($entity)
    {
        $object = new SrosCompanies();

        if (isset($entity['comment']))
            $object->setComment($entity['comment']);
        $sro = $this->em->find('SimpleSolution\SimpleTradeBundle\Entity\Sros', $entity['sro']);
        $object->setSro($sro);
        $company = $this->em->find('SimpleSolution\SimpleTradeBundle\Entity\Companies', $entity['company']);
        $object->setCompany($company);
        $status = $this->em->getRepository('SimpleSolution\SimpleTradeBundle\Entity\CompaniesStatuses')->findOneBy(array('name' => $entity['status']));
        $object->setStatus($status);

        $this->em->persist($object);
        $this->em->flush();

        $transaction = new \History_transaction($this->em, $this->session);
        $transaction->createTransaction('create', $this->entityName, $object);
    }

    public function createFromObjects($entity)
    {
        $object = new SrosCompanies();

        if (isset($entity['comment']))
            $object->setComment($entity['comment']);
        $object->setSro($entity['sro']);
        $object->setCompany($entity['company']);
        $object->setStatus($entity['status']);

        $this->em->persist($object);
        $this->em->flush();

        $transaction = new \History_transaction($this->em, $this->session);
        $transaction->createTransaction('create', $this->entityName, $object);
    }

    public function updateFromObjects($id, $entity)
    {
        $object = $this->em->find($this->entityName, $id);

        if (isset($entity['comment']))
            $object->setComment($entity['comment']);
        $object->setSro($entity['sro']);
        $object->setCompany($entity['company']);
        $object->setStatus($entity['status']);

        $this->em->persist($object);
        $this->em->flush();

        $transaction = new \History_transaction($this->em, $this->session);
        $transaction->createTransaction('update', $this->entityName, $object);
    }

    public function removeByPK($id)
    {
        $object = $this->em->find($this->entityName, $id);

        $transaction = new \History_transaction($this->em, $this->session);
        $transaction->createTransaction('remove', $this->entityName, $object);
        $this->em->remove($object);
        $this->em->flush();
    }

    public function removeBySroId($id)
    {
        $objects = $this->em->getRepository($this->entityName)->findBy(array('sro' => $id));
        foreach($objects as $object)
        {
            $transaction = new \History_transaction($this->em, $this->session);
            $transaction->createTransaction('remove', $this->entityName, $object);
            $this->em->remove($object);
        }
        $this->em->flush();
    }

    public function removeByCompanyId($id)
    {
        $objects = $this->em->getRepository($this->entityName)->findBy(array('company' => $id));
        foreach($objects as $object)
        {
            $transaction = new \History_transaction($this->em, $this->session);
            $transaction->createTransaction('remove', $this->entityName, $object);
            $this->em->remove($object);
        }
        $this->em->flush();
    }
}
