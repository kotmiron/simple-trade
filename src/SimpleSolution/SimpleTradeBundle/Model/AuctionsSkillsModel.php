<?php

namespace SimpleSolution\SimpleTradeBundle\Model;

use SimpleSolution\SimpleTradeBundle\Entity\AuctionsSkills;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

Class AuctionsSkillsModel
{
    protected $em;
    protected $session;
    protected $entityName;

    public function __construct(EntityManager $em, Session $session)
    {
        $this->em = $em;
        $this->session = $session;
        $this->entityName = 'SimpleSolution\SimpleTradeBundle\Entity\AuctionsSkills';
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

    public function findSkillsByAuctionIdAsArray($id)
    {
        $objects = $this->em->getRepository($this->entityName)->findBy(array( 'auction' => $id ));
        $out = array( );
        foreach( $objects as $object ) {
            $out[ $object->getId() ] = $object->getSkill()->getTitle();
        }
        return $out;
    }

    public function findSkillsIdByAuctionIdAsArray($id)
    {
        $objects = $this->em->getRepository($this->entityName)->findBy(array( 'auction' => $id ));
        $out = array( );
        foreach( $objects as $object ) {
            $out[ $object->getSkill()->getId() ] = $object->getIsMain();
        }
        return $out;
    }

    public function create($entity)
    {
        $object = new AuctionsSkills();

        $auction = $this->em->find('SimpleSolution\SimpleTradeBundle\Entity\Auctions', $entity[ 'auction' ]);
        $object->setAuction($auction);
        $skill = $this->em->find('SimpleSolution\SimpleTradeBundle\Entity\Skills', $entity[ 'skill' ]);
        $object->setSkill($skill);
        $object->setIsMain($entity[ 'isMain' ]);

        $this->em->persist($object);
        $this->em->flush();

        $transaction = new \History_transaction($this->em, $this->session);
        $transaction->createTransaction('create', $this->entityName, $object);
    }

    public function createFromObjects($entity)
    {
        $object = new UsersSkills();

        $object->setAuction($entity[ 'auction' ]);
        $object->setSkill($entity[ 'skill' ]);
        $object->setIsMain($entity[ 'isMain' ]);

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

    public function removeByAuctionId($id)
    {
        $objects = $this->em->getRepository($this->entityName)->findBy(array( 'auction' => $id ));
        foreach( $objects as $object ) {
            $transaction = new \History_transaction($this->em, $this->session);
            $transaction->createTransaction('remove', $this->entityName, $object);
            $this->em->remove($object);
        }
        $this->em->flush();
    }

}