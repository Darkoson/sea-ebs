<?php

namespace eProcess\SecurityBundle\Constantes;

/**
 * Description of EtapeFacture
 *
 * @author thomas
 */
class EtapeFacture {

    const FCT_ARRIVE_RECEPTION = 1;
    const FCT_ENVOYE_EBS = 2;
    const FCT_ARRIVE_EBS =3;
    const FCT_REJETE_EBS =4;
    
    const FCT_TRAITE_EBS = 5;
    const FCT_PROBLEME_EBS = 6;
    const FCT_PROBLEME_OPS = 7;
    const FCT_ENVOYE_OPS = 8;
    const FCT_ARRIVE_OPS = 9;
    const FCT_REJETE_OPS = 10;
    const FCT_VALIDE_OPS = 11;
    const FCT_PAYEE = 12;
    
    const FCT_SUPPRIME = 20;

    public static function getEtapeFacture() {

        $liste = array(
            EtapeFacture::FCT_ARRIVE_RECEPTION => "ARRIVE A LA RECEPTION",
            EtapeFacture::FCT_ENVOYE_EBS => "ENCOURS D\'ENVOI A EBS",
            EtapeFacture::FCT_ARRIVE_EBS => "ARRIVE A EBS",
            EtapeFacture::FCT_REJETE_EBS => "FACTURES REJETES PAR EBS",
            EtapeFacture::FCT_TRAITE_EBS => "FACTURES  TRAITEES A EBS ",
            EtapeFacture::FCT_PROBLEME_EBS => "FACTURES A PROBLEME QUI SONT A EBS",
            EtapeFacture::FCT_PROBLEME_OPS => "FACTURES A PROBLEME QUI SONT AUX OPS",
            EtapeFacture::FCT_ENVOYE_OPS => "ENVOI AUX OPS ENCOURS",
            EtapeFacture::FCT_ARRIVE_OPS => "ARRIVE AUX OPS",
            EtapeFacture::FCT_REJETE_OPS => "FACTURES REJETE AUX OPS ET ARRIVE A EBS",
            EtapeFacture::FCT_VALIDE_OPS => "VALIDEE POUR PAIEMENT AUX OPS",
            EtapeFacture::FCT_PAYEE => "FACTURE PAYEE AUX OPS",
        );

        return $liste;
    }
    
    
    public static function readEtape($etape) {
        $liste = EtapeFacture::getEtapeFacture() ;
        $nom = 'INCONU' ;
        foreach ($liste as $code => $name) {
            if($etape == $code){
                $nom = $name ;
            }
        }
        
        return $nom;
    }

}
