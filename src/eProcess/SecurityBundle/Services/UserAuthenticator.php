<?php

namespace eProcess\SecurityBundle\Services;

use Symfony\Component\Security\Core\Authentication\SimpleFormAuthenticatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use eProcess\SecurityBundle\Services\DroitManager;

/**
 * Description of UserAuthenticator
 *
 * @author tdarko
 */
class UserAuthenticator implements SimpleFormAuthenticatorInterface {

    private $encoder;
    private $droitManager;

    public function __construct(UserPasswordEncoderInterface $encoder, DroitManager $dm) {
        $this->encoder = $encoder;
        $this->droitManager = $dm;
    }

    public function authenticateToken(\Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token, \Symfony\Component\Security\Core\User\UserProviderInterface $userProvider, $providerKey) {
        $username = $token->getUsername();
        $password = $token->getCredentials();
        $exist= false ; // initialization of the response from active directory

        //we are getting usernamses for test
        $userTestCollection = $userProvider->getUsernamesForTest() ;
//        $truePassword = $userProvider->getPasswordForTest() ;

        //we escape users of test from authentification of Active Directory
        if (in_array($username, $userTestCollection) ) {
            $exist = true ;
            
        } 
        else {
            // we are logging the user from active directory based on the given credencials
            $exist = $userProvider->authenticate($username, $password);
        }

        if (!$exist) {
            throw new AuthenticationException('Nom d\'utilisateur ou mot de passe incorrecte');
        }

        // we are retrieving the user's informations from DB
        $userDB = $this->droitManager->getActiveUserByUsername($username);

        // if the user exists in the data base, we are going to load its infromations in from the data base
        if ($userDB) {
            // we are loading
            $sessionInformations = $this->droitManager->loadUserInformations($userDB);

            // we are putting in session
            $this->droitManager->setUserData($sessionInformations);

            // we return UsernamePasswordToken Object for symfony to continue it authentication
            $user = new UsernamePasswordToken($userDB, $userDB->getPassword(), $providerKey, $userDB->getRoles());
//            var_dump($user);exit;
            return $user;
        }

        // if there is no user from Active directory ou data base, we have an AuthenticationException
        throw new AuthenticationException('Utilisateur non autorisé sur l\'application');
    }

    public function createToken(\Symfony\Component\HttpFoundation\Request $request, $username, $password, $providerKey) {
        return new UsernamePasswordToken($username, $password, $providerKey);
    }

    public function supportsToken(\Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token, $providerKey) {
        return $token instanceof UsernamePasswordToken && $token->getProviderKey() === $providerKey;
    }

//put your code here
}
