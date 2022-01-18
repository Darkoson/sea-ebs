<?php

namespace eProcess\FactureBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use eProcess\SecurityBundle\Constantes\MessageSysteme;
use eProcess\SecurityBundle\Constantes\EtapeFacture;
use eProcess\EntityBundle\Form\AchatBonType;

/**
 * FactureValidationController : Controleur de gestions des factures aux opérations
 */
class FactureValidationController extends Controller {

    /**
     * constructeur de la classe
     */
    public function __construct() {

        $this->module = Controller::MOD_FCT_VALIDATION;
        $this->descModule = Controller::MOD_FCT_VALIDATION_DESC;
        $this->actionBundle = 'eProcessFactureBundle:';
        parent::__construct('Facture');
        $this->viewFolder = 'FCT_Validation:';
    }
      
    /**
     * arriveFactureValidationAction : fonction d'affichage de la liste des factures 
     *      arrivées à OPS pour être validées 
     * @return view
     */
    public function arriveFactureValidationAction() {

        $description = 'Liste des Factures à Valider aux Opérations';
        $this->checkAuthorization(__FUNCTION__, $description, true);
        $em = $this->getDoctrine()->getManager();

        // recuperation de la lise des factures arrivées à OPS pour être validées
        $param['etape'] = EtapeFacture::FCT_ARRIVE_OPS;
        $listeFactureArrrive = $em->getTable($this->entityBundle . $this->entity)->findBy($param);
        $this->data['listeFacture'] = $listeFactureArrrive;

        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'arriveTraitementOPS.html.twig', $this->data);
    }


    /**
     * validationFactureAction : fonction d'affichage de la page de validation d'une facture
     * $id : identifiant de la facture à valider
     * @return View
     */
    public function validationFactureAction($id = 0) {

        $description = 'Validation des factures';
        $this->checkAuthorization(__FUNCTION__, $description, false);
        $em = $this->getDoctrine()->getManager();
        
        // recuperation de la facture
        $facture = $em->getTable($this->entityBundle . $this->entity)->find($id);
        if (!$facture) {
            $this->get('session')->getFlashBag()->add('ko', MessageSysteme::INEXISTANT);
            return $this->redirectToRoute($this->getHomeRoute());
        }

        $form = $this->createForm(new AchatBonType(), $facture->getAchat());
        $this->data['entity'] = $facture;
        $this->data['checkList'] = $facture->getAchat()->getCheckList();
        $this->data['form'] = $form->createView();
        
        //c'est la route sur laquelle on sera redirigé après validation de la facture: il
        // est passé en paramettre de la fonction JS de validation des factures
        $this->data['route'] = 'facture_validation_arrive';

        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'validationFacture.html.twig', $this->data);
    }
    
  
    /**
     * factureNonConformeAction : Action d'affichage des factures ayant des 
     *  problèmes à  OPS.
     * @return type
     */
    public function factureNonConformeAction() {

        $description = "Liste des Factures Non Conformes ";
        $this->checkAuthorization(__FUNCTION__, $description, true);
        $em = $this->getDoctrine()->getManager();

        // recuperation de la lise des factures à problème qui sont à OPS
        $param['etape'] = EtapeFacture::FCT_PROBLEME_OPS;
        $listeFactureProbleme = $em->getTable($this->entityBundle . $this->entity)->findBy($param);
        $this->data['listeFacture'] = $listeFactureProbleme;

        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'problemeFacturesOPS.html.twig', $this->data);
    }

    /**
     * factureConformeAction : Action d'affichage des factures valids qui sont à  OPS.
     * @return View
     */
    public function factureConformeAction() {

        $description = "Liste des Factures Conformes ";
        $this->checkAuthorization(__FUNCTION__, $description, true);
        $em = $this->getDoctrine()->getManager();

        // recuperation de la lise des factures valide qui sont à OPS
        $param['etape'] = EtapeFacture::FCT_VALIDE_OPS;
        $listeFactureProbleme = $em->getTable($this->entityBundle . $this->entity)->findBy($param);
        $this->data['listeFacture'] = $listeFactureProbleme;

        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'valideFacturesOPS.html.twig', $this->data);
    }
}
