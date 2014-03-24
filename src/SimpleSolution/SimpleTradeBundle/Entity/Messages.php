<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\Messages
 *
 * @ORM\Table(name="messages")
 * @ORM\Entity
 */
class Messages
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
     * @var MessagesContent
     *
     * @ORM\ManyToOne(targetEntity="MessagesContent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     * })
     */
    private $content;

    /**
     * @var $fromUser
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="from_user", referencedColumnName="id")
     * })
     */
    private $fromUser;

    /**
     * @var $toUser
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="to_user", referencedColumnName="id")
     * })
     */
    private $toUser;

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
     * @return Messages
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
     * @param SimpleSolution\SimpleTradeBundle\Entity\MessagesContent $content
     * @return Messages
     */
    public function setContent(\SimpleSolution\SimpleTradeBundle\Entity\MessagesContent $content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\MessagesContent
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set from user
     *
     * @param \SimpleSolution\SimpleTradeBundle\Entity\Users $fromUser
     * @return MessagesVersions
     */
    public function setFromUser(\SimpleSolution\SimpleTradeBundle\Entity\Users $fromUser)
    {
        $this->fromUser = $fromUser;

        return $this;
    }

    /**
     * Get from user
     *
     * @return \SimpleSolution\SimpleTradeBundle\Entity\Users
     */
    public function getFromUser()
    {
        return $this->fromUser;
    }

    /**
     * Set to user
     *
     * @param \SimpleSolution\SimpleTradeBundle\Entity\Users $fromUser
     * @return MessagesVersions
     */
    public function setToUser(\SimpleSolution\SimpleTradeBundle\Entity\Users $toUser)
    {
        $this->toUser = $toUser;

        return $this;
    }

    /**
     * Get to user
     *
     * @return \SimpleSolution\SimpleTradeBundle\Entity\Users
     */
    public function getToUser()
    {
        return $this->toUser;
    }

}