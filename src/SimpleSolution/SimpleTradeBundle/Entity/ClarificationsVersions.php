<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\ClarificationsVersions
 *
 * @ORM\Table(name="clarifications_versions")
 * @ORM\Entity
 */
class ClarificationsVersions
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
     * @ORM\Column(name="group_id", type="integer", nullable=false)
     */
    private $groupId;

    /**
     * @var ClarificationsContent
     *
     * @ORM\ManyToOne(targetEntity="ClarificationsContent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     * })
     */
    private $content;

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
     * @var $clarification
     *
     * @ORM\OneToOne(targetEntity="Clarifications")
     * @ORM\JoinColumn(name="clarification_id", referencedColumnName="id")
     */
    private $clarification;

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
     * @return ClarificationsVersions
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
     * @return ClarificationsVersions
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
     * Set content
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\ClarificationsContent $content
     * @return ClarificationsVersions
     */
    public function setContent(\SimpleSolution\SimpleTradeBundle\Entity\ClarificationsContent $content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\ClarificationsContent
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set transaction
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Transactions $transaction
     * @return ClarificationsVersions
     */
    public function setTransaction(\SimpleSolution\SimpleTradeBundle\Entity\Transactions $transaction = null)
    {
        $this->transaction = $transaction;

        return $this;
    }

    /**
     * Get transaction
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * Set clarification
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Clarifications $clarification
     * @return Clarifications
     */
    public function setClarification(\SimpleSolution\SimpleTradeBundle\Entity\Clarifications $clarification)
    {
        $this->clarification = $clarification;

        return $this;
    }

    /**
     * Get clarification
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\Clarifications
     */
    public function getClarification()
    {
        return $this->clarification;
    }


    public function getIgnoredFields()
    {
        return array('id', 'auction', 'fromUser', 'toUser', 'type');
    }
}