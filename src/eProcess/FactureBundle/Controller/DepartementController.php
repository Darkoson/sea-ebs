<?php

namespace eProcess\FactureBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response ;
use eProcess\EntityBundle\Entity\Departement;
use eProcess\SecurityBundle\Constantes\Status;
use eProcess\SecurityBundle\Constantes\MessageSysteme ;




/**
 * Departement controller.
 *
 */
class DepartementController extends Controller {

    /**
     * constructeur de la classe
     */
    public function __construct() {

        $this->module = Controller::MOD_DEPARTEMENT;
        $this->descModule = Controller::MOD_DEPARTEMENT_DESC;
        $this->actionBundle = 'eProcessFactureBundle:';
        $this->viewFolder = 'Departement:';
        parent::__construct('Departement');
    }

    /**
     * listDepartementAction : fonction d'affichage des profiles
     * 
     *
     */
    public function listDepartementAction() {

        $description = 'Liste des Départements';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $em = $this->getDoctrine()->getManager();

        // recuperation des departements
        $departements = $em->getTable($this->entityBundle . $this->entity)->findAll();
        $this->data['listeDepartement'] = $departements;

        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'listDepartement.html.twig', $this->data);
    }

    /**
     * changeDepartementStatusAction : fonction d'autorisation d'un profile
     * 
     *  
     *
     */
    public function changeDepartementStatusAction() {

        $description = 'Autorisation des Départements';
        $this->checkAuthorization(__FUNCTION__, $description, false);

        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {

            // recuperation des paramettres du formulaire
            $idDepartements = trim($request->get('idDepartementsModif'));
            $status = trim($request->get('status'));

//            
            // enregistrement des departements au cas où il existe au moins un identifiant
            if ($idDepartements) {
                $tabIdDepartements = explode('/', $idDepartements);
                
                // modification des departements  à partir de leur ids 
                $this->gererStatus($this->entityBundle, $this->entity, $tabIdDepartements, $status);
            }
        }

        // redirection sur la vue des liste des profiles
        return $this->redirect($this->generateUrl('list_departement'));
    }

    /**
     * changeDepartementStatusAction : fonction de suppression des departements désactiver d'un profile
     * 
     *  
     *
     */
    public function removeDepartementAction() {

        $description = 'Suppression des Départements';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {

            // recuperation des paramettres du formulaire
            $idDepartements = trim($request->get('idDepartementsDelete'));

            // enregistrement des profiles au cas où il existe au moins un identifiant
            if ($idDepartements) {
                $tabIdDepartements = explode('/', $idDepartements);

                // Supression des profiles  à partir de leur ids 
                $this->gererStatus($this->entityBundle, $this->entity, $tabIdDepartements, Status::SUPPRIME_VRAIE);
            }
        }

        $em = $this->getDoctrine()->getManager();
        // recuperation des departements désactiver pour une suppression
        $departements = $em->getTable($this->entityBundle . $this->entity)->findDeletables();

        $this->data['listeDepartement'] = $departements;
        // redirection sur la vue des liste des departements

        return $this->render($this->actionBundle . $this->viewFolder . 'removeDepartement.html.twig', $this->data, $this->response);
    }

    /**
     * addDepartementAction : fonction d'ajout d'un nouvel departement 
     *
     */
    public function addDepartementAction() {

        $description = 'Création d\'un  Département';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        // recuperation des informations du formaulaire
        if ($request->getMethod() == 'POST') {

            
            $em = $this->getDoctrine()->getManager();
            
            // recuperation des paramettres du formulaire
            $nom = trim($request->get('nom'));
            $description = trim($request->get('description'));
            
            $departement = new Departement($nom,$description) ;
            $departement->setCreerPar($this->getCurrentId()); 
            $em->persist($departement);
            $em->flush() ;
            $this->get('session')->getFlashBag()->add('ok', MessageSysteme::AJOUT_SUCCES );
           
            // redirection sur la vue des liste des departements
            return $this->redirect($this->generateUrl('list_departement'));
        }


        return $this->render($this->actionBundle . $this->viewFolder . 'addDepartement.html.twig', $this->data, $this->response);
    }

    /**
     * Displays a form to edit an existing Departement entity.
     *
     */
    public function editDepartementAction($idDepartement) {


        $description = 'Modification d\'un Département';
        $this->checkAuthorization(__FUNCTION__, $description, false);

        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        // recuperation du departement à modifier
        $depModifiable = $em->getTable($this->entityBundle . $this->entity)->find($idDepartement);

        // si le departement existe on poursuit le traitement, au cas contraire on redirige sr la liste des departements
        if ($depModifiable) {
            // recuperation des informations du formaulaire
            if ($request->getMethod() == 'POST') {

                // recuperation des paramettres du formulaire
                $nomDepartement = trim($request->get('nom'));
                $description = trim($request->get('description'));

                // verification de l'existance d'un autre departement du meme nom
                
                $departementExiste = $em->getTable($this->entityBundle . $this->entity)->findOther($idDepartement,$nomDepartement );

//                var_dump(count($profileExiste)); exit;
                
                // enregistrement du departement au cas où ce nouveau nom n'existe pas encore
                if (count($departementExiste) ==  0) {
                    $depModifiable->setNom($nomDepartement);
                    $depModifiable->setDescription($description);
                    // enregistrement du profile
                    $em->flush();
                    $this->get('session')->getFlashBag()->add('ok', MessageSysteme::MODIF_SUCCES);
                }  else {
                    $this->get('session')->getFlashBag()->add('ko', MessageSysteme::EXISTE_DEJA);
                }

                // redirection sur la vue des liste des profiles
                return $this->redirect($this->generateUrl('list_departement'));
            }

            // retour sur la page de modification des deartements apres reuperation de ses informtions
            $this->data['entity'] = $depModifiable;

            return $this->render($this->actionBundle . $this->viewFolder . 'editDepartement.html.twig', $this->data, $this->response);
        }

        $this->get('session')->getFlashBag()->add('ko', MessageSysteme::INEXISTANT);
        // on revient sur la liste desprofiles au cas où le profile n'existe pas !
        return $this->render($this->entityBundle . $this->viewFolder . ':listDepartement.html.twig', $this->data, $this->response);
    }

}
