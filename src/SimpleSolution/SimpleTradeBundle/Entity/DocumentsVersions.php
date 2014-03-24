<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\DocumentsVersions
 *
 * @ORM\Table(name="documents_versions")
 * @ORM\Entity
 */
class DocumentsVersions
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
     * @var string $isActive
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

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
     * @var DocumentsContent
     *
     * @ORM\ManyToOne(targetEntity="DocumentsContent")
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }



    /**
     * Set isActive
     *
     * @param string $isActive
     * @return Documents
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return string
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Documents
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
     * @param SimpleSolution\SimpleTradeBundle\Entity\DocumentsContent $content
     * @return Documents
     */
    public function setContent(\SimpleSolution\SimpleTradeBundle\Entity\DocumentsContent $content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\DocumentsContent
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set transaction
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Transactions $transaction
     * @return DocumentsVersions
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