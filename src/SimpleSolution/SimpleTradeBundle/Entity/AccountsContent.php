<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\AccountsContent
 *
 * @ORM\Table(name="accounts_content")
 * @ORM\Entity
 */
class AccountsContent
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
     * @var string $account
     *
     * @ORM\Column(name="account", type="float", nullable=false)
     */
    private $account;

    /**
     * @var string $changes
     *
     * @ORM\Column(name="changes", type="float", nullable=false)
     */
    private $changes;

    /**
     * @var string $comment
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @var Tariff
     *
     * @ORM\ManyToOne(targetEntity="Tariffs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tariff_id", referencedColumnName="id")
     * })
     */
    private $tariff;

    public $tariffId;


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
     * Set account
     *
     * @param float $account
     * @return AccountsContent
     */
    public function setAccount($account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return float
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Set changes
     *
     * @param float $changes
     * @return AccountsContent
     */
    public function setChanges($changes)
    {
        $this->changes = $changes;

        return $this;
    }

    /**
     * Get changes
     *
     * @return float
     */
    public function getChanges()
    {
        return $this->changes;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return AccountsContent
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set tariff
     *
     * @param SimpleSolution\SimpleTradeBundle\Entity\Tariffs $tariff
     * @return AccountsContent
     */
    public function setTariff(\SimpleSolution\SimpleTradeBundle\Entity\Tariffs $tariff = null)
    {
        $this->tariff = $tariff;

        return $this;
    }

    /**
     * Get tariff
     *
     * @return SimpleSolution\SimpleTradeBundle\Entity\Tariffs
     */
    public function getTariff()
    {
        return $this->tariff;
    }

    public function getTariffId()
    {
        return $this->tariffId;
    }
}