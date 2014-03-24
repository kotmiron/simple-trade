<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\UsersSros
 *
 * @ORM\Table(name="users_sros")
 * @ORM\Entity
 */
class UsersSros
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var Sros
     *
     * @ORM\ManyToOne(targetEntity="Sros")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sro_id", referencedColumnName="id")
     * })
     */
    private $sro;


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
     * Set user
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Users $user
     * @return UsersSros
     */
    public function setUser(\SimpleSolution\SimpleTradeBundle\Entity\Users $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\Users 
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * Set sro
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Sros $sro
     * @return UsersSros
     */
    public function setSro(\SimpleSolution\SimpleTradeBundle\Entity\Sros $sro = null)
    {
        $this->sro = $sro;
    
        return $this;
    }

    /**
     * Get sro
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\Sros
     */
    public function getSro()
    {
        return $this->sro;
    }
}