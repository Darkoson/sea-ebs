<?php

namespace eProcess\SecurityBundle\Constantes;

/**
 * Description of TypeFichier
 *
 * @author thomas
 */
class TypeFichier {
    
    const BonCommande = 1;
   
    const Facture = 2;
 
    const Contrat = 3;
    
    const Proformat = 4;
    
    const ApprobationHier = 5;
    
    const ApprobationBudget = 6;
    
    const Demande = 7;
    
    const Livraison = 8;
    
    const Exoneration = 9;
    
    const ListeFournisseur = 10;
    
    const ListeBonCommande = 11;
    
    const Autres = 0;
    
    
    public static function getTypeFichier(){
        $types[TypeFichier::Demande] = 'Demande';
        $types[TypeFichier::BonCommande] = 'Bon de Commande';
        $types[TypeFichier::Facture] = 'Facture';
        $types[TypeFichier::Contrat] = 'Contrat';
        $types[TypeFichier::Proformat] = 'Proformat';
        $types[TypeFichier::ApprobationHier] = 'Approbation Hierarchique';
        $types[TypeFichier::ApprobationBudget] = 'Approbation Budget';
        $types[TypeFichier::Exoneration] = 'Exoneration';
        $types[TypeFichier::Livraison] = 'Livraison';
        $types[TypeFichier::ListeFournisseur] = 'Liste des Fournisseurs';
        $types[TypeFichier::ListeBonCommande] = 'Liste des Bons de Bommandes';
        $types[TypeFichier::Autres] = 'Autres';
        
        return $types;
    }
    
    public static function getRepertoiresFichier(){
        $types[TypeFichier::Demande] = 'Demande';
        $types[TypeFichier::BonCommande] = 'BonComande';
        $types[TypeFichier::Facture] = 'Facture';
        $types[TypeFichier::Contrat] = 'Contrat';
        $types[TypeFichier::Proformat] = 'Proformat';
        $types[TypeFichier::ApprobationBudget] = 'ApprobationBudget';
        $types[TypeFichier::ApprobationHier] = 'ApprobationHierarchique';
        $types[TypeFichier::Livraison] = 'Livraison';
        $types[TypeFichier::Exoneration] = 'Exoneration';
        $types[TypeFichier::ListeFournisseur] = 'Chargement/Fournisseurs';
        $types[TypeFichier::ListeBonCommande] = 'Chargement/BonCommandes';
        $types[TypeFichier::Autres] = 'Autres';
        
        return $types;
    }
    
    public static function getInitialeFichier(){
        $types[TypeFichier::Demande] = 'dmd_';
        $types[TypeFichier::BonCommande] = 'bc_';
        $types[TypeFichier::Facture] = 'fct_';
        $types[TypeFichier::Contrat] = 'contrat_';
        $types[TypeFichier::Proformat] = 'prof_';
        $types[TypeFichier::Livraison] = 'livr_';
        $types[TypeFichier::ApprobationBudget] = 'approb_bud_';
        $types[TypeFichier::ApprobationHier] = 'approb_hier_';
        $types[TypeFichier::Exoneration] = 'exo_';
        $types[TypeFichier::ListeFournisseur] = 'four_';
        $types[TypeFichier::ListeBonCommande] = 'list_bc_';
        $types[TypeFichier::Autres] = 'autre_';
        
        return $types;
    }
    
    
    public static function readFichier($type) {
        $liste = TypeFichier::getTypeFichier();
        foreach ($liste as $key => $value) {
            if ($type == $key) {
                return $value;
            }
        }
        return 'Inconnu';
    }
    public static function readRepertoireFichier($type) {
        $liste = TypeFichier::getRepertoiresFichier();
        foreach ($liste as $key => $value) {
            if ($type == $key) {
                return $value;
            }
        }
        return 'INCONNU';
    }
    
    public static function readInitialeFichier($type) {
        $liste = TypeFichier::getInitialeFichier();
        foreach ($liste as $key => $value) {
            if ($type == $key) {
                return $value;
            }
        }
        return 'inconnu_';
    }
    
    
    
}
