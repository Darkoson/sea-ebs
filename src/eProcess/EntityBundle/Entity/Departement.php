<?php

namespace eProcess\EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Departement
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="eProcess\EntityBundle\Entity\DepartementTable")
 */
class Departement
{
    /**
     * Constructor
     */
    public function __construct($nom=' ', $description =' ')
    {
        $this->nom = $nom ;
        $this->description = $description ;
        $this->dateCreation = new \DateTime();
        $this->isActif = false ;
        $this->utilisateurs = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    
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
     * @ORM\Column(name="nom", type="string", length=20)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=200, nullable=true)
     */
    private $description;

    
    
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
    * @ORM\OneToMany(targetEntity="Utilisateur", mappedBy="departement")
    */
    private $utilisateurs;
    
//    /**
//    * @ORM\OneToMany(targetEntity="Achat", mappedBy="departement", cascade={"persist","remove"})
//    */
//    private $achats;

    /**
     * @ORM\OneToMany(targetEntity="AchatDepartement", mappedBy="departement")
     *
     */
    private $achatDepartements;
    
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
     * @return Departement
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
     * @return Departement
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
     * @return Departement
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
     * @return Departement
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
     * @return Departement
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
     * @return Departement
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
     * @return Departement
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
     * @return Departement
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

    public function __toString() {
        return $this->nom;
    }

    /**
     * Add achatDepartement
     *
     * @param \eProcess\EntityBundle\Entity\AchatDepartement $achatDepartement
     *
     * @return Departement
     */
    public function addAchatDepartement(\eProcess\EntityBundle\Entity\AchatDepartement $achatDepartement)
    {
        $this->achatDepartements[] = $achatDepartement;

        return $this;
    }

    /**
     * Remove achatDepartement
     *
     * @param \eProcess\EntityBundle\Entity\AchatDepartement $achatDepartement
     */
    public function removeAchatDepartement(\eProcess\EntityBundle\Entity\AchatDepartement $achatDepartement)
    {
        $this->achatDepartements->removeElement($achatDepartement);
    }

    /**
     * Get achatDepartements
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAchatDepartements()
    {
        return $this->achatDepartements;
    }
}
