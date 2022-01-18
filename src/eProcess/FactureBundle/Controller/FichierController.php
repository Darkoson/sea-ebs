<?php

namespace eProcess\FactureBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use eProcess\SecurityBundle\Constantes\MessageSysteme;
use eProcess\EntityBundle\Form\FichierType;
use eProcess\SecurityBundle\Constantes\Status ;

/**
 * Fichier controller.
 *
 */
class FichierController extends Controller {

    /**
     * constructeur de la classe
     */
    public function __construct() {

        $this->module = Controller::MOD_DEPARTEMENT;
        $this->descModule = Controller::MOD_DEPARTEMENT_DESC;
        $this->actionBundle = 'eProcessFactureBundle:';
        $this->viewFolder = 'Fichier:';
        parent::__construct('Fichier');
    }

    /**
     * uploadFileAction : fonction de chargement de fichiers
     * 
     *
     */
    public function downloadFileAction($id) {

        $description = 'Téléchargement des Fichiers';
        $this->checkAuthorization(__FUNCTION__, $description, false);

        $em = $this->getDoctrine()->getManager();

        // recuperation des departements
        $fichier = $em->getTable($this->entityBundle . $this->entity)->find($id);



        if ($fichier) {
            $nomFichier = $fichier->getUrl();
            $chemin = $fichier->getUploadRootDir();
            header("Content-type: application/force-download");
            header("Content-disposition: filename=$nomFichier");

            readFile($chemin . $nomFichier);
            exit;
        }

        // renvoi de la vue
        return $this->redirectToRoute($this->getHomeRoute());
    }

    /**
     * viewFileAction : fonction de visualisation d'un fichier
     * 
     *
     */
    public function viewFileAction($id) {

        $description = 'Visualisation du contenu des Fichiers';
        $this->checkAuthorization(__FUNCTION__, $description, false);
        $em = $this->getDoctrine()->getManager();

        // recuperation du fichier
        $fichier = $em->getTable($this->entityBundle . $this->entity)->find($id);

        if ($fichier) {
            // definition du nom du fichier temporaire
            $chemin = $fichier->getAbsolutePath();
            $content = file_get_contents($chemin);
            header("Content-Disposition: inline; filename=$chemin");

            $extension = $fichier->getExtention();

            // discussion sur l'extension du fichier:
            if ($extension == 'pdf') {
                header("Content-type: application/pdf");
                header('Cache-Control: private, max-age=0, must-revalidate');
                header('Pragma: public');

                return new Response($content, 200, array('Content-Type' => 'application/pdf'));
            } elseif ($extension == 'xlsx' || $extension == 'csv' || $extension == 'xls') {
                header("Content-type: application/vnd.ms-excel");
                header('Cache-Control: private, max-age=0, must-revalidate');
                header('Pragma: public');
                echo $content;
                exit;
            } elseif ($extension == 'png') {
                header('Content-Length: ' . filesize($chemin));
                header('Content-Type: image/png');
                print $content;
                exit;
            } 
            elseif ($extension == 'jpeg') {
                header('Content-Length: ' . filesize($chemin));
                header('Content-Type: image/jpeg');
                print $content;
                exit;
            }
            elseif ($extension == 'txt') {
                print $content;
                exit;
            } else {
                
                
                var_dump($extension);exit;
                return new Response('IMPOSSIBLE D\'AFFICHIER CE FICHIER, VEUILLEZ LE TELECHARGER !');
            }
        }

        // renvoi de la vue
        return new Response(MessageSysteme::INEXISTANT);
    }

    /**
     * editFileAction : fonction de MAJ d'un fichier
     * 
     *
     */
    public function editFileAction($id) {
        $description = "Modification d'un Fichier Existant";
        $this->checkAuthorization(__FUNCTION__, $description, false);
        $em = $this->getDoctrine()->getManager();

        // recuperation du fichier
        $fichier = $em->getTable($this->entityBundle . $this->entity)->find($id);

        if ($fichier) {
            $form = $this->createForm(new FichierType, $fichier, array(
            'action' => $this->generateUrl('file_edit',array('id'=>$id)),
            'method' => 'POST'));

            $request = $this->getRequest();
            // traitement du formulaire

            if ($request->getMethod() == 'POST') {

               
                $form->handleRequest($request);

                if ($form->isValid()) {
                    $fichier  = $form->getData() ;
                    
                    // pour dire que le fichier de bon de commande est mis à jour donc on peut
                    // poursuivre le traitement
                    $fichier->setIsActif(Status::ACTIF ) ;
                    
                    $em->persist($fichier);
                    $em->flush();
                    
                    $this->get('session')->getFlashBag()->add('ok', MessageSysteme::MODIF_SUCCES );
                    
                    return $this->redirectToRoute('bon_approuve');
                }
                  $this->get('session')->getFlashBag()->add('ko', MessageSysteme::MODIF_ECHEC );
            }
           
            $this->data['formFile'] = $form->createView();
            return  $this->render($this->actionBundle . $this->viewFolder . 'editFile.html.twig', $this->data);
        }

        // renvoi de la vue
        return new Response(MessageSysteme::INEXISTANT);
    }


}
