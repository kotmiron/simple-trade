<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\SrosVersions
 *
 * @ORM\Table(name="sros_versions")
 * @ORM\Entity
 */
class SrosVersions
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
     * @ORM\Column(name="group_id", type="integer", nullable=null)
     */
    private $groupId;

    /**
     * @var SrosContent
     *
     * @ORM\ManyToOne(targetEntity="SrosContent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     * })
     */
    private $content;

    /**
     * @var SrosTypes
     *
     * @ORM\ManyToOne(targetEntity="SrosTypes")
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
     * @return Sros
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
     * @return OffersVersions
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
     * @param SimpleSolution\SimpleTradeBundle\Entity\SrosContent $content
     * @return Sros
     */
    public function setContent(\SimpleSolution\SimpleTradeBundle\Entity\SrosContent $content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\SrosContent
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set type
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\SrosTypes $type
     * @return SrosVersions
     */
    public function setType(\SimpleSolution\SimpleTradeBundle\Entity\SrosTypes $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\SrosTypes
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set transaction
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Transactions $transaction
     * @return SrosVersions
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

    public function getIgnoredFields()
    {
        return array('id');
    }
}