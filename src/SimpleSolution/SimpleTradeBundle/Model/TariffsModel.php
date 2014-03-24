<?php

namespace SimpleSolution\SimpleTradeBundle\Model;

use SimpleSolution\SimpleTradeBundle\Entity\Tariffs;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

Class TariffsModel
{
    protected $em;
    protected $session;
    protected $entityName;

    public function __construct(EntityManager $em, Session $session)
    {
        $this->em = $em;
        $this->session = $session;
        $this->entityName = 'SimpleSolution\SimpleTradeBundle\Entity\Tariffs';
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

    public function findOneByName($name)
    {
        $object = $this->em->getRepository($this->entityName)->findOneBy(array( 'name' => $name ));
        return $object;
    }

    public function create($entity)
    {
        $object = new Tariffs();
        $object->setName($entity[ 'name' ]);
        $object->setTitle($entity[ 'title' ]);
        $object->setService($entity[ 'service' ]);
        $object->setСost($entity[ 'cost' ]);
        $this->em->persist($object);
        $this->em->flush();

        $transaction = new \History_transaction($this->em, $this->session);
        $transaction->createTransaction('create', $this->entityName, $object);

        return $object;
    }

    public function updateFromObject($oEntity)
    {
        $this->em->persist($oEntity);
        $this->em->flush();

        $transaction = new \History_transaction($this->em, $this->session);
        $transaction->createTransaction('update', $this->entityName, $oEntity);
    }

    public function update($entity)
    {
        $object = $this->em->find($this->entityName, $entity[ 'id' ]);
        $object->setCost($entity[ 'cost' ]);

        $this->em->persist($object);
        $this->em->flush();

        $transaction = new \History_transaction($this->em, $this->session);
        $transaction->createTransaction('update', $this->entityName, $object);
    }

    public function removeByPK($id)
    {
        $object = $this->em->find($this->entityName, $id);
        $this->em->remove($object);
        $this->em->flush();
    }

}
?>
