<?php

namespace eProcess\EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use eProcess\SecurityBundle\Constantes\EtapeFacture ;

/**
 * Facture
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="eProcess\EntityBundle\Entity\FactureTable")
 */
class Facture {

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
     * @ORM\Column(name="ordreArrive", type="string", length=10)
     */
    private $ordreArrive;

    
    /**
    * @ORM\OneToOne(targetEntity="Achat", inversedBy="facture", cascade={"persist","remove"})
    * @ORM\JoinColumn(nullable=true)
    */   
    private $achat;

    
     /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateArrive", type="datetime")
     */
    private $dateArrive;
    
     /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateFacture", type="datetime")
     */
    private $dateFacture;
    
     /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateTraitement", type="datetime", nullable=true)
     */
    private $dateTraitement;

    
    /**
     * @var string
     *
     * @ORM\Column(name="refFournisseur", type="string", length=15)
     */
    private $referenceFournisseur;

   
    /**
     * @var integer
     *
     * @ORM\Column(name="etape", type="integer")
     */
    private $etape;

     
    /**
     * @var string
     *
     * @ORM\Column(name="motif", type="string", length=255, nullable=true)
     */
    private $motif;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="hasProbleme", type="boolean")
     */
    private $hasProbleme;

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
     * @ORM\Column(name="traiterPar", type="integer", nullable=true)
     */
    private $traiterPar;

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
     *  @ORM\OneToOne(targetEntity="Fichier", cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $fichier;

    /**
     * @ORM\ManyToOne(targetEntity="Fournisseur", inversedBy="factures", cascade={"persist"})
     * 
     */
//    @ORM\JoinColumn(name="fournisseur_id", referencedColumnName="id")
    private $fournisseur;

    /**
    * @ORM\OneToMany(targetEntity="TransfertFacture" , mappedBy="facture", cascade={"persist","remove"})
    */
    private $transfertFactures;
    

    /**
     * Constructor
     */
    public function __construct() {
        $this->isActif = false ;
        $this->dateCreation = new \DateTime() ;
        $this->hasProbleme = false ;
        $this->etape = EtapeFacture::FCT_ARRIVE_RECEPTION ;
    }
    
    
    public function __toString() {
        return $this->referenceFournisseur;
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
     * Set ordreArrive
     *
     * @param string $ordreArrive
     *
     * @return Facture
     */
    public function setOrdreArrive($ordreArrive)
    {
        $this->ordreArrive = $ordreArrive;

        return $this;
    }

    /**
     * Get ordreArrive
     *
     * @return string
     */
    public function getOrdreArrive()
    {
        return $this->ordreArrive;
    }

    /**
     * Set dateArrive
     *
     * @param \DateTime $dateArrive
     *
     * @return Facture
     */
    public function setDateArrive($dateArrive)
    {
        $this->dateArrive = $dateArrive;

        return $this;
    }

    /**
     * Get dateArrive
     *
     * @return \DateTime
     */
    public function getDateArrive()
    {
        return $this->dateArrive;
    }

    /**
     * Set referenceFournisseur
     *
     * @param string $referenceFournisseur
     *
     * @return Facture
     */
    public function setReferenceFournisseur($referenceFournisseur)
    {
        $this->referenceFournisseur = $referenceFournisseur;

        return $this;
    }

    /**
     * Get referenceFournisseur
     *
     * @return string
     */
    public function getReferenceFournisseur()
    {
        return $this->referenceFournisseur;
    }

    
    /**
     * Set etape
     *
     * @param integer $etape
     *
     * @return Facture
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
     * Set hasProbleme
     *
     * @param boolean $hasProbleme
     *
     * @return Facture
     */
    public function setHasProbleme($hasProbleme)
    {
        $this->hasProbleme = $hasProbleme;

        return $this;
    }

    /**
     * Get hasProbleme
     *
     * @return boolean
     */
    public function getHasProbleme()
    {
        return $this->hasProbleme;
    }

    /**
     * Set isActif
     *
     * @param integer $isActif
     *
     * @return Facture
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
     * @return Facture
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
     * @return Facture
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
     * @return Facture
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
     * @return Facture
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
     * Set fournisseur
     *
     * @param \eProcess\EntityBundle\Entity\Fournisseur $fournisseur
     *
     * @return Facture
     */
    public function setFournisseur(\eProcess\EntityBundle\Entity\Fournisseur $fournisseur = null)
    {
        $this->fournisseur = $fournisseur;
        $fournisseur->addFacture($this) ;

        return $this;
    }

    /**
     * Get fournisseur
     *
     * @return \eProcess\EntityBundle\Entity\Fournisseur
     */
    public function getFournisseur()
    {
        return $this->fournisseur;
    }


    /**
     * Set achat
     *
     * @param \eProcess\EntityBundle\Entity\Achat $achat
     *
     * @return Facture
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
     * Set fichier
     *
     * @param \eProcess\EntityBundle\Entity\Fichier $fichier
     *
     * @return Facture
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
    
    
    public function isTraitementRetard() {
        $resultat = null ;
        if($this->dateCreation){
            $duree = date_diff($this->dateArrive, new DateTime()) ;
            $resultat = $duree ;
        }
        
        return $resultat;
    }

    /**
     * Set dateTraitement
     *
     * @param \DateTime $dateTraitement
     *
     * @return Facture
     */
    public function setDateTraitement($dateTraitement)
    {
        $this->dateTraitement = $dateTraitement;

        return $this;
    }

    /**
     * Get dateTraitement
     *
     * @return \DateTime
     */
    public function getDateTraitement()
    {
        return $this->dateTraitement;
    }

    /**
     * Set traiterPar
     *
     * @param integer $traiterPar
     *
     * @return Facture
     */
    public function setTraiterPar($traiterPar)
    {
        $this->traiterPar = $traiterPar;

        return $this;
    }

    /**
     * Get traiterPar
     *
     * @return integer
     */
    public function getTraiterPar()
    {
        return $this->traiterPar;
    }

    /**
     * Set motif
     *
     * @param string $motif
     *
     * @return Facture
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
     * Add transfertFacture
     *
     * @param \eProcess\EntityBundle\Entity\TransfertFacture $transfertFacture
     *
     * @return Facture
     */
    public function addTransfertFacture(\eProcess\EntityBundle\Entity\TransfertFacture $transfertFacture)
    {
        $this->transfertFactures[] = $transfertFacture;

        return $this;
    }

    /**
     * Remove transfertFacture
     *
     * @param \eProcess\EntityBundle\Entity\TransfertFacture $transfertFacture
     */
    public function removeTransfertFacture(\eProcess\EntityBundle\Entity\TransfertFacture $transfertFacture)
    {
        $this->transfertFactures->removeElement($transfertFacture);
    }

    /**
     * Get transfertFactures
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTransfertFactures()
    {
        return $this->transfertFactures;
    }
    
    
    public function getLastInfoTransfert() {
        $message = 'rien' ;
        $nbreTransf = count($this->transfertFactures) ;
        if($nbreTransf > 0){
            $LastTrans = $this->transfertFactures[$nbreTransf-1] ;
            $message = $LastTrans->getDescription() ;
        }
        return $message;
    }

    /**
     * Set dateFacture
     *
     * @param \DateTime $dateFacture
     *
     * @return Facture
     */
    public function setDateFacture($dateFacture)
    {
        $this->dateFacture = $dateFacture;

        return $this;
    }

    /**
     * Get dateFacture
     *
     * @return \DateTime
     */
    public function getDateFacture()
    {
        return $this->dateFacture;
    }
}
