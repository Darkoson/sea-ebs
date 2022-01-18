<?php

namespace eProcess\FactureBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use eProcess\EntityBundle\Entity\Fichier;
use eProcess\EntityBundle\Entity\CheckList;
use eProcess\SecurityBundle\Constantes\TypeFichier;
use eProcess\SecurityBundle\Constantes\Status;
use eProcess\SecurityBundle\Constantes\MessageSysteme;
use eProcess\SecurityBundle\Constantes\EtapeFacture;
use eProcess\EntityBundle\Form\RechercheFactureType;
use eProcess\EntityBundle\Form\AchatBonType;
use eProcess\EntityBundle\Form\AchatTraitement1Type; // formulaire pour facture sans bon
use eProcess\EntityBundle\Form\AchatTraitement2Type; // formulaire pour facture avec bon
use Symfony\Component\HttpFoundation\Request;
use eProcess\EntityBundle\Entity\Achat;
use \DateTime;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Fill;

/**
 * Facture controller.
 *
 */
class FactureTraitementController extends Controller {

    /**
     * constructeur de la classe
     */
    public function __construct() {

        $this->module = Controller::MOD_FCT_TRAITEMENT;
        $this->descModule = Controller::MOD_FCT_TRAITEMENT_DESC;
        $this->actionBundle = 'eProcessFactureBundle:';
        parent::__construct('Facture');
        $this->viewFolder = 'FCT_Traitement:';
    }

    /**
     * arriveFactureTraitementAction : fonction d'affichage de la liste des factures 
     * 
     * arrivées à EBS pour être traitées 
     * 
     * @return type
     */
    public function arriveFactureTraitementAction() {

        $description = 'Liste des Factures à Traiter à EBS';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $em = $this->getDoctrine()->getManager();

        // recuperation de la lise des factures arrivées à EBS pour être traitées
        $param['etape'] = EtapeFacture::FCT_ARRIVE_EBS;

        $listeFactureArrrive = $em->getTable($this->entityBundle . $this->entity)->findBy($param);

        $this->data['listeFacture'] = $listeFactureArrrive;

        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'arriveTraitementEBS.html.twig', $this->data);
    }

    /**
     * traiterFactureAction : fonction d'affichage de la page de traitement 
     * 
     * d'une facture
     * 
     * @return type
     */
    public function traiterFactureAction($id, $etape) {

        $description = 'Traitement des factures';
        $this->checkAuthorization(__FUNCTION__, $description, false);

        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();

        // recuperation de la facture à traiter
        $facture = $em->getTable($this->entityBundle . $this->entity)->find($id);

        // redirection sur la page d'accueil si la facture n'existe pas
        if (!$facture) {
            return $this->redirect($this->generateUrl($this->getHomeRoute()));
        }

        // recuperation de l'achat relatif à la facture
        $achat = $facture->getAchat();
        $fournisseur = $achat->getFournisseur();
        $bon = $achat->getBonCommande();

        // si nous somme dans le contexte d'une facture arrivée suivant un bon de commande alors on choisi
        // le formtype correspondant
        if ($bon) {
            $form = $this->createForm(new AchatTraitement2Type(), $achat, array(
                'action' => $this->generateUrl('facture_traitement_encours', array('id' => $id, 'etape' => 2)),
                'method' => 'POST',
            ));
        }
        // on choisi le formType de traitement de facture  sans bon de commande
        else {
            $form = $this->createForm(new AchatTraitement1Type(), $achat, array(
                'action' => $this->generateUrl('facture_traitement_encours', array('id' => $id, 'etape' => 2)),
                'method' => 'POST',
            ));
        }





        // si nous somme dans le contexte d'un changement de bon de commande ou de l'enregistrement du traitement
        if ($request->getMethod() == 'POST') {

            // cas de modification des informations sur le traitement de facture ( bon ou fournisseur)
            if ($etape == 1) {
                // recuperation des ids du bon ou du fournisseur selectionné
                $idBon = intval($request->get('idBon'));
                $idFour = intval($request->get('idFour'));

                //recuperation du bon et fournisseur de la base
                $bon = $em->getTable($this->entityBundle . 'BonCommande')->find($idBon);
                $newFournisseur = $em->getTable($this->entityBundle . 'Fournisseur')->find($idFour);

                // si le bon existe alors on recupere l'achat et le fournisseur relatif
                if ($bon) {
                    // selection du nouveau achat lié au bon de commande choisi
                    $achat = $bon->getAchat();
//                    var_dump($achat->toArray()); exit;
                    $fournisseur = $achat->getFournisseur();
                    $form = $this->createForm(new AchatTraitement2Type(), $achat, array(
                        'action' => $this->generateUrl('facture_traitement_encours', array('id' => $id, 'etape' => 2)),
                        'method' => 'POST',
                    ));
                }
                // on choisi le formType de traitement de facture  sans bon de commande
                else {
                    $form = $this->createForm(new AchatTraitement1Type(), $achat, array(
                        'action' => $this->generateUrl('facture_traitement_encours', array('id' => $id, 'etape' => 2)),
                        'method' => 'POST',
                    ));

                    // si le bon n'existe mais le fournisseur recherché existe alors on le modifie 
                    if ($newFournisseur) {
                        $fournisseur = $newFournisseur;
                    }
                }
            }

            // cas d'enregistrement du traitement effectué
            elseif ($etape == 2) {

                // $oldAchat : c'est l'instance d'achat liée à la facture depuis la reception
                $oldAchat = $facture->getAchat();


                $oldBon = $oldAchat->getBonCommande();

                // recuperation du motif de la facture
                $motif = trim($request->get('motif'));
                if($motif){
                    $facture->setMotif($motif) ;
                }
                // recuperation de l'identifiant de l'achat finalement selectionné
                $idAchatModif = intval($request->get('idAchatModif'));
                

                //$newAchat : la nouvelle instance d'achat liée à la facture apres traitement
                $newAchat = $em->getTable($this->entityBundle . 'Achat')->find($idAchatModif);

                $newIbBon = intval($request->get('idBon'));

                //verification de la modification du bon initiale  choisi à la reception pour la facture
                // Nous sommes dans le cas où la facture apres traitement à un bon de commande
                if ($newIbBon > 0) {

                    // recuperation du formulaire apres traitement: ( cas d'existence de bon)
                    $form = $this->createForm(new AchatTraitement2Type(), $newAchat);

                    // ------ avant traitement ----------
//                    var_dump($newAchat->toArray()); 

                    $form->handleRequest($request);
                    $newAchat = $form->getData();

                    // traitement du checkList
                    $checkListString = trim($request->get('checkListString'));
                    $checkListArray = json_decode($checkListString, true);
                    $checkList = new CheckList($checkListArray);
                    $newAchat->setCheckList($checkList);


                    // ------ apres traitement ----------
//                    var_dump($newAchat->toArray()); exit;
                    // dans le cas où la facture etait liée à un bon avant traitement
                    if ($oldBon) {
                        $oldIbBon = $oldBon->getId();
                        // si les ids des bons avant et apres traitement sont identique alors il n'ya pas de modification sur
                        // le bon de commande et le fournisseur
                        if ($oldIbBon == $newIbBon) {
                            // je ne fais rien pour le moment
//                            var_dump('existe bon et non modifier '); exit;
                        }

                        // dans le cas de changement de bon de commande
                        else {
                            // on doit débarrasser la facture de l'achat initiale (l'achat relatif à son ancien bon de commande)
                            $oldAchat->setFacture(null);
                            $newAchat->setFacture($facture);
                            var_dump('existe bon et  modifier ');
                            exit;
                        }
                    }

                    // dans le cas où la facture n'avait pas de bon mais apres traitement se
                    //  trouve attribuée un bon de commande
                    else {
                        // on supprime l'instance d'achat créé initialement avec la facture pour attribuer à la facture, 
                        // l'achat relatif au bon de commande qu'elle possede 
                        $oldAchat->setFacture(null);
                        $newAchat->setFacture($facture);
                        $oldAchat->setIsActif(Status::SUPPRIME);
//                         var_dump('pas  de bon avant mais bon apres '); exit;
                    }
                }

                // cas où la facture n'a pas de bon de commande apres traitement
                else {

                    // recuperation du  departement du bon de commande et du fournisseur finale
                    $newIbDep = intval($request->get('idDep'));
                    $newIbFour = intval($request->get('idFour'));

                    //cas où le receptioniste lui avait attribuer un bon de commande au depart mais qui est enlever lors du traitement
                    if ($oldBon) {
                        // ici, nous coupons la relation entre la facture et l'achat de l'ancien le bon de commande pour 
                        // créer une relation entre cette facture et une nouvelle instance d'achat identique à l'ancien
                        $oldAchat->setFacture(null);


                        // on colne $newAchat car dans ce cas, il faut un autre achat identique à celui traité sur le formtype
                        // $newAchat : stock les informations initiales de l'achat lié à la facture (sauvegarde en cas de modification par formType)
                        $newAchat = clone $newAchat;



                        // recuperation du formulaire apres traitement: ( cas d'absence de bon)
                        $form = $this->createForm(new AchatTraitement1Type(), $newAchat);

                        $form->handleRequest($request);
                        $newAchat = $form->getData();


//                         var_dump($newAchat);
                        // recuperation d'un clone de l'achat 
                        $facture->setAchat($newAchat);
//                         var_dump(' bon avant mais pas de bon apres '); exit;
                    }
                    // cas où avant et après traitement il n'ya pas de bon de commande
                    else {
                        // recuperation du formulaire apres traitement: ( cas d'absence de bon)
                        $form = $this->createForm(new AchatTraitement1Type(), $newAchat);

                        $form->handleRequest($request);
                        $newAchat = $form->getData();
                    }


                    // traitement du checkList
                    $checkListString = trim($request->get('checkListString'));
                    $checkListArray = json_decode($checkListString, true);
                    $checkList = new CheckList($checkListArray);
                    $newAchat->setCheckList($checkList);

                    // ici, nous allons ajouter le fournisseur et departement s'ils sont modifié
                    $dep = $em->getTable($this->entityBundle . 'Departement')->find($newIbDep);
                    $four = $em->getTable($this->entityBundle . 'Fournisseur')->find($newIbFour);
                    $newAchat->setDepartement($dep);
                    $newAchat->setFournisseur($four);
                }


                // dermiere enregistrement sur la facture
                $facture->setDateTraitement(new \DateTime());
                $facture->setTraiterPar($this->getCurrentId());
                $facture->setEtape(EtapeFacture::FCT_TRAITE_EBS);
                $newAchat->setFacture($facture);

                // enregistrement de la facture avec le bon de commande
                $em->persist($newAchat);
                $em->flush();

                $this->get('session')->getFlashBag()->add('ok', MessageSysteme::OPERATION_SUCCES);

                return $this->redirectToRoute('facture_traitement_arrive');
            }
        }

        // creation d'un formulaire dont certains champs sont déjà definis en fonction du bonde commande selectionné
        // recuperation de l'achat relatif à la facture à traiter
        $receptionniste = $em->getTable($this->entityBundle . 'Utilisateur')->find($facture->getCreerPar());

        // liste des bonde commande pouvant être attribuer à une facture en traitement
        $listeBon = $em->getTable($this->entityBundle . 'BonCommande')->getBonOfReception();


        if ($bon && !in_array($bon->getId(), array_keys($listeBon))) {
            $listeBon[$bon->getId()] = $bon->getNumero();
        }


        $this->data['form'] = $form->createView();
        $this->data['idBon'] = $bon ? $bon->getId() : 0;
        $this->data['bonCommande'] = $bon;
        $this->data['listeBonCommande'] = $listeBon;
        $this->data['achat'] = $achat;
        $this->data['fournisseur'] = $fournisseur;
        $this->data['receptionniste'] = $receptionniste;
        $this->data['entity'] = $facture;
        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'traiterFacture.html.twig', $this->data);
    }

    public function enregistrerTraitementAction() {
         $description = 'Enregistrement d\'un Traitement de Facture';
        $this->checkAuthorization(__FUNCTION__, $description, false);

        $request = $this->getRequest();
        $achat = new Achat();
        $form = $this->createForm(new AchatTraitement1Type(), $achat);


        // enregistrement de des informations de la facture en cas d'un traitement
        if ($request->getMethod() == 'POST') {

            $form->handleRequest($request);
            //verification de la validite du formulaire

            $achat = $form->getData();


//            var_dump( $achat->toArray());
//            var_dump($request->get('isFactureContrat'));
            exit;



            // traitement du checkList
            $checkListArray = json_decode($request->get('checkListString'), true);
            $checkList = new CheckList($checkListArray);

            $achat->setCheckList($checkList);

            // traitement de la facture
            $facture->setDateTraitement(new DateTime());
            $facture->setTraiterPar($this->getCurrentId());
            $facture->setEtape(EtapeFacture::FCT_TRAITE_EBS);

            // ajout des types aux fichier afin d'orienter leur stockage dans le bon repertoire.
            $ficDemande = $achat->getFicDemande();
            if ($ficDemande) {
                $ficDemande->setType(TypeFichier::Demande);
            }

            $ficProformat = $achat->getFicProformat();
            if ($ficProformat) {
                $ficProformat->setType(TypeFichier::Proformat);
            }

            $ficApprobation = $achat->getFicApprobation();
            if ($ficApprobation) {
                $ficApprobation->setType(TypeFichier::Approbation);
            }

            $ficLivraison = $achat->getFicLivraison();
            if ($ficLivraison) {
                $ficLivraison->setType(TypeFichier::Livraison);
            }

            // recuperation des fichiers qui ne sont pas liés à l'achat : facture et

            $allFiles = $request->files->all();
            $liste = $allFiles['eprocess_entitybundle_achat_traitement'];

            if (count($liste) > 0) {
                $newUploade = $liste['ficFacture']['fichier'];

                // au cas où il existe un fichier uploadé  pour la facture, on l'enregistre
                if ($newUploade) {
                    $ficFacture = new Fichier();
                    $ficFacture->setFichier($newUploade);
                    $ficFacture->setType(TypeFichier::Facture);
                    $facture->setFichier($ficFacture);
                }
            }

            $em->persist($achat);
            $em->persist($facture);

//                var_dump($facture->getFichier()) ;exit;

            $em->flush();

            $this->get('session')->getFlashBag()->add('ok', MessageSysteme::OPERATION_SUCCES);
            return $this->redirectToRoute('facture_traitement_arrive');
//        
        }


//
//        $em = $this->getDoctrine()->getManager();
//        $request = Request::createFromGlobals();
//
//        if ($request->getMethod() == 'POST') {
//
//            $form->handleRequest($request);
//            //verification de la validite du formulaire
////            if ($form->isValid()) {
//
//            $achat = $form->getData();
//
//            // traitement du checkList
//            $checkListArray = json_decode($request->get('checkListString'), true);
//            $checkList = new CheckList($checkListArray);
//
//            $achat->setCheckList($checkList);
//
//            // traitement de la facture
//            $facture->setDateTraitement(new DateTime());
//            $facture->setTraiterPar($this->getCurrentId());
//            $facture->setEtape(EtapeFacture::FCT_TRAITE_EBS);
//
//            // ajout des types aux fichier afin d'orienter leur stockage dans le bon repertoire.
//            $ficDemande = $achat->getFicDemande();
//            if ($ficDemande) {
//                $ficDemande->setType(TypeFichier::Demande);
//            }
//
//            $ficProformat = $achat->getFicProformat();
//            if ($ficProformat) {
//                $ficProformat->setType(TypeFichier::Proformat);
//            }
//
//            $ficApprobation = $achat->getFicApprobation();
//            if ($ficApprobation) {
//                $ficApprobation->setType(TypeFichier::Approbation);
//            }
//
//            $ficLivraison = $achat->getFicLivraison();
//            if ($ficLivraison) {
//                $ficLivraison->setType(TypeFichier::Livraison);
//            }
//
//            // recuperation des fichiers qui ne sont pas liés à l'achat : facture et
//
//            $allFiles = $request->files->all();
//            $liste = $allFiles['eprocess_entitybundle_achat_traitement'];
//
//            if (count($liste) > 0) {
//                $newUploade = $liste['ficFacture']['fichier'];
//
//                // au cas où il existe un fichier uploadé  pour la facture, on l'enregistre
//                if ($newUploade) {
//                    $ficFacture = new Fichier();
//                    $ficFacture->setFichier($newUploade);
//                    $ficFacture->setType(TypeFichier::Facture);
//                    $facture->setFichier($ficFacture);
//                }
//            }
//
//            $em->persist($achat);
//            $em->persist($facture);
//
////                var_dump($facture->getFichier()) ;exit;
//
//            $em->flush();
//
//            $this->get('session')->getFlashBag()->add('ok', MessageSysteme::OPERATION_SUCCES);
//            return $this->redirectToRoute('facture_traitement_arrive');
////        
//        }
    }

   

    /**
     * finiTraitementFactureAction : fonction d'affichage des factures traitées à EBS
     * 
     * et qui sont en attente d'être envoyées aux opérations
     * 
     * @return type
     */
    public function finiTraitementFactureAction() {

        $description = 'Liste des Factures Traitées à EBS';
        $this->checkAuthorization(__FUNCTION__, $description, true);


        $em = $this->getDoctrine()->getManager();

        // recuperation de la lise des factures à traitées qui sont à EBS 
        $param['etape'] = EtapeFacture::FCT_TRAITE_EBS;

        $listeFactureTraite = $em->getTable($this->entityBundle . $this->entity)->findBy($param);

        $this->data['listeFacture'] = $listeFactureTraite;


        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'facturesTraitees.html.twig', $this->data);
    }

    /**
     * traitementFactureJourneeAction : fonction d'affichage de la liste de facture traitees dans la journée
     * 
     * @return type
     */
    public function traitementFactureJourneeAction() {

        $description = 'Liste des Factures Traitées dans une Journée';
        $this->checkAuthorization(__FUNCTION__, $description, true);
        $ObjeDateJour = new \DateTime();
        $date = $ObjeDateJour->format('d-m-Y');

        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $date = trim($request->get('dateDebut'));
        }
        // recuperation de la liste des factures
        $listeFacture = $em->getTable($this->entityBundle . $this->entity)->findByDateTraitement($date, $date);

//        var_dump(count($listeFacture));exit;
        $this->data['listeFactureJour'] = $listeFacture;
        $this->data['dateJour'] = $date;

        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'factureTraitementJour.html.twig', $this->data);
    }

    /**
     * arriveFactureRejeteAction : fonction d'affichage de la liste de facture rejetées par les
     * 
     * agents de OPS et qui sont reçus par les agents de EBS
     * 
     * @return type
     */
    public function arriveFactureRejeteAction() {

        $description = 'Liste des Factures Rejetées et à Retraiter';
        $this->checkAuthorization(__FUNCTION__, $description, true);


        $em = $this->getDoctrine()->getManager();

        // recuperation de la liste des factures rejetée par OPS et qui sont à EBS 
        $param['etape'] = EtapeFacture::FCT_REJETE_OPS;

        $listeFactureRejetees = $em->getTable($this->entityBundle . $this->entity)->findBy($param);

        $this->data['listeFacture'] = $listeFactureRejetees;



        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'facturesRejetees.html.twig', $this->data);
    }

    /**
     * rechercheFactureMulticritereAction  : fonction de recherche multicritère des factures
     * 
     * @return type
     */
    public function rechercheFactureMulticritereAction() {

        $description = "Recherche Multicritère des Factures ";
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $factureRecherche = array();
        //on le met à false pour cacher le tableau des resultats
        $this->data['isRecherche'] = false;

        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        $isNum = intval($request->get('isNum'));

        // recuperaton des parametres du formuaire
        if ($request->getMethod() == 'POST') {

            // on le met à true pour afficher le tableau des resultats
            $this->data['isRecherche'] = true;

            // recuperation de la facture à partire de son ordre d'arrive
            if ($isNum) {
                $ordreFact = trim($request->get('ordreArrive'));

                $factureRecherche = $em->getTable($this->entityBundle . $this->entity)->findByOrdreArrive($ordreFact);
            }
            // cas d'une recherche multicritère
            else {
                
                $formData = $request->request->get('form_recherche_facture');
                $idFour = intval($request->get('idFour'));
                $idBon = intval($request->get('idBon'));
                $etape = intval($request->get('etape'));
                $montMin = trim($request->get('montMin'));
                $montMax = trim($request->get('montMax'));
                
                
                $debut = trim($request->get('debut'));
                $fin = trim($request->get('fin'));
                $devise = trim($formData['devise']);
                $typeDate = intval($formData['typeDate']);

                $factureRecherche = $em->getTable($this->entityBundle.$this->entity)
                        ->findMultiCritere($idFour, $idBon, $etape, $montMin, $montMax , $devise , $debut , $fin , $typeDate ) ;
  
            }
            
        }

        $form = $this->createForm(new RechercheFactureType());
        // recuperation des bons approuvés et envoyés
        $this->data['form'] = $form->createView();
        $this->data['factureRecherche'] = $factureRecherche;
        $this->data['isNum'] = $isNum;


        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'rechercherFacture.html.twig', $this->data);
    }

  
    /**
     * tracerFactureAction : fonction permettante de tracer une facture
     * 
     * @return type
     */
    public function voirFactureAction($id) {

        $description = "Visualisation des Informations d'une Facture ";
        $this->checkAuthorization(__FUNCTION__, $description, false);

        // recuperation des services 
        $em = $this->getDoctrine()->getManager();

        // recuperation de la facture
        $facture = $em->getTable($this->entityBundle . $this->entity)->find($id);

        if (!$facture) {
            $this->get('session')->getFlashBag()->add('ko', MessageSysteme::INEXISTANT);
            return $this->redirectToRoute($this->getHomeRoute());
        }

        $form = $this->createForm(new AchatBonType(), $facture->getAchat());
        $this->data['entity'] = $facture;
        
        $checkList = $facture->getAchat()->getCheckList() ;
//        var_dump($checkList->toArray());exit;
        $this->data['checkList'] = $facture->getAchat()->getCheckList();
        $this->data['form'] = $form->createView();

//        var_dump($this->data['checkList']);exit;
        $this->data['createur'] = $em->getTable($this->entityBundle . 'Utilisateur')->find(intval($facture->getCreerPar()));
        $this->data['traiteur'] = $em->getTable($this->entityBundle . 'Utilisateur')->find(intval($facture->getTraiterPar()));
        $this->data['activateur'] = $em->getTable($this->entityBundle . 'Utilisateur')->find(intval($facture->getActiverPar()));
        $this->data['transferts'] = $facture->getTransfertFactures();


        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'voirFacture.html.twig', $this->data);
    }

    /**
     * factureProblemeAction : fonction d'affichage des factures ayant des 
     *  problèmes à  EBS.
     * 
     * @return type
     */
    public function factureProblemeAction() {

        $description = "Liste des Factures à Problème ";
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $em = $this->getDoctrine()->getManager();

        // recuperation de la lise des factures à problème qui sont à EBS 
        $param['etape'] = EtapeFacture::FCT_PROBLEME_EBS;

        $listeFactureProbleme = $em->getTable($this->entityBundle . $this->entity)->findBy($param);

        $this->data['listeFacture'] = $listeFactureProbleme;

        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'problemeFactures.html.twig', $this->data);
    }

    /**
     * statusFactureAction : fonction de changer d'étape à une facture
     * 
     * @return type
     */
    public function statusFactureAction() {

        $description = "Changement d'Etat d'une Facture ";
        $this->checkAuthorization(__FUNCTION__, $description, false);

        
        $request = $this->getRequest();

        // traitement du formulaire dans le cas d'une soumission
        $route = trim($request->get('route'));
        if ($request->getMethod() == 'POST') {

            // recuperation des ids des nouvelles actions à ajouter
            $idBons = trim($request->get('ids'));
            $route = trim($request->get('route'));
            $etape = trim($request->get('etape'));
            $status = trim($request->get('etat'));

            $tabIdFacts = explode('/', $idBons);

            $this->gererStatus($this->entityBundle, $this->entity, $tabIdFacts, $status);

            if ($etape != '-1') {
                $this->gererEtapeCurrentEntity($tabIdFacts, $etape);
            }
        }

        // controle pour retourner sur une bonne route
        if ($route == '') {
            $route = $this->getHomeRoute();
        }

        return $this->redirectToRoute($route);
    }

    /**
     * etapeFactureAction : fonction de changer d'étape à une facture
     * 
     * @return type
     */
    public function etapeFactureAction() {
        
        $description = "Changement d'Etape d'une Facture ";
        $this->checkAuthorization(__FUNCTION__, $description, false);


        // recuperation des services 
        $request = $this->getRequest();

        // traitement du formulaire dans le cas d'une soumission

        if ($request->getMethod() == 'POST') {

            // recuperation des ids des nouvelles actions à ajouter
            $ids = trim($request->get('ids'));
            $route = trim($request->get('route'));
            $etape = trim($request->get('etape'));
            $status = trim($request->get('etat'));
//             var_dump($etape);
//             var_dump($status);exit;
            $tabIdFact = explode('/', $ids);
            // changement de l'etape de la facture 
            // gererEtapeCurrentEntity(): change l'etape de la classe de l'entite manipule dans le controleur
            $this->gererEtapeCurrentEntity($tabIdFact, $etape);

            // si $status != '-1' ce qui veut dire on veut modifier l'etat des objets
            //  lors du changement de leur etape
            if ($status != '-1') {
                $this->gererStatus($this->entityBundle, $this->entity, $tabIdFact, $status);
            }
        }


        // controle pour retourner sur une bonne route
        if ($route == '') {
            $route = $this->getHomeRoute();
        }

        return $this->redirectToRoute($route);
    }

    /**
     *  exportExcelFactureAction : exportation des factures en Excel 
     * 
     * @return type
     */
    public function exportExcelFactureAction() {

        $description = 'Exportation en Excel des Factures';
        $this->checkAuthorization(__FUNCTION__, $description, false);


        // recuperation des bons approuvés et envoyés

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $listeBonString = trim($request->get('listFacture'));
            $bonArray = json_decode($listeBonString, true);

            // Create new PHPExcel object
            $objPHPExcel = new PHPExcel();

            // Set document properties
            $objPHPExcel->getProperties()->setCreator("Soft-EBS")
                    ->setLastModifiedBy("Someone")
                    ->setTitle("LISTE DES FACTURES")
                    ->setSubject("Résultats de la recherche");



            // designe des colonnes

            $style_fill_grey = array(
                'font' => array(
                    'bold' => true,
                    'size' => 12,
                    'name' => "Arial",
                    'color' => array(
                        'rgb' => '00000000'
                    )
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array(
                        'argb' => 'CCCCCC'
                    )
                )
            );

            // recueration du feuillet actif
            $objPHPExcel->setActiveSheetIndex(0);
            $feuillet = $objPHPExcel->getActiveSheet();

            //application des styles aux collones
            $feuillet->getStyle('A1:E1')->applyFromArray($style_fill_grey);
            $feuillet->getStyle('A1:E1')->getAlignment()->setHorizontal('HORIZONTAL_CENTER');
        

            // reglage des colommes du feuillet
            $feuillet->getColumnDimension('A')->setWidth(18);
            $feuillet->getColumnDimension('B')->setWidth(80);
            $feuillet->getColumnDimension('C')->setWidth(18);
            $feuillet->getColumnDimension('D')->setWidth(10);
            $feuillet->getColumnDimension('E')->setWidth(30);


            $feuillet->setCellValue('A1', 'numero')
                    ->setCellValue('B1', 'objet')
                    ->setCellValue('C1', 'montant')
                    ->setCellValue('D1', 'devise')
                    ->setCellValue('E1', 'etape');

            $index = 2;

            foreach ($bonArray as $facture) {
                $feuillet->setCellValue('A' . $index, $facture['ordre'])
                        ->setCellValue('B' . $index, $facture['objet'])
                        ->setCellValue('C' . $index, $facture['montant'])
                        ->setCellValue('D' . $index, $facture['devise'])
                        ->setCellValue('E' . $index, $facture['etape']);
                $index ++;
            }

            // Redirect output to a client’s web browser (Excel5)
            $this->response->headers->set('Content-Type', 'application/vnd.ms-excel');
            $this->response->headers->set('Content-Disposition', 'attachment;filename="liste_factures.xls"');
            $this->response->headers->set('Cache-Control', 'max-age=0');
            $this->response->sendHeaders();
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit();
        }

        return $this->redirectToRoute($this->getHomeRoute());
    }

    
}
