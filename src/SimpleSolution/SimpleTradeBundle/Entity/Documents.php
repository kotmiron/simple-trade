<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\Documents
 *
 * @ORM\Table(name="documents")
 * @ORM\Entity
 */
class Documents
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
     * @var string $isActive
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var DocumentsContent
     *
     * @ORM\ManyToOne(targetEntity="DocumentsContent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     * })
     */
    private $content;

    /**
     * @ORM\OneToMany(targetEntity="DocumentsLinks", mappedBy="document")
     */
    private $links;

    public function __construct()
    {
        $this->links = new ArrayCollection();
    }

    /**
     * Set id
     *
     * @return Documents
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
     * @return Documents
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
     * Set isActive
     *
     * @param string $isActive
     * @return Documents
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return string
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set content
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\DocumentsContent $content
     * @return Documents
     */
    public function setContent(\SimpleSolution\SimpleTradeBundle\Entity\DocumentsContent $content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\DocumentsContent
     */
    public function getContent()
    {
        return $this->content;
    }

}