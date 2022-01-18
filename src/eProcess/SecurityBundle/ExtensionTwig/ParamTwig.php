<?php

namespace eProcess\SecurityBundle\ExtensionTwig;

use eProcess\SecurityBundle\Constantes\SessionKeys;
use eProcess\SecurityBundle\Constantes\TypeDate;
use eProcess\SecurityBundle\Constantes\TypeTaxe;
use eProcess\SecurityBundle\Services\DroitManager;
use eProcess\SecurityBundle\Services\UtilManager;

class ParamTwig extends \Twig_Extension {

    private $droitManager;
    private $utilManager;
    private $em;
    private $bundle;

    // pour le moment je n'utilise pas enore le service container
    public function __construct(DroitManager $dm,  UtilManager $util) {
        $this->droitManager = $dm;
        $this->utilManager = $util;
        $this->em = $this->droitManager->getManager();
        $this->bunde = 'eProcessEntityBundle:';
    }

    public function getFunctions() {
        return array(
            'getUserActionIDs' => new \Twig_Function_Method($this, 'getUserActionIDs'),
            'getUserActionNames' => new \Twig_Function_Method($this, 'getUserActionNames'),
            'getUserProfil' => new \Twig_Function_Method($this, 'getUserProfil'),
            'getUserProfilCode' => new \Twig_Function_Method($this, 'getUserProfilCode'),
            'getUserProfilId' => new \Twig_Function_Method($this, 'getUserProfilId'),
            'getMenusActives' => new \Twig_Function_Method($this, 'getMenusActives'),
            'getCurrentUser' => new \Twig_Function_Method($this, 'getCurrentUser'),
            'getCurrentID' => new \Twig_Function_Method($this, 'getCurrentID'),
            'getNextMatricule' => new \Twig_Function_Method($this, 'getNextMatricule'),
            'getSessionKey' => new \Twig_Function_Method($this, 'getSessionKey'),
            'getUsername' => new \Twig_Function_Method($this, 'getUsername'),
            'getHomeRoute' => new \Twig_Function_Method($this, 'getHomeRoute'),
            'getEntities' => new \Twig_Function_Method($this, 'getEntities'),
            'getTypeTaxe' => new \Twig_Function_Method($this, 'getTypeTaxe'),
            'readTaxe' => new \Twig_Function_Method($this, 'readTaxe'),
            'getEntityByID' => new \Twig_Function_Method($this, 'getEntityByID'),
        );
    }

    public function getUserActionIDs() {
        return $this->droitManager->get(SessionKeys::NAME_ACTIONS);
    }

    public function getUserActionNames() {
        return $this->droitManager->get(SessionKeys::NAME_ACTIONS);
    }

    public function getUserProfilId() {
        return $this->droitManager->get(SessionKeys::ID_PROFIL);
    }

    public function getUserProfil() {
        return $this->droitManager->get(SessionKeys::PROFIL);
    }

    public function getUserProfilCode() {
        return $this->droitManager->get(SessionKeys::CODE_PROFIL);
    }

    public function getCurrentIdProfil() {
        return $this->droitManager->get(SessionKeys::ID_PROFIL);
    }

    public function getMenusActives() {
        return $this->droitManager->get(SessionKeys::MENUS_TITRE);
    }

    public function getCurrentUser() {
        return $this->droitManager->get(SessionKeys::USER);
    }

    public function getCurrentID() {
        return $this->droitManager->get(SessionKeys::USER_ID);
    }

    public function getNextMatricule() {
        return $this->em->getTable($this->bundle . 'Facture')->getNextMatricule();
    }

    public function getSessionKey() {
        return $this->droitManager->getSessionKey();
    }

    public function getUsername() {
        return $this->droitManager->get(SessionKeys::USER_USERNAME);
    }

    public function getHomeRoute() {
        return $this->droitManager->get(SessionKeys::HOME_ROUTE);
    }

    public function getTypeDate() {
        return TypeDate::getTypeDate();
    }
    public function getTypeTaxe() {
        return TypeTaxe::getTypeTaxe();
    }
    public function readTaxe($codeTaxe) {
        return TypeTaxe::readTaxe($codeTaxe);
    }

    public function getName() {
        return 'type_param';
    }

    
    public function getEntities($bundle , $classe, $etat = -1, $etape=-1, $autres= array()) {
        return $this->utilManager->getEntities($bundle, $classe, $etat,$etape, $autres);
    }
    
    public function getEntitiesIds($bundle , $classe, $etat = -1,$etape=-1,$autres= array() ) {
        $ids  = array();
        $entities =  $this->utilManager->getEntities($bundle, $classe, $etat,$etape, $autres);
        foreach ($entities as $id => $lib) {
            $ids[] = $id ;
        }
        return $ids;
    }
    
    public function getEntityByID($id, $class, $bundle='eProcessEntityBundle:') {
        return $this->utilManager->getEntityByID($id, $class, $bundle) ;
    }
}
