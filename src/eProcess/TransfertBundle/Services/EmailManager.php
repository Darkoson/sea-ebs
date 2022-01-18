<?php

/**
 * Description of DroitManager
 *
 * @author 
 */

namespace eProcess\TransfertBundle\Services;

use Symfony\Component\Templating\EngineInterface;
use \Swift_Attachment;
use \Symfony\Component\DependencyInjection\Container;
use eProcess\SecurityBundle\Constantes\EtapeTranfert;
use eProcess\SecurityBundle\Constantes\EtapeBon;

class EmailManager {

    private $container;
    protected $mailer;
    protected $templating;
    protected $view;
    protected $parameters;

    public function __construct(\Swift_Mailer $mailer, EngineInterface $templating, Container $container) {

        $this->container = $container;

        $this->parameters = array('titreMail' => 'SoftEBS', 'noteMail' => 'note', 'listeFacture' => array());

        $this->mailer = $mailer;

        $this->templating = $templating;

        $this->view = 'eProcessTransfertBundle:Email:mailFacture.html.twig';
    }

    public function sendUsersMail($listUsers, $listEntity = array(), $etape = '', $entityClass = 'Facture') {

        // preparation des paramètres de la vue
        if (count($listEntity) > 0) {
            $this->parameters['listeFacture'] = $listEntity;
        }
        if ($entityClass == 'Facture') {
            if ($etape) {
                if ($etape == EtapeTranfert::RECEPT_EBS) {
                    $this->parameters['titreMail'] .= ' : Nouvelles factures pour traitement';
                } else if ($etape == EtapeTranfert::EBS_OPS) {
                    $this->parameters['titreMail'] = ' : Nouvelles factures pour Paiement';
                } else if ($etape == EtapeTranfert::OPS_EBS) {
                    $this->parameters['titreMail'] = ' : Rejet des factures ';
                }
            }
        } elseif ($entityClass == 'BonCommande') {
            $this->view = 'eProcessTransfertBundle:Email:mailBon.html.twig';
            if ($etape) {
                if ($etape == EtapeBon::BON_SOUMIS_AUTORISATION) {
                    $this->parameters['titreMail'] .= ' : Nouveaux bons de commande à approuver';
                }
            }
        }



        $from = $this->container->getParameter('mailer_user');

        // recuperation des nom d'utilisateurs de test
        $usernamesOfTest = $this->container->getParameter('users_for_test');

        //preparation des contacts des récepteurs de mail
        foreach ($listUsers as $user) {

            $username = $user->getUsername();

            // on envoi pas de mail aux utilisateurs créés pour les test
            if (!in_array($username, $usernamesOfTest)) {

                //$to = 'tdarko@ecobank.com';
                $to = trim($username) . '@ecobank.com';

                //envoi de mail
                $body = $this->templating->render($this->view, $this->parameters);

                $this->sendMessage($from, $to, $this->parameters['titreMail'], $body);
            }
        }
    }

    public function sendMessage($from, $to, $subject, $body) {
        $mail = \Swift_Message::newInstance();

        $mail
                ->setFrom($from, 'Soft - EBS')
                ->setTo($to)
                ->setSubject($subject)
                ->setBody($body)
                ->setContentType('text/html');

        $this->mailer->send($mail);
    }

    public function sendMessageWithAttach($from, $to, $subject, $body, $path) {
        $mail = \Swift_Message::newInstance();

        $attachment = Swift_Attachment::fromPath($path);

        // Attach it to the message     

        $mail
                ->setFrom($from)
                ->setTo($to)
                ->setSubject($subject)
                ->setBody($body)
                ->setContentType('text/html')
                ->setEncoder('charset', 'utf-8')
                ->attach($attachment);

        $this->mailer->send($mail);
    }

    /**
     * getUserData :  cette methode permet de recupere les données de la session
     * 
     * @return array
     */
    public function sendMailToDevelopper($message = 'RAS', $subject = 'SEA - ERROR') {

        $from = $this->container->getParameter('mailer_user');
        $to = $this->container->getParameter('mailer_user');
//        var_dump($message);exit;
        $this->sendMessage($from, $to, $subject, $message);
    }

}
