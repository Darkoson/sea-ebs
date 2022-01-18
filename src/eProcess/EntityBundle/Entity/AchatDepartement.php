<?php

namespace eProcess\EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * AchatDepartement
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="eProcess\EntityBundle\Entity\AchatDepartementTable")
 */

class AchatDepartement {

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
     * @ORM\Column(name="demandeur", type="string", length=100, nullable=true)
     */
    private $demandeur;


    /**
     * @var string
     *
     * @ORM\Column(name="montant", type="string", length=25)
     */
    private $montant;


    /**
     * @ORM\ManyToOne(targetEntity="Departement", inversedBy="achatDepartements", cascade={"persist"})
     *
     */
    private $departement;

    /**
     * @ORM\ManyToOne(targetEntity="Achat", inversedBy="achatDepartements", cascade={"persist"})
     */
    private $achat;

    /**
     * @ORM\OneToOne(targetEntity="Provision", mappedBy="achatDepartements", cascade={"persist", "remove"})
     * @ORM\JoinColumn( nullable=true)
     */
    private $provision;

    /**
     * @ORM\OneToOne(targetEntity="Voyage", mappedBy="achatDepartements", cascade={"persist", "remove"})
     * @ORM\JoinColumn( nullable=true)
     */
    private $voyage;

    /**
     * Constructor
     */
    public function __construct() {
        $this->montant = '0' ;
        
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
        return $this->montant;
    }

    /**
     * 
     * @return array 
     */
    public function toArray() {
        $array['departement'] = $this->departement ;
        $array['montant'] = $this->montant;

        return $array;
    }
    
    /**
     * ajusterValeurs : fonction de transfert des différents valeurs dans les entités concernée
     * 
     * @return array 
     */
    public function ajusterValeurs() {
        if($this->achat && $this->departement && $this->provision ){
            $this->achat->setTaxe($this->provision->getTaxe());
            $this->setMontant($this->provision->getTotale() );
            $this->achat->setRemise($this->provision->getRemise());
        }
        
    }
    

    /**
     * Set montant
     *
     * @param string $montant
     *
     * @return AchatDepartement
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * Get montant
     *
     * @return string
     */
    public function getMontant()
    {
        return $this->montant .''; // juste pour obliger le resultat 
    }

    /**
     * Set departement
     *
     * @param \eProcess\EntityBundle\Entity\Departement $departement
     *
     * @return AchatDepartement
     */
    public function setDepartement(\eProcess\EntityBundle\Entity\Departement $departement = null)
    {
        $this->departement = $departement;
        if($this->departement){
            $this->departement->addAchatDepartement($this);
        }

        return $this;
    }

    /**
     * Get departement
     *
     * @return \eProcess\EntityBundle\Entity\Departement
     */
    public function getDepartement()
    {
        return $this->departement;
    }

    /**
     * Set achat
     *
     * @param \eProcess\EntityBundle\Entity\Achat $achat
     *
     * @return AchatDepartement
     */
    public function setAchat(\eProcess\EntityBundle\Entity\Achat $achat = null)
    {
        $this->achat = $achat;
        if($this->achat){
            $this->achat->addAchatDepartement($this);
        }

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
     * Set demandeur
     *
     * @param string $demandeur
     *
     * @return AchatDepartement
     */
    public function setDemandeur($demandeur)
    {
        $this->demandeur = $demandeur;

        return $this;
    }

    /**
     * Get demandeur
     *
     * @return string
     */
    public function getDemandeur()
    {
        return $this->demandeur;
    }

    /**
     * Set provision
     *
     * @param \eProcess\EntityBundle\Entity\Provision $provision
     *
     * @return AchatDepartement
     */
    public function setProvision(\eProcess\EntityBundle\Entity\Provision $provision = null)
    {
        $this->provision = $provision;

        return $this;
    }

    /**
     * Get provision
     *
     * @return \eProcess\EntityBundle\Entity\Provision
     */
    public function getProvision()
    {
        return $this->provision;
    }

    /**
     * Set voyage
     *
     * @param \eProcess\EntityBundle\Entity\Voyage $voyage
     *
     * @return AchatDepartement
     */
    public function setVoyage(\eProcess\EntityBundle\Entity\Voyage $voyage = null)
    {
        $this->voyage = $voyage;

        return $this;
    }

    /**
     * Get voyage
     *
     * @return \eProcess\EntityBundle\Entity\Voyage
     */
    public function getVoyage()
    {
        return $this->voyage;
    }

}
