<?php

namespace eProcess\SecurityBundle\Constantes;

/**
 * Description of EtapeBonCommande
 *
 * @author thomas
 */
class EtapeBon {

    const BON_CHARGE = 0;
    const BON_EMIS = 1;
    const BON_VERIFIE = 2;
    const BON_MODIFIE = 3;
    const BON_SOUMIS_AUTORISATION = 4;
    const BON_APPROUVE = 5;
    const BON_REJETE = 6;
    const BON_ATTENE_ENVOI = 7;
    const BON_CHEZ_FOUNISSEUR = 8;
    const BON_ARRIVE_FACTURE = 9;
    const BON_REGLE_FACTURE = 10;
    const BON_SUPPRIME = 20;

    public static function getEtape() {

        $liste = array(
            EtapeBon::BON_CHARGE => 'BONS NOUVELLEMENT CHARGES',
            EtapeBon::BON_EMIS => 'BONS NOUVELLEMENT EDITES',
            EtapeBon::BON_VERIFIE => 'BONS VERIFIES APRES EDITION',
            EtapeBon::BON_MODIFIE => 'BONS NOUVELLEMENT MODIFIES',
            EtapeBon::BON_SOUMIS_AUTORISATION => 'BONS EN ATTENTE D\'APPROBATION',
            EtapeBon::BON_APPROUVE => 'BONS APPROUVES',
            EtapeBon::BON_REJETE => 'BONS REJETES',
            EtapeBon::BON_ATTENE_ENVOI => 'BONS EN ATTENTE D\'ENVOIS AU FOURNISSEUR',
            EtapeBon::BON_CHEZ_FOUNISSEUR => 'BONS CHEZ FOURNISSEUR',
            EtapeBon::BON_ARRIVE_FACTURE => 'BONS DONT FACTURE EN TRAITEMENT ',
            EtapeBon::BON_REGLE_FACTURE => 'BONS REGLES',
            EtapeBon::BON_SUPPRIME => 'BONS SUPPRIMES'
        );

        return $liste;
    }
    
    
    public static function readEtape($etape) {
        $liste = EtapeBon::getEtape() ;
        $nom = 'INCONU' ;
        foreach ($liste as $code => $name) {
            if($etape == $code){
                $nom = $name ;
            }
        }
        
        return $nom;
    }

}
