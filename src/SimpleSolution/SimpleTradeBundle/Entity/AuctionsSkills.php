<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\AuctionsSkills
 *
 * @ORM\Table(name="auctions_skills")
 * @ORM\Entity
 */
class AuctionsSkills
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
     * @var Auctions
     *
     * @ORM\ManyToOne(targetEntity="Auctions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="auction_id", referencedColumnName="id")
     * })
     */
    private $auction;

    /**
     * @var Skills
     *
     * @ORM\ManyToOne(targetEntity="Skills")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="skill_id", referencedColumnName="id")
     * })
     */
    private $skill;

    /**
     * @var string $isMain
     *
     * @ORM\Column(name="is_main", type="boolean")
     */
    private $isMain;


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
     * Set auction
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Auctions $auction
     * @return AuctionsSkills
     */
    public function setAuction(\SimpleSolution\SimpleTradeBundle\Entity\Auctions $auction = null)
    {
        $this->auction = $auction;

        return $this;
    }

    /**
     * Get auction
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\Auctions
     */
    public function getAuction()
    {
        return $this->auction;
    }

    /**
     * Set skill
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Skills $skill
     * @return AuctionsSkills
     */
    public function setSkill(\SimpleSolution\SimpleTradeBundle\Entity\Skills $skill = null)
    {
        $this->skill = $skill;

        return $this;
    }

    /**
     * Get skill
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\Skills
     */
    public function getSkill()
    {
        return $this->skill;
    }

    /**
     * Set isMain
     *
     * @param string $isMain
     * @return AuctionsSkills
     */
    public function setIsMain($isMain)
    {
        $this->isMain = $isMain;

        return $this;
    }

    /**
     * Get isMain
     *
     * @return string
     */
    public function getIsMain()
    {
        return $this->isMain;
    }
}