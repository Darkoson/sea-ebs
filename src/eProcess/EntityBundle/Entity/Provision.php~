<?php

namespace eProcess\EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Provision
 * 
 * @ORM\Table()
 * @ORM\Entity()
 */
class Provision 
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
    * @ORM\OneToOne(targetEntity="AchatDepartement", inversedBy="provision", cascade={"persist","remove"})
    */
    private $achatDepartement;
    
    /**
     * @var array
     *
     * @ORM\Column(name="infoArticles", type="json_array")
     */
    private $infoArticles;

    
    
    
    /**
    * Constructor
    */
    public function __construct($infoArticles = array())
    {
        $this->infoArticles = $infoArticles ;
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

    
    
    public function getRemise() {
        $remise = "0" ;
        
        if(count($this->infoArticles) && key_exists('remise', $this->infoArticles) ){
            $remise = $this->infoArticles['remise']  ;
        }
        
        return $remise;
    }
    
    public function getTotale() {
        $total = "0" ;
        
        if(count($this->infoArticles) && key_exists('total', $this->infoArticles) ){
            $total = $this->infoArticles['total'];
        }
        
        return $total;
    }
    
    
    public function getTaxe() {
        $taxe = 0 ;
        
        if(count($this->infoArticles) && key_exists('taxe', $this->infoArticles) ){
            $taxe = trim($this->infoArticles['taxe']);
        }
        
        return $taxe;
    }
    
    public function getArticles() {
        $listeArticles = array() ;
        
        if(count($this->infoArticles) && key_exists('articles', $this->infoArticles) ){
            $liste = (array) $this->infoArticles['articles'] ;
            foreach ($liste as $articles) {
                $nombre = intval(str_replace (' ',  '',$articles["pu"])) * intval($articles["nbre"]) ;
                $articles["total"] =   number_format($nombre, 2, '.', ' ');
                array_push($listeArticles, $articles);
            }
        }
        
        return $listeArticles;
    }
    
    

    /**
     * Set infoArticles
     *
     * @param array $infoArticles
     *
     * @return Provision
     */
    public function setInfoArticles( array $infoArticles)
    {
        $this->infoArticles = $infoArticles;

        return $this;
    }

    /**
     * Get infoArticles
     *
     * @return array
     */
    public function getInfoArticles()
    {
        $info= $this->infoArticles ;
        if(array_key_exists('total', $info)){
            $info['total'] = number_format(floatval( str_replace (' ',  '',$info['total'])), 2, '.', ' ');
        }
        
        $info['articles'] = $this->getArticles() ;
        return $info;
    }

    /**
     * Set achatDepartement
     *
     * @param \eProcess\EntityBundle\Entity\AchatDepartement $achatDepartement
     *
     * @return Provision
     */
    public function setAchatDepartement(\eProcess\EntityBundle\Entity\AchatDepartement $achatDepartement = null)
    {
        $this->achatDepartement = $achatDepartement;

        return $this;
    }

    /**
     * Get achatDepartement
     *
     * @return \eProcess\EntityBundle\Entity\AchatDepartement
     */
    public function getAchatDepartement()
    {
        return $this->achatDepartement;
    }
}
