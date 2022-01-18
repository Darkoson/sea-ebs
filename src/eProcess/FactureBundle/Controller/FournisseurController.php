<?php

namespace eProcess\FactureBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use eProcess\EntityBundle\Entity\Fournisseur;
use eProcess\EntityBundle\Form\FournisseurType;
use eProcess\SecurityBundle\Constantes\Status;
use eProcess\SecurityBundle\Constantes\TypeFichier;
use eProcess\SecurityBundle\Constantes\MessageSysteme;
use eProcess\EntityBundle\Form\FichierChargementType;
use \eProcess\EntityBundle\Entity\Fichier;
use PHPExcel_IOFactory;

/**
 * Fournisseur controller.
 *
 */
class FournisseurController extends Controller {

    /**
     * constructeur de la classe
     */
    public function __construct() {

        $this->module = Controller::MOD_FOURNISSEUR;
        $this->descModule = Controller::MOD_FOURNISSEUR_DESC;
        $this->actionBundle = 'eProcessFactureBundle:';
        $this->viewFolder = 'Fournisseur:';
        parent::__construct('Fournisseur');
    }

    /**
     * listFournisseurAction : fonction d'affichage des fournisseurs
     * 
     *
     */
    public function listFournisseurAction() {

        $description = 'Liste des Fournisseurs Actifs';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $em = $this->getDoctrine()->getManager();

        // recuperation des fournisseurs
        $fournisseurs = $em->getTable($this->entityBundle . $this->entity)->findByIsActif(Status::ACTIF);
        $this->data['listeFournisseur'] = $fournisseurs;

        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'listFournisseur.html.twig', $this->data);
    }

    /**
     * listFournisseurAction : fonction d'affichage des fournisseurs
     * 
     *
     */
    public function listNewFournisseurAction() {

        $description = 'Liste des Fournisseurs Inactifs';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $em = $this->getDoctrine()->getManager();

        // recuperation des fournisseurs
        $fournisseurs = $em->getTable($this->entityBundle . $this->entity)->findByIsActif(Status::INACTIF);
        $this->data['listeFournisseur'] = $fournisseurs;

        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'listNewFournisseur.html.twig', $this->data);
    }

    /**
     * changeFournisseurStatusAction : fonction d'autorisation d'un fournisseur
     * 
     *  non implémentée
     *
     */
    public function searchFournisseurAction() {

        $description = 'Recherche Multicritère des Fournisseurs';
        $this->checkAuthorization(__FUNCTION__, $description, false);

        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {

            $data = $request->get('from');

            // recuperation des paramettres du formulaire
            $idFournisseurs = trim($request->get('idFournisseursModif'));
            $status = trim($request->get('status'));

//            
            // enregistrement des fournisseurs au cas où il existe au moins un identifiant
            if ($idFournisseurs) {
                $tabIdFournisseurs = explode('/', $idFournisseurs);

                // modification des fournisseurs  à partir de leur ids 
                $this->gererStatus($this->entityBundle, $this->entity, $tabIdFournisseurs, $status);
            }
        }

        // redirection sur la vue des liste des fournisseurs
        return $this->redirect($this->generateUrl('list_fournisseur'));
    }

    /**
     * changeFournisseurStatusAction : fonction de suppression des fournisseurs désactiver d'un fournisseur
     * 
     *  
     *
     */
    public function removeFournisseurAction() {

        $description = 'SuppresSion des  Fournisseurs Inactifs';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {

            // recuperation des paramettres du formulaire
            $idFournisseurs = trim($request->get('idFournisseursDelete'));

            // enregistrement des fournisseurs au cas où il existe au moins un identifiant
            if ($idFournisseurs) {
                $tabIdFournisseurs = explode('/', $idFournisseurs);

                // Supression des fournisseurs  à partir de leur ids 
                $this->gererStatus($this->entityBundle, $this->entity, $tabIdFournisseurs, Status::SUPPRIME_VRAIE);
            }
        }

        $em = $this->getDoctrine()->getManager();
        // recuperation des fournisseurs désactiver pour une suppression
        $fournisseurs = $em->getTable($this->entityBundle . $this->entity)->findDeletables();

        $this->data['listeFournisseur'] = $fournisseurs;
        // redirection sur la vue des liste des fournisseurs

        return $this->render($this->actionBundle . $this->viewFolder . 'removeFournisseur.html.twig', $this->data, $this->response);
    }
    /**
     * statusFournisseurAction : fonction d'autorisation des fournisseurs désactivés
     * 
     *  
     *
     */
    public function statusFournisseurAction() {

        $description = 'Autorisation des Fournisseurs';
        $this->checkAuthorization(__FUNCTION__, $description, false);

        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {

            // recuperation des paramettres du formulaire
            $idFournisseurs = trim($request->get('idFournisseurs'));

            // enregistrement des fournisseurs au cas où il existe au moins un identifiant
            if ($idFournisseurs) {
                $tabIdFournisseurs = explode('/', $idFournisseurs);

                // Supression des fournisseurs  à partir de leur ids 
                $this->gererStatus($this->entityBundle, $this->entity, $tabIdFournisseurs, Status::ACTIF);
            }
        }

        return $this->redirectToRoute('list_new_fournisseur') ;
    }

    /**
     * addFournisseurAction : fonction d'ajout d'un nouvel fournisseur 
     *
     */
    public function addFournisseurAction() {

        $description = 'Création d\'un Fournisseur ';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        $fournisseur = new Fournisseur();

        $form = $this->createForm(new FournisseurType(), $fournisseur, array(
            'action' => $this->generateUrl('add_fournisseur'),
            'method' => 'POST',
        ));

        // recuperation des informations du formaulaire
        if ($request->getMethod() == 'POST') {

            $form->bind($request);
            // enregstrement des informations du fournisseur au cas où le formulaire est valide
            if ($form->isValid()) {
                $fournisseur = $form->getData();
                $fic = $fournisseur->getFichier();
                if ($fic) {
                    $fic->setType(TypeFichier::Contrat);
                }

                $fournisseur->setCreerPar($this->getCurrentId());


                $em->persist($fournisseur);
                $em->flush();


                $this->get('session')->getFlashBag()->add('ok', MessageSysteme::AJOUT_SUCCES);
            } else {
                $this->get('session')->getFlashBag()->add('ko', MessageSysteme::AJOUT_ECHEC . ' à cause de fichier');
            }

            // redirection sur la vue des liste des fournisseurs
            return $this->redirect($this->generateUrl('add_fournisseur'));
        }

        $this->data['form'] = $form->createView();

        return $this->render($this->actionBundle . $this->viewFolder . 'addFournisseur.html.twig', $this->data, $this->response);
    }

    /**
     * Displays a form to edit an existing Fournisseur entity.
     *
     */
    public function editFournisseurAction($idFournisseur) {

        $description = 'Modification d\'un Fournisseur';
        $this->checkAuthorization(__FUNCTION__, $description, false);

        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        // recuperation du fournisseur à modifier
        $fournisseur = $em->getTable($this->entityBundle . $this->entity)->find($idFournisseur);
        if (!$fournisseur) {
            $this->get('session')->getFlashBag()->add('ko', MessageSysteme::INEXISTANT);
            return $this->redirectToRoute('list_fournisseur');
        }


        $form = $this->createForm(new FournisseurType(), $fournisseur, array(
            'action' => $this->generateUrl('edit_fournisseur', array('idFournisseur' => $idFournisseur)),
            'method' => 'POST',
        ));

        // recuperation des informations du formaulaire
        if ($request->getMethod() == 'POST') {

            $form->bind($request);
            // enregstrement des informations du fournisseur au cas où le formulaire est valide
            if ($form->isValid()) {
                $fournisseur = $form->getData();

                $fic = $fournisseur->getFichier();
                if ($fic) {
                    $fic->setType(TypeFichier::Contrat);
                }
                
                $em->persist($fournisseur);
                $em->flush();
                $this->get('session')->getFlashBag()->add('ok', MessageSysteme::MODIF_SUCCES);
            } else {
                $this->get('session')->getFlashBag()->add('ko', MessageSysteme::MODIF_ECHEC . ' à cause de fichier');
            }

            // redirection sur la vue des liste des fournisseurs
            return $this->redirectToRoute('list_fournisseur');
        }

        $this->data['form'] = $form->createView();
        $this->data['fournisseur'] = $fournisseur;

        return $this->render($this->actionBundle . $this->viewFolder . 'editFournisseur.html.twig', $this->data, $this->response);
    }

    /**
     * chargerFournisseurAction : fonction de chargement des fournisseur à partir
     *  d'un fichier Excel
     * 
     */
    public function chargerFournisseurAction() {
        $description = 'Chargement des Fournisseurs  depuis un fichier Excel ';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $form = $this->createForm(new FichierChargementType(), array(), array(
            'action' => $this->generateUrl('load_fournisseur'),
            'method' => 'POST',
            'attr' => array('id' => 'chargerFournisseurForm', 'onsubmit' => 'afficheImageExecution(); '),
        ));

        $request = $this->getRequest();
        $fichier = new Fichier();

        // traitemet du fichier teechargé
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            $data = $form->getData();
            $fichier = $data['fichier'];
            $fichier->setType(TypeFichier::ListeFournisseur);
            // preUpload : on met à jour de champs et effectue des traitements pour que l'entité
            // soit déplacé dans le bon repertoir 
            $fichier->preUpload();
//            var_dump($fichier->getExtention());exit;
            // si le fichier chargé n'est pas excel alors on le rejette
            if (!$fichier->isExcelFile()) {
                $this->get('session')->getFlashBag()->add('ko', ' Format du fichier incorrect : il faut un fichier Excel');
                return $this->redirectToRoute('load_fournisseur');
            }

            // si tout est bien alors on deplace le fichier dans le bon repertoire pour effectuer  le traitement
            $fichier->upload();

            $cheminFichier = $fichier->getAbsolutePath();
       
            // lecture du fichier
            $objPHPExcel = PHPExcel_IOFactory::load($cheminFichier);


            $em = $this->getDoctrine()->getManager();

            // On boucle sur les feuillets
            $i = 0 ;
            $resutat = array() ;
            $ligne1OK = $ligne2OK = true ;
            $sheet = $objPHPExcel->getSheet(0);
            while ($ligne1OK || $ligne2OK){
                // chargement d'un feuillet du fichier
                $resutat[] = $this->chargerFeuillet($sheet) ;
                $i++; 
                $sheet = $objPHPExcel->getSheet($i);
                // ici on verifie si la ligne 1 ou 2 du fichier à un fournisseur avant de la charger
                $ligne1OK = ($sheet->getCell('A' . $i)->getValue());
                $ligne2OK = ($sheet->getCell('A' . ($i+1))->getValue());
            } 


            $em->flush();
            
            //suppression du fichier chargé après traitement
            $fichier->preRemoveUpload();
            $fichier->removeUpload();
            
            $this->data['resultat'] = $resutat ;
            $this->get('session')->getFlashBag()->add('ok', MessageSysteme::OPERATION_SUCCES);
             
        }


        $this->data['form'] = $form->createView();
        return $this->render($this->actionBundle . $this->viewFolder . 'chargerFournisseur.html.twig', $this->data);
    }
    
      
    /** 
     * chargerFeuillet : fonction de chargement des fournisseurs d'un feuillet suivant un format defini
     * 
     * @param type $sheet
     * @return type
     */
    private function chargerFeuillet($sheet) {

        $em = $this->getDoctrine()->getManager();

        // On boucle sur les lignes

        $fourNonCharges =  array();

        $ligneActuelleOK = $ligneSuivanteOK = true; // variable qui indique que la ligne qui suit la ligne courante à  un numero de bon de commande et un objet

        $i = 1; // indice de la ligne courante
        // tant que la ligne courante  ou suivante est conforme, on poursuit le chargement de la ligne conforme 
        while ($ligneActuelleOK || $ligneSuivanteOK) {
            
            $nomFour = trim($sheet->getCell('A' . $i)->getFormattedValue());
           
            $existeFour = $em->getTable($this->entityBundle . $this->entity)->findByNom($nomFour);

            // si le bon de commande  a un mumero et n'existe pas encore, on le charge dans la base de données
            if ((count($existeFour) == 0) && $nomFour ) {
                
                $list[$i] = new Fournisseur(false);
                
                $list[$i]->setNom($nomFour);
                $list[$i]->setCreerPar($this->getCurrentId()) ;
                $list[$i]->setDevisePrefere($sheet->getCell('B' . $i)->getValue());
                $list[$i]->setLanguePrefere($sheet->getCell('C' . $i)->getValue());

                $list[$i]->setTypeOrganisation($sheet->getCell('D' . $i)->getValue());
                $list[$i]->setVerification($sheet->getCell('E' . $i)->getValue());
                $list[$i]->setTelephone($sheet->getCell('F' . $i)->getValue());
                $list[$i]->setEmail($sheet->getCell('G' . $i)->getValue());

                $list[$i]->setHasContrat($sheet->getCell('H' . $i)->getValue());
                $list[$i]->setRepresentant($sheet->getCell('I' . $i)->getValue());
                $list[$i]->setAgence($sheet->getCell('J' . $i)->getValue());
                $list[$i]->setAdresse($sheet->getCell('K' . $i)->getValue());

                $list[$i]->setBoitePostale($sheet->getCell('L' . $i)->getValue());
                $list[$i]->setPays($sheet->getCell('M' . $i)->getValue());
                $list[$i]->setModePaiement($sheet->getCell('N' . $i)->getValue());
                $list[$i]->setCompteBancaire($sheet->getCell('O' . $i)->getValue());
                $list[$i]->setBanque($sheet->getCell('P' . $i)->getValue());
               
                $em->persist($list[$i]);
               
            }
            // au cas ou un bon existe déjà avec ce bon de commande
            else {
                $fourNonCharges[] = $nomFour;
            }

            $i++;
            $j = $i + 1;

            $ligneActuelleOK = ($sheet->getCell('A' . $i)->getValue() );
            $ligneSuivanteOK = ($sheet->getCell('A' . $j)->getValue() );
        }

        $em->flush();

        $total = $i - 1;
        $result['titre'] = $sheet->getTitle();
        $result['total'] = $total;
        $result['charge'] = $total - count($fourNonCharges);

        return $result;
    }

    

}
