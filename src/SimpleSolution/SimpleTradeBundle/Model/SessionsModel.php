<?php

namespace SimpleSolution\SimpleTradeBundle\Model;
use SimpleSolution\SimpleTradeBundle\Entity\Sessions;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

Class SessionsModel
{
    protected $em;
    protected $session;
    
    public function __construct(EntityManager $em, Session $session)
    {
        $this->em = $em;
        $this->session = $session;
    }
    
    public function findAll()
    {
        $session = $this->em->getRepository('SimpleTradeBundle:Sessions')->findAll();
        return $session;
    }
    
    public function findByPK()
    {
        $this->session->start();
        $session = $this->em->find('SimpleTradeBundle:Sessions', $this->session->getId());
        return $session;
    }
    
    public function create($entity)
    {
        $session = new Sessions();
        $session->setCreatedAt(new \DateTime());
        $session->setUser($entity['user']); 
        $this->em->persist($session);
        $this->em->flush();
    }

    public function removeByPK($sessionId)
    {
        $session = $this->em->find('SimpleTradeBundle:Sessions', $sessionId);
        $this->em->remove($session);
        $this->em->flush();
    } 
}
?>
