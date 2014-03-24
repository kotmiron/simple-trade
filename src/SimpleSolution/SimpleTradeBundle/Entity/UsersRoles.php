<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\UsersRoles
 *
 * @ORM\Table(name="users_roles")
 * @ORM\Entity
 */
class UsersRoles
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
     * @var Roles
     *
     * @ORM\ManyToOne(targetEntity="Roles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     * })
     */
    private $role;
    

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
     * @return UsersRoles
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
     * Set role
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Roles $role
     * @return UsersRoles
     */
    public function setRole(\SimpleSolution\SimpleTradeBundle\Entity\Roles $role = null)
    {
        $this->role = $role;
    
        return $this;
    }

    /**
     * Get role
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\Roles 
     */
    public function getRole()
    {
        return $this->role;
    }
}