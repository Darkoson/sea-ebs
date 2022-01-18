<?php

namespace eProcess\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use eProcess\EntityBundle\Entity\Menu;
use eProcess\SecurityBundle\Constantes\Status;
use eProcess\SecurityBundle\Constantes\MessageSysteme ;

/**
 * Menu controller.
 *
 */
class MenuController extends Controller {

    /**
     * constructeur de la classe
     */
    public function __construct() {

        $this->module = Controller::MOD_MENU;
        $this->descModule = Controller::MOD_MENU_DESC;
        $this->actionBundle = 'eProcessSecurityBundle:';
        $this->viewFolder = 'Menu:';
        parent::__construct('Menu');
    }

    /**
     * listMenuAction : fonction d'affichage des menus
     * 
     *
     */
    public function listMenuAction() {

        $description = 'Liste des Menus';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $em = $this->getDoctrine()->getManager();

        // recuperation des menus parent sans poser de contrainte sur leur état
        $parents = $em->getTable($this->entityBundle . $this->entity)->getMenus(TRUE, -1);
        $this->data['Menusparents'] = $parents;

        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'listMenu.html.twig', $this->data);
    }

    /**
     * changeMenuStatusAction : fonction d'autorisation d'un menu
     * 
     *  
     *
     */
    public function activateMenuAction() {

        $description = 'Autorisation des Menus Inactifs';
        $this->checkAuthorization(__FUNCTION__, $description, true);
        
        $em = $this->getDoctrine()->getManager() ;
        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {

            // recuperation des paramettres du formulaire
            $idMenus = trim($request->get('idMenusModif'));
     
            // enregistrement des menus au cas où il existe au moins un identifiant
            if ($idMenus) {
                $tabIdMenus = explode('/', $idMenus);
                
                // modification des menus  à partir de leur ids 
                $this->gererStatus($this->entityBundle, $this->entity, $tabIdMenus, Status::ACTIF);
            }
        }
        
        $listeMenu = $em->getTable($this->entityBundle.$this->entity)->findByIsActif( Status::INACTIF);
        $this->data['listeMenu'] = $listeMenu ;
        
        // redirection sur la vue des liste des menus
        return $this->render($this->actionBundle.$this->viewFolder.'listMenuInactifs.html.twig',  $this->data);
    }
    /**
     * changeMenuStatusAction : fonction d'autorisation d'un menu
     * 
     *  
     *
     */
    public function deactivateMenuAction() {

        $description = 'Désactivation des Menus Actifs'; 
        $this->checkAuthorization(__FUNCTION__, $description, true);

       $em = $this->getDoctrine()->getManager() ;
        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {

            // recuperation des paramettres du formulaire
            $idMenus = trim($request->get('idMenusModif'));
     
            // enregistrement des menus au cas où il existe au moins un identifiant
            if ($idMenus) {
                $tabIdMenus = explode('/', $idMenus);
                
                // modification des menus  à partir de leur ids 
                $this->gererStatus($this->entityBundle, $this->entity, $tabIdMenus, Status::INACTIF);
            }
        }
        
        $listeMenu = $em->getTable($this->entityBundle.$this->entity)->findByIsActif( Status::ACTIF);
        $this->data['listeMenu'] = $listeMenu ;
        
        // redirection sur la vue des liste des menus
        return $this->render($this->actionBundle.$this->viewFolder.'listMenuActifs.html.twig',  $this->data);
    }

    /**
     * changeMenuStatusAction : fonction d'autorisation d'un menu
     * 
     *  
     *
     */
    public function changeMenuStatusAction($status) {

        $description = 'Autorisation des Menus';
        $this->checkAuthorization(__FUNCTION__, $description, false);

        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {

            // recuperation des paramettres du formulaire
            $idMenus = trim($request->get('idMenusModif'));
            $status = trim($request->get('status'));

//            
            // enregistrement des menus au cas où il existe au moins un identifiant
            if ($idMenus) {
                $tabIdMenus = explode('/', $idMenus);
                
                // modification des menus  à partir de leur ids 
                $this->gererStatus($this->entityBundle, $this->entity, $tabIdMenus, $status);
            }
        }

        // redirection sur la vue des liste des menus
        return $this->redirect($this->generateUrl('list_menu'));
    }

    /**
     * changeMenuStatusAction : fonction d'autorisation d'un menu
     * 
     *  
     *
     */
    public function removeMenuAction() {

        $description = 'Suppression des  Menus';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {

            // recuperation des paramettres du formulaire
            $idMenus = trim($request->get('idMenusDelete'));

            // enregistrement des menus au cas où il existe au moins un identifiant
            if ($idMenus) {
                $tabIdMenus = explode('/', $idMenus);

                // Supression des menus  à partir de leur ids 
                $this->gererStatus($this->entityBundle, $this->entity, $tabIdMenus, Status::SUPPRIME_VRAIE);
            }
        }
        $em = $this->getDoctrine()->getManager();
        // recuperation des menus désactiver 
        $menusSupprimables = $em->getTable($this->entityBundle . $this->entity)->findByIsActif(false);

        $this->data['listeMenu'] = $menusSupprimables;
        // redirection sur la vue des liste des profiles

        return $this->render($this->actionBundle . $this->viewFolder . 'removeMenu.html.twig', $this->data, $this->response);
    }

    /**
     * addMenuAction : fonction d'ajout d'un nouveau menu 
     *
     */
    public function addMenuAction() {

        $description = 'Creation d\'un  nouveau Menu';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        // recuperation des informations du formaulaire
        if ($request->getMethod() == 'POST') {

            // recuperation des paramettres du formulaire
            $nomMenu = trim($request->get('name'));
            $idParent = trim($request->get('parent'));
            $idAction = trim($request->get('action'));

            // enregistrement des menus au cas où il existe au moins une action 
            if ($idAction) {
                $action = $em->getTable($this->entityBundle . 'Action')->find($idAction);
                $menuParent = $em->getTable($this->entityBundle . $this->entity)->find($idParent);

                $newMenu = new Menu($nomMenu);
                $newMenu->setAction($action);
                $newMenu->setParent($menuParent);
                $newMenu->setCreerPar($this->getCurrentId());
                $newMenu->setDateCreation(new \DateTime());
                $newMenu->setIsActif(false);
                $em->persist($newMenu);
                $menuParent ? $menuParent->addEnfant($newMenu) : $newMenu->setIsActif(1);
                // enregistrement du menu

                $em->flush();
                $this->get('session')->getFlashBag()->add('ok', MessageSysteme::AJOUT_SUCCES);
            }

            // redirection sur la vue des liste des menus
            return $this->redirect($this->generateUrl('list_menu'));
        }


        return $this->render($this->actionBundle . $this->viewFolder . 'addMenu.html.twig', $this->data, $this->response);
    }

    /**
     * Displays a form to edit an existing Menu entity.
     *
     */
    public function editMenuAction($idMenu) {

        $description = 'Modification d\'un Menu';
        $this->checkAuthorization(__FUNCTION__, $description, false);

        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        // recuperation du menu à modifier
        $menuModifiable = $em->getTable($this->entityBundle . $this->entity)->find($idMenu);

        // si le menu exoste on poursuit le traitement, au cas contraire on redirige sr la liste des menus
        if ($menuModifiable) {
            // recuperation des informations du formaulaire
            if ($request->getMethod() == 'POST') {

                // recuperation des paramettres du formulaire
                $nomMenu = trim($request->get('name'));
                $idParent = trim($request->get('parent'));
                $idAction = trim($request->get('action'));

                // enregistrement des menus au cas où il existe au moins une action 
                if ($idAction) {
                    $action = $em->getTable($this->entityBundle . 'Action')->find($idAction);
                    $menuParent = $em->getTable($this->entityBundle . $this->entity)->find($idParent);

                    $menuModifiable->setNom($nomMenu);
                    $menuModifiable->setAction($action);
                    $menuModifiable->setParent($menuParent);
                    $menuModifiable->setCreerPar($this->getCurrentId());
                    $menuModifiable->setDateCreation(new \DateTime());
                    $menuParent ? $menuParent->addEnfant($menuModifiable) : $menuModifiable->setIsActif(true);


                    // enregistrement du menu
                    $em->flush();
                    $this->get('session')->getFlashBag()->add('ok', MessageSysteme::MODIF_SUCCES);
                }
                

                // redirection sur la vue des liste des menus
                return $this->redirect($this->generateUrl('list_menu'));
            }

            // retour sur la page de modification des menus apres reuperation des informtions sur le menus
            $this->data['entity'] = $menuModifiable;

            return $this->render($this->actionBundle . $this->viewFolder . 'editMenu.html.twig', $this->data, $this->response);
        }
        $this->get('session')->getFlashBag()->add('ko', MessageSysteme::INEXISTANT);


        return $this->render($this->entityBundle . $this->viewFolder . ':listMenu.html.twig', $this->data, $this->response);
    }

}
