<?php

namespace eProcess\EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transfert
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="eProcess\EntityBundle\Entity\TransfertTable")
 */
class Transfert
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="etape", type="integer")
     */
    private $etape;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=250, nullable=true)
     */
    private $description;

    
    /**
     * @var boolean
     *
     * @ORM\Column(name="isActif", type="boolean")
     */
    private $isActif;

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
    * @ORM\ManyToOne(targetEntity="Utilisateur", inversedBy="expeditions")
    * @ORM\JoinColumn(name="envoyeur_id", referencedColumnName="id")
    */
    private $envoyeur;

    /**
    * @ORM\ManyToOne(targetEntity="Utilisateur", inversedBy="receptions")
    * @ORM\JoinColumn(name="recepteur_id", referencedColumnName="id", nullable=true)
    */
    private $recepteur;
    
    /**
    * @ORM\OneToMany(targetEntity="TransfertFacture" , mappedBy="transfert", cascade={"persist","remove"})
    */
    private $transfertFactures;
    
    /**
    * Constructor
    */
    public function __construct()
    {
    
        
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
     * Set etape
     *
     * @param integer $etape
     *
     * @return Transfert
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
     * Set description
     *
     * @param string $description
     *
     * @return Transfert
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
     * @return Transfert
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
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Transfert
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
     * @return Transfert
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
     * Set departement
     *
     * @param \eProcess\EntityBundle\Entity\Departement $departement
     *
     * @return Transfert
     */
    public function setDepartement(\eProcess\EntityBundle\Entity\Departement $departement = null)
    {
        $this->departement = $departement;

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
     * Set envoyeur
     *
     * @param \eProcess\EntityBundle\Entity\Utilisateur $envoyeur
     *
     * @return Transfert
     */
    public function setEnvoyeur(\eProcess\EntityBundle\Entity\Utilisateur $envoyeur = null)
    {
        $this->envoyeur = $envoyeur;
        if($envoyeur){
            $envoyeur->addExpedition($this) ;
        }

        return $this;
    }

    /**
     * Get envoyeur
     *
     * @return \eProcess\EntityBundle\Entity\Utilisateur
     */
    public function getEnvoyeur()
    {
        return $this->envoyeur;
    }

    /**
     * Set recepteur
     *
     * @param \eProcess\EntityBundle\Entity\Utilisateur $recepteur
     *
     * @return Transfert
     */
    public function setRecepteur(\eProcess\EntityBundle\Entity\Utilisateur $recepteur = null)
    {
        $this->recepteur = $recepteur;
        
        if($recepteur){
            $recepteur->addReception($this) ;
        }
        
        return $this;
    }

    /**
     * Get recepteur
     *
     * @return \eProcess\EntityBundle\Entity\Utilisateur
     */
    public function getRecepteur()
    {
        return $this->recepteur;
    }

    
    /**
     * Add transfertFacture
     *
     * @param \eProcess\EntityBundle\Entity\TransfertFacture $transfertFacture
     *
     * @return Transfert
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
}
