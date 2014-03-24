<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\Accounts
 *
 * @ORM\Table(name="accounts")
 * @ORM\Entity
 */
class Accounts
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
     * @var \DateTime $createdAt
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var AccountsContent
     *
     * @ORM\ManyToOne(targetEntity="AccountsContent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     * })
     */
    private $content;

    /**
     * @var Companies
     *
     * @ORM\ManyToOne(targetEntity="Companies")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     * })
     */
    private $company;

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
     * @var AccountsTypes
     *
     * @ORM\ManyToOne(targetEntity="AccountsTypes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="account_type_id", referencedColumnName="id")
     * })
     */
    private $type;

    public $tariff;

    /**
     * Set id
     *
     * @return Accounts
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
     * @return Accounts
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
     * Set content
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\AccountsContent $content
     * @return Accounts
     */
    public function setContent(\SimpleSolution\SimpleTradeBundle\Entity\AccountsContent $content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\AccountsContent
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set company
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Companies $company
     * @return Accounts
     */
    public function setCompany(\SimpleSolution\SimpleTradeBundle\Entity\Companies $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\Companies
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set user
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Users $user
     * @return Accounts
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
     * Set type
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\AccountsTypes $type
     * @return Accounts
     */
    public function setType(\SimpleSolution\SimpleTradeBundle\Entity\AccountsTypes $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\AccountsTypes
     */
    public function getType()
    {
        return $this->type;
    }

    public function getTariff()
    {
        return $this->tariff;
    }
}