<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\RequestsComplaints
 *
 * @ORM\Table(name="requests_complaints")
 * @ORM\Entity
 */
class RequestsComplaints
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
     * @var string $text
     *
     * @ORM\Column(name="text", type="text", nullable=true)
     */
    private $text;

    /**
     * @var string $comment
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

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
     * @var Requests
     *
     * @ORM\ManyToOne(targetEntity="Requests")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="request_id", referencedColumnName="id")
     * })
     */
    private $request;

    /**
     * Set id
     *
     * @return RequestsComplaints
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
     * Set text
     *
     * @param string $text
     * @return RequestsComplaints
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return RequestsComplaints
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
     * Set company
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Companies $company
     * @return RequestsComplaints
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
     * Set sro
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Sros $sro
     * @return RequestsComplaints
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
     * Set request
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Requests $request
     * @return RequestsComplaints
     */
    public function setRequest(\SimpleSolution\SimpleTradeBundle\Entity\Requests $request = null)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Get request
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\Requests
     */
    public function getRequest()
    {
        return $this->request;
    }
}