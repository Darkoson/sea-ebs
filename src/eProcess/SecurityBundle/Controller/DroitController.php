<?php

namespace eProcess\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use eProcess\SecurityBundle\Constantes\Status;
use eProcess\SecurityBundle\Constantes\MessageSysteme ;

class DroitController extends Controller {

    /**
     * constructeur de la classe
     */
    public function __construct() {

        $this->module = Controller::MOD_DROIT;
        $this->descModule = Controller::MOD_DROIT_DESC;
        $this->actionBundle = 'eProcessSecurityBundle:';
        $this->viewFolder = 'Droit:';
        parent::__construct('AvoirDroit');
    }

    /**
     * listRightAction : fonction d'affichage des droits des profiles
     * 
     * @param int $idProfile
     */
    public function listRightAction($idProfile) {

        $description = 'Liste des Droits par Profil';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $em = $this->getDoctrine()->getManager();

        // recuperation des profiles actifs
        $listeProfile = $em->getTable($this->entityBundle . 'Profile')->findByIsActif(true);

        $nbreProfileActif = count($listeProfile);

        
        // recperation des information sur le droit du premier profil
        if ($nbreProfileActif > 0) {

            // recuperation du profile Actif : ces parametres sont pour forcer que le idProfile doit cor
            // respondre à un profile actif
            $param['id'] = $idProfile;
            $param['isActif'] = true;
            $profileActif = null;

            $unProfile = $em->getTable($this->entityBundle . 'Profile')->findBy($param);

            // si le profile recherché n'existe pas , on prend le premeier profile de la liste des profiles
            if (count($unProfile) == 0) {
                $profileActif = $listeProfile[0];
            } else {
                $profileActif = $unProfile[0];
            }

            $toutProfilesActifs = array();

            // recuperation des id et noms des profiles
            foreach ($listeProfile as $prof) {
                $autre['id'] = $prof->getId();
                $autre['nom'] = $prof->getNom();

                $toutProfilesActifs[] = $autre;
            }


            // recuperation des actions par module quisont attribuées à un profil
            $droitManager = $this->get('right_manager');
            $listeDroit = $droitManager->getProfileActionsByModule($idProfile, $attrib = true);

            // ajout des autres paramètres de la vue
            $this->data['listeDroit'] = $listeDroit;
            $this->data['profileActif'] = $profileActif;
            $this->data['toutProfileActif'] = $toutProfilesActifs;
            $this->data['nbreProfileActif'] = $nbreProfileActif;
        }

        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'listRight.html.twig', $this->data);
    }

    /**
     * addRightAction : fonction d'ajout d'un nouveau droit à un profile 
     *      
     *          Remarque : il faut que le profile soit actif
     * 
     * @param type $idProfile
     */
    public function addRightAction($idProfile) {

        $description = 'Ajout de Droits à un Profil';
        $this->checkAuthorization(__FUNCTION__, $description, FALSE);
        // recuperation des services 
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $droitManager = $this->get('right_manager');


        // traitement du formulaire dans le cas d'une soumission

        if ($request->getMethod() == 'POST') {

            // recuperation des ids des nouvelles actions à ajouter
            $idActions = trim($request->get('idActions'));
            $idProfile = intval($request->get('idProfile'));

            $tabIdActions = explode('/', $idActions);

            // creation  de nouveaux droits  à partir des ids des actions
            $droitManager->setProfileActions($idProfile, $tabIdActions, $tatus = false);
            $this->get('session')->getFlashBag()->add('ok', MessageSysteme::AJOUT_SUCCES);
        }


        // verification de l'existance et de l'activation du profile dont veut ajouter le droit
        $param['id'] = $idProfile;
        $param['isActif'] = true;
        $profileActif = null;

        $unProfile = $em->getTable($this->entityBundle . 'Profile')->findBy($param);

        // recuperation des droits non attribués à ce profile
        if (count($unProfile) > 0) {
            $profileActif = $unProfile[0];

            // recuperation des profiles actifs
            $listeProfile = $em->getTable($this->entityBundle . 'Profile')->findByIsActif(true);

            $toutProfilesActifs = array();

            // recuperation des id et noms des profiles
            foreach ($listeProfile as $prof) {
                $autre['id'] = $prof->getId();
                $autre['nom'] = $prof->getNom();
                $toutProfilesActifs[] = $autre;
            }

            // recuperation des actions non attribuées par module pour un profil
            $listDroitsNonAttribues = $droitManager->getProfileActionsByModule($idProfile, $attrib = false);

            // ajout des autres paramètres de la vue
            $this->data['listeDroit'] = $listDroitsNonAttribues;
            $this->data['toutProfileActif'] = $toutProfilesActifs;
        }

        $this->data['profileActif'] = $profileActif;

        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'addRight.html.twig', $this->data);
    }

    /**
     * changeRightStatusAction : fonction de d'autorisation d'un droit
     * 
     * @param int  $idProfile
     * @param bool $toAuthorized
     */
    public function changeRightStatusAction($idProfile ) {

        $description = 'Autorisation ou Desactivation des  Droits';
        $this->checkAuthorization(__FUNCTION__, $description, FALSE);
        // recuperation des services 
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        
        $toAuthorized = $request->get('toAuthorized');

        // traitement du formulaire dans le cas d'une soumission

        if ($request->getMethod() == 'POST') {

            // recuperation des ids des nouvelles actions à ajouter
            $idDroits = trim($request->get('idDroits'));
            $idProfile = intval($request->get('idProfile'));

            $tabIdDroits = explode('/', $idDroits);

            // creation  de nouveaux droits  à partir des ids des actions
            $this->gererStatus($this->entityBundle, $this->entity, $tabIdDroits, $toAuthorized);
        }

        // verification de l'existance et de l'activation du profile dont veut modifier le status des droits

        $param['id'] = $idProfile;
        $param['isActif'] = true;
        $profileActif = null;

        $unProfile = $em->getTable($this->entityBundle . 'Profile')->findBy($param);
  
//            var_dump($unProfile);exit;
        // recuperation des droits à modifier pour ce profile au cas où ce profil  existe
        if (count($unProfile) > 0) {
            $profileActif = $unProfile[0];
          

            // on recupere les droits dont le status est différent de l'etat dans lequel on veut les mettre
           
            $this->data['listeDroit'] = $em->getTable($this->entityBundle . $this->entity)
                    ->getDroitActivable($profileActif->getId(), $this->getCurrentId(), $toAuthorized);
//            var_dump($toAuthorized);exit;
            
             // recuperation des profiles actifs
            $listeProfile = $em->getTable($this->entityBundle . 'Profile')->findByIsActif(true);

            $toutProfilesActifs = array();

            // recuperation des id et noms des profiles
            foreach ($listeProfile as $prof) {
                $autre['id'] = $prof->getId();
                $autre['nom'] = $prof->getNom();
                $toutProfilesActifs[] = $autre;
            }
            $this->data['toutProfileActif'] = $toutProfilesActifs;
            
        }

        // ajout des autres paramètres de la vue
        $this->data['toAuthorized'] = $toAuthorized;
        $this->data['profileActif'] = $profileActif;
        
        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'changeRightStatus.html.twig', $this->data);
    }

    public function removeRightAction($idProfile) {

        $description = 'Suppression des  Droits Refusés';
        $this->checkAuthorization(__FUNCTION__, $description, FALSE);
        // recuperation des services 
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        // traitement du formulaire dans le cas d'une soumission

        if ($request->getMethod() == 'POST') {

            // recuperation des ids des nouvelles actions à ajouter
            $idDroits = trim($request->get('idDroits'));
            $idProfile = intval($request->get('idProfile'));

            $tabIdDroits = explode('/', $idDroits);

            // suprression des droits  à partir de leurs ids
            $this->gererStatus($this->entityBundle, $this->entity, $tabIdDroits, Status::SUPPRIME_VRAIE);
        }

        // verification de l'existance et de l'activation du profile dont veut modifier le status des droits

        $param['id'] = $idProfile;
        $param['isActif'] = true;
        $profileActif = null;

        $unProfile = $em->getTable($this->entityBundle . 'Profile')->findBy($param);
  
//            var_dump($unProfile);exit;
        // recuperation des droits à modifier pour ce profile au cas où ce profil  existe
        if (count($unProfile) > 0) {
            $profileActif = $unProfile[0];
          

            // on recupere les droits dont le status est inactif
            $critere['profile'] = $profileActif;
            $critere['isActif'] = false;
            $this->data['listeDroit'] = $em->getTable($this->entityBundle . $this->entity)
                    ->findBy($critere);
            
             // recuperation des profiles actifs
            $listeProfile = $em->getTable($this->entityBundle . 'Profile')->findByIsActif(true);

            $toutProfilesActifs = array();

            // recuperation des id et noms des profiles
            foreach ($listeProfile as $prof) {
                $autre['id'] = $prof->getId();
                $autre['nom'] = $prof->getNom();
                $toutProfilesActifs[] = $autre;
            }
            $this->data['toutProfileActif'] = $toutProfilesActifs;
            
        }

        // ajout des autres paramètres de la vue
        $this->data['toAuthorized'] = 2;
        $this->data['profileActif'] = $profileActif;
        
        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'removeRight.html.twig', $this->data);
    }

}
