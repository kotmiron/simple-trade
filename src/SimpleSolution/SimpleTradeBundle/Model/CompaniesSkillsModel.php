<?php

namespace SimpleSolution\SimpleTradeBundle\Model;
use SimpleSolution\SimpleTradeBundle\Entity\CompaniesSkills;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

Class CompaniesSkillsModel
{
    protected $em;
    protected $session;
    protected $entityName;

    public function __construct(EntityManager $em, Session $session)
    {
        $this->em = $em;
        $this->session = $session;
        $this->entityName = 'SimpleSolution\SimpleTradeBundle\Entity\CompaniesSkills';
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

    public function findSkillsByCompanyIdAsArray($id)
    {
        $objects = $this->em->getRepository($this->entityName)->findBy(array('company' => $id));
        $out = array();
        foreach($objects as $object)
        {
            $out[$object->getId()] = $object->getSkill()->getTitle();
        }
        return $out;
    }

    public function findSkillsByCompanyIdAsArrayOfIds($id)
    {
        $objects = $this->em->getRepository($this->entityName)->findBy(array('company' => $id));
        $out = array();
        foreach($objects as $object)
        {
            $id = $object->getSkill()->getId();
            $out[$id] = $id.'|'.(int)$object->getIsMain().'|'.(int)$object->getIsDangerous();
        }
        return $out;
    }

    public function create($entity)
    {
        $object = new CompaniesSkills();

        $company = $this->em->find('SimpleSolution\SimpleTradeBundle\Entity\Companies', $entity['company']);
        $object->setCompany($company);
        $skill = $this->em->find('SimpleSolution\SimpleTradeBundle\Entity\Skills', $entity['skill']);
        $object->setSkill($skill);
        $object->setIsMain($entity['isMain']);
        $object->setIsDangerous($entity['isDangerous']);

        $this->em->persist($object);
        $this->em->flush();

        $transaction = new \History_transaction($this->em, $this->session);
        $transaction->createTransaction('create', $this->entityName, $object);
    }

    public function createFromObjects($entity)
    {
        $object = new CompaniesSkills();

        $object->setCompany($entity['company']);
        $object->setSkill($entity['skill']);
        $object->setIsMain($entity['isMain']);
        $object->setIsDangerous($entity['isDangerous']);

        $this->em->persist($object);
        $this->em->flush();

        $transaction = new \History_transaction($this->em, $this->session);
        $transaction->createTransaction('create', $this->entityName, $object);
    }

    public function removeByPK($id)
    {
        $object = $this->em->find($this->entityName, $id);

        $transaction = new \History_transaction($this->em, $this->session);
        $transaction->createTransaction('remove', $this->entityName, $object);
        $this->em->remove($object);
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
?>
