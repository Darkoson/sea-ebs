<?php

namespace eProcess\EntityBundle\Entity;

use eProcess\SecurityBundle\Constantes\Status;

/**
 * ProfileTable
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProfileTable extends \Doctrine\ORM\EntityTable {

    /**
     * getActionIdsFromRight : fonction de recuperation des ids des actions dont le profile 
     *      
     *      a droit en fonction de l'état du droit
     * 
     * @param type $idProfil
     * @param type $status
     * @return type
     */
    public function getActionIdsFromRight($idProfil, $status = -1) {

        $parameters['idP'] = $idProfil;
        $result = array();

        $dql = 'select distinct a.id from eProcessEntityBundle:avoirDroit droit  '
                . 'inner Join  droit.action a '
                . 'inner Join droit.profile p'
                . ' where (p.id = :idP ';
        if ($status > -1) {
            $dql .= 'and droit.isActif = :status';
            $parameters['status'] = $status;
        }

        $dql .= ')';

        $query = $this->_em->createQuery($dql);
        $query->setParameters($parameters);

        $resultSet = $query->getResult();

        foreach ($resultSet as $key => $value) {
            $result[] = $value["id"];
        }
//        var_dump($result);exit;
        return $result;
    }

    /**
     * getActionNamesFromRight : fonction de recuperation des noms des actions dont le profile 
     *      
     *      a droit en fonction de l'état du droit
     * 
     * @param type $idProfil
     * @param type $status
     * @return type
     */
    public function getActionNamesFromRight($idProfil, $status = -1) {

        $parameters['idP'] = $idProfil;
        $result = array();

        $dql = 'select distinct a.nom from eProcessEntityBundle:avoirDroit droit  '
                . 'inner Join  droit.action a '
                . 'inner Join droit.profile p'
                . ' where (p.id = :idP ';

        if ($status > -1) {
            $dql .= 'and droit.isActif = :status';
            $parameters['status'] = $status;
        }

        $dql .= ')';


        $query = $this->_em->createQuery($dql);
        $query->setParameters($parameters);

        $resultSet = $query->getResult();

        foreach ($resultSet as $key => $value) {
            $result[] = $value["nom"];
        }
//        var_dump($result);exit;
        return $result;
    }

    public function getActions($idProfile, $attrib = true, $status = -1) {

        $parameters = array();
        $dql = 'select distinct a.nom ,a.id from eProcessEntityBundle:Action a '
                . ' left Join  a.avoirDroits droit'
                . ' left Join droit.profile p'
                . ' where ( a.id ';
//                . ' where (p.id = :idP and a.id '; 

        $dql .= $attrib ? ' ' : ' not ';
        $dql .= 'in (' . implode(',', $this->getActionIdsFromRight($idProfile)) . ')';

        if ($status != -1) {
            $dql .= 'and droit.isActif = :status';
            $parameters['status'] = $status;
        }

        $dql .= ')';


        $query = $this->_em->createQuery($dql);
        $query->setParameters($parameters);

//        $resultSet = $query->getDQL() ;
        $resultSet = $query->getResult();


//        var_dump($resultSet);exit;

        return $resultSet;
    }

    /**
     * getActionsByModule : fonction de selection des actions parmodule pour un profil
     * 
     * @param type $idProfile
     * @param type $attrib
     * @param type $status
     * @return type
     */
    public function getActionsByModule($idProfile, $attrib = true, $status = -1) {

        $parameters = array();
        
        // ce controle est fait pour éviter une erreur lors de  l'appel de cette fonction
        $status = $attrib ? $status : -1;

        // recuperation des ids des actions aqui sont attribuées à ce profile
        $tabIdActionsAttrib = $this->getActionIdsFromRight($idProfile);

//        var_dump($tabIdActionsAttrib);exit;
        // requete à executer en cas recuperation des actions déjà attribuées  
        $dqlAttrib = 'select   m.nom as mod , a.id , a.description , droit.isActif  '
                . 'from eProcessEntityBundle:Action a '
                . ' inner Join  a.module m'
                . ' left Join  a.avoirDroits droit'
                . ' left Join droit.profile p'
                . ' where ( p.id = :idProfile ';

        // requete à executer en cas recuperation des actions non attribuées  
        $dqlNonAttrib = 'select   m.nom as mod , a.id , a.description  '
                . 'from eProcessEntityBundle:Action a inner Join  a.module m where ( 1=1';

        if($attrib){
            $dql =  $dqlAttrib;
            $parameters['idProfile'] = $idProfile ;
        }
        $dql = $attrib ? $dqlAttrib : $dqlNonAttrib;

        // ce controle s'effectue dans le cas où le profil n'a pas de droit attribué
        if (count($tabIdActionsAttrib)) {
            $dql .= $attrib ? '  and a.id ' : ' and a.id not ';
            $dql .= 'in (' . implode(',', $tabIdActionsAttrib) . ')';
        }

        
        if ($status != -1) {
            $dql .= 'and droit.isActif = :status';
            $parameters['status'] = $status;
        }

        $dql .= ')';


        $query = $this->_em->createQuery($dql);
        $query->setParameters($parameters);

//        $resultSet = $query->getDQL() ;
        $resultSet = $query->getResult();


//        var_dump($resultSet);exit;

        return $resultSet;
    }
    
   
    /**
     * findOther : fonction de recuperation des profiles ayant le meme nom que : $nomProfile 
     * 
     *              mais que l'identifiant est différent de $idProfile. 
     * 
     * @param int $idProfile
     * @param int $nomProfile
     * @return array
     */
    public function findOther($idProfile, $nomProfile) {
        $params['idProfile'] = $idProfile;
        $params['nomPrfile'] = $nomProfile;
        
        $query = $this->createQueryBuilder('p')
                ->where('p.id != :idProfile')
                ->andWhere('p.nom = :nomPrfile')
                ->setParameters($params)->getQuery();
        
        return $query->getResult() ;
    }
    
    /**
     * findDeletables : fonction de recuperation des profiles supprimables
     * 
     */
    public function findDeletables() {
        $result = array() ;
        
        // recuperation des profiles desactivés
        $desactiveProf = $this->createQueryBuilder('p')
                ->where('p.isActif = 0')
                ->getQuery() ->getResult() ;
        
        // recuperation des profile sans utilisateurs parmmis ces profiles desactivés
        foreach ($desactiveProf as $profile) {
            if(count($profile->getUtilisateurs()) == 0){
                $result[] = $profile ;
            }
        }
        
        // renvoi de la liste des profiles à supprimer
        return $result;
        
    }

}