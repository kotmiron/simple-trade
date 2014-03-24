<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\SrosCompanies
 *
 * @ORM\Table(name="sros_companies")
 * @ORM\Entity
 */
class SrosCompanies
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
     * @var string $comment
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @var CompaniesStatuses
     *
     * @ORM\ManyToOne(targetEntity="CompaniesStatuses")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     * })
     */
    protected $status;

    /**
     * @var Sros
     *
     * @ORM\ManyToOne(targetEntity="Sros")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sro_id", referencedColumnName="id")
     * })
     */
    private $sro;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return SrosCompanies
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set status
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\CompaniesStatuses $status
     * @return SrosCompanies
     */
    public function setStatus(\SimpleSolution\SimpleTradeBundle\Entity\CompaniesStatuses $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\CompaniesStatuses
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set sro
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Sros $sro
     * @return SrosCompanies
     */
    public function setSro(\SimpleSolution\SimpleTradeBundle\Entity\Sros $sro = null)
    {
        $this->sro = $sro;

        return $this;
    }

    /**
     * Get sro
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\Sros
     */
    public function getSro()
    {
        return $this->sro;
    }

    /**
     * Set company
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Companies $company
     * @return SrosCompanies
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

}