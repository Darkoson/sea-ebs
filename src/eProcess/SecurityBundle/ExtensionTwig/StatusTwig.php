<?php

namespace eProcess\SecurityBundle\ExtensionTwig;

use eProcess\SecurityBundle\Constantes\Status;

class StatusTwig   extends \Twig_Extension {
    
     public function getFunctions()
    {
        return array(
            'isActivated' => new \Twig_Function_Method($this, 'isActivated'),
            'isInactive' => new \Twig_Function_Method($this, 'isInactive'),
            'isDeleted' => new \Twig_Function_Method($this, 'isDeleted'),
            'isReallyDeleted' => new \Twig_Function_Method($this, 'isReallyDeleted'),
            'isBlocked' => new \Twig_Function_Method($this, 'isBlocked'),
            'statusActivated' => new \Twig_Function_Method($this, 'statusActivated'),
            'statusInactive' => new \Twig_Function_Method($this, 'statusInactive'),
            'statusDeleted' => new \Twig_Function_Method($this, 'statusDeleted'),
            'statusReallyDeleted' => new \Twig_Function_Method($this, 'statusReallyDeleted'),
            'statusBlocked' => new \Twig_Function_Method($this, 'statusBlocked'),
            'readStatus' => new \Twig_Function_Method($this, 'readStatus'),
        );
    }
    
    
    
    

    public function isActivated($etat)
    {
         return Status::ACTIF == $etat;
    }
    public function statusActivated()
    {
         return Status::ACTIF;
    }
    

    public function isInactive( $etat)
    {
         return Status::INACTIF == $etat;
    }
    public function statusInactive()
    {
         return Status::INACTIF ;
    }
    
    
    public function isDeleted($etat)
    {
        return Status::SUPPRIME == $etat;
    }
    
    public function statusDeleted()
    {
        return Status::SUPPRIME;
    }
    
    
    public function statusReallyDeleted()
    {
        return Status::SUPPRIME_VRAIE;
    }
    
    
    public function isBlocked($etat)
    {
        return Status::BLOCKE == $etat;
    }
    
    public function statusBlocked()
    {
        return Status::BLOCKE;
    }
    
    function readStatus($etat){
        return Status::readStatus($etat);
    }


    function getName() {
        return 'etat_objet' ;
    }
    
   
}
