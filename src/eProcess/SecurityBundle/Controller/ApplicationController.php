<?php

namespace eProcess\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use eProcess\SecurityBundle\Constantes\MessageSysteme ;
use Symfony\Component\HttpFoundation\Response ;

/**
 * Profile controller.
 *
 */
class ApplicationController extends Controller {

    /**
     * constructeur de la classe
     */
    public function __construct() {

        $this->module = Controller::MOD_CONFIG;
        $this->descModule = Controller::MOD_CONFIG_DESC;
        $this->actionBundle = 'eProcessSecurityBundle:';
        $this->viewFolder = 'Profile:';
        parent::__construct('Profile');
    }

    /**
     * listProfileAction : fonction d'affichage des profiles
     * 
     *
     */
    public function initBaseAction() {

//        $description = 'Initialisation de la Base de Données';
//        $this->checkAuthorization(__FUNCTION__, $description, true);
//        
//        return new Response('CETTE FONCTION EST BLOCKEE PAR LE DEVELOPPEUR SOUS PEINE DE CAUSER DES DEGATS');

        $em = $this->getDoctrine()->getManager();

        // recuperation des profiles
        $achats = $em->getTable($this->entityBundle .'Achat')->findAll();
        foreach ($achats as $achat) {
            if( substr_count($achat->getObjet(),'SATGURU') ){
                 $em->remove($achat) ;
            }
           
        }
        
        $em->flush();
        
        $fournisseurs = $em->getTable($this->entityBundle .'Achat')->findAll();
        foreach ($fournisseurs as $four) {
            $em->remove($four) ;
        }
        
        $em->flush();
        $this->get('session')->getFlashBag()->add('ok', MessageSysteme::OPERATION_SUCCES);
        return $this->redirectToRoute($this->getHomeRoute());
    }

}
