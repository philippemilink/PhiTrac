<?php

namespace PhiTrac\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Project
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="PhiTrac\ProjectBundle\Entity\ProjectRepository")
 * @ORM\HasLifecycleCallbacks
 *
 * @UniqueEntity("name")
 * @Assert\Callback(methods={"checkWebsite"})
 */
class Project
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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;
    
    /**
     * @Gedmo\Slug(fields={"name"})
     *
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\OneToOne(targetEntity="PhiTrac\ProjectBundle\Entity\Image", cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    private $icon;

    /**
     * @var string
     *
     * @ORM\Column(name="website", type="text", nullable=true)
     * @Assert\Url()
     */
    private $website;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="todo", type="integer")
     */
    private $todo;
    
    /**
     * @ORM\OneToMany(targetEntity="PhiTrac\ProjectBundle\Entity\Item", mappedBy="project", cascade={"persist", "remove"})
     */
    private $items;
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
        $this->todo = 0;
        $this->tempIconPathName = null;
    }
    
    public function checkWebsite(ExecutionContextInterface $context)
    {
        if ($this->website==null) {
            return;
        }
        
        if (!preg_match("#^http://|https://#", $this->website)) { // website doesn't begin by 'http://' or 'https://'
            $this->website = "http://" . $this->website;
        }
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
     * Set name
     *
     * @param string $name
     * @return Project
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set icon
     *
     * @param string $icon
     * @return Project
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon
     *
     * @return string 
     */
    public function getIcon()
    {
        return $this->icon;
    }
    
    /**
     * Set website
     *
     * @param string $website
     * @return Project
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string 
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Project
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set todo
     *
     * @param integer $todo
     * @return Project
     */
    public function setTodo($todo)
    {
        $this->todo = $todo;

        return $this;
    }

    /**
     * Get todo
     *
     * @return integer 
     */
    public function getTodo()
    {
        return $this->todo;
    }

    /**
     * Add items
     *
     * @param \PhiTrac\ProjectBundle\Entity\Item $items
     * @return Project
     */
    public function addItem(\PhiTrac\ProjectBundle\Entity\Item $items)
    {
        $this->items[] = $items;

        return $this;
    }

    /**
     * Remove items
     *
     * @param \PhiTrac\ProjectBundle\Entity\Item $items
     */
    public function removeItem(\PhiTrac\ProjectBundle\Entity\Item $items)
    {
        $this->items->removeElement($items);
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getItems()
    {
        return $this->items;
    }
}
