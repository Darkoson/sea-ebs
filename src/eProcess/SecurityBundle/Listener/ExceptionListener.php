<?php

namespace eProcess\SecurityBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use eProcess\SecurityBundle\Constantes\SessionKeys;

/**
 * Description of ExceptionListener
 *
 * @author TDARKO
 */
class ExceptionListener {

    protected $templating;
    protected $kernel;

    public function __construct(EngineInterface $templating, $kernel) {
        
        $this->templating = $templating;

        $this->kernel = $kernel; 
    }

    public function onKernelException(GetResponseForExceptionEvent $event) {    

        // provide the better way to display a enhanced error page only in prod environment, if you want

        if ('prod' == $this->kernel->getEnvironment() ) {
            // exception object
            $exception = $event->getException();

            // we retrieve informations of the user and the emailManager
            $mailManager = $this->kernel->getContainer()->get('email_manager') ;
            $session = $this->kernel->getContainer()->get('session') ;
            
            // we send an email de notify the developper
            $userData = $session->get(SessionKeys::USER_DATA);
            $message = "User = ". $userData[SessionKeys::USER_USERNAME]. "     >=====>  " ;
            $message .= "Time = ". date('d-m-Y H:i:s') . "     >=====>  ";
            $message .= "Message =  ".$exception->getMessage() ; 
            
            $mailManager->sendMailToDevelopper($message);
           
            // new Response object
            $response = new Response();

            // set response content
            $response->setContent(
                    $this->templating->render( 'eProcessSecurityBundle:Exception:exception.html.twig', array('exception' => $exception) )
            );


            // HttpExceptionInterface is a special type of exception
            // that holds status code and header details

            if ($exception instanceof HttpExceptionInterface) {

                $response->setStatusCode($exception->getStatusCode());

                $response->headers->replace($exception->getHeaders());
            } else {

                $response->setStatusCode(500);
            }


            // set the new $response object to the $event

            $event->setResponse($response);
        }
    }

}
