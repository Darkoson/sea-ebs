<?php

namespace eProcess\SecurityBundle\Constantes;

/**
 * Description of TypeTaxe
 *
 * @author thomas
 */
class TypeTaxe {
    
    const HT = 1 ;
    
    const TVA = 2 ;
    
    public static function getTypeTaxe(){
        $types[TypeTaxe::HT] = 'HT';
        $types[TypeTaxe::TVA] = 'TVA';
        
        return $types;
    }
    public static function readTaxe($codeTaxe){
        $nom = 'INCONNU';
        foreach (TypeTaxe::getTypeTaxe() as $key => $value) {
            if($key == $codeTaxe){
               $nom =  $value ;
            }
        }
        return $nom;
    }
    
}
