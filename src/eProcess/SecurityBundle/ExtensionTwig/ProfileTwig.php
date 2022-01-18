<?php

namespace eProcess\SecurityBundle\ExtensionTwig;

use eProcess\SecurityBundle\Constantes\TypeProfile;

class ProfileTwig   extends \Twig_Extension {
    
    
     public function getFunctions()
    {
        return array(
            'isADMIN' => new \Twig_Function_Method($this, 'isADMIN'),
            'isEBS' => new \Twig_Function_Method($this, 'isEBS'),
            'isRECEPTION' => new \Twig_Function_Method($this, 'isRECEPTION'),
            'isOPERATION' => new \Twig_Function_Method($this, 'isOPERATION'),
            'isFINANCE' => new \Twig_Function_Method($this, 'isFINANCE'),
            
        );
    }

    public function isADMIN($codeProfil)
    {
         return $codeProfil == TypeProfile::ADMIN;
    }
    
    public function isEBS($codeProfil)
    {
         return $codeProfil == TypeProfile::EBS;
    }
    
    public function isOPERATION($codeProfil)
    {
         return $codeProfil == TypeProfile::OPERATIONS;
    }
    
    public function isRECEPTION($codeProfil)
    {
         return $codeProfil == TypeProfile::RECEPTION;
    }
    
    public function isFINANCE($codeProfil)
    {
         return $codeProfil == TypeProfile::FINANCE;
    }
    
    
    public function getProfileApprobateurs() {
        
        
    }

    
    public function getName() {
       return 'profile_utilisateur' ;
    }

}
