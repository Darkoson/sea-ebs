<?php

namespace eProcess\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use eProcess\EntityBundle\Entity\Profile;
use eProcess\SecurityBundle\Constantes\Status;
use eProcess\SecurityBundle\Constantes\MessageSysteme ;

/**
 * Profile controller.
 *
 */
class ProfileController extends Controller {

    /**
     * constructeur de la classe
     */
    public function __construct() {

        $this->module = Controller::MOD_PROFIL;
        $this->descModule = Controller::MOD_PROFIL_DESC;
        $this->actionBundle = 'eProcessSecurityBundle:';
        $this->viewFolder = 'Profile:';
        parent::__construct('Profile');
    }

    /**
     * listProfileAction : fonction d'affichage des profiles
     * 
     *
     */
    public function listProfileAction() {

        $description = 'Liste des Profils';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $em = $this->getDoctrine()->getManager();

        // recuperation des profiles
        $profiles = $em->getTable($this->entityBundle . $this->entity)->findAll();
        $this->data['listeProfile'] = $profiles;

        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'listProfile.html.twig', $this->data);
    }

    /**
     * changeProfileStatusAction : fonction d'autorisation d'un profile
     * 
     *  
     *
     */
    public function changeProfileStatusAction() {

        $description = 'Autorisation des Profils';
        $this->checkAuthorization(__FUNCTION__, $description, false);

        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {

            // recuperation des paramettres du formulaire
            $idProfiles = trim($request->get('idProfilesModif'));
            $status = trim($request->get('status'));

//            
            // enregistrement des profiles au cas où il existe au moins un identifiant
            if ($idProfiles) {
                $tabIdProfiles = explode('/', $idProfiles);
                
                // modification des profiles  à partir de leur ids 
                $this->gererStatus($this->entityBundle, $this->entity, $tabIdProfiles, $status);
            }
        }

        // redirection sur la vue des liste des profiles
        return $this->redirect($this->generateUrl('list_profile'));
    }

    /**
     * changeProfileStatusAction : fonction de suppression des profiles désactiver d'un profile
     * 
     *  
     *
     */
    public function removeProfileAction() {

        $description = 'Suppression des  Profils';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {

            // recuperation des paramettres du formulaire
            $idProfiles = trim($request->get('idProfilesDelete'));

            // enregistrement des profiles au cas où il existe au moins un identifiant
            if ($idProfiles) {
                $tabIdProfiles = explode('/', $idProfiles);

                // Supression des profiles  à partir de leur ids 
                $this->gererStatus($this->entityBundle, $this->entity, $tabIdProfiles, Status::SUPPRIME_VRAIE);
            }
        }
        
        $em = $this->getDoctrine()->getManager() ;
        // recuperation des profiles désactiver et sans utilisateurs pour une suppression
        $profiles = $em->getTable($this->entityBundle . $this->entity)->findDeletables();

        $this->data['listeProfile'] = $profiles ;
        // redirection sur la vue des liste des profiles
        
        return $this->render($this->actionBundle . $this->viewFolder . 'removeProfile.html.twig', $this->data, $this->response);
    
    }

    /**
     * addProfileAction : fonction d'ajout d'un nouveau profile 
     *
     */
    public function addProfileAction() {

        $description = 'Création d\'un Profil ';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        // recuperation des informations du formaulaire
        if ($request->getMethod() == 'POST') {
            
            // recuperation des paramettres du formulaire
            $nomProfile = trim($request->get('name'));
            $description = trim($request->get('description'));
            $canApprove = boolval($request->get('approve'));

            // verification de l'existance de ce profile
            $profileExist = $em->getTable($this->entityBundle . $this->entity)->findByNom($nomProfile);

            // enregistrement du profiles au cas où il n'existe pas encore 
            if (count($profileExist) == 0) {

                $newProfile = new Profile($nomProfile, $description, $canApprove);
                $newProfile->setCreerPar($this->getCurrentId());
                $newProfile->setDateCreation(new \DateTime());
                $newProfile->setIsActif(false);
                $em->persist($newProfile);
                $em->flush();
                $this->get('session')->getFlashBag()->add('ok', MessageSysteme::AJOUT_SUCCES);
            }  else {
                $this->get('session')->getFlashBag()->add('ko', MessageSysteme::EXISTE_DEJA);
            }

            // redirection sur la vue des liste des profiles
            return $this->redirect($this->generateUrl('list_profile'));
        }


        return $this->render($this->actionBundle . $this->viewFolder . 'addProfile.html.twig', $this->data, $this->response);
    }

    /**
     * Displays a form to edit an existing Profile entity.
     *
     */
    public function editProfileAction($idProfile) {

         
        $description = 'Modification d\un Profil ';
        $this->checkAuthorization(__FUNCTION__, $description, false);

        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        // recuperation du profile à modifier
        $profileModifiable = $em->getTable($this->entityBundle . $this->entity)->find($idProfile);

        // si le profile exoste on poursuit le traitement, au cas contraire on redirige sr la liste des profiles
        if ($profileModifiable) {
            // recuperation des informations du formaulaire
            if ($request->getMethod() == 'POST') {

                // recuperation des paramettres du formulaire
                $nomProfile = trim($request->get('name'));
                $description = trim($request->get('description'));
                $canApprove = boolval($request->get('approve'));
                
                // verification de l'existance d'un autre profile du meme nom
                
                $profileExiste = $em->getTable($this->entityBundle . $this->entity)->findOther($idProfile,$nomProfile );

//                var_dump(count($profileExiste)); exit;
                
                // enregistrement du profiles au cas où ce nouveau nom n'existe pas encore
                if (count($profileExiste) ==  0) {
                    $profileModifiable->setNom($nomProfile);
                    $profileModifiable->setDescription($description);
                    $profileModifiable->setCanApprove($canApprove);
                    // enregistrement du profile
                    $em->flush();
                    $this->get('session')->getFlashBag()->add('ok', MessageSysteme::MODIF_SUCCES);
                }  else {
                    $this->get('session')->getFlashBag()->add('ko', MessageSysteme::EXISTE_DEJA);
                }

                // redirection sur la vue des liste des profiles
                return $this->redirect($this->generateUrl('list_profile'));
            }

            // retour sur la page de modification des profiles apres reuperation des informtions sur le profiles
            $this->data['entity'] = $profileModifiable;

            return $this->render($this->actionBundle . $this->viewFolder . 'editProfile.html.twig', $this->data, $this->response);
        }

        $this->get('session')->getFlashBag()->add('ko', MessageSysteme::INEXISTANT);
        // on revient sur la liste desprofiles au cas où le profile n'existe pas !
        return $this->render($this->entityBundle . $this->viewFolder . ':listProfile.html.twig', $this->data, $this->response);
    }

}
