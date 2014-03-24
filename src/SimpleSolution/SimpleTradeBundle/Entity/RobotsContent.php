<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\RobotsContent
 *
 * @ORM\Table(name="robots_content")
 * @ORM\Entity
 */
class RobotsContent
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
     * @var string $bidSize
     *
     * @ORM\Column(name="bid_size", type="float", nullable=false)
     */
    private $bidSize;

    /**
     * @var string $deadline
     *
     * @ORM\Column(name="deadline", type="float", nullable=false)
     */
    private $deadline;

    /**
     * @var \DateTime $bidTime
     *
     * @ORM\Column(name="bid_time", type="datetime", nullable=true)
     */
    private $bidTime;


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
     * Set bidSize
     *
     * @param float $bidSize
     * @return RobotsContent
     */
    public function setBidSize($bidSize)
    {
        $this->bidSize = $bidSize;

        return $this;
    }

    /**
     * Get bidSize
     *
     * @return float
     */
    public function getBidSize()
    {
        return $this->bidSize;
    }

    /**
     * Set deadline
     *
     * @param float $deadline
     * @return RobotsContent
     */
    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;

        return $this;
    }

    /**
     * Get deadline
     *
     * @return float
     */
    public function getDeadline()
    {
        return $this->deadline;
    }

    /**
     * Set bidTime
     *
     * @param \DateTime $bidTime
     * @return RobotsContent
     */
    public function setBidTime($bidTime)
    {
        $this->bidTime = $bidTime;

        return $this;
    }

    /**
     * Get bidTime
     *
     * @return \DateTime
     */
    public function getBidTime()
    {
        return $this->bidTime;
    }
}