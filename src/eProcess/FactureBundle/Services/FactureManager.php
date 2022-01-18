<?php

/**
 * Description of DroitManager
 *
 * @author 
 */

namespace eProcess\FactureBundle\Services;

use Doctrine\ORM\EntityManager ;
use eProcess\SecurityBundle\Constantes\EtapeFacture;
use eProcess\SecurityBundle\Constantes\TypeProfile ;
use eProcess\SecurityBundle\Services\DateManager ;

class FactureManager {

    private $em;
    private $bundle;
    private $entity;
    private $dateManager ;





    public function __construct( EntityManager $manager, DateManager $dm) {
        $this->em = $manager;
        $this->dateManager = $dm ;
        $this->bundle = 'eProcessEntityBundle:';
        $this->entity= 'Facture';
    }

    
    /**
     * getNbreFactureRetard : fonction de recuperation du nombre de facture
     *  en retard de traietment chez le profile qui s'est connecté
     * 
     * @param string $codeProfile
     * @return int
     */
    public function getNbreFactureRetard($codeProfile) {
        
        $listeFactureRetard = $this->getNbreFactureRetard($codeProfile) ;
        
        return count($listeFactureRetard);
    }
    
    /**
     * getFactureRetard : recuperation des facture en retard de traitement
     * 
     */
    public function getFactureRetard($codeProfile = TypeProfile::EBS) {
        $factureRetard = array() ;
        // recuperation des facture qui sont en traitement chez le profil
        $factureTraitement = $this->getFactureTraitement($codeProfile);
        
        
        var_dump($codeProfile);exit;
//        var_dump(count($factureTraitement));exit;
        
        foreach ($factureTraitement as $facture) {
            
            // recuperation des factures dont la durrée de traitement depuis la reception dépasse 5jours
            $duree = $this->dateManager->getNbreJour($facture->getDateArrive(), new DateTime()) ;
            
            if($duree >= 5){
                $factureRetard[] = $facture ;
            }
        }
        
        return $factureRetard;
    }   
    
    
    
    public function getFactureTraitement($codeProfile = TypeProfile::DEFAUT) {
        
        // recuperation des differents etats de la facture en fonction du profil qui est connecté
        $arrayEtape = array() ;
        
        if($codeProfile == TypeProfile::RECEPTION || $codeProfile == TypeProfile::DEFAUT ){
            $arrayEtape[] = EtapeFacture::FCT_ARRIVE_RECEPTION ;
            $arrayEtape[] = EtapeFacture::FCT_ENVOYE_EBS ;
        }
        
        if($codeProfile == TypeProfile::EBS || $codeProfile == TypeProfile::DEFAUT){
            $arrayEtape[] = EtapeFacture::FCT_ARRIVE_EBS;
            $arrayEtape[] = EtapeFacture::FCT_PROBLEME_EBS ;
            $arrayEtape[] = EtapeFacture::FCT_ENVOYE_OPS ;
            $arrayEtape[] = EtapeFacture::FCT_REJETE_OPS ;
        }
        
        if($codeProfile == TypeProfile::OPERATIONS || $codeProfile == TypeProfile::DEFAUT){
            $arrayEtape[] = EtapeFacture::FCT_ARRIVE_OPS;
            $arrayEtape[] = EtapeFacture::FCT_PROBLEME_OPS ;
            $arrayEtape[] = EtapeFacture::FCT_VALIDE_OPS ;
        }
        
        // recuperation des factures dont l'etape est dans la liste des etape en parametre
        $nbreFcture = $this->getFactureArrayEtape($arrayEtape) ;
        
        
        return $nbreFcture;
    }
    
    
    public function getFactureArrayEtape($arrayEtape= array()) {
        $nbreFcture = $this->em->getTable($this->bundle.$this->entity)
                ->getFactureArrayEtape($arrayEtape);
        
        return $nbreFcture;
    }


}
