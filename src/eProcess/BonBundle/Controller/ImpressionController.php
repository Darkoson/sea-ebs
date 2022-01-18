<?php

namespace eProcess\BonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use eProcess\SecurityBundle\Constantes\MessageSysteme;
use eProcess\SecurityBundle\Constantes\Status ;
use eProcess\SecurityBundle\Constantes\TypeProfile;
use eProcess\SecurityBundle\Constantes\EtapeBon;

/**
 * Fichier controller.
 *
 */
class ImpressionController extends Controller {

    /**
     * constructeur de la classe
     */
    public function __construct() {

        $this->module = Controller::MOD_IMPRESSION;
        $this->descModule = Controller::MOD_IMPRESSION_DESC;
        $this->actionBundle = 'eProcessBonBundle:';
        parent::__construct('BonCommande'); 
        $this->viewFolder = 'Impression:';
    }

    
    /**
     * impressionBonAction : Page d'impression des bons de commande
     * 
     * @param type $idBon
     * @return type
     */
    public function impressionBonAction($idBon) {

        $description = "Impression de Bons de Commande";
        $this->checkAuthorization(__FUNCTION__, $description, FALSE);
        // recuperation des services 
        $em = $this->getDoctrine()->getManager();

        // recuperation du bon de commande
        $bon = $em->getTable($this->entityBundle . $this->entity)->find($idBon);
        if (!$bon) {
            $this->get('session')->getFlashBag()->add('ko', MessageSysteme::INEXISTANT);
            return $this->redirectToRoute($this->getHomeRoute());
        }
        
        // controle sur l'etape du bon de commande: on n'imprime pas si l'etape n'est pas la liste des etapes choisie
        $etapes= [ EtapeBon::BON_APPROUVE];
        if(!in_array($bon->getEtape() , $etapes)){
             $this->get('session')->getFlashBag()->add('ko', MessageSysteme::OPERATION_ECHEC." (Bon non Imprimable à ce stade !)");
            return $this->redirectToRoute($this->getHomeRoute());
        }
        
        // controle sur l'etape du bon de commande: on n'imprime pas si on n'est pas de EBS
        if($this->getCurrentCodeProfil()!= TypeProfile::EBS ){
             $this->get('session')->getFlashBag()->add('ko', MessageSysteme::OPERATION_ECHEC." (Votre profil ne peut pas imprimer un bon !)");
            return $this->redirectToRoute($this->getHomeRoute());
        }

        // si le bon na pas encore de ficher alors on genere pour la premiere fois un fichier PDF
        $listAchatDep = $bon->getAchat()->getAchatDepartements();
        $listProvision = array();

        foreach ($listAchatDep as $achatDep) {
            $listProvision[] = $achatDep->getProvision();
        }
        $this->data['entity'] = $bon;
        $this->data['listProvision'] = $listProvision;
        
        return $this->render($this->actionBundle . $this->viewFolder . 'impressionBon.html.twig', $this->data);
       
    }

    
    
    /**
     *  printListBonNonSigneAction : fonction d'impression de la liste des bon de commande non signés mais approuvés
     * 
     *
     */
    public function printListBonNonSigneAction() {
        $description = "Impression des bons de Commande non Signé";
        $this->checkAuthorization(__FUNCTION__, $description, false);
        $this->entity = 'BonCommande';
        $em = $this->getDoctrine()->getManager();

        // recuperation de laliste des bons non signés
        
        $params['isActif'] = Status::INACTIF ;
        $listBon = $em->getTable($this->entityBundle . $this->entity)->findBy($params, array('dateActivation'=>'DESC'));

        
        $this->data['listeBonNonSigne'] = $listBon;
        // renvoi de la vue
        return  $this->render($this->actionBundle . $this->viewFolder . 'impressionBonNonSigne.html.twig', $this->data);
    }
    

}
