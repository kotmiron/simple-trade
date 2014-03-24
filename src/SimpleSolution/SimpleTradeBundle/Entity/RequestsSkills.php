<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\RequestsSkills
 *
 * @ORM\Table(name="requests_skills")
 * @ORM\Entity
 */
class RequestsSkills
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
     * @var string $isMain
     *
     * @ORM\Column(name="is_main", type="boolean")
     */
    private $isMain;

    /**
     * @var string $isDangerous
     *
     * @ORM\Column(name="is_dangerous", type="boolean")
     */
    private $isDangerous;

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
     * @return RequestsSkills
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
     * Set isMain
     *
     * @param boolean $isMain
     * @return RequestsSkills
     */
    public function setIsMain($isMain)
    {
        $this->isMain = $isMain;

        return $this;
    }

    /**
     * Get isMain
     *
     * @return boolean
     */
    public function getIsMain()
    {
        return $this->isMain;
    }

    /**
     * Set isDangerous
     *
     * @param boolean $isDangerous
     * @return RequestsSkills
     */
    public function setIsDangerous($isDangerous)
    {
        $this->isDangerous = $isDangerous;

        return $this;
    }

    /**
     * Get isDangerous
     *
     * @return boolean
     */
    public function getIsDangerous()
    {
        return $this->isDangerous;
    }

    /**
     * Set skill
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Skills $skill
     * @return RequestsSkills
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
     * Set request
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Requests $request
     * @return RequestsSkills
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