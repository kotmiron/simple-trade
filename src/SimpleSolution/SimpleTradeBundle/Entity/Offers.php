<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\Offers
 *
 * @ORM\Table(name="offers")
 * @ORM\Entity
 */
class Offers
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
     * @var OffersContent
     *
     * @ORM\ManyToOne(targetEntity="OffersContent")
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
     * @var Auctions
     *
     * @ORM\ManyToOne(targetEntity="Auctions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="auction_id", referencedColumnName="id")
     * })
     */
    private $auction;

    /**
     * @var OffersStatuses
     *
     * @ORM\ManyToOne(targetEntity="OffersStatuses")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     * })
     */
    private $status;

    /**
     * Set id
     *
     * @return Offers
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
     * @return Offers
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
     * @param SimpleSolution\SimpleTradeBundle\Entity\OffersContent $content
     * @return Offers
     */
    public function setContent(\SimpleSolution\SimpleTradeBundle\Entity\OffersContent $content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\OffersContent
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set company
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Companies $company
     * @return Offers
     */
    public function setCompany(\SimpleSolution\SimpleTradeBundle\Entity\Companies $company= null)
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
     * Set auction
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Auctions $auction
     * @return OffersContent
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
     * Set status
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\OffersStatuses $status
     * @return Offers
     */
    public function setStatus(\SimpleSolution\SimpleTradeBundle\Entity\OffersStatuses $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\OffersStatuses
     */
    public function getStatus()
    {
        return $this->status;
    }

}