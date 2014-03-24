<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\NewsContent
 *
 * @ORM\Table(name="news_content")
 * @ORM\Entity
 */
class NewsContent
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
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var string $text
     *
     * @ORM\Column(name="text", type="text", nullable=false)
     */
    private $text;

    public $show;

    public $emailRepeat;

    /**
     * Set id
     *
     * @return NewsContent
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
     * Set title
     *
     * @param string $title
     * @return NewsContent
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return NewsContent
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    public function getShow()
    {
        return $this->show;
    }

    public function getEmailRepeat()
    {
        return $this->emailRepeat;
    }

    public function getAllFieldsAsArray()
    {
        return array(
            'title' => $this->title,
            'text' => $this->text,
        );
    }

}