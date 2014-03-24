<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\Transactions
 *
 * @ORM\Table(name="transactions")
 * @ORM\Entity
 */
class Transactions
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
     * @var string $actionName
     *
     * @ORM\Column(name="action_name", type="string", length=128, nullable=false)
     */
    private $actionName;

    /**
     * @var string $actionEntity
     *
     * @ORM\Column(name="action_entity", type="string", length=256, nullable=false)
     */
    private $actionEntity;
    
    /**
     * @var string $actionTable
     *
     * @ORM\Column(name="action_table", type="string", length=45, nullable=false)
     */
    private $actionTable;

    /**
     * @var integer $actionId
     *
     * @ORM\Column(name="action_id", type="integer", nullable=false)
     */
    private $actionId;

    /**
     * @var Sessions
     *
     * @ORM\ManyToOne(targetEntity="Sessions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="session_id", referencedColumnName="id")
     * })
     */
    private $session;



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
     * @return Transactions
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
     * Set actionName
     *
     * @param string $actionName
     * @return Transactions
     */
    public function setActionName($actionName)
    {
        $this->actionName = $actionName;
    
        return $this;
    }

    /**
     * Get actionName
     *
     * @return string 
     */
    public function getActionName()
    {
        return $this->actionName;
    }

    /**
     * Set actionEntity
     *
     * @param string $actionEntity
     * @return Transactions
     */
    public function setActionEntity($actionEntity)
    {
        $this->actionEntity = $actionEntity;
    
        return $this;
    }

    /**
     * Get actionEntity
     *
     * @return string 
     */
    public function getActionEntity()
    {
        return $this->actionEntity;
    }
    
    /**
     * Set actionTable
     *
     * @param string $actionTable
     * @return Transactions
     */
    public function setActionTable($actionTable)
    {
        $this->actionTable = $actionTable;
    
        return $this;
    }

    /**
     * Get actionTable
     *
     * @return string 
     */
    public function getActionTable()
    {
        return $this->actionTable;
    }

    /**
     * Set actionId
     *
     * @param integer $actionId
     * @return Transactions
     */
    public function setActionId($actionId)
    {
        $this->actionId = $actionId;
    
        return $this;
    }

    /**
     * Get actionId
     *
     * @return integer 
     */
    public function getActionId()
    {
        return $this->actionId;
    }

    /**
     * Set session
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Sessions $session
     * @return Transactions
     */
    public function setSession(\SimpleSolution\SimpleTradeBundle\Entity\Sessions $session = null)
    {
        $this->session = $session;
    
        return $this;
    }

    /**
     * Get session
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\Sessions 
     */
    public function getSession()
    {
        return $this->session;
    }
}