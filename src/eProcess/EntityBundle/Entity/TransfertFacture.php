<?php

namespace eProcess\EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transfert
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="eProcess\EntityBundle\Entity\TransfertFactureTable")
 */
class TransfertFacture
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
     * @ORM\Column(name="isActif", type="integer")
     */
    private $isActif;

    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=250, nullable=true)
     */
    private $description;

    
    /**
    * @ORM\ManyToOne(targetEntity="Facture" , inversedBy="transfertFactures" )
    */
    private $facture;
    
    
    /**
    * @ORM\ManyToOne(targetEntity="Transfert" , inversedBy="transfertFactures" )
    */
    private $transfert;
    
    /**
    * Constructor
    */
    public function __construct()
    {
        $this->isActif = false ;
        
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
     * Set isActif
     *
     * @param integer $isActif
     *
     * @return TransfertFacture
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
     * Set description
     *
     * @param string $description
     *
     * @return TransfertFacture
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
     * Set facture
     *
     * @param \eProcess\EntityBundle\Entity\Facture $facture
     *
     * @return TransfertFacture
     */
    public function setFacture(\eProcess\EntityBundle\Entity\Facture $facture)
    {
        $this->facture = $facture;
        if($facture){
            $facture->addTransfertFacture($this) ;
        }

        return $this;
    }

    /**
     * Get facture
     *
     * @return \eProcess\EntityBundle\Entity\Facture
     */
    public function getFacture()
    {
        return $this->facture;
    }

    /**
     * Set transfert
     *
     * @param \eProcess\EntityBundle\Entity\Transfert $transfert
     *
     * @return TransfertFacture
     */
    public function setTransfert(\eProcess\EntityBundle\Entity\Transfert $transfert = null)
    {
        $this->transfert = $transfert;
        if($transfert){
            $transfert->addTransfertFacture($this) ;
        }

        return $this;
    }

    /**
     * Get transfert
     *
     * @return \eProcess\EntityBundle\Entity\Transfert
     */
    public function getTransfert()
    {
        return $this->transfert;
    }
}
