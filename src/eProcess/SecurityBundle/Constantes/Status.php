<?php

namespace eProcess\SecurityBundle\Constantes;

/**
 * Description of Status
 *
 * @author thomas
 */
class Status {
    
    const INACTIF = 0 ;
    
    const ACTIF = 1;
        
    const SUPPRIME = 2;
    
    const BLOCKE = 3;
    
    const SUPPRIME_VRAIE = 10;
    
    public static function getStatus() {
        $array[Status::INACTIF] = 'INACTIF';
        $array[Status::ACTIF] = 'ACTIF';
        $array[Status::SUPPRIME] = 'SUPPRIME';
        $array[Status::BLOCKE] = 'BLOCKE';
    }    
    
     public static function readStatus($etat) {
        $etats = Status::getStatus();
        foreach ($etats as $key => $value){
            if ($key == $etat) {
                return $value;
            }
        }
         return 'INCONNU';
    }
}
