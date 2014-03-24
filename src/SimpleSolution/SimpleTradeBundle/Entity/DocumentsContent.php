<?php

namespace SimpleSolution\SimpleTradeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SimpleSolution\SimpleTradeBundle\Entity\DocumentsContent
 *
 * @ORM\Table(name="documents_content")
 * @ORM\Entity
 */
class DocumentsContent
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
     * @var string $filename
     *
     * @ORM\Column(name="filename", type="string", length=128, nullable=false)
     */
    private $filename;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;


    /**
     * Set id
     *
     * @return DocumentsContent
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
     * Set filename
     *
     * @param string $filename
     * @return Documents
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return DocumentsContent
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

    //-------------------------------------

    /*
     * @Assert\File(maxSize="6000000")
     */
    public $file;

    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getAbsolutePath()
    {
        return null === $this->filename
            ? null
            : $this->getUploadRootDir().'/'.$this->filename;
    }

    public function getWebPath()
    {
        return null === $this->filename
            ? null
            : $this->getUploadDir().'/'.$this->filename;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/documents';
    }



    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->file)
        {
            return;
        }

        $extension = pathinfo($this->file->getClientOriginalName(), PATHINFO_EXTENSION);

        $filename = sha1(uniqid(mt_rand(), true)).'.'.$extension;
        $this->file->move(
            $this->getUploadRootDir(), $filename
        );

        // set the path property to the filename where you've saved the file
        $this->filename = $filename;

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }




}