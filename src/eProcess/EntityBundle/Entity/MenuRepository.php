<?php

namespace eProcess\EntityBundle\Entity;

use eProcess\SecurityBundle\Constantes\Status;

/**
 * MenuTable
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MenuTable extends \Doctrine\ORM\EntityTable {

    private $bundle = 'eProcessEntityBundle:';

    /**
     * getProfilMenuParent : fonction de reuperation des menus d'un profil en fonction des droits 
     * 
     *      qui sont autorisés à ce profil sur les actions pour ce profil
     * 
     * @param integer $idProfil
     * @param integer $etat
     * @return array
     */
    public function getProfilMenuParent($idProfil, $etat = Status::ACTIF) {

        // recuperation des id des actions dont le profil a des  droits autorisés
        $idActions = $this->_em->getTable($this->bundle . 'Profile')->getActionIdsFromRight($idProfil);

//        var_dump($idActions);exit;

        // recuperation des menus parents actifs
        $menuParentsActif = $this->getMenus(TRUE, $etat);

//        var_dump(count($menuParentsActif));exit;

        // recuperation des menusparents et sous menus dont le profil a droit en effectant un filtrage de ces 
        // menus avec les actions autorisées au profil qui est : $idActions
        $result = $this->getMenuFavorable($menuParentsActif, $idActions);

//                var_dump(count($menuParents));  exit;
        
        return $result;
    }

    /**
     * getMenus : fonction de recuperation  des menus actif/inactif ou parent/fils
     * 
     * @param boolean $parent
     * @param integer $etat :
     *      -1 pour indiquer que la recuperation des menus sans contrainte sur leur etat
     *      1 pour indiquer que la recuperation des menus activés ou autorisés
     *      0 pour indiquer que la recuperation des menus non activés
     * @return ArrayCollection sur les Menus
     */
    public function getMenus($parent = false, $etat = -1) {
        $qb = $this->getMenusTable($parent, $etat);
        return $qb->getQuery()->getResult();
    }

    /**
     * getMenusTable : fonction de recuperation du queryBuolder des menus actif/inactif ou parent/fils
     * 
     * @param boolean $parent
     * @param integer $etat
     * @return QueryBuilder
     */
    public function getMenusTable($parent = false, $etat = Status::ACTIF) {

        $qb = $this->createQueryBuilder('m');
        if($etat!= -1) {
            $qb ->where('m.isActif = :etat')->setParameter('etat', $etat);
        }
               
                
        // si parent = true, alors on recupere les menus parent 
        // ie des menus qui n'ont pas de parent
        if ($parent) {
            $qb->andWhere('m.parent  is null');
        } else {
            $qb->andWhere('m.parent  is not null');
        }
        $qb->orderBy('m.nom', 'DESC');

        return $qb;
    }

    /**
     * getMenuFavorable  : fonction de filtrage des menus et sous menus en fonction des 
     * 
     *      droits aux actions attachées à ces différents menus.
     * 
     * @param array $menuParents : liste des menusParents
     * @param array $idActions : id des Actions dont le profil a des droits autorisés
     * @return array
     */
    public function getMenuFavorable($menuParents, $idActions) {

        $result = array();

//        var_dump($menuParents[2]);exit;

        foreach ($menuParents as $menuP) {

            // ici je dois faire assez d'attention
            $ation = $menuP->getAction();

            $idAction = ($ation != NULL) ? $ation->getId() : NULL;
            $description = ($ation != NULL) ? $ation->getDescription() : NULL;



            if (in_array($idAction, $idActions) && $menuP->getIsActif()) {


                $data['id'] = $menuP->getId();
                $data['description'] = $description;
                $data['nom'] = $menuP->getNom();

                $data['type'] = $menuP->getType();
                ($menuP->getAction()) ? $data['route'] = $menuP->getAction()->getRoute() : '#';
                ($menuP->getParent()) ? $data['parent_id'] = $menuP->getParent()->getId() : '';

                $sousMenus = $menuP->getEnfants();
                if (count($sousMenus) > 0) {
                    $data['sousMenu'] = $this->getMenuFavorable($sousMenus, $idActions);
                } else {
                    $data['sousMenu'] = array();
                }


                $result[] = $data;
            }
        }

        return $result;
    }

    
}
