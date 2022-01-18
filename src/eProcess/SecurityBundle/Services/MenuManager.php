<?php

namespace eProcess\SecurityBundle\Services;

use Doctrine\ORM\EntityManager;

/**
 * Description of MenuManager
 *
 * @author thomas
 */
class MenuManager extends \Twig_Extension {

    private $em;
    private $bundle;

    public function __construct(EntityManager $em) {
        $this->em = $em;
        $this->bundle = 'eProcessEntityBundle:';
    }

    
    public function getFunctions() {
        return array(
//            'readTypeMenu' => new \Twig_Function_Method($this, 'readTypeMenu'),
//            'readNiveauMenu' => new \Twig_Function_Method($this, 'readNiveauMenu'),
//            'getMenusActive' => new \Twig_Function_Method($this, 'getMenusActive'),
//            'getMenusActive' => new \Twig_Function_Method($this, 'getMenusActive'),
//            'isMenuHaut' => new \Twig_Function_Method($this, 'isMenuHaut'),
//            'isMenuGauche' => new \Twig_Function_Method($this, 'isMenuGauche'),
            'getMenuParents' => new \Twig_Function_Method($this, 'getMenuParents'),
        );
    }
    
    
    
    
    
    public function getActivesMenu($idProfil) {
        $tabMenus = $this->em->getTable($this->bundle . 'Menu')
                ->getProfilMenuParent($idProfil); 
        
        return $tabMenus;
    }
    
    
    public function getMenuParents() {
    
        return $this->getParent();
    }
    
    public function getParent() {
        $tabMenus = $this->em->getTable($this->bundle . 'Menu')
                ->getMenus(true);
        
        return $tabMenus;
    }

     public function getManager() {
        return $this->em;
    }

    public function getName() {
          return 'menu_manager';
    }

}
