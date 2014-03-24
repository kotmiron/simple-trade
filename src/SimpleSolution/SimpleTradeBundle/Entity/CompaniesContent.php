<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\CompaniesContent
 *
 * @ORM\Table(name="companies_content")
 * @ORM\Entity
 */
class CompaniesContent
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
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string $inn
     *
     * @ORM\Column(name="inn", type="string", length=12, nullable=false)
     */
    private $inn;

    /**
     * @var string $kpp
     *
     * @ORM\Column(name="kpp", type="string", length=20, nullable=true)
     */
    private $kpp;

    /**
     * @var string $ogrn
     *
     * @ORM\Column(name="ogrn", type="string", length=15, nullable=true)
     */
    private $ogrn;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @var string $phone
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=false)
     */
    private $phone;

    /**
     * @var string $position
     *
     * @ORM\Column(name="position", type="string", length=255, nullable=true)
     */
    private $position;

    /**
     * @var string $grounds
     *
     * @ORM\Column(name="grounds", type="string", length=255, nullable=true)
     */
    private $grounds;

    /**
     * @var string $userName
     *
     * @ORM\Column(name="user_name", type="string", length=128, nullable=false)
     */
    private $userName;

    /**
     * @var string $comment
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @var Regions
     *
     * @ORM\ManyToOne(targetEntity="Regions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="region_id", referencedColumnName="id")
     * })
     */
    private $region;

    public $regionId;

    public $skills;

    public $files;

    public $deletedFiles;

    public $attributes;

    /**
     * Set id
     *
     * @return Companies
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * @return CompaniesContent
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
     * @return CompaniesContent
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
     * Set inn
     *
     * @param string $inn
     * @return CompaniesContent
     */
    public function setInn($inn)
    {
        $this->inn = $inn;

        return $this;
    }

    /**
     * Get inn
     *
     * @return string
     */
    public function getInn()
    {
        return $this->inn;
    }

    /**
     * Set kpp
     *
     * @param string $kpp
     * @return CompaniesContent
     */
    public function setKpp($kpp)
    {
        $this->kpp = $kpp;

        return $this;
    }

    /**
     * Get kpp
     *
     * @return string
     */
    public function getKpp()
    {
        return $this->kpp;
    }


    /**
     * Set ogrn
     *
     * @param string $ogrn
     * @return CompaniesContent
     */
    public function setOgrn($ogrn)
    {
        $this->ogrn = $ogrn;

        return $this;
    }

    /**
     * Get ogrn
     *
     * @return string
     */
    public function getOgrn()
    {
        return $this->ogrn;
    }

    /**
     * Set region
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Regions $region
     * @return CompaniesContent
     */
    public function setRegion(\SimpleSolution\SimpleTradeBundle\Entity\Regions $region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\Regions
     */
    public function getRegion()
    {
        return $this->region;
    }

    public function getRegionId()
    {
        return $this->regionId;
    }

    /**
     * Set userName
     *
     * @param string $userName
     * @return CompaniesContent
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * Get userName
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Set position
     *
     * @param string $position
     * @return CompaniesContent
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set grounds
     *
     * @param string $grounds
     * @return CompaniesContent
     */
    public function setGrounds($grounds)
    {
        $this->grounds = $grounds;

        return $this;
    }

    /**
     * Get grounds
     *
     * @return string
     */
    public function getGrounds()
    {
        return $this->grounds;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return CompaniesContent
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return CompaniesContent
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return CompaniesContent
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    public function getSkills()
    {
        return $this->skills;
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function getDeletedFiles()
    {
        return $this->deletedFiles;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getAllFieldsAsArray()
    {
        return array(
            'title'    => $this->title,
            'name'     => $this->name,
            'inn'      => $this->inn,
            'kpp'      => $this->kpp,
            'ogrn'     => $this->ogrn,
            'email'    => $this->email,
            'phone'    => $this->phone,
            'position' => $this->position,
            'inn'      => $this->inn,
            'grounds'  => $this->grounds,
            'userName' => $this->userName,
            'region'   => $this->region,
            'comment'  => $this->comment,
        );
    }
}