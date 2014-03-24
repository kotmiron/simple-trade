<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\RolesPrimitives
 *
 * @ORM\Table(name="roles_primitives")
 * @ORM\Entity
 */
class RolesPrimitives
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
     * @var string $path
     *
     * @ORM\Column(name="path", type="string", length=256, nullable=false)
     */
    private $path;

    /**
    * @ORM\ManyToMany(targetEntity="SimpleSolution\SimpleTradeBundle\Entity\Roles", mappedBy="roles_primitives")
    */
    private $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

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
     * Set path
     *
     * @param string $path
     * @return RolesPrimitives
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Add roles
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Roles $roles
     */
    public function addRoles(\SimpleSolution\SimpleTradeBundle\Entity\Roles $roles)
    {
        $this->roles[] = $roles;
    }

    /**
     * Get roles
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getRoles()
    {
        return $this->roles;
    }
}