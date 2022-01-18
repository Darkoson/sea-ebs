<?php

namespace eProcess\EntityBundle\Entity;
use  eProcess\SecurityBundle\Constantes\Status;

/**
 * UtilisateurTable
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UtilisateurTable extends \Doctrine\ORM\EntityTable
{
    public function findDeletables() {
        
        // recuperation des profiles desactivés
        $desactiveUser = $this->createQueryBuilder('u')
                ->where('u.isActif ='.Status::INACTIF)
                ->getQuery() ->getResult() ;
        // renvoi de la liste des profiles à supprimer
        return $desactiveUser;
    }
    
    public function findNotDeleted() {
        
        // recuperation des profiles desactivés
        $nonSupprime = $this->createQueryBuilder('u')
                ->where('u.isActif != '.Status::SUPPRIME )
                ->getQuery() ->getResult() ;
        // renvoi de la liste des profiles à supprimer
        return $nonSupprime;
    }
    
    public function isApprobateurValid($id) {
        
//        var_dump($id) ; exit;
        // recuperation des profiles desactivés
        $listUser = $this->createQueryBuilder('u')
                ->innerJoin('u.profile','p')
                ->where('u.id = '.$id )
                ->andWhere('p.isActif = '.Status::ACTIF )
                ->andWhere('p.isActif = '.Status::ACTIF )
                ->andWhere('p.canApprove = '. true)
                ->getQuery() ->getResult() ;
        
        // renvoi de l'utilisateur 
        return count($listUser)? $listUser[0] : null ;
    }
}
