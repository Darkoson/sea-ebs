<?php

namespace eProcess\BonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use eProcess\SecurityBundle\Constantes\Status;
use eProcess\SecurityBundle\Constantes\EtapeBon;
use eProcess\EntityBundle\Entity\Achat;
use eProcess\EntityBundle\Form\AchatBonType;
use eProcess\SecurityBundle\Constantes\TypeFichier;
use eProcess\SecurityBundle\Constantes\MessageSysteme;
use eProcess\EntityBundle\Entity\CheckList;
use eProcess\EntityBundle\Form\RechercheBonType;
use eProcess\EntityBundle\Entity\Provision;
use Symfony\Component\HttpFoundation\Response;
use eProcess\EntityBundle\Entity\Fichier;
use eProcess\EntityBundle\Form\FichierChargementType;
use eProcess\EntityBundle\Entity\BonCommande;
use eProcess\EntityBundle\Entity\Fournisseur;
use eProcess\EntityBundle\Entity\AchatDepartement;
use eProcess\EntityBundle\Entity\ApprobationBon;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Fill;
use DateTime;
use Html2Pdf_Html2Pdf;
use PHPExcel_Style_NumberFormat;

/**
 * BonCommande controller.
 *
 */
class BonCommandeController extends Controller {

    /**
     * constructeur de la classe
     */
    public function __construct() {

        $this->module = Controller::MOD_BON;
        $this->descModule = Controller::MOD_BON_DESC;
        $this->actionBundle = 'eProcessBonBundle:';
        parent::__construct('BonCommande');
        $this->viewFolder = 'BonCommande:';
    }

    /**
     * bonEmisAction : fonction permettante de lister les bons nouvellement émis
     * 
     * 
     */
    public function bonEmisAction() {

        $description = 'Liste des Bons Nouvellement Edités';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $em = $this->getDoctrine()->getManager();

        // recuperation des bons nouvellement emis
        $listBon = $em->getTable($this->entityBundle . $this->entity)->getBonEdition();
        // ajout des autres paramètres de la vue
        $this->data['route'] = $this->get('Request')->get('_route');
        $this->data['listeBonCommande'] = $listBon;

        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'listBonEmis.html.twig', $this->data);
    }

    /**
     * bonModifieAction : fonction permettante de lister les bons recemment modifiés
     * 
     * 
     */
    public function listeBonModifieAction() {

        $description = 'Liste des bons récemment modifiés';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $em = $this->getDoctrine()->getManager();

        // recuperation des bons nouvellement emis
        $params['etape'] = EtapeBon::BON_MODIFIE;
        $listBon = $em->getTable($this->entityBundle . $this->entity)->findBy($params);

        // ajout des autres paramètres de la vue
        $this->data['route'] = $this->get('Request')->get('_route');
        $this->data['listeBonCommande'] = $listBon;

        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'listBonModif.html.twig', $this->data);
    }

    /**
     * addBonAction : fonction de génération d'un nouveau bon 
     *      
     */
    public function addBonArticleAction() {

        $description = "Edition d'un Bon de Commande d'Article";
        $this->checkAuthorization(__FUNCTION__, $description, true);
        // recuperation des services 
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('Request');

        $achat = new Achat();

        $form = $this->createForm(new AchatBonType(), $achat, array(
            'action' => $this->generateUrl('add_bonCommande_article'),
            'method' => 'POST',
            'attr' => array(
                'id' => 'generateBonForm',
                'onsubmit' => 'afficheImageExecution(); '
            ),
        ));

        // traitement du formulaire dans le cas d'une soumission
        if ($request->getMethod() == 'POST') {

            $form->handleRequest($request);
            $achat = $form->getData();

            // generation d'un numero de bon de commande
            $numBon = $em->getTable($this->entityBundle . $this->entity)->getNextNumber();

            // traitement du bon de commande
            $bonCommande = new \eProcess\EntityBundle\Entity\BonCommande($numBon, $this->getCurrentId());
            $bonCommande->setEtape(EtapeBon::BON_EMIS);
            $bonCommande->setIsActif(Status::INACTIF);
            $achat->setBonCommande($bonCommande);

            $formRecapString = trim($request->get('formRecap'));
            $formRecapArray = json_decode($formRecapString, true);

            // traitement du checkList
            $checkListArray = $formRecapArray['checkList'];
            $checkList = new CheckList($checkListArray);
            $achat->setCheckList($checkList);


            //recuperation des informations et articles pour les enregistrer

            $infoArray = $formRecapArray['info'];
            $objet = trim($infoArray['objet']);
            $pourcentagePayeALaCommande = $infoArray['paieCommande'];
            $delaisLivraison = intval($infoArray['delaisLivraison']);
            $idDep = $infoArray['departement'];

            $departement = $em->getTable($this->entityBundle . 'Departement')->find($idDep);

            // controle de la validite des valeurs recuperes
            if (!$departement || !$objet) {
                $this->get('session')->getFlashBag()->add('ko', MessageSysteme::AJOUT_ECHEC);
                // renvoi de la vue
                $this->data['form'] = $form->createView();
                return $this->render($this->actionBundle . $this->viewFolder . 'addBonCommandeArticle.html.twig', $this->data);
            }



            // mise à jour de l'objet Achat
            $achat->setObjet($objet);
            $achat->setPaieCommande($pourcentagePayeALaCommande);
            $achat->setDelaisLivraison($delaisLivraison);

            $articlesArray = $formRecapArray['article'];
            $provision = new Provision($articlesArray);

            $achatDepartement = new AchatDepartement();
            $achatDepartement->setAchat($achat);
            $achatDepartement->setProvision($provision);
            $achatDepartement->setDepartement($departement);
            $achatDepartement->ajusterValeurs(); // pour ajuster les valeurs
//            var_dump($provision->getTaxe());
//            var_dump($achat->getTaxe());
//            
//            exit;
            // ajout des types aux fichier afin d'orienter leur stockage dans le bon repertoire.
            $ficDemande = $achat->getFicDemande();
            if ($ficDemande) {
                $ficDemande->setType(TypeFichier::Demande);
            }

            $ficProformat = $achat->getFicProformat();
            if ($ficProformat) {
                $ficProformat->setType(TypeFichier::Proformat);
            }

            $ficApprobationBudget = $achat->getFicApprobationBudget();
            if ($ficApprobationBudget) {
                $ficApprobationBudget->setType(TypeFichier::ApprobationBudget);
            }

            $ficApprobationHier = $achat->getFicApprobationHier();
            if ($ficApprobationHier) {
                $ficApprobationHier->setType(TypeFichier::ApprobationHier);
            }


            // enregistrement des informations 
            $em->persist($achat);
            $em->flush();
            $this->get('session')->getFlashBag()->add('ok', MessageSysteme::AJOUT_SUCCES);
        }


        // les paramettres
        $this->data['form'] = $form->createView();

        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'addBonCommandeArticle.html.twig', $this->data);
    }

    /**
     * addBonVoyageAction : fonction de génération d'un nouveau bon de voyage
     *      
     */
    public function addBonVoyageAction() {

        $description = "Enregistrement d'un Bon de Commande de Voyage";
        $this->checkAuthorization(__FUNCTION__, $description, true);
        // recuperation des services 
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('Request');

        $achat = new Achat();

        $form = $this->createForm(new AchatBonType(), $achat, array(
            'action' => $this->generateUrl('add_bonCommande_voyage'),
            'method' => 'POST',
            'attr' => array(
                'id' => 'generateBonForm',
                'onsubmit' => 'afficheImageExecution(); '
            ),
        ));

        // traitement du formulaire dans le cas d'une soumission
        if ($request->getMethod() == 'POST') {

            $form->handleRequest($request);
            $achat = $form->getData();

            // generation d'un numero de bon de commande
            $numBon = $em->getTable($this->entityBundle . $this->entity)->getNextNumber();

            // traitement du bon de commande
            $bonCommande = new \eProcess\EntityBundle\Entity\BonCommande($numBon, $this->getCurrentId());
            $bonCommande->setEtape(EtapeBon::BON_EMIS);
            $bonCommande->setIsActif(Status::INACTIF);
            $achat->setBonCommande($bonCommande);

            $formRecapString = trim($request->get('formRecap'));
            $formRecapArray = json_decode($formRecapString, true);

            // traitement du checkList
            $checkListArray = $formRecapArray['checkList'];
            $checkList = new CheckList($checkListArray);
            $achat->setCheckList($checkList);


            //recuperation des informations et articles pour les enregistrer

            $infoArray = $formRecapArray['info'];
            $objet = trim($infoArray['objet']);
            $pourcentagePayeALaCommande = $infoArray['paieCommande'];
            $delaisLivraison = intval($infoArray['delaisLivraison']);
            $idDep = $infoArray['departement'];

            $departement = $em->getTable($this->entityBundle . 'Departement')->find($idDep);

            // controle de la validite des valeurs recuperes
            if (!$departement || !$objet) {
                $this->get('session')->getFlashBag()->add('ko', MessageSysteme::AJOUT_ECHEC);
                // renvoi de la vue
                $this->data['form'] = $form->createView();
                return $this->render($this->actionBundle . $this->viewFolder . 'addBonCommandeArticle.html.twig', $this->data);
            }



            // mise à jour de l'objet Achat
            $achat->setObjet($objet);
            $achat->setPaieCommande($pourcentagePayeALaCommande);
            $achat->setDelaisLivraison($delaisLivraison);

            $articlesArray = $formRecapArray['article'];
            $provision = new Provision($articlesArray);

            $achatDepartement = new AchatDepartement();
            $achatDepartement->setAchat($achat);
            $achatDepartement->setProvision($provision);
            $achatDepartement->setDepartement($departement);
            $achatDepartement->ajusterValeurs(); // pour ajuster les valeurs
//            var_dump($provision->getTaxe());
//            var_dump($achat->getTaxe());
//            
//            exit;
            // ajout des types aux fichier afin d'orienter leur stockage dans le bon repertoire.
            $ficDemande = $achat->getFicDemande();
            if ($ficDemande) {
                $ficDemande->setType(TypeFichier::Demande);
            }

            $ficProformat = $achat->getFicProformat();
            if ($ficProformat) {
                $ficProformat->setType(TypeFichier::Proformat);
            }

            $ficApprobationBudget = $achat->getFicApprobationBudget();
            if ($ficApprobationBudget) {
                $ficApprobationBudget->setType(TypeFichier::ApprobationBudget);
            }

            $ficApprobationHier = $achat->getFicApprobationHier();
            if ($ficApprobationHier) {
                $ficApprobationHier->setType(TypeFichier::ApprobationHier);
            }


            // enregistrement des informations 
            $em->persist($achat);
            $em->flush();
            $this->get('session')->getFlashBag()->add('ok', MessageSysteme::AJOUT_SUCCES);
        }



        // les paramettres
        $this->data['form'] = $form->createView();

        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'addBonCommandeVoyage.html.twig', $this->data);
    }

    /**
     * bonSoumisApprobAction : fonction d'approbation d'un bon de commande
     * 
     */
    public function bonSoumisApprobAction() {
        $description = 'Autorisation des Bons de Commande';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $em = $this->getDoctrine()->getManager();
        // recuperation des bon en attente d'autorisation et les bon autorisé et qui sont 
        // en attente d'envoi pour poursuivre le traitement 

        $this->data['route'] = $this->get('Request')->get('_route');
        // recuperation des bons arrivés en approbation et qui ne sont pas generés par l'utilisateur courant mais qui sont à valider par lui
        // false : pour dire que les bons ne sont pas encore signé
        $this->data['bonArriveApprobation'] = $em->getTable($this->entityBundle . $this->entity)->findBonApprobation($this->getCurrentId(), false);

        // recupertion de la liste des bons de commande approuvés par l'utilisateur courant
        // true : pour dire que les bons qui  sont   approuvés et non signé
        $this->data['bonApprouveNonSigne'] = $em->getTable($this->entityBundle . $this->entity)->findBonApprobation($this->getCurrentId(), true);

        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'listeBonEnApprobation.html.twig', $this->data);
    }

    /**
     * listBonApprouveAction : fonction d'affichage des bons de commande dejà approuvés
     * 
     */
    public function listBonApprouveAction() {
        $description = 'Liste des Bons Approuvés ou Rejetés';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $em = $this->getDoctrine()->getManager();

        // recuperation des bons approuvés et envoyés

        $this->data['route'] = $this->get('Request')->get('_route');
        $params['etape'] = EtapeBon::BON_APPROUVE;
        $this->data['bonAccepte'] = $em->getTable($this->entityBundle . $this->entity)->findBy($params, array('dateCreation' => 'DESC'), 30);

        $params['etape'] = EtapeBon::BON_REJETE;
        $this->data['bonRejete'] = $em->getTable($this->entityBundle . $this->entity)->findBy($params, array('dateCreation' => 'DESC'), 30);

        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'listBonAprouve.html.twig', $this->data);
    }

    public function bonAttenteEnvoiAction() {
        $description = 'Liste des Bons à envoyer aux Fournisseurs';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $em = $this->getDoctrine()->getManager();
        $recherche = false;
        $request = $this->get('request');
        $bonRecherche = array();

        // recuperaton des parametres du formuaire
        if ($request->getMethod() == 'POST') {
            $recherche = true;
            $idFour = intval($request->get('idFour'));
            $etape = intval($request->get('etape'));

            $bonRecherche = $em->getTable($this->entityBundle . $this->entity)->findByEtapeAndFour($etape, $idFour);
            $this->data['fournisseur'] = $em->getTable($this->entityBundle . 'Fournisseur')->find($idFour);
        }


        // recuperation des bons approuvés et envoyés
        $params['etape'] = EtapeBon::BON_ATTENE_ENVOI;
        $params['isActif'] = Status::ACTIF;
        $this->data['bonAttente'] = $em->getTable($this->entityBundle . $this->entity)->findBy($params);

        $params['etape'] = EtapeBon::BON_CHEZ_FOUNISSEUR;
        $this->data['bonFournisseur'] = $em->getTable($this->entityBundle . $this->entity)->findBy($params);

        $this->data['bonRecherche'] = $bonRecherche;

        //recuperation de quelques params important pour la vue

        $this->data['route'] = $this->get('Request')->get('_route');
        $this->data['recherche'] = $recherche;
        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'listBonFournisseur.html.twig', $this->data);
    }

    /**
     * changeRightStatusAction : fonction de d'autorisation d'un droit
     * 
     * @param int  $idProfile
     * @param bool $toAuthorized
     */
    public function changeBonStatusAction() {

        $description = 'Changement d\'Etat des Bons de Commande';
        $this->checkAuthorization(__FUNCTION__, $description, false);
        // recuperation des services 
        $request = $this->get('Request');
        $em = $this->getDoctrine()->getManager();

        // traitement du formulaire dans le cas d'une soumission
        $route = trim($request->get('route'));

        if ($request->getMethod() == 'POST') {

            // recuperation des ids des nouvelles actions à ajouter
            $idBons = trim($request->get('idBons'));
            $route = trim($request->get('route'));
            $etape = intval($request->get('etape'));
            $status = intval($request->get('status'));
            $message = trim($request->get('message'));

            $tabIdBons = explode('/', $idBons);

            foreach ($tabIdBons as $id) {
                if (intval($id) > 0) {
                    $entity = $em->getTable($this->entityBundle . $this->entity)->find($id);

                    if ($entity && $status != Status::SUPPRIME_VRAIE) {
                        $achat = $entity->getAchat();
                        $entity->setIsActif($status);
                        $entity->setMotif($message);
                        $entity->setDateActivation(new DateTime()); // il doit etre dans une condition
                        $entity->setDateActivation(new DateTime()); // il doit etre dans une condition
                        $entity->setActiverPar($this->getCurrentId());  // il doit etre dans une condition
                        // si on supprime le bon de commande alors on supprime aussi l'achat
                        if ($status == Status::SUPPRIME) {
                            $achat->setIsActif($status);
                        }
                        $em->persist($entity);
                        $em->persist($achat);
                    }

                    // si c'est une suppression vrai alors on supprime l'achat relatif au bon de commande
                    else if ($status == Status::SUPPRIME_VRAIE) {
                        $achat = $entity->getAchat();
                        $em->remove($achat);
                    }
                }
            }
            $em->flush();

            if ($status == Status::ACTIF) {
                $this->get('session')->getFlashBag()->add('ok', MessageSysteme::ACTIVE_SUCCES);
            } elseif ($status == Status::SUPPRIME) {
                $this->get('session')->getFlashBag()->add('ok', MessageSysteme::SUPPR_SUCCES);
            } elseif ($status == Status::BLOCKE) {
                $this->get('session')->getFlashBag()->add('ok', MessageSysteme::REJET_SUCCES);
            } else {
                $this->get('session')->getFlashBag()->add('ok', MessageSysteme::OPERATION_SUCCES);
            }

            if ($etape != '-1') {
                $this->gererEtapeCurrentEntity($tabIdBons, $etape);
            }
        }

        // controle pour retourner sur une bonne route
        if ($route == '') {
            $route = $this->getHomeRoute();
        }


        return $this->redirectToRoute($route);
    }

    /**
     * changeBonEtapeAction : fonction de changement de l'etape d'un bon de commande
     * 
     * @return type
     */
    public function changeBonEtapeAction() {

        $description = 'Changement d\'Etape des Bons de Commande';
        $this->checkAuthorization(__FUNCTION__, $description, false);


        // recuperation des services 
        $request = $this->get('Request');

        // traitement du formulaire dans le cas d'une soumission

        if ($request->getMethod() == 'POST') {

            // recuperation des ids des nouvelles actions à ajouter
            $idBons = trim($request->get('idBons'));
            $route = trim($request->get('route'));
            $etape = intval($request->get('etape'));
            $status = intval($request->get('status'));
            $message = trim($request->get('message'));
            $appprobateurId = trim($request->get('appprobateur'));

//            var_dump($appprobateurId.' appro ID'); exit;

            $tabIdBons = explode('/', $idBons);

            $em = $this->getDoctrine()->getManager();
            $validUser = null; // approbateur du bon: null ou objet user
            $listeBon = array(); // approbateur du bon: null ou objet user
            // cas ou on met un accent particulier sur l'etape que sur l'etat
            if ($etape > 0) {

                // verification dans le contexte d'une soumission
                if ($etape == EtapeBon::BON_SOUMIS_AUTORISATION) {
                    // verification du profil de l'approbateur choisis
                    $validUser = $em->getTable($this->entityBundle . 'Utilisateur')->isApprobateurValid($appprobateurId);
//                    var_dump($validUser);exit;
                    if (!$validUser) {
                        $this->get('session')->getFlashBag()->add('ko', MessageSysteme::OPERATION_ECHEC . ' ( Utilisateur Incorrect !) ');
                        return $this->redirectToRoute($route);
                    }
                }


                foreach ($tabIdBons as $id) {
                    if (intval($id) > 0) {
                        $entity = $em->getTable($this->entityBundle . $this->entity)->find($id);
                        if ($entity) {

                            // verification dans le contexte d'une soumission
                            if ($etape == EtapeBon::BON_SOUMIS_AUTORISATION) {
                                $entity->setActiverPar($appprobateurId);
                            }

                            // changement d'autorisateur
                            elseif ($etape == EtapeBon::BON_APPROUVE || $etape == EtapeBon::BON_REJETE) {
                                //enregistrement des information dans le bon de commande
                                $entity->setDateActivation(new DateTime()); // il doit etre dans une condition
                                $entity->setDateActivation(new DateTime()); // il doit etre dans une condition
                                //enregistrement des informations dans l'historiques des differentes approbations
                                $approbation = new ApprobationBon();
                                $approbation->setBonCommande($entity);
                                $approbation->setUtilisateur($this->getCurrentUser());
                                $approbation->setMotif($message);
                                $em->persist($approbation);
                            }
                            // changement d'autorisateur
                            elseif ($etape == EtapeBon::BON_VERIFIE) {
                                //enregistrement des information dans le bon de commande
                                $entity->setDateVerification(new DateTime());
                                $entity->setVerifierPar($this->getCurrentId());
                            }

                            // changement des etape et status
                            $entity->setEtape($etape);
                            ($status > 0) ? $entity->setIsActif($status) : "";
                            ($message) ? $entity->setMotif($message) : "";


                            $em->persist($entity);
                            $listeBon[] = $entity;
                        }
                    }
                }


                //envoi de mail à l'approbateur 
                if ($etape == EtapeBon::BON_SOUMIS_AUTORISATION) {
                    //envoi de mail à l'approbateur
                    $mailer = $this->get('email_manager');
                    try {
                        // envoi de mails de notification
                        $mailer->sendUsersMail(array($validUser), $listeBon, $etape, $this->entity);
                        $this->get('session')->getFlashBag()->add('ok', MessageSysteme::MAIL_SUCCES);
                    } catch (Exception $exc) {
                        $this->get('session')->getFlashBag()->add('ko', MessageSysteme::MAIL_ECHEC);
                        $this->data['message'] = 'Erreur survenue lors de l\'envoi de mail. Enregistrement effectuée avec succès';
                    }
                }

               
            }

            // cas ou l'attention est plus sur le status que sur l'etape
            elseif ($status > 0) {

                foreach ($tabIdBons as $id) {
                    if (intval($id) > 0) {
                        $entity = $em->getTable($this->entityBundle . $this->entity)->find($id);
                        if ($entity) {
                            $entity->setIsActif($status);
                            $em->persist($entity);
                        }
                    }
                }
            }
            
             $em->flush();
             
             $this->get('session')->getFlashBag()->add('ok', MessageSysteme::OPERATION_SUCCES );
        }


        // controle pour retourner sur une bonne route
        if ($route == '') {
            $route = $this->getHomeRoute();
        }

        return $this->redirectToRoute($route);
    }

    public function removeBonAction() {

        $description = 'Suppression des Bons récemment générés';
        $this->checkAuthorization(__FUNCTION__, $description, false);

        // recuperation des services 
        $request = $this->get('Request');

        // traitement du formulaire dans le cas d'une soumission

        if ($request->getMethod() == 'POST') {

            // recuperation des ids des nouvelles actions à ajouter
            $idBons = trim($request->get('idBonRemove'));

            $tabIdBons = explode('/', $idBons);

            // suprression des droits  à partir de leurs ids
            $this->gererStatus($this->entityBundle, $this->entity, $tabIdBons, Status::SUPPRIME);
            $this->gererEtapeCurrentEntity($tabIdBons, EtapeBon::BON_SUPPRIME);
        }

        return $this->redirectToRoute('list_bon_emis');
    }

    /**
     * voirBonAction : affichages des informations sur le bon de commande
     * 
     * @param type $idBon
     * @param type $fromApprobation
     * @return type
     */
    public function voirBonAction($idBon, $fromApprobation) {

        $description = 'Visualisation des informations d\'un Bon de Commande';
        $this->checkAuthorization(__FUNCTION__, $description, FALSE);

        // recuperation des services 
        $em = $this->getDoctrine()->getManager();

        // recuperation du bon de commande
        $bon = $em->getTable($this->entityBundle . $this->entity)->find($idBon);

        if (!$bon) {
            $this->get('session')->getFlashBag()->add('ko', MessageSysteme::INEXISTANT);
            return $this->redirectToRoute($this->getHomeRoute());
        }
        $achat = $bon->getAchat();
        $achatDep = $achat->getAchatDepartements()[0];

//        $form = $this->createForm(new AchatBonType(), $bon->getAchat());
        $this->data['entity'] = $bon;
        $this->data['achat'] = $achat;
        $this->data['achatDep'] = $achatDep;
        $this->data['provision'] = ($achatDep) ? $achatDep->getProvision() : null;
        $this->data['checkList'] = $achat->getCheckList();
        ;

//        $this->data['form'] = $form->createView();
//        var_dump($this->data['checkList']);exit;
        // $fromApprobation : il faut un controle par rapport au profil qui approuve le bon

        $this->data['createur'] = $em->getTable($this->entityBundle . 'Utilisateur')->find(intval($bon->getCreerPar()));
        $this->data['verificateur'] = $em->getTable($this->entityBundle . 'Utilisateur')->find(intval($bon->getVerifierPar()));
        $this->data['activateur'] = $em->getTable($this->entityBundle . 'Utilisateur')->find(intval($bon->getActiverPar()));
        $this->data['fromApprobation'] = $fromApprobation;
        $this->data['route'] = 'bon_soumis_approbation';

        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'voirBon.html.twig', $this->data);
    }

    /**
     * editBonAction : modification des informations du bon de commande
     * 
     * @param type $idAchat
     * @return type
     */
    public function editBonActionArticleAction($idAchat) {

        $description = 'Modification d\'un Bon de Commande d\'Article';
        $this->checkAuthorization(__FUNCTION__, $description, FALSE);

        // recuperation des services 
        $em = $this->getDoctrine()->getManager();


        // recuperation de l'objet Achat correspondant
        $achat = $em->getTable($this->entityBundle . 'Achat')->find($idAchat);

        if (!$achat) {
            $this->get('session')->getFlashBag()->add('ko', MessageSysteme::INEXISTANT);
            return $this->redirectToRoute($this->getHomeRoute());
        }

        $bonCommande = $achat->getBonCommande();
        if (!$bonCommande) {
            $this->get('session')->getFlashBag()->add('ko', MessageSysteme::INEXISTANT);
            return $this->redirectToRoute($this->getHomeRoute());
        }
        $etape = $bonCommande->getEtape();
        $route = ($etape == EtapeBon::BON_CHARGE) ? 'list_bon_loaded' : "list_bon_modif";

        /** ce control permet de signaler que ce bon est en modification afin de refuser la modifcation par toute autre personne
         * 
         */
        $etatModification = $bonCommande->getModificationEncours();

        if (!$etatModification) {
//            var_dump($etatModification);exit;
            // changement de l'etat de l'ancien bon qui serait encours de modification par l'utilisateur courant
            $params['modificationEncours'] = true;
            $params['modifierPar'] = $this->getCurrentId();
            $ancienneModifs = $em->getTable($this->entityBundle . $this->entity)->findBy($params);
            foreach ($ancienneModifs as $oldModif) {
                $oldModif->setModificationEncours(false);
            }

            $bonCommande->setModificationEncours(true);
            $bonCommande->setModifierPar($this->getCurrentId());
        } else {
            $idModificateur = $bonCommande->getModifierPar();
            // redirection au cas où un autre utilisateur est encours de modification du bon de commande
            if (!$idModificateur) {
                $bonCommande->setModifierPar($this->getCurrentId());
            } else if ($idModificateur != $this->getCurrentId()) {
                $modificateur = $em->getTable($this->entityBundle . 'Utilisateur')->find($idModificateur);
                $this->get('session')->getFlashBag()->add('ko', MessageSysteme::MODIF_ENCOURS . ' (Par ' . $modificateur . ' )');

                return $this->redirectToRoute($route);
            }
        }
        $em->flush();

        $request = $this->get('Request');
        $form = $this->createForm(new AchatBonType(), $achat, array(
            'action' => $this->generateUrl('edit_bonCommande', array('idAchat' => $achat->getId())),
            'method' => 'POST',
            'attr' => array('id' => 'modifBonForm', 'onsubmit' => 'afficheImageExecution(); '),
        ));


        // traitement du formulaire
        if ($request->getMethod() == 'POST') {

            // recuperation des ancennes données du bon de commande avant faire la modification

            $form->handleRequest($request);
            $achat = $form->getData();

            // modification des informations sur le bon de commande
            $bonCommande->setEtape(EtapeBon::BON_MODIFIE);
            $bonCommande->setModificationEncours(false);




            $formRecapString = trim($request->get('formRecap'));
            $formRecapArray = json_decode($formRecapString, true);

            // traitement du checkList
            $checkList = $achat->getCheckList();
            $checkList->hydrate($formRecapArray['checkList']);

            //recuperation des informations et articles pour les enregistrer
            $infoArray = $formRecapArray['info'];
            $objet = trim($infoArray['objet']);
            $pourcentagePayeALaCommande = $infoArray['paieCommande'];
            $delaisLivraison = intval($infoArray['delaisLivraison']);
            $idDep = $infoArray['departement'];

            $departement = $em->getTable($this->entityBundle . 'Departement')->find($idDep);

            // controle de la validite des valeurs recuperes
            if (!$departement || !$objet) {
                $this->get('session')->getFlashBag()->add('ko', MessageSysteme::MODIF_ECHEC);
                // renvoi de la vue
                $this->data['form'] = $form->createView();
                return $this->render($this->actionBundle . $this->viewFolder . 'editBonArticle.html.twig', $this->data);
            }



            // mise à jour de l'objet Achat
            $achat->setObjet($objet);
            $achat->setPaieCommande($pourcentagePayeALaCommande);
            $achat->setDelaisLivraison($delaisLivraison);

            $articlesArray = $formRecapArray['article'];
            $achatDepartement = $achat->getAchatDepartements()[0];
            if (!$achatDepartement) {
                $achatDepartement = new AchatDepartement();
                $provision = new Provision();
                $achatDepartement->setProvision($provision);
                $achat->addAchatDepartement($achatDepartement);
            }

            $achatDepartement->getProvision()->setInfoArticles($articlesArray);
            $achatDepartement->setDepartement($departement);
            $achatDepartement->ajusterValeurs(); // pour ajuster les valeurs
            // ajout des types aux fichier afin d'orienter leur stockage dans le bon repertoire.
            $ficDemande = $achat->getFicDemande();
            if ($ficDemande) {
                $ficDemande->setType(TypeFichier::Demande);
            }

            $ficProformat = $achat->getFicProformat();
            if ($ficProformat) {
                $ficProformat->setType(TypeFichier::Proformat);
            }

            $ficApprobationBudget = $achat->getFicApprobationBudget();
            if ($ficApprobationBudget) {
                $ficApprobationBudget->setType(TypeFichier::ApprobationBudget);
            }

            $ficApprobationHier = $achat->getFicApprobationHier();
            if ($ficApprobationHier) {
                $ficApprobationHier->setType(TypeFichier::ApprobationHier);
            }




            // enregistrement des informations 
            $em->persist($achat);
            $em->flush();

            $params['etape'] = EtapeBon::BON_MODIFIE;
//            $params['isActif'] = Status::ACTIF;

            $this->get('session')->getFlashBag()->add('ok', MessageSysteme::MODIF_SUCCES);
            return ($etape == EtapeBon::BON_CHARGE) ? $this->redirectToRoute('list_bon_loaded') : $this->redirectToRoute('list_bon_modif');
        }

        $this->data['numeroBon'] = $bonCommande->getNumero();
        $this->data['achat'] = $achat;
        $this->data['checkList'] = $achat->getCheckList();



        $objProvision = $achat->getAchatDepartements()[0]->getProvision();
        $departement = $achat->getAchatDepartements()[0]->getDepartement();

        $this->data['provision'] = $objProvision ? $objProvision->getInfoArticles() : array();
        $this->data['idDepartement'] = $departement ? $departement->getId() : 0;
        $this->data['form'] = $form->createView();

//         var_dump($this->data['idDepartement']);exit;

        $this->data['route'] = 'bon_soumis_approbation';

        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'editBonArticle.html.twig', $this->data);
    }

    /**
     * genererPDFBonAction : montre un aperçu sur le bon de commande en pdf
     * 
     * @param type $idBon
     * @return type
     */
    public function genererPDFBonAction($idBon) {

        $description = 'Generation du pdf pour un Bon de Commande';
        $this->checkAuthorization(__FUNCTION__, $description, FALSE);
        // recuperation des services 
        $em = $this->getDoctrine()->getManager();

        // recuperation du bon de commande
        $bon = $em->getTable($this->entityBundle . $this->entity)->find($idBon);
        if (!$bon) {
            $this->get('session')->getFlashBag()->add('ko', MessageSysteme::INEXISTANT);
            return $this->redirectToRoute($this->getHomeRoute());
        }

        // si le bons avait déjà un fichier alors on conserve l'ancien fichier
//        $oldfichier = $bon->getFichier() ;
//        // si le fichier existe  alors on l'affiche
//        if($oldfichier){
//            return $this->redirectToRoute('file_view',array('id'=> $oldfichier->getId()));
//        }
        // si le bon na pas encore de ficher alors on genere pour la premiere fois un fichier PDF
        $listAchatDep = $bon->getAchat()->getAchatDepartements();
        $listProvision = array();

        foreach ($listAchatDep as $achatDep) {
            $listProvision[] = $achatDep->getProvision();
        }
        $this->data['entity'] = $bon;
        $this->data['listProvision'] = $listProvision;
        ob_start();

        $donneperation = $this->get('templating')->render($this->actionBundle . $this->viewFolder . 'apercuPDFBon.html.twig', $this->data);
        $html2pdf = new Html2Pdf_Html2Pdf('P', 'A4', 'fr');
        $html2pdf->WriteHTML($donneperation, false);

        // creation d'un nouveau fichier à enregistrer
        $fichier = new Fichier(TypeFichier::BonCommande);
        // generation d'un nom pour le fichier en fonction de son type et mise à jour des information de l'entité
        $fichier->generateFileName();

        // definition du nom du fichier temporaire
        $tempFileName = $fichier->getAbsolutePath();

        // creation du fichier à l'endroit spécifié
        $html2pdf->Output($tempFileName, "F");

        // si le fichier est créer à l'emplacement indiqué, alors on enregistre l'objet concerné
        if (file_exists($tempFileName)) {

            // recuperation de l'ancien fichier s'il existe
            $oldFic = $bon->getFichier();
            ($oldFic) ? $em->remove($oldFic) : '';

            // enregistrement des informations du fichier generé dans la base de données
            $bon->setFichier($fichier);
            $em->flush();

            // affichage du fichier dans un navigateur
            $content = file_get_contents($tempFileName);
            header("Content-Disposition: inline; filename=$tempFileName");
            header("Content-type: application/pdf");
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
            echo $content;
            exit;
        }

        // si le fichier n'est pas generé alors on afffiche le message suivant:
        return new Response('La génération du fichier de bon à rencontrer des erreurs !');
    }

  
    /**
     * rechercheBonAction : fonction de recherche multicrtere sur un bon de commande
     * 
     * @return type
     */
    public function rechercheBonAction() {

        $description = 'Recherche Multicritère des  Bons de Commande';
        $this->checkAuthorization(__FUNCTION__, $description, true);
        $bonRecherche = array();

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('Request');

        $isNum = intval($request->get('isNum'));

        // recuperaton des parametres du formuaire
        if ($request->getMethod() == 'POST') {



            // recuperation du bon de commande à partire de son numero
            if ($isNum) {
                $numBon = trim($request->get('numBon'));

                $bonRecherche = $em->getTable($this->entityBundle . $this->entity)->findByNumero($numBon);
            }
            // cas d'une recherche multicritère
            else {
                $formData = $request->request->get('eprocess_entitybundle_achat_bon');
                $idFour = intval($request->get('idFour'));
                $etape = intval($request->get('etape'));
                $montMin = intval($request->get('montMin'));
                $montMax = intval($request->get('montMax'));
                $debut = trim($request->get('debut'));
                $fin = trim($request->get('fin'));
                $devise = trim($formData['devise']);
                $typeDate = intval($formData['typeDate']);


                $bonRecherche = $em->getTable($this->entityBundle . $this->entity)
                        ->findMultiCritere($idFour, $etape, $montMin, $montMax, $devise, $debut, $fin, $typeDate);
            }
//            
        }

        $form = $this->createForm(new RechercheBonType());
        // recuperation des bons approuvés et envoyés
        $this->data['form'] = $form->createView();

        $this->data['bonRecherche'] = $bonRecherche;
        $this->data['isNum'] = $isNum;


        return $this->render($this->actionBundle . $this->viewFolder . 'rechercheBon.html.twig', $this->data);
    }

    /**
     *  indexBonCommandeAction : tableau de bord de 
     * 
     * @return type
     */
    public function indexBonCommandeAction() {

        $description = 'Tableau de bord des Bons ';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $em = $this->getDoctrine()->getManager();

        $resultStat = $em->getTable($this->entityBundle . $this->entity)
                ->getStatistiqueMensuel();
        $this->data['stat'] = $resultStat;

        return $this->render($this->actionBundle . $this->viewFolder . 'index.html.twig', $this->data);
    }

    /**
     *  exportExcelBonCommandeAction : exportation des bons de commande en Excel 
     * 
     * @return type
     */
    public function exportExcelBonCommandeAction() {

        $description = 'Exportation en Excel des Bons de Commande';
        $this->checkAuthorization(__FUNCTION__, $description, false);

        // recuperation des bons approuvés et envoyés

        $request = $this->get('Request');
        if ($request->getMethod() == 'POST') {
            $listeBonString = trim($request->get('listBon'));
            $bonArray = json_decode($listeBonString, true);


            // Create new PHPExcel object
            $objPHPExcel = new PHPExcel();

            // Set document properties
            $objPHPExcel->getProperties()->setCreator("Soft-EBS")
                    ->setLastModifiedBy("Someone")
                    ->setTitle("LISTE DES BONS DE COMMANDE")
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
            ;

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
            foreach ($bonArray as $bon) {
                $feuillet->setCellValue('A' . $index, $bon['numero'])
                        ->setCellValue('B' . $index, $bon['objet'])
                        ->setCellValue('C' . $index, $bon['montant'])
                        ->setCellValue('D' . $index, $bon['devise'])
                        ->setCellValue('E' . $index, $bon['etape']);
                $index ++;
            }

            // Redirect output to a client’s web browser (Excel5)
            $this->response->headers->set('Content-Type', 'application/vnd.ms-excel');
            $this->response->headers->set('Content-Disposition', 'attachment;filename="liste_bon_commande.xls"');
            $this->response->headers->set('Cache-Control', 'max-age=0');
            $this->response->sendHeaders();
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit();
        }

        return $this->redirectToRoute($this->getHomeRoute());
    }

    /**
     * chargerBonAction : fonction de chargement des bons de comande à partir
     *  d'un fichier Excel
     * 
     */
    public function chargerBonAction() {
        $description = 'Chargement des Bons de Commande  depuis un fichier Excel ';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $form = $this->createForm(new FichierChargementType(), array(), array(
            'action' => $this->generateUrl('load_bon'),
            'method' => 'POST',
            'attr' => array('id' => 'chargerBonForm', 'onsubmit' => 'afficheImageExecution(); '),
        ));

        $request = $this->get('Request');
        $fichier = new Fichier();

        // traitemet du fichier teechargé
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            $data = $form->getData();
            $fichier = $data['fichier'];
            $fichier->setType(TypeFichier::ListeBonCommande);
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

            // On boucle sur les feuillets
            $i = 0;
            $resutat = array();
            $ligne1OK = $ligne2OK = true;
            $sheet = $objPHPExcel->getSheet(0);
            while ($ligne1OK || $ligne2OK) {
                // chargement d'un feuillet du fichier
                $resutat[] = $this->chargerFeuillet($sheet);
                $i++;
                $sheet = $objPHPExcel->getSheet($i);
                // ici on verifie si la ligne 1 ou 2 du fichier à un numero de bon et un objet avant de la charger
                $ligne1OK = ($sheet->getCell('B' . $i)->getValue() && $sheet->getCell('D' . $i)->getValue());
                $ligne2OK = ($sheet->getCell('B' . ($i + 1))->getValue() && $sheet->getCell('D' . ($i + 1))->getValue());
            }


            //suppression du fichier chargé après traitement
            $fichier->preRemoveUpload();
            $fichier->removeUpload();

            // redirection sur la page des fichiers chargé
            $this->get('session')->getFlashBag()->add('ok', MessageSysteme::OPERATION_SUCCES);
            $this->data['resultat'] = $resutat;
        }


        $this->data['form'] = $form->createView();
        return $this->render($this->actionBundle . $this->viewFolder . 'chargerBon.html.twig', $this->data);
    }

    /**
     * chargerFeuillet : fonction de chargement des bons d'un feuillet suivant un format defini
     * 
     * @param type $sheet
     * @return type
     */
    private function chargerFeuillet($sheet) {

        $em = $this->getDoctrine()->getManager();

        // On boucle sur les lignes

        $listBon = $listAchat = array();

        $bonNonCharges = array();
        $ligneActuelleOK = $ligneSuivanteOK = true; // variable qui indique que la ligne qui suit la ligne courante à  un numero de bon de commande et un objet

        $i = 1; // indice de la ligne courante
        // tant que la ligne courante  ou suivante est conforme, on poursuit le chargement de la ligne conforme 
        while ($ligneActuelleOK || $ligneSuivanteOK) {
            $date = $sheet->getCell('A' . $i)->getFormattedValue();
            $dateCreation = ($date) ? date_create_from_format('d/m/y', $date) : new DateTime();
            $numBon = $sheet->getCell('B' . $i)->getFormattedValue();
            $nomFour = $sheet->getCell('C' . $i)->getFormattedValue();
            $objet = $sheet->getCell('D' . $i)->getFormattedValue();
            $mont = $sheet->getCell('E' . $i)->getFormattedValue();
            $devise = $sheet->getCell('F' . $i)->getFormattedValue();
            $remise = $sheet->getCell('G' . $i)->getFormattedValue();
            $montNet = $sheet->getCell('H' . $i)->getFormattedValue();

            $existeBon = $em->getTable($this->entityBundle . $this->entity)->findOneBy(array('numero' => $numBon));

            // si le bon de commande  a un mumero et n'existe pas encore, on le charge dans la base de données
            if ($numBon && $objet && !$existeBon) {
                $listAchat[$i] = new Achat;
                $listAchat[$i]->setCreerPar($this->getCurrentId());
                $listAchat[$i]->setDateCreation(new DateTime());
                $listAchat[$i]->setDevise($devise);
                $listAchat[$i]->setMontant($mont);
                $listAchat[$i]->setObjet($objet);

                $listBon[$i] = new BonCommande($numBon, $this->getCurrentId());
                $listBon[$i]->setNumero($numBon);
                $listBon[$i]->setDateCreation($dateCreation);
                $listBon[$i]->setIsActif(Status::INACTIF);
                $listBon[$i]->setEtape(EtapeBon::BON_CHARGE);

                $listFour[$i] = new Fournisseur();

                $oldFournisseurs = $em->getTable($this->entityBundle . 'Fournisseur')->findNomLike($nomFour);

                $nbreFour = count($oldFournisseurs);

                // au cas où le fournisseur existe
                if ($nbreFour == 0) {
                    $listFour[$i]->setNom($nomFour);
                    $listFour[$i]->setCreerPar($this->getCurrentId());
                } else {
                    $idFour = $oldFournisseurs[0]['id'];
                    $listFour[$i] = $em->getTable($this->entityBundle . 'Fournisseur')->find($idFour);
                }




                $infoProvision[$i] = new Provision(array('remise' => $remise, 'total' => $montNet + $remise));

                $listAchat[$i]->setFournisseur($listFour[$i]);
                $listAchat[$i]->setBonCommande($listBon[$i]);
                $listAchat[$i]->setProvision($infoProvision[$i]);

                $em->persist($listAchat[$i]);
                $em->flush(); // il faut persister chaque ligne pour éviter des doublons
            }
            // au cas ou un bon existe déjà avec ce bon de commande
            else {
                $bonNonCharges[] = $existeBon;
            }

            $i++;
            $j = $i + 1;

            $ligneActuelleOK = ($sheet->getCell('B' . $i)->getValue() && $sheet->getCell('D' . $i)->getValue());
            $ligneSuivanteOK = ($sheet->getCell('B' . $j)->getValue() && $sheet->getCell('D' . $j)->getValue());
        }

        $em->flush();

        $total = $i - 1;
        $result['titre'] = $sheet->getTitle();
        $result['total'] = $total;
        $result['charge'] = $total - count($bonNonCharges);

        return $result;
    }

    /**
     * listBonChargeAction : liste des bons de commande nouvellement chargés
     * 
     * @return type
     */
    public function listBonChargeAction() {
        $description = 'Liste des bons nouvellement Chargés';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $em = $this->getDoctrine()->getManager();

        // recuperation des bons nouvellement emis
        $params['etape'] = EtapeBon::BON_CHARGE;
        $params['isActif'] = Status::ACTIF;
        $this->data['listeBonCommandeActif'] = $em->getTable($this->entityBundle . $this->entity)->findBy($params);

        $params['isActif'] = Status::INACTIF;
        $this->data['listeBonCommandeInactif'] = $em->getTable($this->entityBundle . $this->entity)->findBy($params);

        // ajout des autres paramètres de la vue
        $this->data['route'] = $this->get('Request')->get('_route');


        // renvoi de la vue
        return $this->render($this->actionBundle . $this->viewFolder . 'listBonCharge.html.twig', $this->data);
    }

}
