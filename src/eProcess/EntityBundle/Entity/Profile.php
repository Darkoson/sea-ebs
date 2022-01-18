<?php

namespace eProcess\EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use eProcess\SecurityBundle\Constantes\TypeProfile; 

/**
 * Profile
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="eProcess\EntityBundle\Entity\ProfileTable")
 */
class Profile
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
     * @ORM\Column(name="nom", type="string", length=30)
     */
    private $nom;
    
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=6)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=200)
     */
    private $description;

    
    /**
     * @var boolean
     *
     * @ORM\Column(name="isActif", type="boolean")
     */
    private $isActif;
    
    /**
     * @var boolean $canApprove:  indique si les utiliateur de ce profil peuvent appouver
     *
     * @ORM\Column(name="canApprove", type="boolean")
     */
    private $canApprove;


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
    * @ORM\OneToMany(targetEntity="Utilisateur", mappedBy="profile")
    */
    private $utilisateurs;    
    
    /**
    * @ORM\OneToMany(targetEntity="AvoirDroit", mappedBy="profile")
    */
    private $avoirDroits;
    
    
    /**
    * Constructor
    */
    public function __construct($nom='' , $desc='',$canApprove=false)
    {
        $this->nom = $nom ;
        $this->description = $desc ;
        $this->code  = TypeProfile::DEFAUT ;
        $this->canApprove = $canApprove ;
        $this->utilisateurs = null ;
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
     * @return Profile
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
     * @return Profile
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
     * Set isActif
     *
     * @param boolean $isActif
     *
     * @return Profile
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
     * @return Profile
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
     * @return Profile
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
     * @return Profile
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
     * @return Profile
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
     * Add utilisateur
     *
     * @param \eProcess\EntityBundle\Entity\Utilisateur $utilisateur
     *
     * @return Profile
     */
    public function addUtilisateur(\eProcess\EntityBundle\Entity\Utilisateur $utilisateur)
    {
        $this->utilisateurs[] = $utilisateur;

        return $this;
    }

    /**
     * Remove utilisateur
     *
     * @param \eProcess\EntityBundle\Entity\Utilisateur $utilisateur
     */
    public function removeUtilisateur(\eProcess\EntityBundle\Entity\Utilisateur $utilisateur)
    {
        $this->utilisateurs->removeElement($utilisateur);
    }

    /**
     * Get utilisateurs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUtilisateurs()
    {
        return $this->utilisateurs;
    }

    /**
     * Add avoirDroit
     *
     * @param \eProcess\EntityBundle\Entity\AvoirDroit $avoirDroit
     *
     * @return Profile
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

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Profile
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
    
    
    public function __toString() {
        return $this->nom;
    }

    /**
     * Set canApprove
     *
     * @param boolean $canApprove
     *
     * @return Profile
     */
    public function setCanApprove($canApprove)
    {
        $this->canApprove = $canApprove;

        return $this;
    }

    /**
     * Get canApprove
     *
     * @return boolean
     */
    public function getCanApprove()
    {
        return $this->canApprove;
    }
}
