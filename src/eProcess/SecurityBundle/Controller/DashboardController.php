<?php

namespace eProcess\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use eProcess\SecurityBundle\Constantes\MessageSysteme;
use eProcess\SecurityBundle\Constantes\TypeProfile;

class DashboardController extends Controller {

    /**
     * constructeur de la classe
     */
    public function __construct() {

        $this->module = Controller::MOD_DASHBOARD;
        $this->descModule = Controller::MOD_DASHBOARD_DESC;
        $this->actionBundle = 'eProcessSecurityBundle:';
        $this->viewFolder = 'Dashboard:';
        parent::__construct('');
    }

    /**
     * indexAction : action qui redirige chaque utilisateurs sur son tableau de bord en fonction de son profile
     * 
     */
    public function indexAction($error) {

        $route = 'dashboard_default';



        $codeProfile = $this->getCurrentCodeProfil();


        $profileManager = $this->get('profile_manager');

        if ($profileManager->isADMIN($codeProfile)) {
            $route = 'dashboard_admin';
        } elseif ($profileManager->isEBS($codeProfile)) {
            $route = 'dashboard_ebs';
        } elseif ($profileManager->isOPERATION($codeProfile)) {
            $route = 'dashboard_ops';
        }
        elseif ($profileManager->isRECEPTION($codeProfile)) {
            $route = 'dashboard_reception';
        }
        elseif ($profileManager->isFINANCE($codeProfile)) {
            $route = 'bon_soumis_approbation';
        }


        return $this->redirectToRoute($route, array('error' => $error));
    }

    /**
     * homeADMINAction : fonction d'affichage du tableau de bord des Administrateurs
     * 
     * @return type
     */
    public function homeADMINAction($error) {
        $description = 'Tableau de Bord des Administrateurs';
        $this->checkAuthorization(__FUNCTION__, $description, FALSE);

        if ($error) {
            $this->get('session')->getFlashBag()->add('ko', MessageSysteme::USER_DROIT_ECHEC);
        }

        return $this->render($this->actionBundle . $this->viewFolder . 'admin.html.twig', $this->data);
    }

    /**
     * homeOPSAction : fonction d'affichage du tableau de bord des agents des opérations
     * 
     * @return type
     */
    public function homeOPSAction($error) {
        $description = 'Tableau de Bord des Opérateurs';
        $this->checkAuthorization(__FUNCTION__, $description, FALSE);

        if ($error) {
            $this->get('session')->getFlashBag()->add('ko', MessageSysteme::USER_DROIT_ECHEC);
        }

        return $this->render($this->actionBundle . $this->viewFolder . 'ops.html.twig', $this->data);
    }

    /**
     * homeOPSAction : fonction d'affichage du tableau de bord des réceptionnistes
     * 
     * @return type
     */
    public function homeRECEPTIONAction($error) {
        $description = 'Tableau de Bord  des Réceptionnistes';
        $this->checkAuthorization(__FUNCTION__, $description, FALSE);

        if ($error) {
            $this->get('session')->getFlashBag()->add('ko', MessageSysteme::USER_DROIT_ECHEC);
        }

        return $this->render($this->actionBundle . $this->viewFolder . 'reception.html.twig', $this->data);
    }

    /**
     * homeEBSAction : fonction d'affichage du tableau de bord des agents de EBS
     * 
     * @return type
     */
    public function homeEBSAction($error) {
        $description = 'Tableau de Bord EBS';
        $this->checkAuthorization(__FUNCTION__, $description, FALSE);

        if ($error) {
            $this->get('session')->getFlashBag()->add('ko', MessageSysteme::USER_DROIT_ECHEC);
        }
        $em = $this->getDoctrine()->getManager();

        // recuperation du code du profil connecté
        $result = $em->getTable($this->entityBundle . 'Facture')->getStatistiqueEtape(TypeProfile::EBS);
        $this->data['stat'] = $result;
//        var_dump($result);exit;

        return $this->render($this->actionBundle . $this->viewFolder . 'ebs.html.twig', $this->data);
    }

    /**
     * homeDEFAULTAction : fonction d'affichage du tableau de bord par défaut
     * 
     * @return type
     */
    public function homeDEFAULTAction($error) {
        $description = 'Tableau de Bord par Défaut';
        $this->checkAuthorization(__FUNCTION__, $description, FALSE);

        if ($error) {
            $this->get('session')->getFlashBag()->add('ko', MessageSysteme::USER_DROIT_ECHEC);
        }

        return $this->render($this->actionBundle . $this->viewFolder . 'default.html.twig', $this->data);
    }

    /**
     * associatedEntityAction : recuperation des elements qui sont lies a un objet
     * 
     * @return Response
     */
    public function associatedEntityAction() {
        $request = $this->get('request');

        $bundle = trim($request->get('bundle'));
        $entityFrom = trim($request->get('entityFrom'));
        $attrFrom = $request->get('attrFrom');
        $entityTo = $request->get('entityTo');

        $attrTo = $request->get('attrTo');

//        var_dump($attrFrom);
//        var_dump($entityFrom);
//        var_dump($fromId);
//        var_dump($attrTo);
//        var_dump($etat);
//        exit;

        $em = $this->getDoctrine()->getManager();
        $rep = array();
        $status = true;
        $message = '';
        $objetFrom = '';
        if (is_array($attrFrom)) {
            $objetFrom = $em->getTable($bundle . ':' . $entityFrom)->findOneBy($attrFrom);
        }


        if ($objetFrom) {
            $method = 'get' . $entityTo . 's';
            $objetsTo = array();

            // verification de l'existance de la methde dans l'objet
            if (method_exists($objetFrom, $method)) {
                $objetsTo = $objetFrom->$method();
            }

            // traitement des objets de destination s'il sont des actions
            if ($entityTo == 'Action') {
                foreach ($objetsTo as $objTo) {
                    $accessible = $objTo->getIsAccessible();
                    // ici on recupere les action Acessible
                    if ($accessible) {
                        $ligne['id'] = $objTo->getId();
                        $ligne['libelle'] = $objTo->__toString();
                        $rep[] = $ligne;
                    }
                }
            }

            // traitement des objets de destinantion s'il sont différents des actions
            if ($entityTo != 'Action') {
                foreach ($objetsTo as $objTo) {
                    if (is_array($attrTo)) {
                        $valid = true;

                        foreach ($attrTo as $key => $value) {
                            $method = 'get' . ucfirst($key);
                           
                            // verification de l'existance de la methde dans l'objet et des valeurs
                            if (!method_exists($objTo, $method) || $objTo->$method() != $value) {
                                $valid = false;
                            }
                        }

                        if ($valid) {
                            $ligne['id'] = $objTo->getId();
                            $ligne['libelle'] =(method_exists($objTo, '__toString'))? $objTo->__toString() : '';
                            $rep[] = $ligne;
                        }
                    }
                }
            }


            $message = count($rep) . ' ' . $entityTo . ' trouves dans ' . $entityFrom . ' selectionne';
        } else {
            $status = FALSE;
            $message = 'Le' . $entityFrom . ' selectionne n\'existe plus !';
        }
        $this->data['status'] = $status;
        $this->data['rep'] = $rep;
        $this->data['message'] = $message;
//        var_dump($rep); exit;
        return new Response(json_encode($this->data));
    }

}
