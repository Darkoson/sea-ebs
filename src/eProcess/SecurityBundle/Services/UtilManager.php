<?php

/**
 * Description of DroitManager
 *
 * @author 
 */

namespace eProcess\SecurityBundle\Services;

use eProcess\SecurityBundle\Constantes\Status;
use eProcess\EntityBundle\Entity\Module;
use eProcess\EntityBundle\Entity\Action;
use Doctrine\ORM\EntityManager;

class UtilManager {

    private $em;
    private $entityBundle;

    public function __construct(EntityManager $em) {
        $this->em = $em;
        $this->entityBundle = 'eProcessEntityBundle:';
    }

    public function getEntities($bundle, $classe, $etat = -1, $etape = -1, $autres = array()) {
        $entities = $resultat = array();
        $this->entityBundle = $bundle . ':';
        $params = array();

        if ($etat != -1) {
            $params['isActif'] = $etat;
        }
        if ($etape != -1) {
            $params['etape'] = $etape;
        }
        if (is_array($autres)) {
            foreach ($autres as $key => $value) {
                
                // on recupere les proprietes qui existent seule !
                if(property_exists('eProcess\EntityBundle\Entity\\'.$classe, $key)){
                    $params[$key] = $value;
                }
                
            }
        }
        
        



        if (count($params) > 0) {
            $entities = $this->em->getTable($this->entityBundle . $classe)->findBy($params);
        } else {
            $entities = $this->em->getTable($this->entityBundle . $classe)->findAll();
        }
        foreach ($entities as $objet) {
            $resultat[$objet->getId()] = $objet->__toString();
        }
        return $resultat;
    }

    public function registerAction($nomMod, $descModule, $nomAct, $descAction, $isAccessibleForMenus = true) {
        // initialisation de la réponse
        $rep = FALSE;
        /* Contrôles liés aux paramètres */
        $nomModule = trim($nomMod);
        $nomAction = trim($nomAct);
        /* fin Contrôles liés aux paramètres */



        // recuperation du module
        $module = $this->em->getTable($this->entityBundle . 'Module')->findOneByNom($nomModule);
        if ($module == NULL) { //enregistrment du module au cas ou il est innexistant
            $module = new Module($nomModule, $descModule);
            $this->em->persist($module);
        }


        // récupération de l'action par rapport au module
        $action = $this->em->getTable($this->entityBundle . 'Action')->findOneBy(array('nom' => $nomAction, 'module' => $module));

        //enregistrment de l'action au cas ou elle est innexistante
        if ($action == NULL) {
            $action = new Action($nomAction, $descAction, $isAccessibleForMenus);
            $action->setModule($module);
            $this->em->persist($action);
            $this->em->flush();

            $module->addAction($action);
            $this->em->flush();
        }

        return $rep;
    }

    /**
     * updateActionRoute : fonction de mis à jour de la route d'une action existante
     * 
     * @param type $nomAction
     * @param type $nomRoute
     */
    public function updateActionRoute($nomAction, $nomRoute, $isAccessible) {
        $params['isAccessible'] = $isAccessible;
        $params['nom'] = $nomAction;

        $action = $this->em->getTable($this->entityBundle . 'Action')->findByNom($nomAction);
        if (count($action) > 0) {
            $action[0]->setRoute($nomRoute);
            $action[0]->setIsAccessible($isAccessible);
            $this->em->persist($action[0]);
            $this->em->flush();
        }
    }

    /**
     * getEntityByID : recuperation d'un element à patir de son ID 
     * et du nom de sa classe
     * 
     * @param type $idEntity
     * @param type $class
     * @param type $bundle
     */
    public function getEntityByID($idEntity = 0, $class = 'Utilisateur', $bundle = 'eProcessEntityBundle:') {
        $id = intval($idEntity);
        return $this->em->getTable($bundle . $class)->find($id);
    }

}
