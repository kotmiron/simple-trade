<?php

namespace SimpleSolution\SimpleTradeBundle\Model;

use SimpleSolution\SimpleTradeBundle\Entity\DocumentsLinks;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

Class DocumentsLinksModel
{
    protected $em;
    protected $session;
    protected $entityName;

    public function __construct(EntityManager $em, Session $session)
    {
        $this->em = $em;
        $this->session = $session;
        $this->entityName = 'SimpleSolution\SimpleTradeBundle\Entity\DocumentsLinks';
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

    public function findAllByOwner($id, $name)
    {
        $query = $this->em->createQuery('
                SELECT dl.id, d.createdAt, d.isActive, dc.title, dc.filename FROM SimpleTradeBundle:DocumentsLinks dl
                JOIN dl.document d
                JOIN d.content dc
                WHERE dl.ownerId = :id AND dl.owner = :name'
            )->setParameters(array(
            'id' => $id,
            'name' => $name
            ));

        return $query->getResult();
    }

    public function findAllByOwners($ids, $name)
    {
        if (count($ids) == 0) return array( );
        $query = $this->em->createQuery('
                SELECT dl, d, dc FROM SimpleTradeBundle:DocumentsLinks dl
                JOIN dl.document d
                JOIN d.content dc
                WHERE dl.ownerId IN (:ids) AND dl.owner = :name'
            )->setParameters(array(
            'ids' => $ids,
            'name' => $name
            ));

        $files = $query->getResult();
        $out = array( );
        foreach( $files as $file ) {
            $ownerId = $file->getOwnerId();
            if (!isset($out[ $ownerId ])) {
                $out[ $ownerId ] = array();
            }
            $out[ $ownerId ][ ] = $file;
        }
        return $out;
    }

    public function findDocument($documentId, $ownerId, $owner)
    {
        $query = $this->em->createQuery("
            SELECT d, dc
            FROM SimpleTradeBundle:Documents d
            JOIN d.content dc
            JOIN d.links dl
            WHERE dl.ownerId = :owner_id
            AND dl.owner = :owner
            AND d.id = :document_id"
        );

        $query->setParameters(array(
            'document_id' => $documentId,
            'owner_id' => $ownerId,
            'owner' => $owner
        ));

        return $query->getOneOrNullResult();
    }

    public function create($entity)
    {
        $object = new DocumentsLinks();
        $object->setOwner($entity[ 'owner' ]);
        $object->setOwnerId($entity[ 'ownerId' ]);
        $object->setDocument($entity[ 'document' ]);

        $this->em->persist($object);
        $this->em->flush();
    }

    public function removeByPK($id)
    {
        $object = $this->em->find($this->entityName, $id);

        $transaction = new \History_transaction($this->em, $this->session);
        $transaction->createTransaction('remove', $this->entityName, $object);
        $this->em->remove($object);
        $this->em->flush();
    }

    public function removeByDocumentId($id)
    {
        $objects = $this->em->getRepository($this->entityName)->findBy(array( 'document' => $id ));
        foreach( $objects as $object ) {
            $transaction = new \History_transaction($this->em, $this->session);
            $transaction->createTransaction('remove', $this->entityName, $object);
            $this->em->remove($object);
        }
        $this->em->flush();
    }

}
