<?php

/**
 * Description of DroitManager
 *
 * @author 
 */

namespace eProcess\TransfertBundle\Services;

use Doctrine\ORM\EntityManager ;
use eProcess\SecurityBundle\Constantes\EtapeTranfert;

class TransfertManager {

    private $em;
    private $bundle;
    private $entity;
    
   

    public function __construct( EntityManager $manager) {

        $this->em = $manager;
        $this->bundle = 'eProcessEntityBundle:';
        $this->entity= 'Transfert';
        
    }

    /**
     * getNbreTransfertAttenteReception : recuperation du nombre de transfert en attente de reception
     * 
     * @param type $codeProfile : code du profile concerné
     * @return array
     */
    public function getNbreTransfertAttenteReception($codeProfile, $etapeFacture = 0) {
        
        $nbreTransfert = $this->em->getTable($this->bundle.$this->entity)
                ->findTransfert($codeProfile, EtapeTranfert::RECEVOIR, false, $etapeFacture);
        
        return count($nbreTransfert);
    }

     /**
     * getNbreTransfertEncoursEnvoi : recuperation du nombre de transfert encours d'envoi
     * 
     * @param type $codeProfile : code du profile concerné
     * @return array
     */
    public function getNbreTransfertEncoursEnvoi($codeProfile, $etapeFacture = 0) {
        
        $nbreTransfert = $this->em->getTable($this->bundle.$this->entity)
                ->findTransfert($codeProfile, EtapeTranfert::ENVOYER, false, $etapeFacture);
        
        return count($nbreTransfert);
    }

}
