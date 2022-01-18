<?php

/**
 * Description of DateManager
 *
 * @author 
 */

namespace eProcess\SecurityBundle\Services;

use DateTime;

class DateManager extends \Twig_Extension {

    public function getFunctions() {
        return array(
            'getNbreSeconde' => new \Twig_Function_Method($this, 'getNbreSeconde'),
            'getNbreJour' => new \Twig_Function_Method($this, 'getNbreJour'),
            'getDureeJoursAction' => new \Twig_Function_Method($this, 'getDureeJoursAction'),
            'getDureeAMJAction' => new \Twig_Function_Method($this, 'getDureeAMJAction'),
        );
    }

    public function getNbreSeconde(DateTime $date1, DateTime $date2) {
        $resultat = abs($date1->getTimestamp() - $date2->getTimestamp() );

        return $resultat;
    }
    public function getNbreJour(DateTime $date1, DateTime $date2) {
        $seconde = $this-> getNbreSeconde( $date1,  $date2);

        return round($seconde/(60*60*24)-1) ;
    }

    public function getDureeJoursAction(DateTime $date1, DateTime $date2=null) {
        !$date2 ? $date2 = new DateTime() : '' ;
        $diff = $date1->diff($date2);
        
        $jour = $diff->d;
        $heure = $diff->h;
        
        $resultat = $jour.' jour(s) '.$heure.' heure(s)' ;
        return $resultat;
    }

  

    public function getDureeAMJAction(DateTime $date1, DateTime $date2=null) {

        !$date2 ? $date2 = new DateTime() : '' ;
        $diff = $date1->diff($date2);
        
        $annee = $diff->y;
        $mois = $diff->m;
        $jour = $diff->d;
        
        
        $resultat = $annee . 'an(s) ' . $mois . 'mois ' . $jour . 'jour(s) ' ;
        return $resultat ;
    }

    public function getName() {
        
    }

}
