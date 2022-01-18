<?php

namespace eProcess\FactureBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use eProcess\EntityBundle\Entity\Facture;
use eProcess\SecurityBundle\Constantes\Status;
use eProcess\SecurityBundle\Constantes\MessageSysteme;
use eProcess\SecurityBundle\Constantes\EtapeFacture;
use eProcess\SecurityBundle\Constantes\EtapeBon;
use eProcess\EntityBundle\Entity\Achat;
use eProcess\EntityBundle\Entity\Fichier;
use eProcess\EntityBundle\Form\AchatReceptionType;
use eProcess\SecurityBundle\Constantes\EtapeAchat;
use eProcess\SecurityBundle\Constantes\TypeFichier;

/**
 * Facture controller.
 *
 */
class FactureArriveController extends Controller {

    /**
     * constructeur de la classe
     */
    public function __construct() {

        $this->module = Controller::MOD_FCT_RECEPTION;
        $this->descModule = Controller::MOD_FCT_RECEPTION_DESC;
        $this->actionBundle = 'eProcessFactureBundle:';
        parent::__construct('Facture');
        $this->viewFolder = 'FCT_Reception:';
    }

    /**
     *  indexFactureAction : tableau de bord des factures
     * 
     * @return type
     */
    public function indexFactureAction() {

        $description = 'Tableau de Bord des Factures';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $em = $this->getDoctrine()->getManager();

        $resultStat = $em->getTable($this->entityBundle . $this->entity)
                ->getStatistiqueEtape($this->getCurrentCodeProfil());

        $this->data['stat'] = $resultStat;

//        var_dump($resultStat);exit;
        return $this->render($this->actionBundle . $this->viewFolder . 'index.html.twig', $this->data);
    }

    public function receptionFactureAction() {

        $description = 'Réception des Factures';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $achat = new Achat();
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        // recuperation des informations du formaulaire
        if ($request->getMethod() == 'POST') {
            // phase de recuperation des informations de l'achat relatif au bon selectionné 

            $idBon = trim($request->get('idBon'));
            $bonCommande = $em->getTable($this->entityBundle . 'BonCommande')->find($idBon);
            if ($bonCommande) {
                $this->data['bonCommande'] = $bonCommande;
                $achat = $bonCommande->getAchat();
            }
        }

        // recuperation du numero d'ordre de cette nouvelle facture
        $ordre = $em->getTable($this->entityBundle . $this->entity)->getNextNumber();
        $form = $this->createForm(new AchatReceptionType(), $achat);
        $this->data['ordreArrive'] = $ordre;
        $this->data['listeBons'] = $em->getTable($this->entityBundle . 'BonCommande')->getBonOfReception();
        $this->data['form'] = $form->createView();

        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'reception.html.twig', $this->data);
    }

    /**
     * enregistrerFactureArriveAction : Enregistrement d'une facture arrivée
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function enregistrerFactureArriveAction(\Symfony\Component\HttpFoundation\Request $request) {

        $description = 'Enregistrement des Factures Arrivées';
        $this->checkAuthorization(__FUNCTION__, $description, false);

        $em = $this->getDoctrine()->getManager();
        $achat = new Achat();



        // s'il existe un bon de commande relatf à la facture qu'on veut enregistrer, 
        // on recupere l'instance d'achat relatif à ce bon pour lui attribuer la facture
        $existBon = trim($request->get('existBon'));

        if ($existBon == '1') {

            $idAchat = $request->request->getInt('idAchat');
            $achat = $em->getTable($this->entityBundle . 'Achat')->find($idAchat);

            // ici nous changeons l'état de la facture du bon de commande
            $bon = $achat ? $achat->getBonCommande() : null;
            $bon ? $bon->setEtape(EtapeBon::BON_ARRIVE_FACTURE) : '';
        }

        // on verifie si une facture est déja arrivé pour ce bon de commande
        // et si oui, on affiche un message d'erreur
        $facureEnreg = $achat ? $achat->getFacture() : '';

        if ($facureEnreg) {
            $this->get('session')->getFlashBag()->add('ko', MessageSysteme::FACT_DEJA_ENRENG);
            return $this->redirect($this->generateUrl('facture_reception'));
        }

        $form = $this->createForm(new AchatReceptionType(), $achat);

        $form->handleRequest($request);


        // recuperation des infromation de l'achat ie: qui sont saisies sur le formulaire
        if ($request->getMethod() == 'POST') {

            // recuperation des informations de la facture
            $dateArriveString = trim($request->get('dateArrive'));
            $dateFactureString = trim($request->get('dateFacture'));
            $nomFicFacture = $request->get('ficFacture');
            $refFacture = trim($request->get('refFact'));
            
            

            // affichage d'un message d'erreur au cas où un de  ces informtionsest vide
            if (!$dateArriveString && !$refFacture && !$dateFactureString && !$nomFicFacture) {
                $this->get('session')->getFlashBag()->add('ko', MessageSysteme::AJOUT_ECHEC);
                return $this->redirect($this->generateUrl('facture_reception'));
            }

            // preparation des informations de la facture
            $dateArrive = \DateTime::createFromFormat('d-m-Y', $dateArriveString);
            $dateFacture = \DateTime::createFromFormat('d-m-Y', $dateFactureString);
            
            // Enregistrement de la nouvelle facture arrivée
            $facture = new Facture();
            $facture->setCreerPar($this->getCurrentId());
            $facture->setDateArrive($dateArrive);
            $facture->setDateFacture($dateFacture);
            $facture->setEtape(EtapeFacture::FCT_ARRIVE_RECEPTION);
            $facture->setReferenceFournisseur($refFacture);

            // traitement du fichier de la facture
            
            $file = $request->request->all()
//                    files->all() 
                    ;
            var_dump($nomFicFacture);
            var_dump($_FILES);
            exit;
            $objFichier = new Fichier(TypeFichier::Facture);
            $objFichier->setFichier($file);
            // recuperation du numero d'ordre de cette nouvelle facture 
            $ordre = $em->getTable($this->entityBundle . $this->entity)->getNextNumber();
            $facture->setOrdreArrive($ordre);
            
            //enregistrement de la facture qui inclu aussi l'enregistrement du des bons et de la facture liés
            $achat = $form->getData();
            $achat->setFacture($facture);

            // ici un controle sur achat doit être faite

            $em->persist($achat);
            $em->flush();

            $this->get('session')->getFlashBag()->add('ok', MessageSysteme::AJOUT_SUCCES);
        } else {
            $this->get('session')->getFlashBag()->add('ko', MessageSysteme::AJOUT_ECHEC);
        }

        return $this->redirect($this->generateUrl('facture_reception'));
    }

    /**
     * listeReceptionFactureAction : fonction d'affichage de la liste des factures arrivées 
     * 
     * à la réception
     *
     */
    public function listeReceptionFactureAction() {

        $description = 'Liste des Factures Arrivées à la Réception';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $em = $this->getDoctrine()->getManager();
        // recuperation de la liste des factues arrivées
        $listeArrive = $em->getTable($this->entityBundle . $this->entity)->findByEtape(EtapeFacture::FCT_ARRIVE_RECEPTION);
        $this->data['listeFactureArrive'] = $listeArrive;
        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'listArriveReception.html.twig', $this->data);
    }

    /**
     * listeRejetFactureParEBSAction : fonction d'affichage de la liste des factures rejetés par EBS 
     * 
     * à la réception
     *
     */
    public function listeRejetFactureParEBSAction() {

        $description = 'Liste des Factures rejetées par EBS';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $em = $this->getDoctrine()->getManager();
        // recuperation de la liste des factues arrivées
        $listeArrive = $em->getTable($this->entityBundle . $this->entity)->findByEtape(EtapeFacture::FCT_REJETE_EBS);
        $this->data['listeFactureRejete'] = $listeArrive;
        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'listRejeteEBS.html.twig', $this->data);
    }

    /**
     * removeFactureAction : fonction de suppression des factures désactiver d'un facture
     * 
     *  
     *
     */
    public function removeFactureAction() {

        $description = 'Suppression de Factures';
        $this->checkAuthorization(__FUNCTION__, $description, false);

        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {

            // recuperation des paramettres du formulaire
            $idFactures = trim($request->get('idFacturesDelete'));

            // enregistrement des factures au cas où il existe au moins un identifiant
            if ($idFactures) {
                $tabIdFactures = explode('/', $idFactures);

                // Supression des factures  à partir de leur ids 
                $this->gererStatus($this->entityBundle, $this->entity, $tabIdFactures, Status::SUPPRIME);
                $this->gererEtapeCurrentEntity($tabIdFactures, EtapeFacture::FCT_SUPPRIME);


                // modification des état des achats relatifs à ces factures
                foreach ($tabIdFactures as $id) {

                    $em = $this->getDoctrine()->getManager();
                    // recuperation de la liste des factues arrivées
                    $facture = $em->getTable($this->entityBundle . $this->entity)->find($id);
                    
                    if ($facture) {
                        $achat = $facture->getAchat();

                        // si la facture était liée à un bon de commande, alors on supprime
                        if ($achat && $achat->getBonCommande()) {
                            $achat->setFacture(null);
                            $facture->setAchat(null);
                        }

                        // cas où la facture est sans bon de commande, on supprime l'achat aussi
                        else if($achat) {
                            $achat->setEtape(EtapeAchat::ACHAT_SUPPRIME);
                            $achat->setIsActif(Status::SUPPRIME);
                        }
                    }
                }
                $em->flush() ;
            }
            $this->get('session')->getFlashBag()->add('ok', MessageSysteme::SUPPR_SUCCES);
            
        }

        return $this->redirect($this->generateUrl('facture_liste_reception'));
    }

    /**
     * listePeriodeFactureAction : fonction de recherche des factures par période 
     *
     */
    public function listePeriodeFactureAction() {

        $description = 'Liste des Factures  par Période ';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        $listeArrive = null;
        $debut = trim($request->get('dateDebut'));
        $fin = trim($request->get('dateFin'));
        // recuperation des informations du formaulaire : les dates d
        if ($request->getMethod() == 'POST') {

            $listeArrive = $em->getTable($this->entityBundle . $this->entity)->findByDateReception($debut, $fin);
        } else { // recuperation des facture reçu dans la journée actuelle
            $listeArrive = $em->getTable($this->entityBundle . $this->entity)->findByDateReception();
        }
        $this->data['dateDebut'] = $debut;
        $this->data['dateFin'] = $fin;
        $this->data['listeFactureArrive'] = $listeArrive;

        return $this->render($this->actionBundle . $this->viewFolder . 'listPeriodeFacture.html.twig', $this->data, $this->response);
    }

    /**
     * pas encore bien implémenté
     *
     */
    public function editFactureAction($idAchat) {

        $description = 'Modification d\'une Facture Récemment arrivée';

        $this->checkAuthorization(__FUNCTION__, $description, false);

        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();


        /**
         * ICI NOUS SOMMES EN TRAIN DE FAIRE UN CONTROLE SUR L'EXISTANCE DES PARAMETRES
         */
        // recuperation de l'acaht de la facture à modifier
        $achatModifiable = $em->getTable($this->entityBundle . 'Achat')->find($idAchat);

        if (!$achatModifiable) {
//             var_dump("non achat");exit;
            $this->get('session')->getFlashBag()->add('ko', MessageSysteme::INEXISTANT);
            return $this->redirectToRoute($this->getHomeRoute());
        }

        $factModifiable = $achatModifiable->getFacture();
        if (!$factModifiable) {
//             var_dump("non facture");exit;
            $this->get('session')->getFlashBag()->add('ko', MessageSysteme::INEXISTANT);
            return $this->redirectToRoute($this->getHomeRoute());
        }

        // on doit modifier seulement les factures qui sont à la réception
        $etape = $factModifiable->getEtape();
        if ($etape != EtapeFacture::FCT_ARRIVE_RECEPTION) {
//            var_dump("non etape");exit;
            $this->get('session')->getFlashBag()->add('ko', MessageSysteme::MODIF_ECHEC);
            return $this->redirectToRoute($this->getHomeRoute());
        }

        $form = $this->createForm(new AchatReceptionType(), $achatModifiable);
        $this->data['facture'] = $factModifiable;
        $this->data['listeBons'] = $em->getTable($this->entityBundle . 'BonCommande')->getBonOfReception();
        $this->data['achat'] = $achatModifiable;
        $this->data['form'] = $form->createView();





        // recuperation des infromation de l'achat ie: qui sont saisies sur le formulaire
        if ($request->getMethod() == 'POST') {

            $form->handleRequest($request);
            $achatModifiable = $form->getData();

            // recuperation des informations de la facture
            $dateArriveString = trim($request->get('dateArrive'));
            $refFacture = trim($request->get('refFact'));

            // affichage d'un message d'erreur au cas où un de  ces informtionsest vide
            if ($dateArriveString && $refFacture) {
                // preparation des informations de la facture
                $dateArrive = \DateTime::createFromFormat('d-m-Y', $dateArriveString);

                // Enregistrement de la modification de la facture arrivée

                $factModifiable->setCreerPar($this->getCurrentId());
                $factModifiable->setDateArrive($dateArrive);
                $factModifiable->setEtape(EtapeFacture::FCT_ARRIVE_RECEPTION);
                $factModifiable->setReferenceFournisseur($refFacture);


                //enregistrement de la facture qui inclu aussi l'enregistrement du des bons et de la facture liés
                // ici un controle sur achat doit être faite

                $em->persist($achatModifiable);
                $em->flush();

                $this->get('session')->getFlashBag()->add('ok', MessageSysteme::MODIF_SUCCES);
                return $this->redirect($this->generateUrl('facture_liste_reception'));
            } else {
                $this->get('session')->getFlashBag()->add('ko', MessageSysteme::MODIF_ECHEC);
            }
        }


        return $this->render($this->actionBundle . $this->viewFolder . 'editFacture.html.twig', $this->data, $this->response);
    }

}
