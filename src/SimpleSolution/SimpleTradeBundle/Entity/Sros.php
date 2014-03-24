<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\Sros
 *
 * @ORM\Table(name="sros")
 * @ORM\Entity
 */
class Sros
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
     * @var SrosContent
     *
     * @ORM\ManyToOne(targetEntity="SrosContent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     * })
     */
    private $content;

    /**
     * @var SrosTypes
     *
     * @ORM\ManyToOne(targetEntity="SrosTypes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     * })
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity="SrosCompanies", mappedBy="sro")
     */
    private $companies;

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
     * @return Sros
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
     * @param SimpleSolution\SimpleTradeBundle\Entity\SrosContent $content
     * @return Sros
     */
    public function setContent(\SimpleSolution\SimpleTradeBundle\Entity\SrosContent $content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\SrosContent
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set type
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\SrosTypes $type
     * @return Sros
     */
    public function setType(\SimpleSolution\SimpleTradeBundle\Entity\SrosTypes $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\SrosTypes
     */
    public function getType()
    {
        return $this->type;
    }
}