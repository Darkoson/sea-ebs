<?php

namespace eProcess\SecurityBundle\Constantes;

/**
 * Description of TypeDate
 *
 * @author thomas
 */
class TypeDate {
    
    
    const DATE_CREATION = 1;
    const DATE_ARRIVE = 2;
    const DATE_AUTORISE = 3;
 
    const DATE_TRAITEMENT = 4;
    
    public static function getTypeDateBon(){
        $types[TypeDate::DATE_CREATION] = 'DATE CREATION';
        $types[TypeDate::DATE_AUTORISE] = 'DATE AUTORISATION';
        
        return $types;
    }
    
    public static function getTypeDateFacture(){
        $types[TypeDate::DATE_ARRIVE] = 'DATE ARRIVE';
        $types[TypeDate::DATE_CREATION] = 'DATE ENREGISTREMENT';
        $types[TypeDate::DATE_TRAITEMENT] = 'DATE TRAITEMENT';
        $types[TypeDate::DATE_AUTORISE] = 'DATE VALIDATION';
        
        return $types;
    }
    
}
