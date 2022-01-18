<?php

namespace eProcess\SecurityBundle\ExtensionTwig;


use eProcess\SecurityBundle\Constantes\TypeFichier ;

class FichierTwig   extends \Twig_Extension {
    
     public function getFunctions()
    {
        return array(
            'ficDemande' => new \Twig_Function_Method($this, 'ficDemande'),
            'ficFacture' => new \Twig_Function_Method($this, 'ficFacture'),
            'ficLivraison' => new \Twig_Function_Method($this, 'ficLivraison'),
            'ficBonCommande' => new \Twig_Function_Method($this, 'ficBonCommande'),
            'ficApprobationBudget' => new \Twig_Function_Method($this, 'ficApprobationBudget'),
            'ficApprobationHier' => new \Twig_Function_Method($this, 'ficApprobationHier'),
            'ficContrat' => new \Twig_Function_Method($this, 'ficContrat'),
            'ficProformat' => new \Twig_Function_Method($this, 'ficProformat'),
            'ficExoneration' => new \Twig_Function_Method($this, 'ficExoneration'),
            'ficListFournisseur' => new \Twig_Function_Method($this, 'ficListFournisseur'),
            'ficAutre' => new \Twig_Function_Method($this, 'ficAutre'),
            'getTypeFichier' => new \Twig_Function_Method($this, 'getTypeFichier'),
            'readTypeFichier' => new \Twig_Function_Method($this, 'readTypeFichier'),
        );
    }

    public function ficDemande()
    {
         return TypeFichier::Demande;
    }

    public function ficFacture()
    {
         return TypeFichier::Facture;
    }

    public function ficBonCommande()
    {
         return TypeFichier::BonCommande;
    }

    public function ficApprobationBudget()
    {
         return TypeFichier::ApprobationBudget;
    }
    
    public function ficApprobationHier()
    {
         return TypeFichier::ApprobationHier;
    }

    public function ficContrat()
    {
         return TypeFichier::Contrat;
    }

    public function ficProformat()
    {
         return TypeFichier::Proformat;
    }
    
    public function ficExoneration()
    {
         return TypeFichier::Exoneration;
    }

    public function ficLivraison()
    {
         return TypeFichier::Livraison;
    }
    public function ficListFournisseur()
    {
         return TypeFichier::ListeFournisseur;
    }
    
    public function ficAutre()
    {
         return TypeFichier::Autres;
    }

   
    function getTypeFichier(){
        return TypeFichier::getTypeFichier();   
    }
    
    function readTypeFichier($type){
        return TypeFichier::readFichier($type);   
    }
    
   


    function getName() {
        return 'type_fichier' ;
    }
    
   
}
