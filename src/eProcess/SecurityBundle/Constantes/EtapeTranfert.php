<?php

namespace eProcess\SecurityBundle\Constantes;

/**
 * Description of EtapeTranfert
 *
 * @author thomas
 */
class EtapeTranfert {

    const AUCUN = 0;
    const RECEPT_EBS = 1;
    const EBS_RECEPT = 2;
    const EBS_OPS = 3;
    const OPS_EBS = 4;
    
    
    // ces constntes sont utilisées pour récuperer les transferts en fonction des envoyeurs ou récepteurs
    const ENVOYER = 1;
    const RECEVOIR = 2;
    
    


    public static function getEtapeTransfert() {

        $liste = array(
            EtapeTranfert::RECEPT_EBS => 'ENVOI:  RECEPTION ---> EBS',
            EtapeTranfert::EBS_RECEPT => 'ENVOI:  EBS ---> RECEPTION',
            EtapeTranfert::EBS_OPS => 'ENVOI:  EBS ---> OPS',
            EtapeTranfert::OPS_EBS => 'ENVOIS:  OPS ---> EBS'
        );

        return $liste;
    }
    
    
    
    
    public static function readTransfert($etape) {

        $liste = EtapeTranfert::getEtapeTransfert() ;
        $nom = 'INCONU' ;
        foreach ($liste as $code => $name) {
            if($etape == $code){
                $nom = $name ;
            }
        }
        
        return $nom;
    }

}
