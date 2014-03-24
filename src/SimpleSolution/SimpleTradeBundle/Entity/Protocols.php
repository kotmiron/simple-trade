<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\Protocols
 *
 * @ORM\Table(name="protocols")
 * @ORM\Entity
 */
class Protocols
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
     * @var ProtocolsContent
     *
     * @ORM\ManyToOne(targetEntity="ProtocolsContent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     * })
     */
    private $content;

    /**
     * @var ProtocolsTypes
     *
     * @ORM\ManyToOne(targetEntity="ProtocolsTypes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     * })
     */
    protected $type;

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
     * @var Auctions
     *
     * @ORM\ManyToOne(targetEntity="Auctions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="auction_id", referencedColumnName="id")
     * })
     */
    private $auction;

    /**
     * Set id
     *
     * @return Protocols
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
     * @return Protocols
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
     * @param SimpleSolution\SimpleTradeBundle\Entity\ProtocolsContent $content
     * @return Protocols
     */
    public function setContent(\SimpleSolution\SimpleTradeBundle\Entity\ProtocolsContent $content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\ProtocolsContent
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set type
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\ProtocolsTypes $type
     * @return Protocols
     */
    public function setType(\SimpleSolution\SimpleTradeBundle\Entity\ProtocolsTypes $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\ProtocolsTypes
     */
    public function getType()
    {
        return $this->type;
    }


    /**
     * Set user
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Users $user
     * @return Protocols
     */
    public function setUser(\SimpleSolution\SimpleTradeBundle\Entity\Users $user= null)
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
     * Set auction
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Auctions $auction
     * @return Protocols
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

}