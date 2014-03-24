<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\Roles
 *
 * @ORM\Table(name="roles")
 * @ORM\Entity
 */
class Roles
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
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=128, nullable=false)
     */
    private $title;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=128, nullable=false)
     */
    private $name;
    
    /**
    * @ORM\ManyToMany(targetEntity="SimpleSolution\SimpleTradeBundle\Entity\RolesPrimitives")
    * @ORM\JoinTable(name="roles_roles_primitives",
    *     joinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")},
    *     inverseJoinColumns={@ORM\JoinColumn(name="primitive_id", referencedColumnName="id")}
    *     )
    */
    private $roles_primitives;

    public $roles;

    public function __construct()
    {
        $this->roles_primitives = new ArrayCollection();
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
     * Set title
     *
     * @param string $title
     * @return Roles
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Roles
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add roles_primitives
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\RolesPrimitives $rolePrimitive
     */
    public function addRolesPrimitive(\SimpleSolution\SimpleTradeBundle\Entity\RolesPrimitives $rolesPrimitive)
    {
        $this->roles_primitives[] = $rolesPrimitive;
    }

    /**
     * Set roles_primitives
     *
     * @param array $rolesPrimitives
     */
    public function setRolesPrimitives(array $rolesPrimitives)
    {
        $this->roles_primitives = $rolesPrimitives;
    }

    /**
     * Get roles_primitives
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getRolesPrimitives()
    {
        return $this->roles_primitives;
    }

    public function getRolesPrimitivesAsArrayOfStrings()
    {
        $out = array();
        foreach ($this->getRolesPrimitives() as $primitive) {
             $out[$primitive->getId()] = $primitive->getPath();
        }
        return $out;
    }

    public function getRoles()
    {
        return $this->roles;
    }
}