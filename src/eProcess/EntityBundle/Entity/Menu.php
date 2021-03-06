<?php

namespace eProcess\EntityBundle\Entity;

use eProcess\SecurityBundle\Constantes\TypeMenu ;
use Doctrine\ORM\Mapping as ORM;

/**
 * Menu
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="eProcess\EntityBundle\Entity\MenuTable")
 */
class Menu
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
     * @ORM\Column(name="nom", type="string", length=40)
     */
    private $nom;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer")
     */
    private $type;
    
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="isActif", type="boolean")
     */
    private $isActif;


    /**
     * @var integer
     *
     * @ORM\Column(name="creerPar", type="integer")
     */
    private $creerPar;

    /**
     * @var integer
     *
     * @ORM\Column(name="activerPar", type="integer", nullable=true)
     */
    private $activerPar;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreation", type="datetime")
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateActivation", type="datetime", nullable=true)
     */
    private $dateActivation;

    /**
    * @ORM\ManyToOne(targetEntity="Action", inversedBy="menus")
    * @ORM\JoinColumn(name="action_id", referencedColumnName="id")
    */
    private $action;
    
    /**
    * @ORM\ManyToOne(targetEntity="Menu", inversedBy="enfants")
    * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
    */
    private $parent;
    
    /**
    * @ORM\OneToMany(targetEntity="Menu", mappedBy="parent")
    */
    private $enfants;  
    
    /**
    * Constructor
    */
    public function __construct($nom)
    {
        $this->nom = $nom ;
        $this->type = TypeMenu::GAUCHE ;
        $this->enfants = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Menu
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
     * Set type
     *
     * @param integer $type
     *
     * @return Menu
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set isActif
     *
     * @param boolean $isActif
     *
     * @return Menu
     */
    public function setIsActif($isActif)
    {
        $this->isActif = $isActif;

        return $this;
    }

    /**
     * Get isActif
     *
     * @return boolean
     */
    public function getIsActif()
    {
        return $this->isActif;
    }

    /**
     * Set creerPar
     *
     * @param integer $creerPar
     *
     * @return Menu
     */
    public function setCreerPar($creerPar)
    {
        $this->creerPar = $creerPar;

        return $this;
    }

    /**
     * Get creerPar
     *
     * @return integer
     */
    public function getCreerPar()
    {
        return $this->creerPar;
    }

    /**
     * Set activerPar
     *
     * @param integer $activerPar
     *
     * @return Menu
     */
    public function setActiverPar($activerPar)
    {
        $this->activerPar = $activerPar;

        return $this;
    }

    /**
     * Get activerPar
     *
     * @return integer
     */
    public function getActiverPar()
    {
        return $this->activerPar;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Menu
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set dateActivation
     *
     * @param \DateTime $dateActivation
     *
     * @return Menu
     */
    public function setDateActivation($dateActivation)
    {
        $this->dateActivation = $dateActivation;

        return $this;
    }

    /**
     * Get dateActivation
     *
     * @return \DateTime
     */
    public function getDateActivation()
    {
        return $this->dateActivation;
    }

    /**
     * Set action
     *
     * @param \eProcess\EntityBundle\Entity\Action $action
     *
     * @return Menu
     */
    public function setAction(\eProcess\EntityBundle\Entity\Action $action = null)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return \eProcess\EntityBundle\Entity\Action
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set parent
     *
     * @param \eProcess\EntityBundle\Entity\Menu $parent
     *
     * @return Menu
     */
    public function setParent(\eProcess\EntityBundle\Entity\Menu $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \eProcess\EntityBundle\Entity\Menu
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add enfant
     *
     * @param \eProcess\EntityBundle\Entity\Menu $enfant
     *
     * @return Menu
     */
    public function addEnfant(\eProcess\EntityBundle\Entity\Menu $enfant)
    {
        $this->enfants[] = $enfant;

        return $this;
    }

    /**
     * Remove enfant
     *
     * @param \eProcess\EntityBundle\Entity\Menu $enfant
     */
    public function removeEnfant(\eProcess\EntityBundle\Entity\Menu $enfant)
    {
        $this->enfants->removeElement($enfant);
    }

    /**
     * Get enfants
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEnfants()
    {
        return $this->enfants;
    }
    
    public function __toString() {
        return $this->nom;
    }
}
