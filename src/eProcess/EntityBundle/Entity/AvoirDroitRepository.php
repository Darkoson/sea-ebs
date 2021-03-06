<?php

namespace eProcess\EntityBundle\Entity;

/**
 * AvoirDroitTable
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AvoirDroitTable extends \Doctrine\ORM\EntityTable {

    /**
     * getDroitActivable : fonction de recuperation des droits non créé par l'utlisateur courant
     * 
     * @param type $idProf
     * @param type $idUser
     */
    public function getDroitActivable($idProf, $idUser, $etat) {
        $qb = $this->createQueryBuilder('d')
                ->innerJoin('d.profile', 'p');
        ($etat == "1") ? $qb->where('d.isActif = 0') : $qb->where('d.isActif = 1');

        $qb->andWhere('d.creerPar != ' . $idUser)
                ->andWhere('p.id =' . $idProf);

        return $qb->getQuery()->getResult();
    }

}
