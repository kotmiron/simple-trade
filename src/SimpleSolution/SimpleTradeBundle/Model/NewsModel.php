<?php

namespace SimpleSolution\SimpleTradeBundle\Model;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

Class NewsModel extends \History_version
{
    public function __construct(EntityManager $em, Session $session, $acl)
    {
        parent::__construct($em, $session, 'SimpleSolution\SimpleTradeBundle\Entity\News', $acl);
    }

    public function findAllByPermission($permission)
    {
        $query = $this->em->createQuery("
            SELECT n, c
            FROM SimpleTradeBundle:News n
            JOIN n.content c");

        $out = array( );
        foreach( $query->getResult() as $news ) {
            if (($news->getPermission() & $permission) == $permission)
                array_push($out, $news);
        }

        return $out;
    }
}
