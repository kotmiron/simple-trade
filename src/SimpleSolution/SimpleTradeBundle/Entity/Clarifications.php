<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\Clarifications
 *
 * @ORM\Table(name="clarifications")
 * @ORM\Entity
 */
class Clarifications
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
     * @var ClarificationsContent
     *
     * @ORM\ManyToOne(targetEntity="ClarificationsContent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     * })
     */
    private $content;

    /**
     * @var $fromUser
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="from_user", referencedColumnName="id")
     * })
     */
    private $fromUser;

    /**
     * @var $toUser
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="to_user", referencedColumnName="id")
     * })
     */
    private $toUser;

    /**
     * @var $clarification
     *
     * @ORM\OneToOne(targetEntity="Clarifications")
     * @ORM\JoinColumn(name="clarification_id", referencedColumnName="id")
     */
    private $clarification;

    /**
     * @var Auctions
     *
     * @ORM\ManyToOne(targetEntity="Auctions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="auction_id", referencedColumnName="id")
     * })
     */
    private $auction;

    /**
     * @var ClarificationsTypes
     *
     * @ORM\ManyToOne(targetEntity="ClarificationsTypes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     * })
     */
    protected $type;

    /**
     * Set id
     *
     * @return Clarifications
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
     * @return Clarifications
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
     * @param SimpleSolution\SimpleTradeBundle\Entity\ClarificationsContent $content
     * @return Clarifications
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
     * Set from user
     *
     * @param \SimpleSolution\SimpleTradeBundle\Entity\Users $fromUser
     * @return ClarificationsVersions
     */
    public function setFromUser(\SimpleSolution\SimpleTradeBundle\Entity\Users $fromUser)
    {
        $this->fromUser = $fromUser;

        return $this;
    }

    /**
     * Get from user
     *
     * @return \SimpleSolution\SimpleTradeBundle\Entity\Users
     */
    public function getFromUser()
    {
        return $this->fromUser;
    }

    /**
     * Set to user
     *
     * @param \SimpleSolution\SimpleTradeBundle\Entity\Users $fromUser
     * @return ClarificationsVersions
     */
    public function setToUser(\SimpleSolution\SimpleTradeBundle\Entity\Users $toUser = null)
    {
        $this->toUser = $toUser;

        return $this;
    }

    /**
     * Get to user
     *
     * @return \SimpleSolution\SimpleTradeBundle\Entity\Users
     */
    public function getToUser()
    {
        return $this->toUser;
    }

    /**
     * Set clarification
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Clarification $clarification
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
     * @return SimpleSolution\SimpleTradeBundle\Entity\Clarification
     */
    public function getClarification()
    {
        return $this->clarification;
    }

    /**
     * Set auction
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Auctions $auction
     * @return Clarifications
     */
    public function setAuction(\SimpleSolution\SimpleTradeBundle\Entity\Auctions $auction = null)
    {
        $this->auction = $auction;

        return $this;
    }

    /**
     * Get auction
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\Auctions
     */
    public function getAuction()
    {
        return $this->auction;
    }

    /**
     * Set type
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\ClarificationsTypes $type
     * @return Clarifications
     */
    public function setType(\SimpleSolution\SimpleTradeBundle\Entity\ClarificationsTypes $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\ClarificationsTypes
     */
    public function getType()
    {
        return $this->type;
    }

}