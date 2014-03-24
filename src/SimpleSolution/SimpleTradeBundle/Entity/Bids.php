<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\Bids
 *
 * @ORM\Table(name="bids")
 * @ORM\Entity
 */
class Bids
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
     * @var \DateTime $bidTime
     *
     * @ORM\Column(name="bid_time", type="datetime", nullable=false)
     */
    private $bidTime;

    /**
     * @var string $contractBid
     *
     * @ORM\Column(name="current_bid", type="float", nullable=false)
     */
    private $currentBid;

    /**
     * @var string $bestPrice
     *
     * @ORM\Column(name="best_price", type="float")
     */
    private $bestPrice;

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
     * @var Robots
     *
     * @ORM\ManyToOne(targetEntity="Robots")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="robot_id", referencedColumnName="id")
     * })
     */
    private $robot;

    public function __construct()
    {
        $this->bidTime = new \DateTime();
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
     * Set bidTime
     *
     * @param \DateTime $bidTime
     * @return Bids
     */
    public function setBidTime($bidTime)
    {
        $this->bidTime = $bidTime;

        return $this;
    }

    /**
     * Get bidTime
     *
     * @return \DateTime
     */
    public function getBidTime()
    {
        return $this->bidTime;
    }

    /**
     * Get currentBid
     *
     * @return float
     */
    public function getCurrentBid()
    {
        return $this->currentBid;
    }

    /**
     * Set currentBid
     *
     * @param float $currentBid
     * @return Bids
     */
    public function setCurrentBid($currentBid)
    {
        $this->currentBid = $currentBid;

        return $this;
    }

    /**
     * Get bestPrice
     *
     * @return float
     */
    public function getBestPrice()
    {
        return $this->bestPrice;
    }

    /**
     * Set bestPrice
     *
     * @param float $bestPrice
     * @return Bids
     */
    public function setBestPrice($bestPrice)
    {
        $this->bestPrice = $bestPrice;

        return $this;
    }

    /**
     * Set user
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Users $user
     * @return Bids
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
     * Set company
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Companies $company
     * @return Bids
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
     * Set auction
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Auctions $auction
     * @return Bids
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
     * Set robot
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Robots $robot
     * @return Bids
     */
    public function setRobot(\SimpleSolution\SimpleTradeBundle\Entity\Robots $robot = null)
    {
        $this->robot = $robot;

        return $this;
    }

    /**
     * Get robot
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\Robots
     */
    public function getRobot()
    {
        return $this->robot;
    }
}