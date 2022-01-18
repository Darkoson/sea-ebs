<?php

namespace eProcess\EntityBundle\Entity;

use eProcess\SecurityBundle\Constantes\Status;
use Doctrine\ORM\Mapping as ORM;


/**
 * ApprobationBon
 * @ORM\Table()
 * @ORM\Entity()
 */

class ApprobationBon {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
    * @ORM\ManyToOne(targetEntity="Utilisateur", inversedBy="approbationBons", cascade={"persist"})
    * @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
    */
    private $utilisateur;
    
    /**
    * @ORM\ManyToOne(targetEntity="BonCommande", inversedBy="approbationBons", cascade={"persist"})
    * @ORM\JoinColumn(name="bon_commande_id", referencedColumnName="id")
    */
    private $bonCommande;
    
    /**
     * @var string
     *
     * @ORM\Column(name="motif", type="string", length=255, nullable=true)
     */
    private $motif;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateApprobation", type="datetime")
     */
    private $dateApprobation;

    
    public function __construct() {
        $this->dateApprobation = new \DateTime();
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
     * Set motif
     *
     * @param string $motif
     *
     * @return ApprobationBon
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
     * Set dateApprobation
     *
     * @param \DateTime $dateApprobation
     *
     * @return ApprobationBon
     */
    public function setDateApprobation($dateApprobation)
    {
        $this->dateApprobation = $dateApprobation;

        return $this;
    }

    /**
     * Get dateApprobation
     *
     * @return \DateTime
     */
    public function getDateApprobation()
    {
        return $this->dateApprobation;
    }

    /**
     * Set utilisateur
     *
     * @param \eProcess\EntityBundle\Entity\Utilisateur $utilisateur
     *
     * @return ApprobationBon
     */
    public function setUtilisateur(\eProcess\EntityBundle\Entity\Utilisateur $utilisateur)
    {
        $this->utilisateur = $utilisateur;
        if($utilisateur){
            $utilisateur->addApprobationBon($this);
        }

        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return \eProcess\EntityBundle\Entity\Utilisateur
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }

    /**
     * Set bonCommande
     *
     * @param \eProcess\EntityBundle\Entity\BonCommande $bonCommande
     *
     * @return ApprobationBon
     */
    public function setBonCommande(\eProcess\EntityBundle\Entity\BonCommande $bonCommande)
    {
        $this->bonCommande = $bonCommande;
        if($bonCommande){
            $bonCommande->addApprobationBon($this);
        }
        return $this;
    }

    /**
     * Get bonCommande
     *
     * @return \eProcess\EntityBundle\Entity\BonCommande
     */
    public function getBonCommande()
    {
        return $this->bonCommande;
    }
}
