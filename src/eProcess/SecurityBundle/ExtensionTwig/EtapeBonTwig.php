<?php

namespace eProcess\SecurityBundle\ExtensionTwig;

use eProcess\SecurityBundle\Constantes\EtapeBon;

class EtapeBonTwig   extends \Twig_Extension {
    
     public function getFunctions()
    {
        return array(
            'bonCharge' => new \Twig_Function_Method($this, 'bonCharge'),
            'bonEmis' => new \Twig_Function_Method($this, 'bonEmis'),
            'bonModifie' => new \Twig_Function_Method($this, 'bonModifie'),
            'bonVerifie' => new \Twig_Function_Method($this, 'bonVerifie'),
            'bonSoumisAutor' => new \Twig_Function_Method($this, 'bonSoumisAutor'),
            'bonApprouve' => new \Twig_Function_Method($this, 'bonApprouve'),
            'bonRejete' => new \Twig_Function_Method($this, 'bonRejete'),
            'bonAttenteEnvoi' => new \Twig_Function_Method($this, 'bonAttenteEnvoi'),
            'bonEnvoiFournisseur' => new \Twig_Function_Method($this, 'bonEnvoiFournisseur'),
            'bonArriveFacture' => new \Twig_Function_Method($this, 'bonArriveFacture'),
            'bonRegleFacture' => new \Twig_Function_Method($this, 'bonRegleFacture'),
            'bonSupprime' => new \Twig_Function_Method($this, 'bonSupprime'),
            'getEtapeBon' => new \Twig_Function_Method($this, 'getEtapeBon'),
            'readEtapeBon' => new \Twig_Function_Method($this, 'readEtapeBon'),
        );
    }

    
    
     public function bonCharge() {
        return EtapeBon::BON_CHARGE;
    }
     public function bonEmis() {
        return EtapeBon::BON_EMIS;
    }
     public function bonModifie() {
        return EtapeBon::BON_MODIFIE;
    }
     public function bonVerifie() {
        return EtapeBon::BON_VERIFIE;
    }
    
    
     public function bonSupprime() {
        return EtapeBon::BON_SUPPRIME;
    }
    
    public function bonSoumisAutor() {
        return EtapeBon::BON_SOUMIS_AUTORISATION;
    }
    
    public function bonApprouve() {
        return EtapeBon::BON_APPROUVE;
    }
    
    public function bonRejete() {   
        return EtapeBon::BON_REJETE;
    }
    
    public function bonAttenteEnvoi() {
        return EtapeBon::BON_ATTENE_ENVOI;
    }
    
    public function bonEnvoiFournisseur() {
        return EtapeBon::BON_CHEZ_FOUNISSEUR;
    }
    
    public function bonArriveFacture() {
        return EtapeBon::BON_ARRIVE_FACTURE;
    }
    
    public function bonRegleFacture() {
        return EtapeBon::BON_REGLE_FACTURE;
    }
    
   
    
    
   public function getEtapeBon()
    {
         return EtapeBon::getEtape() ;
    }
    
   public function readEtapeBon($etape)
    {
         return EtapeBon::readEtape($etape) ;
    }


    function getName() {
        return 'etape_bon' ;
    }
    
   
}
