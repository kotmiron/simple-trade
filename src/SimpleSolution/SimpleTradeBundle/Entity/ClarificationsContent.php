<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\ClarificationsContent
 *
 * @ORM\Table(name="clarifications_content")
 * @ORM\Entity
 */
class ClarificationsContent
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
     * @var string $subject
     *
     * @ORM\Column(name="subject", type="string", length=256, nullable=false)
     */
    private $subject;

    /**
     * @var string $body
     *
     * @ORM\Column(name="body", type="string", length=65535, nullable=false)
     */
    private $body;

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
     * Set subject
     *
     * @param string $subject
     * @return ClarificationsContent
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set body
     *
     * @param string $body
     * @return ClarificationsContent
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return body
     */
    public function getBody()
    {
        return $this->body;
    }

}