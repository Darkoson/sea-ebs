<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace eProcess\SecurityBundle\Constantes;

/**
 * Description of TypeDonnee
 *
 * @author edmond
 */
class SessionKeys {

    const PROFIL = 'nomProfil';
    const CODE_PROFIL = 'code_profil';
    const ID_PROFIL = 'id_profil';
    const ID_ACTIONS = 'id_actions';
    const NAME_ACTIONS = 'name_actions';
    const LOCALE = 'locale';
    
    const USER_DATA = 'data_user';
    const USER = 'names';
//    const USER_NOM = 'nom';
//    const USER_PRENOM = 'prenom';
    
    const USER_ID = 'currentID';
    const USER_USERNAME = 'username';
    
    const MENUS_TITRE = 'menus1';
    const MENUS_ACCORDEON = 'menus2';
    const HOME_ROUTE = 'homeUrl';

    public static function getSessionKeys(){
        $rep[] = SessionKeys::CODE_PROFIL;
        $rep[] = SessionKeys::LOCALE;
        $rep[] = SessionKeys::HOME_ROUTE;
        $rep[] = SessionKeys::ID_ACTIONS;
        $rep[] = SessionKeys::MENUS_ACCORDEON;
        $rep[] = SessionKeys::MENUS_TITRE;
        $rep[] = SessionKeys::PROFIL;
        $rep[] = SessionKeys::ID_PROFIL;
        $rep[] = SessionKeys::USER_DATA;
       
        $rep[] = SessionKeys::USER_ID;
        $rep[] = SessionKeys::USER_NOM;
        $rep[] = SessionKeys::USER;
//        $rep[] = SessionKeys::USER_PRENOM;
        $rep[] = SessionKeys::USER_USERNAME;
        
        
        
        return $rep;
    }
}
