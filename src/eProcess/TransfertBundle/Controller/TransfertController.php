<?php

namespace eProcess\TransfertBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use eProcess\SecurityBundle\Constantes\TypeProfile;
use eProcess\SecurityBundle\Constantes\Status;
use eProcess\EntityBundle\Entity\Transfert;
use \DateTime;
use eProcess\SecurityBundle\Constantes\MessageSysteme;
use eProcess\SecurityBundle\Constantes\EtapeTranfert;
use eProcess\SecurityBundle\Constantes\EtapeFacture;
use eProcess\EntityBundle\Entity\TransfertFacture;

/**
 * Facture controller.
 *
 */
class TransfertController extends Controller {

    /**
     * constructeur de la classe
     */
    public function __construct() {

        $this->module = Controller::MOD_ENVOIS;
        $this->descModule = Controller::MOD_ENVOIS_DESC;
        $this->actionBundle = 'eProcessTransfertBundle:';
        parent::__construct('Transfert');
        $this->viewFolder = 'Transfert:';
    }

    /**
     *  indexTransfertAction : tableau de bord des transferts de factures
     * 
     * @return type
     */
    public function indexTransfertAction() {

        $description = 'Tableau de Bord des Transferts de factures ';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $em = $this->getDoctrine()->getManager();
        
        $codeProfile = $this->getCurrentCodeProfil();
        $resultStat = $em->getTable($this->entityBundle . $this->entity)
                ->getStatistique($codeProfile);
        
        $this->data['stat'] = $resultStat;

        // recuperation des bons approuvés et envoyés

        return $this->render($this->actionBundle . $this->viewFolder . 'index.html.twig', $this->data);
    }

    /**
     * transfertFactureAction : fonction d'envoi de facture
     * 
     * @return Response
     */
    public function transfertFactureAction() {

        $description = 'Transfert des factures';
        $this->checkAuthorization(__FUNCTION__, $description, false);

        $this->data['status'] = true;
        $this->data['message'] = '';

        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

//        $mailer = $this->get('email_manager');
//        $listeUtilisateur = array();
//        $listeFactures = array();
//        $tabIds = array(1, 2, 3, 4, 5);
//        foreach ($tabIds as $idFacture) {
//            $facture = $em->getTable($this->entityBundle . 'Facture')->find($idFacture);
//
//            if ($facture) {
//                $listeFactures[] = $facture;
//            }
//        }
//        $this->data['titreMail'] = 'SoftEBS';
//        $this->data['noteMail'] = 'note';
//        $this->data['titreMail'] = 'SoftEBS';
//        $this->data['listeFacture'] = $listeFactures;
//        
//        return $this->render('eProcessTransfertBundle:Email:mail.html.twig',  $this->data);
        // recuperation des informations du formaulaire
        if ($request->getMethod() == 'POST') {

            $ids = trim($request->get('ids'));
            $etape = trim($request->get('etape'));
            $messageDescriptif = trim($request->get('message'));

            // ici je dois faire recuperer les utilisateurs et leur en voyer des notifications  
            if ($ids && $etape) {


                $mailer = $this->get('email_manager');
                $listeUtilisateur = array();
                $listeFactures = array();
                $profile = null;

                //enregistrement du transfert
                try {

                    // recuperation du profile de destination  : c'est soit EBS ou OPS
                    if ($etape == EtapeTranfert::RECEPT_EBS || $etape == EtapeTranfert::OPS_EBS) {
                        $profile = $em->getTable($this->entityBundle . 'Profile')->findOneBy(array('code' => TypeProfile::EBS));
                    } else if ($etape == EtapeTranfert::EBS_OPS) {
                        $profile = $em->getTable($this->entityBundle . 'Profile')->findOneBy(array('code' => TypeProfile::OPERATIONS));
                    }

                    // recuperation des utilisateurs actifs du profile de destination
                    if ($profile) {
                        $params['isActif'] = Status::ACTIF;
                        $params['profile'] = $profile;
                        $listeUtilisateur = $em->getTable($this->entityBundle . 'Utilisateur')->findBy($params);
                    }

//                    var_dump(count($listeUtilisateur));exit;
                    // création d'un transfert pour enregistrement des informations
                    $transfert = new Transfert();

                    $transfert->setDateCreation(new DateTime());
                    $transfert->setIsActif(false);
                    $transfert->setEtape($etape);
//                    $transfert->setDescription($messageDescriptif);
                    $transfert->setEnvoyeur($this->getCurrentUser());


                    // recuperation factures à partire de leurs IDs
                    $tabIds = explode('/', $ids);


                    foreach ($tabIds as $idFacture) {
                        $facture = $em->getTable($this->entityBundle . 'Facture')->find($idFacture);

                        if ($facture) {

                            // changement de l'etape de la facture en fonction du tye de transfert
                            // envoi depuis reception à EBS ou OPS à EBS
                            if ($etape == EtapeTranfert::RECEPT_EBS || $etape == EtapeTranfert::OPS_EBS) {
                                $facture->setEtape(EtapeFacture::FCT_ENVOYE_EBS);
                            }
                            // envoi depuis EBS à OPS
                            elseif ($etape == EtapeTranfert::EBS_OPS) {
                                $facture->setEtape(EtapeFacture::FCT_ENVOYE_OPS);
                            }
                            $transfertFacture = new TransfertFacture();
                            $transfertFacture->setDescription($messageDescriptif);
                            $transfertFacture->setFacture($facture);
                            $transfertFacture->setTransfert($transfert);


                            $listeFactures[] = $facture;
                        }
                    }


                    // enregistrement du transfert
                    // creation et persistance des notifications
                    $em->persist($transfert);
                    $em->flush();



                    $this->get('session')->getFlashBag()->add('ok', MessageSysteme::TRANSF_SUCCES);
                } catch (Exception $exc) {
                    $this->get('session')->getFlashBag()->add('ok', MessageSysteme::TRANSF_ECHEC);
                    $this->data['status'] = false;
                    $this->data['message'] = 'Erreur survenue lors de l\'enregistrement du transfert';
                }




                //envoi de mail dans le cas où ce n'est pas un envoi de factures de EBS à  la reception
                if ($etape != EtapeTranfert::EBS_RECEPT) {
                    try {
                        // envoi de mails de notification
                        $mailer->sendUsersMail($listeUtilisateur, $listeFactures, $etape);
                        $this->get('session')->getFlashBag()->add('ok', MessageSysteme::MAIL_SUCCES);
                    } catch (Exception $exc) {
                        $this->get('session')->getFlashBag()->add('ko', MessageSysteme::MAIL_ECHEC);
                        $this->data['status'] = false;
                        $this->data['message'] = 'Erreur survenue lors de l\'envoi de mail. Enregistrement effectuée avec succès';
                    }
                }
            }
            // au cas où on n'a pas d'etat de transfert ni de facture
            else {
                $this->get('session')->getFlashBag()->add('ko', MessageSysteme::INEXISTANT);
                $this->data['status'] = false;
                $this->data['message'] = 'le formulaire est incorrecte ';
            }

            // enregistrement des informations du facture au cas où le formulaire est valide
        }


        return new Response(json_encode($this->data));
    }

    /**
     * receptionFactureTransfereAction : fonction de reception de factures envoyées
     * 
     * @return Response
     */
    public function receptionFactureTransfereAction($id) {

        $description = 'Réception de Factures Transferés';
        $this->checkAuthorization(__FUNCTION__, $description, false);

        $em = $this->getDoctrine()->getManager();

        // recuperation du transfert à accepter
        $transfert = $em->getTable($this->entityBundle . $this->entity)->find($id);

        // si le transfert existe alors on le reçoit et on se redirige sur la page 
        // d'acceptation des factures relatives à ce transfert si non on se rend sur la page de provenance
        if ($transfert) {

            // mise à jour des informations du transfert
            $transfert->setRecepteur($this->getCurrentUser());
            $transfert->setIsActif(true);
            $transfert->setDateActivation(new DateTime());

            //mise à jour des etapes des factures du transfert
            $etape = $transfert->getEtape();
            $listeTransfertFactures = $transfert->getTransfertFactures();

            $listeFactures = array();

            // recuperation  de la liste des facture relative à ce trannsfert
            foreach ($listeTransfertFactures as $transFacture) {
                $facture = $transFacture->getFacture();
                if ($facture) {
                    $listeFactures[] = $facture;
                }
            }



            // reception des factures envoyées par les receptionistes
            if ($etape == EtapeTranfert::RECEPT_EBS) {
                foreach ($listeFactures as $facture) {
                    $facture->setEtape(EtapeFacture::FCT_ARRIVE_EBS);
                }
            }
            // reception des factures envoyées par EBS à la RECEPTION
            elseif ($etape == EtapeTranfert::EBS_RECEPT) {
                foreach ($listeFactures as $facture) {
                    $facture->setEtape(EtapeFacture::FCT_REJETE_EBS);
                }
            }

            // reception des factures envoyées par les agents de EBS
            elseif ($etape == EtapeTranfert::EBS_OPS) {
                foreach ($listeFactures as $facture) {
                    $facture->setEtape(EtapeFacture::FCT_ARRIVE_OPS);
                }
            }

            // reception des factures rejetés par les agents de OPS
            elseif ($etape == EtapeTranfert::OPS_EBS) {
                foreach ($listeFactures as $facture) {
                    $facture->setEtape(EtapeFacture::FCT_REJETE_OPS);
                }
            }

            // creation et persistance des notifications
            $em->flush();
//            $this->get('session')->getFlashBag()->add('ok', MessageSysteme::RECEPT_SUCCES);
            // redirection sur la route de reception des factures relatives à ce transfert
            return $this->redirectToRoute('e_process_transfert_approbation', array('id' => $transfert->getId()));
        }

        return $this->redirectToRoute('e_process_transfert_encours_recept');
    }

    /**
     * transfertApprobationAction : fonction de confirmation ou rejet de factures envoyées reçus
     * 
     * @return Response
     */
    public function transfertApprobationAction($id) {

        $description = 'Confirmation ou Rejet des Factures Transferées';
        $this->checkAuthorization(__FUNCTION__, $description, false);


        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        // recuperation du transfert à accepter
        $transfert = $em->getTable($this->entityBundle . $this->entity)->find($id);


        // si le transfert n'existe alors on  se redirige sur la page des nouveaux transferts
        if ($transfert) {

            // reception ou rejet de facture
            if ($request->getMethod() == 'POST') {


                // recuperation des transFacture à approver
                $ids = trim($request->get('ids'));
                $avis = intval($request->get('avis'));
                $message = trim($request->get('note'));
                $listeFactures = array();

//                var_dump($message);exit;

                $idArray = explode('/', $ids);

                // recuperation de la liste des transfertFactures
                foreach ($idArray as $idTrans) {
                    $transFact = $em->getTable($this->entityBundle . 'TransfertFacture')->find(intval($idTrans));
                    if ($transFact) {
                        $transFact->setIsActif($avis);
                        $transFact->setDescription($message);
                        $listeFactures[] = $transFact->getFacture();
                    }
                }


                // en cas validation on change l'etat de la facture
                if ($avis == Status::ACTIF) {
                    $etape = $transfert->getEtape();


                    // reception des factures envoyées par les receptionistes
                    if ($etape == EtapeTranfert::RECEPT_EBS) {
                        foreach ($listeFactures as $facture) {
                            $facture->setEtape(EtapeFacture::FCT_ARRIVE_EBS);
                        }
                    }
                    // reception des factures envoyées par EBS à la RECEPTION
//                    elseif ($etape == EtapeTranfert::EBS_RECEPT) {
//                        foreach ($listeFactures as $facture) {
//                            $facture->setEtape(EtapeFacture::FCT_REJETE_EBS);
//                        }
//                    }
                    // reception des factures envoyées par les agents de EBS
                    elseif ($etape == EtapeTranfert::EBS_OPS) {
                        foreach ($listeFactures as $facture) {
                            $facture->setEtape(EtapeFacture::FCT_ARRIVE_OPS
                                    );
                        }
                    }

//                    // reception des factures rejetés par les agents de OPS
//                    elseif ($etape == EtapeTranfert::OPS_EBS) {
//                        foreach ($listeFactures as $facture) {
//                            $facture->setEtape(EtapeFacture::FCT_REJETE_OPS);
//                        }
//                    }
                }
                // en cas de refus on change l'etat de la facture
                else if ($avis == Status::BLOCKE) {
                    $etape = $transfert->getEtape();


                    // reception des factures envoyées par les receptionistes
                    if ($etape == EtapeTranfert::RECEPT_EBS) {
                        foreach ($listeFactures as $facture) {
                            $facture->setEtape(EtapeFacture::FCT_REJETE_EBS);
                        }
                    }
                    // reception des factures envoyées par EBS à la RECEPTION
//                    elseif ($etape == EtapeTranfert::EBS_RECEPT) {
//                        foreach ($listeFactures as $facture) {
//                            $facture->setEtape(EtapeFacture::FCT_REJETE_EBS);
//                        }
//                    }
                    // reception des factures envoyées par les agents de EBS
                    elseif ($etape == EtapeTranfert::EBS_OPS) {
                        foreach ($listeFactures as $facture) {
                            $facture->setEtape(EtapeFacture::FCT_REJETE_OPS);
                        }
                    }

                    // reception des factures rejetés par les agents de OPS
                    elseif ($etape == EtapeTranfert::OPS_EBS) {
                        foreach ($listeFactures as $facture) {
                            $facture->setEtape(EtapeFacture::FCT_REJETE_OPS);
                        }
                    }
                }

                $em->flush();

                
                // message de notification à afficher sur la page de traitement
                if ($avis == Status::ACTIF) {
                    $this->get('session')->getFlashBag()->add('ok', MessageSysteme::RECEPT_SUCCES);
                } elseif ($avis == Status::BLOCKE) {
                    $this->get('session')->getFlashBag()->add('ok', MessageSysteme::REJET_SUCCES);
                }
            }

            // apres va lidation, on retourne sur l page de validation des factures
            $this->data['entity'] = $transfert;
            return $this->render($this->actionBundle . $this->viewFolder . 'approuveTransfert.html.twig', $this->data, $this->response);
        }

        return $this->redirectToRoute('e_process_transfert_encours_recept');
    }

    /**
     * transfertEncoursEnvoiAction : fonction de recuperation des tranferts de factures envoyées par 
     * 
     *      le profile de l'utilisateur qui est connecté mais qui n'est pas encore reçus
     * 
     * @return Response
     */
    public function transfertEncoursEnvoiAction() {

        $description = 'Liste des envois encours';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $em = $this->getDoctrine()->getManager();

        // fecuperation du code du profile connecté
        $codeProfile = $this->getCurrentCodeProfil();
//        var_dump($codeProfile) ;exit;
        // recupertion des 100 derniers envois de factures fait par ce profile et qui ne sont pas encore reçus par le destinataire
        $transfertEnvoye = $em->getTable($this->entityBundle . $this->entity)->findTransfert($codeProfile, EtapeTranfert::ENVOYER, false);

        // renvoi de la vue
        $this->data['listeEnvoi'] = $transfertEnvoye;
        return $this->render($this->actionBundle . $this->viewFolder . 'envoiEncours.html.twig', $this->data, $this->response);
    }

    /**
     * transfertEnvoyeAction : fonction de recuperation des tranferts de factures envoyées par 
     * 
     *      le profile de l'utilisateur qui est connecté et qui est reçu par le destinateur
     * 
     * @return Response
     */
    public function transfertEnvoyeAction() {

        $description = 'Liste des envois de factures aboutis';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $em = $this->getDoctrine()->getManager();

        // fecuperation du code du profile connecté
        $codeProfile = $this->getCurrentCodeProfil();

        // recupertion des 100 derniers envois de factures fait par ce profile et qui  sont reçus par le destinataire
        $transfertEnvoye = $em->getTable($this->entityBundle . $this->entity)->findTransfert($codeProfile, EtapeTranfert::ENVOYER, true);

        // renvoi de la vue
        $this->data['listeEnvoi'] = $transfertEnvoye;
        return $this->render($this->actionBundle . $this->viewFolder . 'listeEnvoye.html.twig', $this->data, $this->response);
    }

    /**
     * transfertEncoursReceptionAction : fonction de recuperation des transferts encours de reception
     * 
     * @return Response
     */
    public function transfertEncoursReceptionAction() {

        $description = 'Liste des Réceptions Encours';
        $this->checkAuthorization(__FUNCTION__, $description, true);


        $em = $this->getDoctrine()->getManager();

        // fecuperation du code du profile connecté
        $codeProfile = $this->getCurrentCodeProfil();

        // recupertion des 100 derniers réceptions de factures encours pour ce profile 
        $transfertEnvoye = $em->getTable($this->entityBundle . $this->entity)->findTransfert($codeProfile, EtapeTranfert::RECEVOIR, false);

        // renvoi de la vue
        $this->data['listeEnvoi'] = $transfertEnvoye;
        return $this->render($this->actionBundle . $this->viewFolder . 'receptionEncours.html.twig', $this->data, $this->response);
    }

    /**
     * transfertRecuAction : liste des réceptions de factures faites par le profile de l'utiisateur connecté
     * 
     * @return Response
     */
    public function transfertRecuAction() {

        $description = 'Liste des Réceptions ';
        $this->checkAuthorization(__FUNCTION__, $description, true);

        $em = $this->getDoctrine()->getManager();

        // fecuperation du code du profile connecté
        $codeProfile = $this->getCurrentCodeProfil();

        // recupertion des 100 derniers réceptions de factures fait par ce profile 
        $transfertEnvoye = $em->getTable($this->entityBundle . $this->entity)->findTransfert($codeProfile, EtapeTranfert::RECEVOIR, true);

        // renvoi de la vue
        $this->data['listeEnvoi'] = $transfertEnvoye;
        return $this->render($this->actionBundle . $this->viewFolder . 'listeRecu.html.twig', $this->data, $this->response);
    }

    /**
     * transfertRecuAction : liste des réceptions de factures faites par le profile de l'utiisateur connecté
     * 
     * @return Response
     */
    public function voirTransfertAction($id) {

        $description = "Visualisation des Informations d'un Transfert";
        $this->checkAuthorization(__FUNCTION__, $description, false);

        $em = $this->getDoctrine()->getManager();

        $transfert = $em->getTable($this->entityBundle . $this->entity)->find($id);
        // redirection sur la page d'accueil en cas d'bsence du transfert dans la base
        if (!$transfert) {
            $this->get('session')->getFlashBag()->add('ko', MessageSysteme::INEXISTANT);
            return $this->redirect($this->generateUrl($this->getHomeRoute()));
        }



        // renvoi de la vue
        $this->data['entity'] = $transfert;
        return $this->render($this->actionBundle . $this->viewFolder . 'voirTransfert.html.twig', $this->data, $this->response);
    }

}
