<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\ProtocolsContent
 *
 * @ORM\Table(name="protocols_content")
 * @ORM\Entity
 */
class ProtocolsContent
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
     * @var string $placeView
     *
     * @ORM\Column(name="place_view", type="string", length=256)
     */
    private $placeView;

    /**
     * @var \DateTime $datetimeStartView
     *
     * @ORM\Column(name="datetime_start_view", type="datetime")
     */
    private $datetimeStartView;

    /**
     * @var \DateTime $datetimeEndView
     *
     * @ORM\Column(name="datetime_end_view", type="datetime")
     */
    private $datetimeEndView;

    /**
     * @var string $fullName1
     *
     * @ORM\Column(name="full_name_1", type="string", length=128)
     */
    private $fullName1;

    /**
     * @var string $fullName2
     *
     * @ORM\Column(name="full_name_2", type="string", length=128)
     */
    private $fullName2;

    /**
     * @var string $fullName3
     *
     * @ORM\Column(name="full_name_3", type="string", length=128)
     */
    private $fullName3;

    /**
     * @var string $position1
     *
     * @ORM\Column(name="position_1", type="string", length=64)
     */
    private $position1;

    /**
     * @var string $position2
     *
     * @ORM\Column(name="position_2", type="string", length=64)
     */
    private $position2;

    /**
     * @var string $position3
     *
     * @ORM\Column(name="position_3", type="string", length=64)
     */
    private $position3;

    /**
     * @ORM\OneToMany(targetEntity="ProtocolsCompanyValues", mappedBy="protocolContent")
     */
    protected $companyValues;

    public function __construct()
    {
        $this->companyValues = new ArrayCollection();
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
     * Set placeView
     *
     * @param string $placeView
     * @return ProtocolsContent
     */
    public function setPlaceView($placeView)
    {
        $this->placeView = $placeView;

        return $this;
    }

    /**
     * Get placeView
     *
     * @return string
     */
    public function getPlaceView()
    {
        return $this->placeView;
    }

    /**
     * Set datetimeStartView
     *
     * @param \DateTime $datetimeStartView
     * @return ProtocolsContent
     */
    public function setDatetimeStartView($datetimeStartView)
    {
        $this->datetimeStartView = $datetimeStartView;

        return $this;
    }

    /**
     * Get datetimeStartView
     *
     * @return \DateTime
     */
    public function getDatetimeStartView()
    {
        return $this->datetimeStartView;
    }

    /**
     * Set datetimeEndView
     *
     * @param \DateTime $datetimeEndView
     * @return ProtocolsContent
     */
    public function setDatetimeEndView($datetimeEndView)
    {
        $this->datetimeEndView = $datetimeEndView;

        return $this;
    }

    /**
     * Get datetimeEndView
     *
     * @return \DateTime
     */
    public function getDatetimeEndView()
    {
        return $this->datetimeEndView;
    }

    /**
     * Set fullName1
     *
     * @param string $fullName1
     * @return ProtocolsContent
     */
    public function setFullName1($fullName1)
    {
        $this->fullName1 = $fullName1;

        return $this;
    }

    /**
     * Get fullName1
     *
     * @return string
     */
    public function getFullName1()
    {
        return $this->fullName1;
    }

    /**
     * Set fullName2
     *
     * @param string $fullName2
     * @return ProtocolsContent
     */
    public function setFullName2($fullName2)
    {
        $this->fullName2 = $fullName2;

        return $this;
    }

    /**
     * Get fullName2
     *
     * @return string
     */
    public function getFullName2()
    {
        return $this->fullName2;
    }

    /**
     * Set fullName3
     *
     * @param string $fullName3
     * @return ProtocolsContent
     */
    public function setFullName3($fullName3)
    {
        $this->fullName3 = $fullName3;

        return $this;
    }

    /**
     * Get fullName3
     *
     * @return string
     */
    public function getFullName3()
    {
        return $this->fullName3;
    }

    /**
     * Set position1
     *
     * @param string $position1
     * @return ProtocolsContent
     */
    public function setPosition1($position1)
    {
        $this->position1 = $position1;

        return $this;
    }

    /**
     * Get position1
     *
     * @return string
     */
    public function getPosition1()
    {
        return $this->position1;
    }

    /**
     * Set position2
     *
     * @param string $position2
     * @return ProtocolsContent
     */
    public function setPosition2($position2)
    {
        $this->position2 = $position2;

        return $this;
    }

    /**
     * Get position2
     *
     * @return string
     */
    public function getPosition2()
    {
        return $this->position2;
    }

    /**
     * Set position3
     *
     * @param string $position3
     * @return ProtocolsContent
     */
    public function setPosition3($position3)
    {
        $this->position3 = $position3;

        return $this;
    }

    /**
     * Get position3
     *
     * @return string
     */
    public function getPosition3()
    {
        return $this->position3;
    }

    /**
     * Add companyValues
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\ProtocolsCompanyValues $companyValues
     * @return ProtocolsContent
     */
    public function addCompanyValue(\SimpleSolution\SimpleTradeBundle\Entity\ProtocolsCompanyValues $companyValues)
    {
        $this->companyValues[] = $companyValues;

        return $this;
    }

    /**
     * Remove companyValues
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\ProtocolsCompanyValues $companyValues
     */
    public function removeCompanyValue(\SimpleSolution\SimpleTradeBundle\Entity\ProtocolsCompanyValues $companyValues)
    {
        $this->companyValues->removeElement($companyValues);
    }

    /**
     * Get companyValues
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getCompanyValues()
    {
        return $this->companyValues;
    }

    /**
     * Set companyValues
     *
     * @return ProtocolsContent
     */
    public function setCompanyValues(array $companyValues)
    {
        $this->companyValues = $companyValues;

        return $this;
    }
}