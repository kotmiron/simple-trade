<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\DocumentsLinks
 *
 * @ORM\Table(name="documents_links")
 * @ORM\Entity
 */
class DocumentsLinks
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
     * @var string $owner
     *
     * @ORM\Column(name="owner", type="string", length=128, nullable=false)
     */
    private $owner;

    /**
     * @var integer $ownerId
     *
     * @ORM\Column(name="owner_id", type="integer", nullable=false)
     */
    private $ownerId;

    /**
     * @var Documents
     *
     * @ORM\ManyToOne(targetEntity="Documents")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="documents_id", referencedColumnName="id")
     * })
     */
    private $document;


    /**
     * Set id
     *
     * @return DocumentsLinks
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
     * Set owner
     *
     * @param string $owner
     * @return DocumentsLinks
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return string
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set ownerId
     *
     * @param integer $ownerId
     * @return DocumentsLinks
     */
    public function setOwnerId($ownerId)
    {
        $this->ownerId = $ownerId;

        return $this;
    }

    /**
     * Get ownerId
     *
     * @return integer
     */
    public function getOwnerId()
    {
        return $this->ownerId;
    }

    /**
     * Set document
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Documents $document
     * @return DocumentsLinks
     */
    public function setDocument(\SimpleSolution\SimpleTradeBundle\Entity\Documents $document = null)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get document
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\Documents
     */
    public function getDocument()
    {
        return $this->document;
    }



}
