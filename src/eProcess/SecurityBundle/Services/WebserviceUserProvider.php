<?php

namespace eProcess\SecurityBundle\Services;

/**
 * Description of WebserviceUserProvider
 *
 * @author tdarko
 */
use Symfony\Component\Security\Core\User\UserProviderInterface;
use eProcess\SecurityBundle\Services\LdapManager;
use eProcess\EntityBundle\Entity\Utilisateur;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use eProcess\SecurityBundle\Services\DroitManager;

class WebserviceUserProvider implements UserProviderInterface {

    private $ldapManager;
    private $droitManager;

    public function __construct(LdapManager $ldp, DroitManager $dm) {
        $this->ldapManager = $ldp;
        $this->droitManager = $dm;
    }

    public function loadUserByUsername($username) {

        // make a call seach user from database
        $utilisateur = $this->droitManager->getActiveUserByUsername($username);

        // pretend it returns an array on success, false if there is no user

        if ($utilisateur) {
            return $utilisateur;
        }

        throw new UsernameNotFoundException(
        sprintf('Utilisateur non autorisé sur l\'appication')
        );
    }

    public function refreshUser(UserInterface $user) {
        if (!$user instanceof Utilisateur) {
            throw new UnsupportedUserException(
            sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class) {
        return $class === 'eProcess\EntityBundle\Entity\Utilisateur';
    }

    /**
     * authenticate : fonction d'authentification de l'utilisateur depuis l'active directory
     * 
     * @param type $username
     * @param type $password
     * @param type $default la réponse par défaut : il est utilisé en cas de test, il faut le mette à false en cas de production
     * @return type
     */
    public function authenticate($username, $password, $default = false) {
        
        // $default: est le resultat par défaut à retourner en cas d'échec de connexion 
        // $default = true : pour forcer l'authentification si nous n'avons pas acces à l'active directory.
        
        // ce traitement permet de remplacer les terminaison _ops _ebs _recept dans les noms d'utilisateur
        $name = str_replace('_ebs', '', str_replace('_ops', '', str_replace('_recept', '',  str_replace('_admin', '', $username))));
        
//        var_dump($name);exit;
        $login = trim($name).'@ecobank.group' ; 

        $result = $this->ldapManager->authenticate($login, $password,$default);

        return $result;
    }
    
    
    /**
     * getUsernamesForTest : recuperaion des noms d'utilisteurs de test
     * 
     * @return type
     */
    public function getUsernamesForTest() {
        $container = $this->ldapManager->getContainer() ;
        return $container->getParameter('users_for_test');
    }
    /**
     * getPasswordForTest : recuperaion des mots de pass des utilisteurs de test
     * 
     * @return type
     */
    public function getPasswordForTest() {
        $container = $this->ldapManager->getContainer() ;
        return $container->getParameter('passwd_for_test');
    }

}
