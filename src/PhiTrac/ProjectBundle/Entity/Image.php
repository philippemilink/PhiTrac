<?php

namespace PhiTrac\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Image
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="PhiTrac\ProjectBundle\Entity\ImageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Image
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="extension", type="string", length=10)
     */
    private $extension;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255)
     */
    private $alt;
    
    /**
     * @Assert\Image(maxWidth=100, maxHeight=100)
    */
    private $file;
    
    private $tempIconPathName;
    
    
    public function __construct()
    {
        $this->file = null;
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
     * Set extension
     *
     * @param string $extension
     * @return Image
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension
     *
     * @return string 
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set alt
     *
     * @param string $alt
     * @return Image
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }
    
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }
    
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Get alt
     *
     * @return string 
     */
    public function getAlt()
    {
        return $this->alt;
    }
    
    /**
     * @ORM\PrePersist()
    */
    public function preUpload()
    {
        if ($this->file===null) {
            return;
        }
        
        $this->extension = $this->file->guessExtension();
        $this->alt = $this->file->getClientOriginalName();
    }
    
    /**
     * @ORM\PostPersist()
    */
    public function upload()
    {
        if ($this->file===null) {
            return;
        }
        
        $dir = __DIR__ . "/../../../../web/uploads/img";
        $name = $this->id . "." . $this->extension;
        $pathName = $dir . "/" . $name;
        
        if (file_exists($pathName)) {
            unlink($pathName);
        }
        
        $this->file->move($dir, $name);
    }
    
    /**
     * @ORM\PreRemove()
    */
    public function preRemove()
    {
        $this->tempIconPathName = __DIR__ . "/../../../../web/uploads/img/" . $this->id . "." . $this->extension;
    }
    
    /**
     * @ORM\PostRemove()
    */
    public function postRemove()
    {
        unlink($this->tempIconPathName); 
    }

}
