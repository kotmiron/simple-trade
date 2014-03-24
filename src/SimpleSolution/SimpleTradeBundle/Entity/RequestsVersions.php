<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\RequestsVersions
 *
 * @ORM\Table(name="requests_versions")
 * @ORM\Entity
 */
class RequestsVersions
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
     * @var integer $groupId
     *
     * @ORM\Column(name="group_id", type="integer", nullable=true)
     */
    private $groupId;

    /**
     * @var RequestsStatuses
     *
     * @ORM\ManyToOne(targetEntity="RequestsStatuses")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     * })
     */
    protected $status;

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
     * @var RequestsTypes
     *
     * @ORM\ManyToOne(targetEntity="RequestsTypes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     * })
     */
    private $type;

    /**
     * @var Transactions
     *
     * @ORM\ManyToOne(targetEntity="Transactions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="transaction_id", referencedColumnName="id")
     * })
     */
    private $transaction;

    /**
     * Set id
     *
     * @return RequestsVersions
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getIgnoredFields()
    {
        return array('id');
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
     * @return RequestsVersions
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
     * Set groupId
     *
     * @param integer $groupId
     * @return RequestsVersions
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
    
        return $this;
    }

    /**
     * Get groupId
     *
     * @return integer 
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Set status
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\RequestsStatuses $status
     * @return RequestsVersions
     */
    public function setStatus(\SimpleSolution\SimpleTradeBundle\Entity\RequestsStatuses $status = null)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\RequestsStatuses 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set company
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Companies $company
     * @return RequestsVersions
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
     * Set type
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\RequestsTypes $type
     * @return RequestsVersions
     */
    public function setType(\SimpleSolution\SimpleTradeBundle\Entity\RequestsTypes $type = null)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\RequestsTypes 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set transaction
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Transactions $transaction
     * @return RequestsVersions
     */
    public function setTransaction(\SimpleSolution\SimpleTradeBundle\Entity\Transactions $transaction = null)
    {
        $this->transaction = $transaction;
    
        return $this;
    }

    /**
     * Get transaction
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\Transactions 
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
}