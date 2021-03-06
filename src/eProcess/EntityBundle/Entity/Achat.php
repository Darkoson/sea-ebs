<?php

namespace eProcess\EntityBundle\Entity;

use eProcess\SecurityBundle\Constantes\Status;
use Doctrine\ORM\Mapping as ORM;


/**
 * Achat
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="eProcess\EntityBundle\Entity\AchatTable")
 */

class Achat {

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
     * @ORM\Column(name="objet", type="string", length=200, nullable=true)
     */
    private $objet;

    /**
     * @var string
     *
     * @ORM\Column(name="devise", type="string", length=20)
     */
    private $devise;
    
    /**
     * @var string
     *
     * @ORM\Column( name="remise", type="string", length=20, nullable=true )
     */
    private $remise;
    
    /**
     * @var string
     *
     * @ORM\Column(name="taxe", type="integer")
     */
    private $taxe;

    
    /**
     * @var string : exprime le pourcentage du montant paye à la commande
     *
     * @ORM\Column(name="paieCommande", type="string", length=6)
     */
    private $paieCommande;

    
    /**
     * @var string : exprime le delais de livraison de la commande ou du service (c'est le nbre de semaine)
     *
     * @ORM\Column(name="delaisLivraison", type="string", length=6)
     */
    private $delaisLivraison;

    
    /**
     * @var integer : relative = à un voyage ou achat des articles
     *
     * @ORM\Column(name="type", type="integer", nullable= true)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="etape", type="integer", nullable= true)
     */
    private $etape;

    /**
     * @var integer
     *
     * @ORM\Column(name="isActif", type="integer", nullable= true)
     */
    private $isActif;

    /**
     * @var integer
     *
     * @ORM\Column(name="creerPar", type="integer", nullable= true)
     */
    private $creerPar;

    /**
     * @var integer
     *
     * @ORM\Column(name="activerPar", type="integer", nullable= true)
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
     * @ORM\Column(name="dateActivation", type="datetime", nullable= true)
     */
    private $dateActivation;

    /**
     * @ORM\OneToMany(targetEntity="AchatDepartement", mappedBy="achat", cascade={"persist"})
     *
     */
    private $achatDepartements;

    /**
     * @ORM\ManyToOne(targetEntity="Fournisseur", inversedBy="achats", cascade={"persist"})
     */
    private $fournisseur;

    /**
     * @ORM\OneToOne(targetEntity="BonCommande", mappedBy="achat", cascade={"persist", "remove"})
     * @ORM\JoinColumn( nullable=true)
     */
    private $bonCommande;

   
    
    /**
     * @ORM\OneToOne(targetEntity="Facture", mappedBy="achat", cascade={"persist","remove"})
     * @ORM\JoinColumn( nullable=true)
     */
    private $facture;

    /**
     * @ORM\OneToOne(targetEntity="CheckList", cascade={"persist","remove"})
     * @ORM\JoinColumn( nullable=true)
     */
    private $checkList;

    /**
     * @ORM\OneToOne(targetEntity="Fichier", cascade={"persist","remove"})
     * @ORM\JoinColumn( nullable=true)
     */
    private $ficDemande;

    /**
     *  @ORM\OneToOne(targetEntity="Fichier" , cascade={"persist","remove"})
     * @ORM\JoinColumn( nullable=true)
     */
    private $ficProformat;

    /**
     *  @ORM\OneToOne(targetEntity="Fichier" , cascade={"persist","remove"})
     * @ORM\JoinColumn( nullable=true)
     */
    private $ficApprobationHier;
    
    /**
     *  @ORM\OneToOne(targetEntity="Fichier" , cascade={"persist","remove"})
     * @ORM\JoinColumn( nullable=true)
     */
    private $ficApprobationBudget;
    
    /**
     *  @ORM\OneToOne(targetEntity="Fichier" , cascade={"persist","remove"})
     * @ORM\JoinColumn( nullable=true)
     */
    private $ficDemandeExo;

    /**
     *  @ORM\OneToOne(targetEntity="Fichier" , cascade={"persist","remove"})
     * @ORM\JoinColumn( nullable=true)
     */
    private $ficLivraison;

    /**
     * Constructor
     */
    public function __construct() {
        $this->dateCreation = new \DateTime();
        $this->isActif = Status::ACTIF ;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * 
     * @return String
     */
    public function __toString() {
        return $this->objet;
    }

    /**
     * 
     * @return array 
     */
    public function toArray() {
        $array['id'] = $this->id ;
        $array['bonCommande'] = ($this->bonCommande)?  $this->bonCommande->__toString() : null;
        $array['facture'] = ($this->facture)? $this->facture->__toString() : null;
        $array['montant'] = $this->montant ;
        $array['devise'] = $this->devise ;
        $array['taxe'] = $this->taxe ;
        $array['etape'] = $this->etape ;
        $array['checkList'] = ($this->checkList)? $this->checkList->__toString() : null;
        return $array;
    }

    /**
     * Set objet
     *
     * @param string $objet
     *
     * @return Achat
     */
    public function setObjet($objet) {
        $this->objet = $objet;

        return $this;
    }

    /**
     * Get objet
     *
     * @return string
     */
    public function getObjet() {
        return $this->objet;
    }


    /**
     * Get montant : fonction de recuperation du montant à partir de DepartementAchat
     *
     * @return string
     */
    public function getMontant() {
        $montant = 0;
        if(is_array($this->achatDepartements) && count($this->achatDepartements)>0){
            foreach ($this->achatDepartements as $achatDep) {
                $montant += floatval(str_replace(' ', '', $achatDep->getMontant()));
            }
        }
        
        return number_format($montant,2,'.',' ');
    }

    /**
     * Set devise
     *
     * @param string $devise
     *
     * @return Achat
     */
    public function setDevise($devise) {
        $this->devise = $devise;

        return $this;
    }

    /**
     * Get devise
     *
     * @return string
     */
    public function getDevise() {
        return $this->devise;
    }

    
    /**
     * Set paieCommande
     *
     * @param string $paieCommande
     *
     * @return AchatDepartement
     */
    public function setPaieCommande($paieCommande)
    {
        $this->paieCommande = $paieCommande;

        return $this;
    }

    /**
     * Get paieCommande
     *
     * @return string
     */
    public function getPaieCommande()
    {
        return $this->paieCommande;
    }

    
    /**
     * Set delaisLivraison
     *
     * @param string $delaisLivraison
     *
     * @return AchatDepartement
     */
    public function setDelaisLivraison($delaisLivraison)
    {
        $this->delaisLivraison = $delaisLivraison;

        return $this;
    }

    /**
     * Get delaisLivraison
     *
     * @return string
     */
    public function getDelaisLivraison()
    {
        return $this->delaisLivraison;
    }
    
    /**
     * Set etape
     *
     * @param integer $etape
     *
     * @return Achat
     */
    public function setEtape($etape) {
        $this->etape = $etape;

        return $this;
    }

    /**
     * Get etape
     *
     * @return integer
     */
    public function getEtape() {
        return $this->etape;
    }

    /**
     * Set isActif
     *
     * @param integer $isActif
     *
     * @return Achat
     */
    public function setIsActif($isActif) {
        $this->isActif = $isActif;

        return $this;
    }

    /**
     * Get isActif
     *
     * @return integer
     */
    public function getIsActif() {
        return $this->isActif;
    }

    /**
     * Set creerPar
     *
     * @param integer $creerPar
     *
     * @return Achat
     */
    public function setCreerPar($creerPar) {
        $this->creerPar = $creerPar;

        return $this;
    }

    /**
     * Get creerPar
     *
     * @return integer
     */
    public function getCreerPar() {
        return $this->creerPar;
    }

    /**
     * Set activerPar
     *
     * @param integer $activerPar
     *
     * @return Achat
     */
    public function setActiverPar($activerPar) {
        $this->activerPar = $activerPar;

        return $this;
    }

    /**
     * Get activerPar
     *
     * @return integer
     */
    public function getActiverPar() {
        return $this->activerPar;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Achat
     */
    public function setDateCreation($dateCreation) {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation() {
        return $this->dateCreation;
    }

    /**
     * Set dateActivation
     *
     * @param \DateTime $dateActivation
     *
     * @return Achat
     */
    public function setDateActivation($dateActivation) {
        $this->dateActivation = $dateActivation;

        return $this;
    }

    /**
     * Get dateActivation
     *
     * @return \DateTime
     */
    public function getDateActivation() {
        return $this->dateActivation;
    }


    /**
     * Set fournisseur
     *
     * @param \eProcess\EntityBundle\Entity\Fournisseur $fournisseur
     *
     * @return Achat
     */
    public function setFournisseur(\eProcess\EntityBundle\Entity\Fournisseur $fournisseur = null) {
        $this->fournisseur = $fournisseur;
        if($fournisseur){
            $this->fournisseur->addAchat($this);
        }

        return $this;
    }

    /**
     * Get fournisseur
     *
     * @return \eProcess\EntityBundle\Entity\Fournisseur
     */
    public function getFournisseur() {
        return $this->fournisseur;
    }

    /**
     * Set bonCommande
     *
     * @param \eProcess\EntityBundle\Entity\BonCommande $bonCommande
     *
     * @return Achat
     */
    public function setBonCommande(\eProcess\EntityBundle\Entity\BonCommande $bonCommande = null) {
        $this->bonCommande = $bonCommande;
        if($this->bonCommande){
            $this->bonCommande->setAchat($this);
        }
       

        return $this;
    }

    /**
     * Get bonCommande
     *
     * @return \eProcess\EntityBundle\Entity\BonCommande
     */
    public function getBonCommande() {
        return $this->bonCommande;
    }


    /**
     * Set facture
     *
     * @param \eProcess\EntityBundle\Entity\Facture $facture
     *
     * @return Achat
     */
    public function setFacture(\eProcess\EntityBundle\Entity\Facture $facture = null) {
        $this->facture = $facture;
        if ($this->facture) {
            $facture->setAchat($this);
        }
        if ($this->fournisseur && $this->facture) {
            $this->facture->setFournisseur($this->fournisseur);
        }


        return $this;
    }

    /**
     * Get facture
     *
     * @return \eProcess\EntityBundle\Entity\Facture
     */
    public function getFacture() {
        return $this->facture;
    }


    /**
     * Set checkList
     *
     * @param \eProcess\EntityBundle\Entity\CheckList $checkList
     *
     * @return Achat
     */
    public function setCheckList(\eProcess\EntityBundle\Entity\CheckList $checkList)
    {
        $this->checkList = $checkList;

        return $this;
    }

    /**
     * Get checkList
     *
     * @return \eProcess\EntityBundle\Entity\CheckList
     */
    public function getCheckList()
    {
        return $this->checkList;
    }

    /**
     * Set ficDemande
     *
     * @param \eProcess\EntityBundle\Entity\Fichier $ficDemande
     *
     * @return Achat
     */
    public function setFicDemande(\eProcess\EntityBundle\Entity\Fichier $ficDemande = null)
    {
        $this->ficDemande = $ficDemande;

        return $this;
    }

    /**
     * Get ficDemande
     *
     * @return \eProcess\EntityBundle\Entity\Fichier
     */
    public function getFicDemande()
    {
        return $this->ficDemande;
    }

    /**
     * Set ficProformat
     *
     * @param \eProcess\EntityBundle\Entity\Fichier $ficProformat
     *
     * @return Achat
     */
    public function setFicProformat(\eProcess\EntityBundle\Entity\Fichier $ficProformat = null)
    {
        $this->ficProformat = $ficProformat;

        return $this;
    }

    /**
     * Get ficProformat
     *
     * @return \eProcess\EntityBundle\Entity\Fichier
     */
    public function getFicProformat()
    {
        return $this->ficProformat;
    }

    /**
     * Set ficApprobationHier
     *
     * @param \eProcess\EntityBundle\Entity\Fichier $ficApprobationHier
     *
     * @return Achat
     */
    public function setFicApprobationHier(\eProcess\EntityBundle\Entity\Fichier $ficApprobationHier = null)
    {
        $this->ficApprobationHier = $ficApprobationHier;

        return $this;
    }

    /**
     * Get ficApprobationHier
     *
     * @return \eProcess\EntityBundle\Entity\Fichier
     */
    public function getFicApprobationHier()
    {
        return $this->ficApprobationHier;
    }

    /**
     * Set ficLivraison
     *
     * @param \eProcess\EntityBundle\Entity\Fichier $ficLivraison
     *
     * @return Achat
     */
    public function setFicLivraison(\eProcess\EntityBundle\Entity\Fichier $ficLivraison = null)
    {
        $this->ficLivraison = $ficLivraison;

        return $this;
    }

    /**
     * Get ficLivraison
     *
     * @return \eProcess\EntityBundle\Entity\Fichier
     */
    public function getFicLivraison()
    {
        return $this->ficLivraison;
    }

    
   
    public function __clone() {
        $this->id = null ;
        $this->bonCommande = null ;
        $this->facture     = null ;
        $this->checkList   = ($this->checkList)? clone $this->checkList : null ;
        $this->provision   = ($this->provision)? clone $this->provision : null ;
        $this->ficApprobationHier =  ($this->ficApprobationHier)?clone $this->ficApprobationHier : null ;
        $this->ficDemande     =  ($this->ficDemande)? clone $this->ficDemande : null ;
        $this->ficLivraison   =  ($this->ficLivraison)? clone $this->ficLivraison : null ;
        $this->ficProformat   =  ($this->ficProformat)? clone $this->ficProformat : null ;
    }
    

    /**
     * Add achatDepartement
     *
     * @param \eProcess\EntityBundle\Entity\AchatDepartement $achatDepartement
     *
     * @return Achat
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

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return Achat
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
     * Set ficApprobationBudget
     *
     * @param \eProcess\EntityBundle\Entity\Fichier $ficApprobationBudget
     *
     * @return Achat
     */
    public function setFicApprobationBudget(\eProcess\EntityBundle\Entity\Fichier $ficApprobationBudget = null)
    {
        $this->ficApprobationBudget = $ficApprobationBudget;

        return $this;
    }

    /**
     * Get ficApprobationBudget
     *
     * @return \eProcess\EntityBundle\Entity\Fichier
     */
    public function getFicApprobationBudget()
    {
        return $this->ficApprobationBudget;
    }

    /**
     * Set ficDemandeExo
     *
     * @param \eProcess\EntityBundle\Entity\Fichier $ficDemandeExo
     *
     * @return Achat
     */
    public function setFicDemandeExo(\eProcess\EntityBundle\Entity\Fichier $ficDemandeExo = null)
    {
        $this->ficDemandeExo = $ficDemandeExo;

        return $this;
    }

    /**
     * Get ficDemandeExo
     *
     * @return \eProcess\EntityBundle\Entity\Fichier
     */
    public function getFicDemandeExo()
    {
        return $this->ficDemandeExo;
    }

    /**
     * Set taxe
     *
     * @param integer $taxe
     *
     * @return Achat
     */
    public function setTaxe($taxe)
    {
        $this->taxe = $taxe;

        return $this;
    }

    /**
     * Get taxe
     *
     * @return integer
     */
    public function getTaxe()
    {
        return $this->taxe;
    }

    /**
     * Set remise
     *
     * @param string $remise
     *
     * @return Achat
     */
    public function setRemise($remise)
    {
        $this->remise = $remise;

        return $this;
    }

    /**
     * Get remise
     *
     * @return string
     */
    public function getRemise()
    {
        return $this->remise;
    }
}
