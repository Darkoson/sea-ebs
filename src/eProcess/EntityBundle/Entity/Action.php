<?php

namespace eProcess\EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Action
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="eProcess\EntityBundle\Entity\ActionTable")
 */
class Action
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
     * @ORM\Column(name="nom", type="string", length=50)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=200)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="route", type="string", length=50)
     */
    private $route;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isAccessible", type="boolean")
     */
    private $isAccessible;

    /**
    * @ORM\ManyToOne(targetEntity="Module", inversedBy="actions")
    * @ORM\JoinColumn(name="module_id", referencedColumnName="id")
    */
    private $module;
    
    /**
    * @ORM\OneToMany(targetEntity="Menu", mappedBy="action")
    */
    private $menus;
    
    /**
    * @ORM\OneToMany(targetEntity="AvoirDroit", mappedBy="action")
    */
    private $avoirDroits;
    
    /**
    * Constructor
    */
    public function __construct($nom , $desc,$accessible = true , $route = "")
    {
        
        $this->nom = $nom ;
        
        $this->description = $desc ;
        
        $this->isAccessible = $accessible ;
        
        $this->route = $route ;
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
     * Set nom
     *
     * @param string $nom
     *
     * @return Action
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Action
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
     * Set route
     *
     * @param string $route
     *
     * @return Action
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set isAccessible
     *
     * @param boolean $isAccessible
     *
     * @return Action
     */
    public function setIsAccessible($isAccessible)
    {
        $this->isAccessible = $isAccessible;

        return $this;
    }

    /**
     * Get isAccessible
     *
     * @return boolean
     */
    public function getIsAccessible()
    {
        return $this->isAccessible;
    }

    /**
     * Set module
     *
     * @param \eProcess\EntityBundle\Entity\Module $module
     *
     * @return Action
     */
    public function setModule(\eProcess\EntityBundle\Entity\Module $module = null)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * Get module
     *
     * @return \eProcess\EntityBundle\Entity\Module
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Add menu
     *
     * @param \eProcess\EntityBundle\Entity\Menu $menu
     *
     * @return Action
     */
    public function addMenu(\eProcess\EntityBundle\Entity\Menu $menu)
    {
        $this->menus[] = $menu;

        return $this;
    }

    /**
     * Remove menu
     *
     * @param \eProcess\EntityBundle\Entity\Menu $menu
     */
    public function removeMenu(\eProcess\EntityBundle\Entity\Menu $menu)
    {
        $this->menus->removeElement($menu);
    }

    /**
     * Get menus
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMenus()
    {
        return $this->menus;
    }

    /**
     * Add avoirDroit
     *
     * @param \eProcess\EntityBundle\Entity\AvoirDroit $avoirDroit
     *
     * @return Action
     */
    public function addAvoirDroit(\eProcess\EntityBundle\Entity\AvoirDroit $avoirDroit)
    {
        $this->avoirDroits[] = $avoirDroit;

        return $this;
    }

    /**
     * Remove avoirDroit
     *
     * @param \eProcess\EntityBundle\Entity\AvoirDroit $avoirDroit
     */
    public function removeAvoirDroit(\eProcess\EntityBundle\Entity\AvoirDroit $avoirDroit)
    {
        $this->avoirDroits->removeElement($avoirDroit);
    }

    /**
     * Get avoirDroits
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAvoirDroits()
    {
        return $this->avoirDroits;
    }
    
    
    public function __toString() {
        return $this->description;
    }
}
