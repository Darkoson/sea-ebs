<?php

namespace eProcess\SecurityBundle\ExtensionTwig;


use eProcess\SecurityBundle\Constantes\EtapeTranfert ;
use eProcess\TransfertBundle\Services\TransfertManager ;

class TransfertFactureTwig   extends \Twig_Extension {
    
    private $transfertManager ;
    
    
    public function __construct(TransfertManager $tm) {
        $this->transfertManager = $tm ;
        
    }
    
     public function getFunctions()
    {
        return array(
            'fromEbsToOps' => new \Twig_Function_Method($this, 'fromEbsToOps'),
            'fromEbsToRecept' => new \Twig_Function_Method($this, 'fromEbsToRecept'),
            'fromReceptToEbs' => new \Twig_Function_Method($this, 'fromReceptToEbs'),
            'fromOpsToEbs' => new \Twig_Function_Method($this, 'fromOpsToEbs'),
            'readEtapeTransfert' => new \Twig_Function_Method($this, 'readEtapeTransfert'),
            'getEtapeTransfert' => new \Twig_Function_Method($this, 'getEtapeTransfert'),
            'nbreTransfertArrive' => new \Twig_Function_Method($this, 'nbreTransfertArrive'),
            'nbreTransfertEncoursEnvoi' => new \Twig_Function_Method($this, 'nbreTransfertEncoursEnvoi'),
        );
    }

    public function fromEbsToOps()
    {
         return EtapeTranfert::EBS_OPS;
    }
    public function fromEbsToRecept()
    {
         return EtapeTranfert::EBS_RECEPT;
    }

    public function fromReceptToEbs()
    {
         return EtapeTranfert::RECEPT_EBS;
    }

    public function fromOpsToEbs()
    {
         return EtapeTranfert::OPS_EBS;
    }

   
    function getEtapeTransfert(){
        return EtapeTranfert::getEtapeTransfert();
        
    }
    
    function readEtapeTransfert($etape){
        return EtapeTranfert::readTransfert($etape);
    }
    
    
    
    function nbreTransfertArrive($codeProfile,$etapeTransfert = 0){
        return $this->transfertManager->getNbreTransfertAttenteReception($codeProfile,$etapeTransfert) ;
    }
    
    function nbreTransfertEncoursEnvoi($codeProfile,$etapeTransfert = 0){
        return $this->transfertManager->getNbreTransfertEncoursEnvoi($codeProfile,$etapeTransfert) ;
    }


    function getName() {
        return 'transfert_facture' ;
    }
    
   
}
