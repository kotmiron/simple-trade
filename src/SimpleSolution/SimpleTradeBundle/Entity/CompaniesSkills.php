<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\CompaniesSkills
 *
 * @ORM\Table(name="companies_skills")
 * @ORM\Entity
 */
class CompaniesSkills
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
     * @var Companies
     *
     * @ORM\ManyToOne(targetEntity="Companies")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     * })
     */
    private $company;

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
     * @var string $isDangerous
     *
     * @ORM\Column(name="is_dangerous", type="boolean")
     */
    private $isDangerous;


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
     * Set company
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Companies $company
     * @return CompaniesSkills
     */
    public function setCompany(\SimpleSolution\SimpleTradeBundle\Entity\Companies $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\Companies
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set skill
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Skills $skill
     * @return CompaniesSkills
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
     * @return CompaniesSkills
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

    /**
     * Set isDangerous
     *
     * @param boolean $isDangerous
     * @return CompaniesSkills
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

}