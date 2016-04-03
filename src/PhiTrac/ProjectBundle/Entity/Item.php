<?php

namespace PhiTrac\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use PhiTrac\UserBundle\Entity\User;

/**
 * Item
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="PhiTrac\ProjectBundle\Entity\ItemRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Item
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=100)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=100)
     */
    private $status;
    
    /**
     * @var string
     *
     * @ORM\Column(name="priority", type="string", length=100)
     */
    private $priority;
    
    /**
     * @Gedmo\Slug(fields={"title"})
     *
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity="PhiTrac\UserBundle\Entity\User")
     */
    private $creator;
    
    /**
     * @ORM\ManyToOne(targetEntity="PhiTrac\ProjectBundle\Entity\Project", inversedBy="items", cascade={"persist"})
     *
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;
    

    private $previousStatus;
    
    
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
     * @return Item
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
     * Set type
     *
     * @param string $type
     * @return Item
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Item
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Item
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set project
     *
     * @param \PhiTrac\ProjectBundle\Entity\Project $project
     * @return Item
     */
    public function setProject(\PhiTrac\ProjectBundle\Entity\Project $project)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \PhiTrac\ProjectBundle\Entity\Project 
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set priority
     *
     * @param string $priority
     * @return Item
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return string 
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Item
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
     * @ORM\PostLoad
     */
    public function maj()
    {
        $this->previousStatus = $this->status;
    }
    
    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTodo()
    {
        if ($this->getStatus()!=$this->previousStatus) { // Status changed
            $todo = $this->getProject()->getTodo();
            
            if ($this->previousStatus===null) { // Just created
                if ($this->getStatus()!="DONE") {
                    $todo++;
                }
            } else { // Modified
                if ($this->previousStatus!="DONE" && $this->getStatus()=="DONE") {
                    $todo--;
                }
                if ($this->previousStatus=="DONE" && $this->getStatus()!="DONE") {
                    $todo++;
                }
            }
            $this->getProject()->setTodo($todo);
        }
    }
    
    /**
     * @ORM\PreRemove
     */
    public function removeTodo()
    {
        if ($this->getStatus()!="DONE") {
            $todo = $this->getProject()->getTodo();
            $todo--;
            $this->getProject()->setTodo($todo);
        }
    }

    /**
     * Set creator
     *
     * @param \PhiTrac\UserBundle\Entity\User $creator
     * @return Item
     */
    public function setCreator(\PhiTrac\UserBundle\Entity\User $creator = null)
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * Get creator
     *
     * @return \PhiTrac\UserBundle\Entity\User 
     */
    public function getCreator()
    {
        return $this->creator;
    }
}
