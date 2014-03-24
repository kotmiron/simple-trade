<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\AuctionsContent
 *
 * @ORM\Table(name="auctions_content")
 * @ORM\Entity
 */
class AuctionsContent
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
     * @var AuctionsTypes
     *
     * @ORM\ManyToOne(targetEntity="AuctionsTypes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="auction_type_id", referencedColumnName="id")
     * })
     */
    private $tradingForm;

    /**
     * @var string $internetAddress
     *
     * @ORM\Column(name="internet_address", type="string", length=256, nullable=false)
     */
    private $internetAddress;

    /**
     * @var string $noticeNumber
     *
     * @ORM\Column(name="notice_number", type="string", length=256, nullable=false)
     */
    private $noticeNumber;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=128, nullable=false)
     */
    private $title;

    /**
     * @var string $noticeLink
     *
     * @ORM\Column(name="notice_link", type="string", length=128, nullable=false)
     */
    private $noticeLink;

    /**
     * @var string $noticePrintform
     *
     * @ORM\Column(name="notice_printform", type="string", length=128, nullable=false)
     */
    private $noticePrintform;

    /**
     * @var string $nomenclature
     *
     * @ORM\Column(name="nomenclature", type="string", length=128, nullable=false)
     */
    private $nomenclature;

    /**
     * @var string $companyName
     *
     * @ORM\Column(name="company_name", type="string", length=128, nullable=false)
     */
    private $companyName;

    /**
     * @var Regions
     *
     * @ORM\ManyToOne(targetEntity="Regions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="region_id", referencedColumnName="id")
     * })
     */
    private $region;

    /**
     * @var string $mail
     *
     * @ORM\Column(name="mail", type="string", length=128, nullable=false)
     */
    private $mail;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=128, nullable=false)
     */
    private $email;

    /**
     * @var string $phones
     *
     * @ORM\Column(name="phones", type="text", nullable=false)
     */
    private $phones;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=128, nullable=false)
     */
    private $name;

    /**
     * @var string $contactName
     *
     * @ORM\Column(name="contact_name", type="string", length=128, nullable=false)
     */
    private $contactName;

    /**
     * @var string $startPrice
     *
     * @ORM\Column(name="start_price", type="float", nullable=true)
     */
    private $startPrice;

    /**
     * @var string $endPrice
     *
     * @ORM\Column(name="end_price", type="float", nullable=true)
     */
    private $endPrice;

    /**
     * @var Currencies
     *
     * @ORM\ManyToOne(targetEntity="Currencies")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="currency_id", referencedColumnName="id")
     * })
     */
    private $currency;

    /**
     * @var string $deliverySize
     *
     * @ORM\Column(name="delivery_size", type="string", length=128, nullable=true)
     */
    private $deliverySize;

    /**
     * @var string $deliveryPlace
     *
     * @ORM\Column(name="delivery_place", type="string", length=128, nullable=true)
     */
    private $deliveryPlace;

    /**
     * @var string $deliveryPeriod
     *
     * @ORM\Column(name="delivery_period", type="string", length=128, nullable=true)
     */
    private $deliveryPeriod;

    /**
     * @var string $info
     *
     * @ORM\Column(name="info", type="text", nullable=true)
     */
    private $info;

    /**
     * @var \DateTime $endOffer
     *
     * @ORM\Column(name="end_offer", type="datetime", nullable=true)
     */
    private $endOffer;

    /**
     * @var \DateTime $endConsideration
     *
     * @ORM\Column(name="end_consideration", type="datetime", nullable=true)
     */
    private $endConsideration;

    /**
     * @var \DateTime $startAuction
     *
     * @ORM\Column(name="start_auction", type="datetime", nullable=true)
     */
    private $startAuction;

    /**
     * @var \DateTime $endAuction
     *
     * @ORM\Column(name="end_auction", type="datetime", nullable=true)
     */
    private $endAuction;

    /**
     * @var string $placeToAcceptOffers
     *
     * @ORM\Column(name="place_to_accept_offers", type="string", length=256, nullable=true)
     */
    private $placeToAcceptOffers;

    /**
     * @var string $datetimeToAcceptOffers
     *
     * @ORM\Column(name="datetime_to_accept_offers", type="datetime", nullable=true)
     */
    private $datetimeToAcceptOffers;

    private $regionId;
    private $currencyId;
    private $tradingFormId;
    private $skills;
    private $attributes;
    private $files;
    private $deletedFiles;

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
     * Set tradingForm
     *
     * @param string $tradingForm
     * @return AuctionsContent
     */
    public function setTradingForm($tradingForm)
    {
        $this->tradingForm = $tradingForm;

        return $this;
    }

    /**
     * Get tradingForm
     *
     * @return string
     */
    public function getTradingForm()
    {
        return $this->tradingForm;
    }

    public function getTradingFormId()
    {
        return $this->tradingForm->getId();
    }

    /**
     * Set internetAddress
     *
     * @param string $internetAddress
     * @return AuctionsContent
     */
    public function setInternetAddress($internetAddress)
    {
        $this->internetAddress = $internetAddress;

        return $this;
    }

    /**
     * Get internetAddress
     *
     * @return string
     */
    public function getInternetAddress()
    {
        return $this->internetAddress;
    }

    /**
     * Set noticeNumber
     *
     * @param string $noticeNumber
     * @return AuctionsContent
     */
    public function setNoticeNumber($noticeNumber)
    {
        $this->noticeNumber = $noticeNumber;

        return $this;
    }

    /**
     * Get noticeNumber
     *
     * @return string
     */
    public function getNoticeNumber()
    {
        return $this->noticeNumber;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return AuctionsContent
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set noticeLink
     *
     * @param string $noticeLink
     * @return AuctionsContent
     */
    public function setNoticeLink($noticeLink)
    {
        $this->noticeLink = $noticeLink;

        return $this;
    }

    /**
     * Get noticeLink
     *
     * @return string
     */
    public function getNoticeLink()
    {
        return $this->noticeLink;
    }

    /**
     * Set noticePrintform
     *
     * @param string $noticePrintform
     * @return AuctionsContent
     */
    public function setNoticePrintform($noticePrintform)
    {
        $this->noticePrintform = $noticePrintform;

        return $this;
    }

    /**
     * Get noticePrintform
     *
     * @return string
     */
    public function getNoticePrintform()
    {
        return $this->noticePrintform;
    }

    /**
     * Set nomenclature
     *
     * @param string $nomenclature
     * @return AuctionsContent
     */
    public function setNomenclature($nomenclature)
    {
        $this->nomenclature = $nomenclature;

        return $this;
    }

    /**
     * Get nomenclature
     *
     * @return string
     */
    public function getNomenclature()
    {
        return $this->nomenclature;
    }

    /**
     * Set companyName
     *
     * @param string $companyName
     * @return AuctionsContent
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * Get companyName
     *
     * @return string
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * Set mail
     *
     * @param string $mail
     * @return AuctionsContent
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail
     *
     * @return string
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return AuctionsContent
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phones
     *
     * @param string $phones
     * @return AuctionsContent
     */
    public function setPhones($phones)
    {
        $this->phones = $phones;

        return $this;
    }

    /**
     * Get phones
     *
     * @return string
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return AuctionsContent
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set contactName
     *
     * @param string $contactName
     * @return AuctionsContent
     */
    public function setContactName($contactName)
    {
        $this->contactName = $contactName;

        return $this;
    }

    /**
     * Get contactName
     *
     * @return string
     */
    public function getContactName()
    {
        return $this->contactName;
    }

    /**
     * Set startPrice
     *
     * @param string $startPrice
     * @return AuctionsContent
     */
    public function setStartPrice($startPrice)
    {
        $this->startPrice = $startPrice;

        return $this;
    }

    /**
     * Get startPrice
     *
     * @return string
     */
    public function getStartPrice()
    {
        return $this->startPrice;
    }

    /**
     * Set endPrice
     *
     * @param string $endPrice
     * @return AuctionsContent
     */
    public function setEndPrice($endPrice)
    {
        $this->endPrice = $endPrice;

        return $this;
    }

    /**
     * Get endPrice
     *
     * @return string
     */
    public function getEndPrice()
    {
        return $this->endPrice;
    }

    /**
     * Set deliverySize
     *
     * @param string $deliverySize
     * @return AuctionsContent
     */
    public function setDeliverySize($deliverySize)
    {
        $this->deliverySize = $deliverySize;

        return $this;
    }

    /**
     * Get deliverySize
     *
     * @return string
     */
    public function getDeliverySize()
    {
        return $this->deliverySize;
    }

    /**
     * Set deliveryPeriod
     *
     * @param string $deliveryPeriod
     * @return AuctionsContent
     */
    public function setDeliveryPeriod($deliveryPeriod)
    {
        $this->deliveryPeriod = $deliveryPeriod;

        return $this;
    }

    /**
     * Get deliveryPeriod
     *
     * @return string
     */
    public function getDeliveryPeriod()
    {
        return $this->deliveryPeriod;
    }

    /**
     * Set info
     *
     * @param string $info
     * @return AuctionsContent
     */
    public function setInfo($info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get info
     *
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Set endOffer
     *
     * @param \DateTime $endOffer
     * @return AuctionsContent
     */
    public function setEndOffer($endOffer)
    {
        $this->endOffer = $endOffer;

        return $this;
    }

    /**
     * Get endOffer
     *
     * @return \DateTime
     */
    public function getEndOffer()
    {
        return $this->endOffer;
    }

    /**
     * Set endConsideration
     *
     * @param \DateTime $endConsideration
     * @return AuctionsContent
     */
    public function setEndConsideration($endConsideration)
    {
        $this->endConsideration = $endConsideration;

        return $this;
    }

    /**
     * Get endConsideration
     *
     * @return \DateTime
     */
    public function getEndConsideration()
    {
        return $this->endConsideration;
    }

    /**
     * Set region
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Regions $region
     * @return AuctionsContent
     */
    public function setRegion(\SimpleSolution\SimpleTradeBundle\Entity\Regions $region = null)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\Regions
     */
    public function getRegion()
    {
        return $this->region;
    }

    public function getRegionId()
    {
        return $this->region->getId();
    }

    /**
     * Set deliveryPlace
     *
     * @param string $deliveryPlace
     * @return AuctionsContent
     */
    public function setDeliveryPlace($deliveryPlace)
    {
        $this->deliveryPlace = $deliveryPlace;

        return $this;
    }

    /**
     * Get deliveryPlace
     *
     * @return string
     */
    public function getDeliveryPlace()
    {
        return $this->deliveryPlace;
    }

    /**
     * Set startAuction
     *
     * @param \DateTime $startAuction
     * @return AuctionsContent
     */
    public function setStartAuction($startAuction)
    {
        $this->startAuction = $startAuction;

        return $this;
    }

    /**
     * Get startAuction
     *
     * @return \DateTime
     */
    public function getStartAuction()
    {
        return $this->startAuction;
    }

    /**
     * Set endAuction
     *
     * @param \DateTime $endAuction
     * @return AuctionsContent
     */
    public function setEndAuction($endAuction)
    {
        $this->endAuction = $endAuction;

        return $this;
    }

    /**
     * Get endAuction
     *
     * @return \DateTime
     */
    public function getEndAuction()
    {
        return $this->endAuction;
    }

    /**
     * Set currency
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Currencies $currency
     * @return AuctionsContent
     */
    public function setCurrency(\SimpleSolution\SimpleTradeBundle\Entity\Currencies $currency = null)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\Currencies
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    public function getCurrencyId()
    {
        return $this->currency->getId();
    }

    /**
     * Set placeToAcceptOffers
     *
     * @param string $placeToAcceptOffers
     * @return AuctionsContent
     */
    public function setPlaceToAcceptOffers($placeToAcceptOffers)
    {
        $this->placeToAcceptOffers = $placeToAcceptOffers;

        return $this;
    }

    /**
     * Get placeToAcceptOffers
     *
     * @return string
     */
    public function getPlaceToAcceptOffers()
    {
        return $this->placeToAcceptOffers;
    }

    /**
     * Set datetimeToAcceptOffers
     *
     * @param \DateTime $datetimeToAcceptOffers
     * @return AuctionsContent
     */
    public function setDatetimeToAcceptOffers($datetimeToAcceptOffers)
    {
        $this->datetimeToAcceptOffers = $datetimeToAcceptOffers;

        return $this;
    }

    /**
     * Get datetimeToAcceptOffers
     *
     * @return \DateTime
     */
    public function getDatetimeToAcceptOffers()
    {
        return $this->datetimeToAcceptOffers;
    }

    public function getAllFieldsAsArray()
    {
        return array(
            'tradingForm' => $this->tradingForm,
            'internetAddress' => $this->internetAddress,
            'noticeNumber' => $this->noticeNumber,
            'title' => $this->title,
            'noticeLink' => $this->noticeLink,
            'noticePrintform' => $this->noticePrintform,
            'nomenclature' => $this->nomenclature,
            'companyName' => $this->companyName,
            'region' => $this->region,
            'mail' => $this->mail,
            'email' => $this->email,
            'phones' => $this->phones,
            'name' => $this->name,
            'contactName' => $this->contactName,
            'startPrice' => $this->startPrice,
            'endPrice' => $this->endPrice,
            'currency' => $this->currency,
            'deliverySize' => $this->deliverySize,
            'deliveryPlace' => $this->deliveryPlace,
            'deliveryPeriod' => $this->deliveryPeriod,
            'info' => $this->info,
            'endOffer' => $this->endOffer,
            'endConsideration' => $this->endConsideration,
            'startAuction' => $this->startAuction,
            'endAuction' => $this->endAuction,
            'placeToAcceptOffers' => $this->placeToAcceptOffers,
            'datetimeToAcceptOffers' => $this->datetimeToAcceptOffers
        );
    }

    public function setSkills($skills)
    {
        $this->skills = $skills;
    }

    public function getSkills()
    {
        return $this->skills;
    }

    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setFiles($files)
    {
        $this->files = $files;
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function setDeletedFiles($deletedFiles)
    {
        $this->deletedFiles = $deletedFiles;
    }

    public function getDeletedFiles()
    {
        return $this->deletedFiles;
    }

}