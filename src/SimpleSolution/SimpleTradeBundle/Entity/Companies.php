<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use CompaniesSkills;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\Companies
 *
 * @ORM\Table(name="companies")
 * @ORM\Entity
 */
class Companies
{

    public function __construct()
    {
        $this->companiesSkills = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime $createdAt
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var CompaniesStatuses
     *
     * @ORM\ManyToOne(targetEntity="CompaniesStatuses")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     * })
     */
    protected $status;

    /**
     * @var CompaniesTypes
     *
     * @ORM\ManyToOne(targetEntity="CompaniesTypes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     * })
     */
    protected $type;

    /**
     * @var CompaniesContent
     *
     * @ORM\ManyToOne(targetEntity="CompaniesContent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     * })
     */
    private $content;

    /**
     * @ORM\OneToMany(targetEntity="CompaniesSkills", mappedBy="company")
     */
    private $companiesSkills;

    /**
     * @ORM\OneToMany(targetEntity="SrosCompanies", mappedBy="company")
     */
    private $companiesSros;

    /**
     * @var integer $penalty
     *
     * @ORM\Column(name="penalty", type="integer")
     */
    private $penalty = 0;

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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Companies
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set status
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\CompaniesStatuses $status
     * @return Companies
     */
    public function setStatus(\SimpleSolution\SimpleTradeBundle\Entity\CompaniesStatuses $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\CompaniesStatuses
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set type
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\CompaniesTypes $type
     * @return Companies
     */
    public function setType(\SimpleSolution\SimpleTradeBundle\Entity\CompaniesTypes $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\CompaniesTypes
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set content
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\CompaniesContent $content
     * @return Companies
     */
    public function setContent(\SimpleSolution\SimpleTradeBundle\Entity\CompaniesContent $content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\CompaniesContent
     */
    public function getContent()
    {
        return $this->content;
    }

    public function getCompaniesSkills()
    {
        return $this->companiesSkills;
    }

    public function getCompaniesSros()
    {
        return $this->companiesSros;
    }

    /**
     * Set penalty
     *
     * @param integer $penalty
     * @return Companies
     */
    public function setPenalty($penalty)
    {
        $this->penalty = $penalty;

        return $this;
    }

    /**
     * Get penalty
     *
     * @return integer
     */
    public function getPenalty()
    {
        return $this->penalty;
    }
}