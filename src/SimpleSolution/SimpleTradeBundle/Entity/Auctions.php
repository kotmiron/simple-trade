<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\Auctions
 *
 * @ORM\Table(name="auctions")
 * @ORM\Entity
 */
class Auctions
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
     * @var AuctionsContent
     *
     * @ORM\ManyToOne(targetEntity="AuctionsContent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     * })
     */
    private $content;

    /**
     * @var AuctionsStatuses
     *
     * @ORM\ManyToOne(targetEntity="AuctionsStatuses")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     * })
     */
    protected $status;

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
     * @ORM\OneToMany(targetEntity="AuctionsSkills", mappedBy="auction")
     */
    private $auctionsSkills;
    public $tradingForm;
    public $internetAddress;
    public $noticeNumber;
    public $title;
    public $noticeLink;
    public $noticePrintform;
    public $nomenclature;
    public $companyName;
    public $region;
    public $mail;
    public $email;
    public $phones;
    public $name;
    public $contactName;
    public $startPrice;
    public $endPrice;
    public $currency;
    public $size;
    public $place;
    public $period;
    public $info;
    public $endOffer;
    public $endConsideration;
    public $start;

    /**
     * Set id
     *
     * @return Auctions
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
     * @return Auctions
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
     * @param SimpleSolution\SimpleTradeBundle\Entity\AuctionsContent $content
     * @return Auctions
     */
    public function setContent(\SimpleSolution\SimpleTradeBundle\Entity\AuctionsContent $content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\AuctionsContent
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set status
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\AuctionsStatuses $status
     * @return Auctions
     */
    public function setStatus(\SimpleSolution\SimpleTradeBundle\Entity\AuctionsStatuses $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\AuctionsStatuses
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set user
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Users $user
     * @return Auctions
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
     * @return Auctions
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

    public function getAuctionsSkills()
    {
        $out = array();
        foreach( $this->auctionsSkills as $skill ) {
            $out[] = $skill->getSkill()->getTitle();
        }
        return $out;
    }

    public function getTradingForm()
    {
        return $this->tradingForm;
    }

    public function getInternetAddress()
    {
        return $this->internetAddress;
    }

    public function getNoticeNumber()
    {
        return $this->noticeNumber;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getNoticeLink()
    {
        return $this->noticeLink;
    }

    public function getNoticePrintform()
    {
        return $this->noticePrintform;
    }

    public function getNomenclature()
    {
        return $this->nomenclature;
    }

    public function getCompanyName()
    {
        return $this->companyName;
    }

    public function getMail()
    {
        return $this->mail;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPhones()
    {
        return $this->phones;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getContactName()
    {
        return $this->contactName;
    }

    public function getStartPrice()
    {
        return $this->startPrice;
    }

    public function getEndPrice()
    {
        return $this->endPrice;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getPlace()
    {
        return $this->place;
    }

    public function getPeriod()
    {
        return $this->period;
    }

    public function getInfo()
    {
        return $this->info;
    }

    public function getEndOffer()
    {
        return $this->endOffer;
    }

    public function getEndConsideration()
    {
        return $this->endConsideration;
    }

    public function getRegion()
    {
        return $this->region;
    }

    public function getStart()
    {
        return $this->start;
    }

}