<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Session\Storage\PdoSessionHandler;
/**
 * SimpleSolution\SimpleTradeBundle\Entity\Sessions
 *
 * @ORM\Table(name="sessions")
 * @ORM\Entity
 */
class Sessions
{
    /**
     * @var string $id
     *
     * @ORM\Column(name="id", type="string", length=255, nullable=false)
     * @ORM\Id
     */
    private $id;

    /**
     * @var string $data
     *
     * @ORM\Column(name="data", type="text", nullable=true)
     */
    private $data;
    
    /**
     * @var string $time
     *
     * @ORM\Column(name="time", type="integer", nullable=true)
     */
    private $time;
    
//    /**
//     * @var \DateTime $createdAt
//     *
//     * @ORM\Column(name="created_at", type="datetime", nullable=false)
//     */
//    private $createdAt;
//
//    /**
//     * @var Users
//     *
//     * @ORM\ManyToOne(targetEntity="Users")
//     * @ORM\JoinColumns({
//     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
//     * })
//     */
//    private $user;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set data
     *
     * @param string $data
     * @return Sessions
     */
    public function setData($data)
    {
        $this->data = $data;
    
        return $this;
    }

    /**
     * Get data
     *
     * @return string 
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * Set time
     *
     * @param string $time
     * @return Sessions
     */
    public function setTime($time)
    {
        $this->time = $time;
    
        return $this;
    }

    /**
     * Get time
     *
     * @return string 
     */
    public function getTime()
    {
        return $this->time;
    }
    
//    /**
//     * Set createdAt
//     *
//     * @param \DateTime $createdAt
//     * @return Sessions
//     */
//    public function setCreatedAt($createdAt)
//    {
//        $this->createdAt = $createdAt;
//    
//        return $this;
//    }
//
//    /**
//     * Get createdAt
//     *
//     * @return \DateTime 
//     */
//    public function getCreatedAt()
//    {
//        return $this->createdAt;
//    }
//
//    /**
//     * Set user
//     *
//     * @param SimpleSolution\SimpleTradeBundle\Entity\Users $user
//     * @return Sessions
//     */
//    public function setUser(\SimpleSolution\SimpleTradeBundle\Entity\Users $user = null)
//    {
//        $this->user = $user;
//    
//        return $this;
//    }
//
//    /**
//     * Get user
//     *
//     * @return SimpleSolution\SimpleTradeBundle\Entity\Users 
//     */
//    public function getUser()
//    {
//        return $this->user;
//    }
}