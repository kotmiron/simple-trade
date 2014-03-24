<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\News
 *
 * @ORM\Table(name="news")
 * @ORM\Entity
 */
class News
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
     * @var integer $permission
     *
     * @ORM\Column(name="permission", type="integer", nullable=false)
     */
    private $permission;

    /**
     * @var NewsStatuses
     *
     * @ORM\ManyToOne(targetEntity="NewsStatuses")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     * })
     */
    protected $status;

    /**
     * @var NewsContent
     *
     * @ORM\ManyToOne(targetEntity="NewsContent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     * })
     */
    private $content;

    public $title;

    public $text;

    /**
     * Set id
     *
     * @return News
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
     * @return News
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
     * Set permission
     *
     * @return News
     */
    public function setPermission($permission)
    {
        $this->permission = $permission;

        return $this;
    }

    /**
     * Get permission
     *
     * @return integer
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * Set status
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\NewsStatuses $status
     * @return News
     */
    public function setStatus(\SimpleSolution\SimpleTradeBundle\Entity\NewsStatuses $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\NewsStatuses
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set content
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\NewsContent $content
     * @return News
     */
    public function setContent(\SimpleSolution\SimpleTradeBundle\Entity\NewsContent $content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\NewsContent
     */
    public function getContent()
    {
        return $this->content;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getText()
    {
        return $this->text;
    }

}