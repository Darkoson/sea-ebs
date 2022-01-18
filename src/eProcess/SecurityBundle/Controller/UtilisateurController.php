<?php

namespace eProcess\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use eProcess\EntityBundle\Entity\Utilisateur;
use eProcess\SecurityBundle\Constantes\Status;
use eProcess\EntityBundle\Form\UtilisateurType;
use eProcess\SecurityBundle\Constantes\MessageSysteme;

/**
 * Utilisateur controller.
 *
 */
class UtilisateurController extends Controller {

    /**
     * constructeur de la classe
     */
    public function __construct() {

        $this->module = Controller::MOD_USER;
        $this->descModule = Controller::MOD_USER_DESC;
        $this->actionBundle = 'eProcessSecurityBundle:';
        $this->viewFolder = 'Utilisateur:';
        parent::__construct('Utilisateur');
    }

    /**
     * listUtilisateurAction : fonction d'affichage des profiles
     * 
     *
     */
    public function listUtilisateurAction() {

        $description = 'Liste des Utilisateurs';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $em = $this->getDoctrine()->getManager();

        // recuperation des utilisateurs
        $profiles = $em->getTable($this->entityBundle . $this->entity)->findNotDeleted();
        $this->data['listeUtilisateur'] = $profiles;

        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'listUtilisateur.html.twig', $this->data);
    }

    /**
     * changeUtilisateurStatusAction : fonction d'autorisation d'un profile
     * 
     *  
     *
     */
    public function changeUtilisateurStatusAction() {

        $description = 'Autorisation des Utilisateurs';
        $this->checkAuthorization(__FUNCTION__, $description, false);

        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {

            // recuperation des paramettres du formulaire
            $idUtilisateurs = trim($request->get('idUtilisateursModif'));
            $status = trim($request->get('status'));

//            
            // enregistrement des utilisateurs au cas où il existe au moins un identifiant
            if ($idUtilisateurs) {
                $tabIdUtilisateurs = explode('/', $idUtilisateurs);

                // modification des utilisateurs  à partir de leur ids 
                $this->gererStatus($this->entityBundle, $this->entity, $tabIdUtilisateurs, $status);
            }
        }

        // redirection sur la vue des liste des profiles
        return $this->redirect($this->generateUrl('list_utilisateur'));
    }

    /**
     * changeUtilisateurStatusAction : fonction de suppression des utilisateurs désactiver d'un profile
     * 
     *  
     *
     */
    public function removeUtilisateurAction() {

        $description = 'Suppression des  Utilisateurs';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {

            // recuperation des paramettres du formulaire
            $idUtilisateurs = trim($request->get('idUtilisateursDelete'));

            // enregistrement des profiles au cas où il existe au moins un identifiant
            if ($idUtilisateurs) {
                $tabIdUtilisateurs = explode('/', $idUtilisateurs);

                // Supression des profiles  à partir de leur ids 
                $this->gererStatus($this->entityBundle, $this->entity, $tabIdUtilisateurs, Status::SUPPRIME);
            }
        }

        $em = $this->getDoctrine()->getManager();
        // recuperation des utilisateurs désactiver pour une suppression
        $utilisateurs = $em->getTable($this->entityBundle . $this->entity)->findDeletables();

        $this->data['listeUtilisateur'] = $utilisateurs;
        // redirection sur la vue des liste des utilisateurs

        return $this->render($this->actionBundle . $this->viewFolder . 'removeUtilisateur.html.twig', $this->data, $this->response);
    }

    /**
     * addUtilisateurAction : fonction d'ajout d'un nouvel utilisateur 
     *
     */
    public function addUtilisateurAction() {

        $description = 'Création d\'un Utilisateur';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $utilisateur = new Utilisateur('');
        $form = $this->createForm(new UtilisateurType(), $utilisateur);


        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();


        // recuperation des informations du formaulaire
        if ($request->getMethod() == 'POST') {

            $form->handleRequest($request);
            if ($form->isValid()) {
                $utilisateur = $form->getData();

                // verification de l'existance du nom d'utilisateur saisie
                $username = $utilisateur->getUsername();

                $ancienUser = $em->getTable($this->entityBundle . $this->entity)->findByUsername($username);

                // si ce non d'utilisateur n'existe pas encore alors on l'enregistre
                if (!$ancienUser) {
                    // on continue par enregistrer l'utilisateur si rien ne se passe
                    $utilisateur->setCreerPar($this->getCurrentId());
                    $utilisateur->setDateCreation(new \DateTime());
                    $utilisateur->setIsActif(FALSE);
                    $em->persist($utilisateur);
                    $em->flush();
                    $this->get('session')->getFlashBag()->add('ok', MessageSysteme::AJOUT_SUCCES);
                
                    $form = $this->createForm(new UtilisateurType(), $utilisateur);
                } 
                
                //  affichage des informations lui indiquant l'existance de ce username
                else {

                    $this->get('session')->getFlashBag()->add('ko', MessageSysteme::EXISTE_DEJA);
                }
            }
            //affichage des informations lui indiquant que l'ajout n'est pas aboutis
            else {
                $this->get('session')->getFlashBag()->add('ko', MessageSysteme::AJOUT_ECHEC);
            }
        }


        $this->data['form'] = $form->createView();

        return $this->render($this->actionBundle . $this->viewFolder . 'addUtilisateur.html.twig', $this->data, $this->response);
    }

    /**
     * Displays a form to edit an existing Utilisateur entity.
     *
     */
    public function editUtilisateurAction($idUtilisateur) {


        $description = 'Modification d\'un Utilisateur';
        $this->checkAuthorization(__FUNCTION__, $description, false);

        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        // recuperation de l'utilisateur à modifier
        $userModifiable = $em->getTable($this->entityBundle . $this->entity)->find($idUtilisateur);

        // si l'utilisateur existe on poursuit le traitement, au cas contraire on redirige sr la liste des utilisateurs
        if ($userModifiable) {
            // recuperation des informations du formaulaire
            if ($request->getMethod() == 'POST') {

                // recuperation des paramettres du formulaire
                $idProfile = trim($request->get('idProfile'));
                $idDepartement = trim($request->get('idDepartement'));

                // recuperation du profile de l'utilisateur
                // verification de l'existance d'un autre profile du meme nom

                $profile = $em->getTable($this->entityBundle . 'Profile')->find($idProfile);
                $departement = $em->getTable($this->entityBundle . 'Departement')->find($idDepartement);

                // modification du profile et departement au cas où ce ils existent.
                if ($profile) {
                    $profile->addUtilisateur($userModifiable);
                    $userModifiable->setProfile($profile);
                }
                if ($departement) {
                    $departement->addUtilisateur($userModifiable);
                    $userModifiable->setDepartement($departement);
                }

                // enregistrement des modifications
                $em->flush();
                // redirection sur la vue des liste des profiles
                return $this->redirect($this->generateUrl('list_utilisateur'));
            }

            // retour sur la page de modification des profiles apres reuperation des informtions sur le profiles
            $this->data['entity'] = $userModifiable;

            return $this->render($this->actionBundle . $this->viewFolder . 'editUtilisateur.html.twig', $this->data, $this->response);
        }
        $this->get('session')->getFlashBag()->add('ko', MessageSysteme::INEXISTANT);

        // on revient sur la liste desprofiles au cas où le profile n'existe pas !
        return $this->render($this->entityBundle . $this->viewFolder . ':listUtilisateur.html.twig', $this->data, $this->response);
    }

}
