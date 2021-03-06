<?php

namespace eProcess\EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CheckList
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="eProcess\EntityBundle\Entity\CheckListTable")
 */
class CheckList {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="hasBon", type="boolean")
     */
    private $hasBon;

    /**
     * @var string
     *
     * @ORM\Column(name="hasBonComment", type="string", length=100, nullable=true)
     */
    private $hasBonComment;

    /**
     * @var boolean
     *
     * @ORM\Column(name="fctNormale", type="boolean")
     */
    private $fctNormale;

    /**
     * @var boolean
     *
     * @ORM\Column(name="hasContrat", type="boolean")
     */
    private $hasContrat;

    /**
     * @var string
     *
     * @ORM\Column(name="hasContratComment", type="string", length=100, nullable=true)
     */
    private $hasContratComment;

    /**
     * @var boolean
     *
     * @ORM\Column(name="hasDemande", type="boolean")
     */
    private $hasDemande;

    /**
     * @var string
     *
     * @ORM\Column(name="hasDemandeComment", type="string", length=100, nullable=true)
     */
    private $hasDemandeComment;

    /**
     * @var boolean
     *
     * @ORM\Column(name="hasApprobationHier", type="boolean")
     */
    private $hasApprobationHier;

    /**
     * @var string
     *
     * @ORM\Column(name="hasApprobationHierComment", type="string", length=100, nullable=true)
     */
    private $hasApprobationHierComment;

    /**
     * @var boolean
     *
     * @ORM\Column(name="hasApprobationBudget", type="boolean")
     */
    private $hasApprobationBudget;

    /**
     * @var string
     *
     * @ORM\Column(name="hasApprobationBudgetComment", type="string", length=100, nullable=true)
     */
    private $hasApprobationBudgetComment;

    /**
     * @var boolean
     *
     * @ORM\Column(name="hasProforma", type="boolean")
     */
    private $hasProforma;

    /**
     * @var string
     *
     * @ORM\Column(name="hasProformaComment", type="string", length=100, nullable=true)
     */
    private $hasProformaComment;

    /**
     * @var boolean
     *
     * @ORM\Column(name="hasFacture", type="boolean")
     */
    private $hasFacture;

    /**
     * @var string
     *
     * @ORM\Column(name="hasFactureComment", type="string", length=100, nullable=true)
     */
    private $hasFactureComment;

    /**
     * @var boolean
     *
     * @ORM\Column(name="hasLivraison", type="boolean")
     */
    private $hasLivraison;

    /**
     * @var string
     *
     * @ORM\Column(name="hasLivraisonComment", type="string", length=100, nullable=true)
     */
    private $hasLivraisonComment;

    /**
     * @ORM\OneToOne(targetEntity="Achat", inversedBy="checkList", cascade={"persist","remove"})
     */
    private $achat;

    /**
     * Constructor
     */
    public function __construct($params = array()) {

        $this->fctNormale = true;
        $this->hasBon = false;
        $this->hasApprobationHier = false;
        $this->hasApprobationBudget = false;
        $this->hasDemande = false;
        $this->hasFacture = false;
        $this->hasProforma = false;
        $this->hasContrat = false;
        $this->hasLivraison = false;

        $this->hydrate($params);
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
     * Set hasBon
     *
     * @param boolean $hasBon
     *
     * @return CheckList
     */
    public function setHasBon($hasBon) {
        $this->hasBon = $hasBon;

        return $this;
    }

    /**
     * Get hasBon
     *
     * @return boolean
     */
    public function getHasBon() {
        return $this->hasBon;
    }

    /**
     * Set hasDemande
     *
     * @param boolean $hasDemande
     *
     * @return CheckList
     */
    public function setHasDemande($hasDemande) {
        $this->hasDemande = boolval($hasDemande);

        return $this;
    }

    /**
     * Get hasDemande
     *
     * @return boolean
     */
    public function getHasDemande() {
        return $this->hasDemande;
    }

    /**
     * Set hasApprobationHier
     *
     * @param boolean $hasApprobationHier
     *
     * @return CheckList
     */
    public function setHasApprobationHier($hasApprobationHier) {
        $this->hasApprobationHier = boolval($hasApprobationHier);

        return $this;
    }

    /**
     * Get hasApprobationHier
     *
     * @return boolean
     */
    public function getHasApprobationHier() {
        return $this->hasApprobationHier;
    }

    /**
     * Set hasApprobationBudget
     *
     * @param boolean $hasApprobationBudget
     *
     * @return CheckList
     */
    public function setHasApprobationBudget($hasApprobationBudget) {
        $this->hasApprobationHier = boolval($hasApprobationBudget);

        return $this;
    }

    /**
     * Get hasApprobationBudget
     *
     * @return boolean
     */
    public function getHasApprobationBudget() {
        return $this->hasApprobationBudget;
    }

    /**
     * Set hasProforma
     *
     * @param boolean $hasProforma
     *
     * @return CheckList
     */
    public function setHasProforma($hasProforma) {
        $this->hasProforma = boolval($hasProforma);

        return $this;
    }

    /**
     * Get hasProforma
     *
     * @return boolean
     */
    public function getHasProforma() {
        return $this->hasProforma;
    }

    /**
     * Set hasFacture
     *
     * @param boolean $hasFacture
     *
     * @return CheckList
     */
    public function setHasFacture($hasFacture) {
        $this->hasFacture = boolval($hasFacture);

        return $this;
    }

    /**
     * Get hasFacture
     *
     * @return boolean
     */
    public function getHasFacture() {
        return $this->hasFacture;
    }

    public function hydrate($params) {

        if (is_array($params)) {
            
            $listeAttributValeurs = get_object_vars($this);
            $listeAttributs = array();

            // reconstruction d'un tableau des nom des propri??t??s simples
            foreach ($listeAttributValeurs as $attrib => $value) {
                $listeAttributs[] = $attrib;
            }

            foreach ($params as $attrib => $value) {
                // mise initialisation des valeurs de propri??t??s du checkList
                if (in_array($attrib, $listeAttributs)) {
                    $this->$attrib = substr_count($attrib, 'Comment') ? $value : boolval($value);
                }
            }
        }
        
//                var_dump($this->toArray()); exit;
    }

    /**
     * Set hasBonComment
     *
     * @param string $hasBonComment
     *
     * @return CheckList
     */
    public function setHasBonComment($hasBonComment) {
        $this->hasBonComment = $hasBonComment;

        return $this;
    }

    /**
     * Get hasBonComment
     *
     * @return string
     */
    public function getHasBonComment() {
        return $this->hasBonComment;
    }

    /**
     * Set hasDemandeComment
     *
     * @param string $hasDemandeComment
     *
     * @return CheckList
     */
    public function setHasDemandeComment($hasDemandeComment) {
        $this->hasDemandeComment = $hasDemandeComment;

        return $this;
    }

    /**
     * Get hasDemandeComment
     *
     * @return string
     */
    public function getHasDemandeComment() {
        return $this->hasDemandeComment;
    }

    /**
     * Set hasApprobationHierComment
     *
     * @param string $hasApprobationHierComment
     *
     * @return CheckList
     */
    public function setHasApprobationHierComment($hasApprobationHierComment) {
        $this->hasApprobationHierComment = $hasApprobationHierComment;

        return $this;
    }

    /**
     * Get hasApprobationHierComment
     *
     * @return string
     */
    public function getHasApprobationHierComment() {
        return $this->hasApprobationHierComment;
    }

    /**
     * Set hasApprobationBudgetComment
     *
     * @param string $hasApprobationBudgetComment
     *
     * @return CheckList
     */
    public function setHasApprobationBudgetComment($hasApprobationBudgetComment) {
        $this->hasApprobationBudgetComment = $hasApprobationBudgetComment;

        return $this;
    }

    /**
     * Get hasApprobationHierComment
     *
     * @return string
     */
    public function getHasApprobationBudgetComment() {
        return $this->hasApprobationBudgetComment;
    }

    /**
     * Set hasProformaComment
     *
     * @param string $hasProformaComment
     *
     * @return CheckList
     */
    public function setHasProformaComment($hasProformaComment) {
        $this->hasProformaComment = $hasProformaComment;

        return $this;
    }

    /**
     * Get hasProformaComment
     *
     * @return string
     */
    public function getHasProformaComment() {
        return $this->hasProformaComment;
    }

    /**
     * Set hasFactureComment
     *
     * @param string $hasFactureComment
     *
     * @return CheckList
     */
    public function setHasFactureComment($hasFactureComment) {
        $this->hasFactureComment = $hasFactureComment;

        return $this;
    }

    /**
     * Get hasFactureComment
     *
     * @return string
     */
    public function getHasFactureComment() {
        return $this->hasFactureComment;
    }

//
//    /**
//     * Set hasCheckList
//     *
//     * @param boolean $hasCheckList
//     *
//     * @return CheckList
//     */
//    public function setHasCheckList($hasCheckList)
//    {
//        $this->hasCheckList = $hasCheckList;
//
//        return $this;
//    }
//
//    /**
//     * Get hasCheckList
//     *
//     * @return boolean
//     */
//    public function getHasCheckList()
//    {
//        return $this->hasCheckList;
//    }
//
//    /**
//     * Set hasCheckListComment
//     *
//     * @param string $hasCheckListComment
//     *
//     * @return CheckList
//     */
//    public function setHasCheckListComment($hasCheckListComment)
//    {
//        $this->hasCheckListComment = $hasCheckListComment;
//
//        return $this;
//    }
//
//    /**
//     * Get hasCheckListComment
//     *
//     * @return string
//     */
//    public function getHasCheckListComment()
//    {
//        return $this->hasCheckListComment;
//    }
//
//    

    /**
     * Set achat
     *
     * @param \eProcess\EntityBundle\Entity\Achat $achat
     *
     * @return CheckList
     */
    public function setAchat(\eProcess\EntityBundle\Entity\Achat $achat = null) {
        $this->achat = $achat;

        return $this;
    }

    /**
     * Get achat
     *
     * @return \eProcess\EntityBundle\Entity\Achat
     */
    public function getAchat() {
        return $this->achat;
    }

    /**
     * Set fctNormale
     *
     * @param boolean $fctNormale
     *
     * @return CheckList
     */
    public function setFctNormale($fctNormale) {
        $this->fctNormale = $fctNormale;

        return $this;
    }

    /**
     * Get fctNormale
     *
     * @return boolean
     */
    public function getFctNormale() {
        return $this->fctNormale;
    }

    /**
     * Set hasContrat
     *
     * @param boolean $hasContrat
     *
     * @return CheckList
     */
    public function setHasContrat($hasContrat) {
        $this->hasContrat = $hasContrat;

        return $this;
    }

    /**
     * Get hasContrat
     *
     * @return boolean
     */
    public function getHasContrat() {
        return $this->hasContrat;
    }

    /**
     * Set hasContratComment
     *
     * @param string $hasContratComment
     *
     * @return CheckList
     */
    public function setHasContratComment($hasContratComment) {
        $this->hasContratComment = $hasContratComment;

        return $this;
    }

    /**
     * Get hasContratComment
     *
     * @return string
     */
    public function getHasContratComment() {
        return $this->hasContratComment;
    }

    /**
     * Set hasLivraison
     *
     * @param boolean $hasLivraison
     *
     * @return CheckList
     */
    public function setHasLivraison($hasLivraison) {
        $this->hasLivraison = $hasLivraison;

        return $this;
    }

    /**
     * Get hasLivraison
     *
     * @return boolean
     */
    public function getHasLivraison() {
        return $this->hasLivraison;
    }

    /**
     * Set hasLivraisonComment
     *
     * @param string $hasLivraisonComment
     *
     * @return CheckList
     */
    public function setHasLivraisonComment($hasLivraisonComment) {
        $this->hasLivraisonComment = $hasLivraisonComment;

        return $this;
    }

    /**
     * Get hasLivraisonComment
     *
     * @return string
     */
    public function getHasLivraisonComment() {
        return $this->hasLivraisonComment;
    }

    public function toArray() {
        $array = array();

        foreach ($this as $attr => $value) {
            $array[$attr] = $value;
        }

        return $array;
    }

    public function __toString() {
        return json_encode($this->toArray());
    }

}
