<?php

namespace SimpleSolution\SimpleTradeBundle\Model;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

Class RobotsModel extends \History_version
{
    public function __construct(EntityManager $em, Session $session, $acl)
    {
        parent::__construct($em, $session, 'SimpleSolution\SimpleTradeBundle\Entity\Robots', $acl);
    }

    public function findByUserIdAndAuctionId($idUser, $idAuction)
    {
        return $this->em->getRepository($this->entityName)->findOneBy(array(
            'user' => $idUser,
            'auction' => $idAuction
            ));
    }

}
