<?php

namespace eProcess\SecurityBundle\Constantes;

/**
 * Description of TypeDate
 *
 * @author thomas
 */
class TypeMenu {
    
    const HAUT = 1;
   
    const GAUCHE = 2;
 
    const PARENT = 1;
    
    const ENFANT = 2;
    
    public static function getNiveauMenu(){
        
        $types[TypeMenu::PARENT] = 'PARENT';
        
        $types[TypeMenu::ENFANT] = 'ENFANT';
        
        return $types;
    }
    
    
    public static function getTypesMenu() {
        
         $types[TypeMenu::HAUT ] = 'HAUT';
         
        $types[TypeMenu::GAUCHE] = 'GAUCHE';
        
        return $types;
    }
    
    
}
