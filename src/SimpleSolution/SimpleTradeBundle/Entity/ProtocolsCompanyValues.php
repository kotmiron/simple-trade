<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\ProtocolsTypes
 *
 * @ORM\Table(name="protocols_company_values")
 * @ORM\Entity
 */
class ProtocolsCompanyValues
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
     * @var Companies
     *
     * @ORM\ManyToOne(targetEntity="Companies")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     * })
     */
    private $company;

    /**
     * @var string $name
     *
     * @ORM\Column(name="value", type="integer")
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity="ProtocolsContent", inversedBy="companyValues")
     * @ORM\JoinColumn(name="protocol_content_id", referencedColumnName="id")
     */
    protected $protocolContent;


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
     * Set value
     *
     * @param integer $value
     * @return ProtocolsCompanyValues
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set company
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Companies $company
     * @return ProtocolsCompanyValues
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
     * Set protocolContent
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\ProtocolsContent $protocolContent
     * @return ProtocolsCompanyValues
     */
    public function setProtocolContent(\SimpleSolution\SimpleTradeBundle\Entity\ProtocolsContent $protocolContent = null)
    {
        $this->protocolContent = $protocolContent;

        return $this;
    }

    /**
     * Get protocolContent
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\ProtocolsContent
     */
    public function getProtocolContent()
    {
        return $this->protocolContent;
    }
}