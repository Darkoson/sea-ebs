<?php

namespace eProcess\SecurityBundle\Constantes;

/**
 * Description of EtapeAchat
 *
 * @author thomas
 */
class EtapeAchat {

    
    
    const ACHAT_SUPPRIME = 20;

    
    public static function getEtapeAchat() {

        $liste = array(
            EtapeAchat::ACHAT_SUPPRIME => "ACHAT SUPPRIME",
        );

        return $liste;
    }
    
    
    
    /**
     * readEtape : 
     * 
     * @param type $etape
     * @return type
     */
    public static function readEtape($etape) {
        $liste = EtapeAchat::getEtapeAchat() ;
        $nom = 'INCONU' ;
        foreach ($liste as $code => $name) {
            if($etape == $code){
                $nom = $name ;
            }
        }
        
        return $nom;
    }

}
