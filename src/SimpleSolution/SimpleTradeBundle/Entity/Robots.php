<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\Robots
 *
 * @ORM\Table(name="robots")
 * @ORM\Entity
 */
class Robots
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
     * @var string $isActive
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=false)
     */
    private $isActive;

    /**
     * @var RobotsContent
     *
     * @ORM\ManyToOne(targetEntity="RobotsContent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     * })
     */
    private $content;

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
     * Set id
     *
     * @return Robots
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
     * @return Robots
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
     * Set isActive
     *
     * @param boolean $isActive
     * @return Robots
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set content
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\RobotsContent $content
     * @return Robots
     */
    public function setContent(\SimpleSolution\SimpleTradeBundle\Entity\RobotsContent $content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\RobotsContent
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set user
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Users $user
     * @return Robots
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
     * @return Robots
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
     * @return Robots
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