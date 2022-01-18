<?php

/**
 * Description of DroitManager
 *
 * @author 
 */

namespace eProcess\SecurityBundle\Services;

use eProcess\SecurityBundle\Services\MenuManager;
use Symfony\Component\HttpFoundation\Session\Session;
use eProcess\SecurityBundle\Constantes\Status;
use eProcess\SecurityBundle\Constantes\SessionKeys;
use eProcess\EntityBundle\Entity\AvoirDroit;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class DroitManager {

    private $em;
    private $menuManager;
    private $session;
    private $entityBundle = 'eProcessEntityBundle:';
    private $clef;

    public function __construct(Session $session, MenuManager $menuManager) {
        $this->clef = SessionKeys::USER_DATA;

        $this->menuManager = $menuManager;

        $this->em = $this->menuManager->getManager();

        $this->session = $session;
    }

    /**
     * getUserData :  cette methode permet de recupere les données de la session
     * 
     * @return array
     */
    public function getUserData() {
        return $this->session->get($this->clef);
    }

    /**
     * setUserData :  cette methode permet de mettre les données dans la session
     * 
     * @return array
     */
    public function setUserData($data) {
        // suppression de l'ancienne données de la session
        $this->unSetUserData();
        
        $this->session->set($this->clef, $data);
    }
    
    /**
     * unSetUserData :  cette methode permet de retirer les données de la session
     * 
     * @return array
     */
    public function unSetUserData() {
        $this->session->remove($this->clef);
    }

    /**
     * getProfilActionID : fonction de recuperation des identifiants des actions en
     *      
     *   fonction des état des droits sur ces actions
     * 
     * @param type $idProfil
     * @param type $status : si $status = -1 alors on recpere les ids des actions du profil sans tenir compte
     *              de l'etat des droits sur ces actions 
     */
    public function getProfilActionID($idProfil, $status = -1) {
        return $this->em->getTable($this->entityBundle . 'Profile')
                        ->getActionIdsFromRight($idProfil, $status);
    }

    /**
     * getProfilActionName : fonction de recuperation des noms des actions en
     *      
     *   fonction des état des droits sur ces actions
     * 
     * @param type $idProfil
     * @param type $status  : si $status = -1 alors on recpere les noms des actions du profil sans tenir compte
     *              de l'etat des droits sur ces actions 
     */
    public function getProfilActionName($idProfil, $status = -1) {
        return $this->em->getTable($this->entityBundle . 'Profile')
                        ->getActionNamesFromRight($idProfil, $status);
    }

    /**
     * setProfileActions : fonction d'ajout de noueaux droits à un profil
     * 
     * @param int $idProfile identifiant du profile
     * @param array $tabIdActions : identifiant des actions a ajouter comme droit au profile  
     * @param bool $status : le statut garder par les droits après leur creation
     */
    public function setProfileActions($idProfile, $tabIdActions, $status = false) {
        $rep = true;
        $profil = $this->em->getTable($this->entityBundle . 'Profile')->find($idProfile);

//        var_dump( $profil);exit;
        if ($profil) {
            try {
                foreach ($tabIdActions as $idAction) {
                    if ($idAction != '') {
                        $action = $this->em->getTable($this->entityBundle . 'Action')->find($idAction);

                        // creation des informations sur le droit
                        $droit = new AvoirDroit();
                        $droit->setAction($action);
                        $droit->setProfile($profil);
                        $droit->setCreerPar($this->get(SessionKeys::USER_ID));
                        $droit->setIsActif($status);

                        $this->em->persist($droit);
                        $this->em->flush();

                        $profil->addAvoirDroit($droit);
                        $action->addAvoirDroit($droit);
                        $this->em->persist($action);
                        $this->em->persist($droit);
                    }
                }
                $this->em->persist($profil);
                $this->em->flush();
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
                $rep = FALSE;
            }
        } else {
            $rep = FALSE;
        }

        return $rep;
    }

    /**
     * getProfileActions : fonction de recuperation des actions d'un profile en fonction des critères.
     * 
     * @param int $idProfile : iddentifiant du profile dont on veut recuperer les action
     * @param bool $attrib : indique si les actions sont déjà attribuées à ce profile ou non 
     * @param bool $status : indique si les droits attribués sont autorisés ou non et  -1 indique que l'action
     *                       est indépendant d l'état du droit.
     */
    public function getProfileActions($idProfile, $attrib = true, $status = -1) {
        return $this->em->getTable($this->entityBundle . 'Profile')
                        ->getActions($idProfile, $attrib, $status);
    }

    /**
     * getProfileActionsByModule : fonction de recuperation des actions d'un profile par module en fonction des critères.
     * 
     * @param int $idProfile : iddentifiant du profile dont on veut recuperer les action
     * @param bool $attrib : indique si les actions à recuperer sont déjà attribuées à ce profile ou non 
     * @param bool $status : indique si les droits attribués sont autorisés ou non et  -1 indique que l'action
     *                       est indépendant d l'état du droit.
     */
    public function getProfileActionsByModule($idProfile, $attrib = true, $status = -1) {
        return $this->em->getTable($this->entityBundle . 'Profile')
                        ->getActionsByModule($idProfile, $attrib, $status);
    }

    public function getSessionKey() {
        return $this->clef;
    }

    /**
     * hasAccess : fonction de verification si une action se trouve dans toutes les actions possible que 
     *             l'utilisateur connecté peut exécuter
     * 
     * @param string $nomAction 
     */
    public function hasAccess($nomAction) {

        //initilalisation de la reponse
        $rep = FALSE;

        // recuperation de la liste des actions autorisées du profil de l'utlisateur courant
        $actions = $this->getProfilActionName($this->get(SessionKeys::ID_PROFIL), Status::ACTIF);

        // on verifie si l'action courant existe dans cette liste
        if (is_array($actions) && in_array($nomAction, $actions)) {
            $rep = true;
        }

        return $rep;
    }

    /**
     * get : fonction de recuperation des informations sur un utilisateur 
     * 
     *       dont ces informations sont dans la session 
     * 
     * @param type $key
     * @return type
     */
    public function get($key) {

        return $this->getArrayParam($key, $this->getUserData());
    }

    /**
     * getArrayParam : fonction de recuperation des informations dans un tableau
     * 
     * @param type $key la clef d'entré du tableau
     * @param type $array : le tableau
     * @param type $default : la valeur par defaut à renvoyer si la clef n'existe pas!
     * @return type
     */
    public function getArrayParam($key, $array, $default = false) {
        if (is_array($array)) {
            if (isset($array[$key])) {
                return $array[$key];
            }
        }
        return $default;
    }

    /**
     * 
     * @return type
     */
    public function getManager() {
        return $this->em;
    }

    /**
     * Fonction qui recupere les informations nécéssaires de l'utilisateur 
     * 
     *      afin de les charger dans la session par une autre methode.
     * @param type $user
     */
    public function loadUserInformations($user) {
        $profil = $user->getProfile();
        $idProfil = $profil->getId();
        $idUser = $user->getId();
        
//        var_dump($user.'');exit;
//        var_dump($profil->getCode());exit;

        // recuperation du nom de profil qui se connecte
        $datas[SessionKeys::HOME_ROUTE] = 'dashboard';

        // recuperation du nom de profil qui se connecte
        $datas[SessionKeys::PROFIL] = $profil->getNom();

        // recuperation de l'iD du profil connecté
        $datas[SessionKeys::ID_PROFIL] = $profil->getId();

        // recuperation du code du profil connecté
        $datas[SessionKeys::CODE_PROFIL] = $profil->getCode();

        // recuperation des action dont le profil a desdroits autoriés
        $datas[SessionKeys::ID_ACTIONS] = $this->getProfilActionID($idProfil, Status::ACTIF);

        // recupertion du nom et prenom de l'utilisateur connecté
        $datas[SessionKeys::USER] = $user->getNom() . ' ' . $user->getPrenom();

        // recuperation de l'Id de l'utilisateur connecté
        $datas[SessionKeys::USER_ID] = $idUser;

        // recuperation du nom d'utilisateur de la personne qui se connecte
        $datas[SessionKeys::USER_USERNAME] = $user->getUsername();

        // recuperation des enus dont le profil qui se connecte a le droit d'exécuter
        $datas[SessionKeys::MENUS_TITRE] = $this->menuManager->getActivesMenu($idProfil);
        $datas[SessionKeys::MENUS_ACCORDEON] = $this->menuManager->getActivesMenu($idProfil);

        return $datas;
    }

    public function getActiveUserByUsername($username) {
        $user = null;
        $param['username'] = $username;
        $param['isActif'] = Status::ACTIF;

        $userDB = $this->em->getTable('eProcessEntityBundle:Utilisateur')->findBy($param);
        if (count($userDB) > 0) {
            $user = $userDB[0];
        }
        return $user;
    }

}
