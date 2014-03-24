<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\RequestsSros
 *
 * @ORM\Table(name="requests_sros")
 * @ORM\Entity
 */
class RequestsSros
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
     * @var Sros
     *
     * @ORM\ManyToOne(targetEntity="Sros")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sro_id", referencedColumnName="id")
     * })
     */
    private $sro;

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
     * @return RequestsSros
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
     * Set comment
     *
     * @param string $comment
     * @return RequestsSros
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
     * Set sro
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Sros $sro
     * @return RequestsSros
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
     * @return RequestsSros
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