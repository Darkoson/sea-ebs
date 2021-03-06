<?php

namespace eProcess\EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BonCommande
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="eProcess\EntityBundle\Entity\BonCommandeTable")
 */
class BonCommande
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
     * @ORM\Column(name="numero", type="string", length=15)
     */
    private $numero;

    /**
     * @var integer
     *
     * @ORM\Column(name="etape", type="integer")
     */
    private $etape;

    
    /**
     * @var integer
     *
     * @ORM\Column(name="isActif", type="integer")
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
     * @ORM\Column(name="modifierPar", type="integer", nullable=true)
     */
    private $modifierPar;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="modificationEncours", type="boolean", nullable=true)
     */
    private $modificationEncours;

    /**
     * @var integer
     *
     * @ORM\Column(name="verifierPar", type="integer", nullable=true)
     */
    private $verifierPar;
    

    /**
     * @var integer
     *
     * @ORM\Column(name="activerPar", type="integer", nullable=true)
     */
    private $activerPar;
    
    /**
     * @var string
     *
     * @ORM\Column(name="motif", type="string", length=255, nullable=true)
     */
    private $motif;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreation", type="datetime")
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateVerification", type="datetime", nullable=true)
     */
    private $dateVerification;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateActivation", type="datetime", nullable=true)
     */
    private $dateActivation;
    
    /**
    * @ORM\OneToMany(targetEntity="ApprobationBon", mappedBy="bonCommande" , cascade={"persist"})
    */
    private $approbationBons;    
    
  
    /**
    * @ORM\OneToOne(targetEntity="Achat", inversedBy="bonCommande", cascade={"persist","remove"})
    */
    private $achat;
    
    /**
    * Constructor
    */
    public function __construct($numero= ' ' ,$idCreateur=0)
    {
        $this->numero = $numero ;
        $this->isActif = 0;  
        $this->etape = 1 ;
        $this->creerPar = $idCreateur  ;
        $this->dateCreation = new \DateTime() ;
        $this->modificationEncours = false ;
        
    }
    /**
     *
     * @ORM\OneToOne(targetEntity="Fichier" , cascade={"persist","remove"}) 
     * @ORM\JoinColumn(nullable=true)
     */
    private $fichier ;

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
     * Set numero
     *
     * @param string $numero
     *
     * @return BonCommande
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    
    /**
     * Set etape
     *
     * @param integer $etape
     *
     * @return BonCommande
     */
    public function setEtape($etape)
    {
        $this->etape = $etape;

        return $this;
    }

    /**
     * Get etape
     *
     * @return integer
     */
    public function getEtape()
    {
        return $this->etape;
    }

    /**
     * Set isActif
     *
     * @param integer $isActif
     *
     * @return BonCommande
     */
    public function setIsActif($isActif)
    {
        $this->isActif = $isActif;

        return $this;
    }

    /**
     * Get isActif
     *
     * @return integer
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
     * @return BonCommande
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
     * @return BonCommande
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
     * @return BonCommande
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
     * @return BonCommande
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
     * Set achat
     *
     * @param \eProcess\EntityBundle\Entity\Achat $achat
     *
     * @return BonCommande
     */
    public function setAchat(\eProcess\EntityBundle\Entity\Achat $achat = null)
    {
        $this->achat = $achat;

        return $this;
    }

    /**
     * Get achat
     *
     * @return \eProcess\EntityBundle\Entity\Achat
     */
    public function getAchat()
    {
        return $this->achat;
    }
    
    /**
     * 
     * @return string
     */
    
    public function __toString() {
        return $this->numero;
    }


    /**
     * Set fichier
     *
     * @param \eProcess\EntityBundle\Entity\Fichier $fichier
     *
     * @return BonCommande
     */
    public function setFichier(\eProcess\EntityBundle\Entity\Fichier $fichier = null)
    {
        $this->fichier = $fichier;

        return $this;
    }

    /**
     * Get fichier
     *
     * @return \eProcess\EntityBundle\Entity\Fichier
     */
    public function getFichier()
    {
        return $this->fichier;
    }

    /**
     * Set motif
     *
     * @param string $motif
     *
     * @return BonCommande
     */
    public function setMotif($motif)
    {
        $this->motif = $motif;

        return $this;
    }

    /**
     * Get motif
     *
     * @return string
     */
    public function getMotif()
    {
        return $this->motif;
    }

    /**
     * Set modifierPar
     *
     * @param integer $modifierPar
     *
     * @return BonCommande
     */
    public function setModifierPar($modifierPar)
    {
        $this->modifierPar = $modifierPar;

        return $this;
    }

    /**
     * Get modifierPar
     *
     * @return integer
     */
    public function getModifierPar()
    {
        return $this->modifierPar;
    }

    /**
     * Set modificationEncours
     *
     * @param boolean $modificationEncours
     *
     * @return BonCommande
     */
    public function setModificationEncours($modificationEncours)
    {
        $this->modificationEncours = $modificationEncours;

        return $this;
    }

    /**
     * Get modificationEncours
     *
     * @return boolean
     */
    public function getModificationEncours()
    {
        return $this->modificationEncours;
    }

    /**
     * Set verifierPar
     *
     * @param integer $verifierPar
     *
     * @return BonCommande
     */
    public function setVerifierPar($verifierPar)
    {
        $this->verifierPar = $verifierPar;

        return $this;
    }

    /**
     * Get verifierPar
     *
     * @return integer
     */
    public function getVerifierPar()
    {
        return $this->verifierPar;
    }

    /**
     * Set dateVerification
     *
     * @param \DateTime $dateVerification
     *
     * @return BonCommande
     */
    public function setDateVerification($dateVerification)
    {
        $this->dateVerification = $dateVerification;

        return $this;
    }

    /**
     * Get dateVerification
     *
     * @return \DateTime
     */
    public function getDateVerification()
    {
        return $this->dateVerification;
    }

    /**
     * Add approbationBon
     *
     * @param \eProcess\EntityBundle\Entity\ApprobationBon $approbationBon
     *
     * @return BonCommande
     */
    public function addApprobationBon(\eProcess\EntityBundle\Entity\ApprobationBon $approbationBon)
    {
        $this->approbationBons[] = $approbationBon;

        return $this;
    }

    /**
     * Remove approbationBon
     *
     * @param \eProcess\EntityBundle\Entity\ApprobationBon $approbationBon
     */
    public function removeApprobationBon(\eProcess\EntityBundle\Entity\ApprobationBon $approbationBon)
    {
        $this->approbationBons->removeElement($approbationBon);
    }

    /**
     * Get approbationBons
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getApprobationBons()
    {
        return $this->approbationBons;
    }
}
