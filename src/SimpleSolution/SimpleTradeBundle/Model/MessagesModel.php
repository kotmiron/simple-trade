<?php

namespace SimpleSolution\SimpleTradeBundle\Model;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;
use SimpleSolution\SimpleTradeBundle\Services\ACL;

Class MessagesModel extends \History_version
{
    protected $em;
    protected $entityName;

    public function __construct(EntityManager $em, Session $session, ACL $acl)
    {
        parent::__construct($em, $session, 'SimpleSolution\SimpleTradeBundle\Entity\Messages', $acl);
    }

    public function findAll()
    {
        $query = $this->em->createQuery("
            SELECT m, u_from, u_to, c
            FROM SimpleTradeBundle:Messages m
            JOIN m.fromUser u_from
            JOIN m.toUser u_to
            JOIN m.content c
            ORDER BY m.createdAt DESC
            ");

        return $query->getResult();
    }

    public function getCount($user)
    {
        $query = $this->em->createQuery("
            SELECT COUNT( m )
            FROM SimpleTradeBundle:Messages m
            WHERE m.fromUser=:from_user_id
            ");
        $query->setParameter('from_user_id', $user->getId());

        return $query->getSingleScalarResult();
    }

}