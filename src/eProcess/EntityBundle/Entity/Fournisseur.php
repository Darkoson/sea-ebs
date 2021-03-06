<?php

namespace eProcess\EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraint as Assert;

/**
 * Fournisseur
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="eProcess\EntityBundle\Entity\FournisseurTable")
 */
class Fournisseur {

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
     * @ORM\Column(name="nom", type="string", length=100)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=100, nullable=true)
     */
    private $telephone;

   
    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100, nullable=true)
     */
    private $email;

    
    
    /**
     * @var string
     *
     * @ORM\Column(name="pays", type="string", length=50, nullable=true)
     */
    private $pays;
    
    /**
     * @var string
     *
     * @ORM\Column(name="boitePostale", type="string", length=50, nullable=true)
     */
    private $boitePostale;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=255, nullable=true)
     */
    private $adresse;
    
    /**
     * @var string
     *
     * @ORM\Column(name="typeOrganisation", type="string", length=100, nullable=true)
     */
    private $typeOrganisation;
    
    /**
     * @var string
     *
     * @ORM\Column(name="languePrefere", type="string", length=100, nullable=true)
     */
    private $languePrefere;
    
    /**
     * @var string
     *
     * @ORM\Column(name="devisePrefere", type="string", length=50, nullable=true)
     */
    private $devisePrefere;
    
    /**
     * @var string
     *
     * @ORM\Column(name="representant", type="string", length=50, nullable=true)
     */
    private $representant;
    
    /**
     * @var string
     *
     * @ORM\Column(name="modePaiement", type="string", length=50, nullable=true)
     */
    private $modePaiement;
    
    /**
     * @var string
     *
     * @ORM\Column(name="compteBancaire", type="string", length=50, nullable=true)
     */
    private $compteBancaire;
    
    /**
     * @var string
     *
     * @ORM\Column(name="agence", type="string", length=50, nullable=true)
     */
    private $agence;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="banque", type="string", length=100, nullable=true)
     */
    private $banque;

    
    
    /**
     * @var string
     *
     * @ORM\Column(name="hasContrat", type="string", length=3, nullable=true)
     */
    private $hasContrat;

    
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="verification", type="string", length=10, nullable=true)
     */
    private $verification;

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
     * @ORM\OneToMany(targetEntity="Facture", mappedBy="fournisseur")
     */
    private $factures;

    /**
     * @ORM\OneToMany(targetEntity="Achat", mappedBy="fournisseur")
     */
    private $achats;

    /**
     *  @ORM\OneToOne(targetEntity="Fichier", cascade={"persist","remove"})
     *  @ORM\JoinColumn(nullable=true)
     * 
     */
    private $fichier;

    /**
     * Constructor
     */
    public function __construct($actif = true) {
        $this->dateCreation = new \DateTime();
        $this->dateActivation = new \DateTime();
        $this->isActif = $actif;
        $this->hasContrat = 'NO' ;
    }

    function getAutre() {
        return $this->autre;
    }

    function setAutre($autre) {
        $this->autre = $autre;
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
     * Set nom
     *
     * @param string $nom
     *
     * @return Fournisseur
     */
    public function setNom($nom) {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom() {
        return $this->nom;
    }

    /**
     * Set pays
     *
     * @param string $pays
     *
     * @return Fournisseur
     */
    public function setPays($pays) {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays
     *
     * @return string
     */
    public function getPays() {
        return $this->pays;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     *
     * @return Fournisseur
     */
    public function setTelephone($telephone) {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone() {
        return $this->telephone;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Fournisseur
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set isActif
     *
     * @param boolean $isActif
     *
     * @return Fournisseur
     */
    public function setIsActif($isActif) {
        $this->isActif = $isActif;

        return $this;
    }

    /**
     * Get isActif
     *
     * @return boolean
     */
    public function getIsActif() {
        return $this->isActif;
    }

    /**
     * Set creerPar
     *
     * @param integer $creerPar
     *
     * @return Fournisseur
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
     * @return Fournisseur
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
     * @return Fournisseur
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
     * @return Fournisseur
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
     * Add facture
     *
     * @param \eProcess\EntityBundle\Entity\Facture $facture
     *
     * @return Fournisseur
     */
    public function addFacture(\eProcess\EntityBundle\Entity\Facture $facture) {
        $this->factures[] = $facture;

        return $this;
    }

    /**
     * Remove facture
     *
     * @param \eProcess\EntityBundle\Entity\Facture $facture
     */
    public function removeFacture(\eProcess\EntityBundle\Entity\Facture $facture) {
        $this->factures->removeElement($facture);
    }

    /**
     * Get factures
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFactures() {
        return $this->factures;
    }

    /**
     * Add achat
     *
     * @param \eProcess\EntityBundle\Entity\Achat $achat
     *
     * @return Fournisseur
     */
    public function addAchat(\eProcess\EntityBundle\Entity\Achat $achat) {
        $this->achats[] = $achat;

        return $this;
    }

    /**
     * Remove achat
     *
     * @param \eProcess\EntityBundle\Entity\Achat $achat
     */
    public function removeAchat(\eProcess\EntityBundle\Entity\Achat $achat) {
        $this->achats->removeElement($achat);
    }

    /**
     * Get achats
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAchats() {
        return $this->achats;
    }

    public function __toString() {
        return $this->nom;
    }

    /**
     * Set fichier
     *
     * @param \eProcess\EntityBundle\Entity\Fichier $fichier
     *
     * @return Fournisseur
     */
    public function setFichier(\eProcess\EntityBundle\Entity\Fichier $fichier = null) {
        
        $this->fichier = $fichier;

        return $this;
    }

    /**
     * Get fichier
     *
     * @return \eProcess\EntityBundle\Entity\Fichier
     */
    public function getFichier() {
        return $this->fichier;
    }
    
    


    /**
     * Set boitePostale
     *
     * @param string $boitePostale
     *
     * @return Fournisseur
     */
    public function setBoitePostale($boitePostale)
    {
        $this->boitePostale = $boitePostale;

        return $this;
    }

    /**
     * Get boitePostale
     *
     * @return string
     */
    public function getBoitePostale()
    {
        return $this->boitePostale;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     *
     * @return Fournisseur
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return string
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set typeOrganisation
     *
     * @param string $typeOrganisation
     *
     * @return Fournisseur
     */
    public function setTypeOrganisation($typeOrganisation)
    {
        $this->typeOrganisation = $typeOrganisation;

        return $this;
    }

    /**
     * Get typeOrganisation
     *
     * @return string
     */
    public function getTypeOrganisation()
    {
        return $this->typeOrganisation;
    }

    /**
     * Set languePrefere
     *
     * @param string $languePrefere
     *
     * @return Fournisseur
     */
    public function setLanguePrefere($languePrefere)
    {
        $this->languePrefere = $languePrefere;

        return $this;
    }

    /**
     * Get languePrefere
     *
     * @return string
     */
    public function getLanguePrefere()
    {
        return $this->languePrefere;
    }

    /**
     * Set devisePrefere
     *
     * @param string $devisePrefere
     *
     * @return Fournisseur
     */
    public function setDevisePrefere($devisePrefere)
    {
        $this->devisePrefere = $devisePrefere;

        return $this;
    }

    /**
     * Get devisePrefere
     *
     * @return string
     */
    public function getDevisePrefere()
    {
        return $this->devisePrefere;
    }

    /**
     * Set representant
     *
     * @param string $representant
     *
     * @return Fournisseur
     */
    public function setRepresentant($representant)
    {
        $this->representant = $representant;

        return $this;
    }

    /**
     * Get representant
     *
     * @return string
     */
    public function getRepresentant()
    {
        return $this->representant;
    }

    /**
     * Set modePaiement
     *
     * @param string $modePaiement
     *
     * @return Fournisseur
     */
    public function setModePaiement($modePaiement)
    {
        $this->modePaiement = $modePaiement;

        return $this;
    }

    /**
     * Get modePaiement
     *
     * @return string
     */
    public function getModePaiement()
    {
        return $this->modePaiement;
    }

    /**
     * Set compteBancaire
     *
     * @param string $compteBancaire
     *
     * @return Fournisseur
     */
    public function setCompteBancaire($compteBancaire)
    {
        $this->compteBancaire = $compteBancaire;

        return $this;
    }

    /**
     * Get compteBancaire
     *
     * @return string
     */
    public function getCompteBancaire()
    {
        return $this->compteBancaire;
    }

    /**
     * Set banque
     *
     * @param string $banque
     *
     * @return Fournisseur
     */
    public function setBanque($banque)
    {
        $this->banque = $banque;

        return $this;
    }

    /**
     * Get banque
     *
     * @return string
     */
    public function getBanque()
    {
        return $this->banque;
    }

    /**
     * Set hasContrat
     *
     * @param string $hasContrat
     *
     * @return Fournisseur
     */
    public function setHasContrat($hasContrat)
    {
        $this->hasContrat = $hasContrat;

        return $this;
    }

    /**
     * Get hasContrat
     *
     * @return string
     */
    public function getHasContrat()
    {
        return $this->hasContrat;
    }

    /**
     * Set verification
     *
     * @param string $verification
     *
     * @return Fournisseur
     */
    public function setVerification($verification)
    {
        $this->verification = $verification;

        return $this;
    }

    /**
     * Get verification
     *
     * @return string
     */
    public function getVerification()
    {
        return $this->verification;
    }

    /**
     * Set agence
     *
     * @param string $agence
     *
     * @return Fournisseur
     */
    public function setAgence($agence)
    {
        $this->agence = $agence;

        return $this;
    }

    /**
     * Get agence
     *
     * @return string
     */
    public function getAgence()
    {
        return $this->agence;
    }
}
