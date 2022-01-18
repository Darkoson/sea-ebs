<?php

namespace eProcess\SecurityBundle\ExtensionTwig;

use eProcess\SecurityBundle\Constantes\EtapeFacture;
use eProcess\FactureBundle\Services\FactureManager ;

class EtapeFactureTwig   extends \Twig_Extension {
    
    private $factureManaget; 
    
    public function __construct(FactureManager $fm) {
        $this->factureManaget  = $fm ;
    }
    
    
     public function getFunctions()
    {
        return array(
            'factureReception' => new \Twig_Function_Method($this, 'factureReception'),
            'factureEnvoyerEBS' => new \Twig_Function_Method($this, 'factureEnvoyerEBS'),
            'factureArriveEBS' => new \Twig_Function_Method($this, 'factureArriveEBS'),
            'factureRejeteEBS' => new \Twig_Function_Method($this, 'factureRejeteEBS'),
            'factureProblemeEBS' => new \Twig_Function_Method($this, 'factureProblemeEBS'),
            'factureTraiteEBS' => new \Twig_Function_Method($this, 'factureTraiteEBS'),
            'factureEnvoyerOPS' => new \Twig_Function_Method($this, 'factureEnvoyerOPS'),
            'factureArriveOPS' => new \Twig_Function_Method($this, 'factureArriveOPS'),
            'factureProblemeOPS' => new \Twig_Function_Method($this, 'factureProblemeOPS'),
            'factureValideOPS' => new \Twig_Function_Method($this, 'factureValideOPS'),
            'facturePayeeOPS' => new \Twig_Function_Method($this, 'facturePayeeOPS'),
            'getEtapeFacture' => new \Twig_Function_Method($this, 'getEtapeFacture'),
            'readEtapeFacture' => new \Twig_Function_Method($this, 'readEtapeFacture'),
            'getNbreFactureRetard' => new \Twig_Function_Method($this, 'getNbreFactureRetard'),
        );
    }

    
    
     public function factureReception() {
        return EtapeFacture::FCT_ARRIVE_RECEPTION;
    }
    
    public function factureEnvoyerEBS() {
        return EtapeFacture::FCT_ENVOYE_EBS;
    }
    
    public function factureArriveEBS() {
        return EtapeFacture::FCT_ARRIVE_EBS;
    }
    
    public function factureRejeteEBS() {
        return EtapeFacture::FCT_REJETE_EBS;
    }
    
    public function factureProblemeEBS() {
        return EtapeFacture::FCT_PROBLEME_EBS;
    }
    
    public function factureTraiteEBS() {
        return EtapeFacture::FCT_TRAITE_EBS;
    }
    
    public function factureEnvoyerOPS() {
        return EtapeFacture::FCT_ENVOYE_OPS;
    }
    
    public function factureArriveOPS() {
        return EtapeFacture::FCT_ARRIVE_OPS;
    }
    
    public function factureProblemeOPS() {
        return EtapeFacture::FCT_PROBLEME_OPS;
    }
    
    public function factureValideOPS() {
        return EtapeFacture::FCT_VALIDE_OPS;
    }
    
    public function facturePayeeOPS() {
        return EtapeFacture::FCT_PAYEE;
    }
    
    
   public function getEtapeFacture()
    {
         return EtapeFacture::getEtapeFacture() ;
    }
   public function readEtapeFacture($etape)
    {
         return EtapeFacture::readEtape($etape) ;
    }
    
   public function getNbreFactureRetard($codeProfile) 
    {
         return $this->factureManaget->getNbreFactureRetard($codeProfile);
    }


    function getName() {
        return 'etape_facture' ;
    }
    
   
}
