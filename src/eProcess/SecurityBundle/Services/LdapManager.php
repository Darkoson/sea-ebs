<?php

/**
 * Description of AuthManager
 *
 * @author tdarko
 */

namespace eProcess\SecurityBundle\Services;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use \Symfony\Component\DependencyInjection\Container;

;

/**
 * LdapManager : service charger de recuperer des inforationns dans l'active directory pour four
 */
class LdapManager {

    private $em;
    private $container;
    private $entityBundle;
    private $ldapHost;
    private $ldapPort;
    private $ldapPath;
    private $ldapUser;
    private $ldapPassword;

    public function __construct(Container $container) {

        $this->container = $container;
        $this->em = $this->container->get('doctrine.orm.default_entity_manager');
        $this->entityBundle = 'eProcessEntityBundle:';
        $this->ldapHost = $this->container->getParameter('ldap_host');
        $this->ldapPort = $this->container->getParameter('ldap_port');
        $this->ldapPath = $this->container->getParameter('ldap_path');
        $this->ldapUser = $this->container->getParameter('ldap_user');
        $this->ldapPassword = $this->container->getParameter('ldap_password');

        $this->entityBundle = 'eProcessEntityBundle:';
    }

    /**
     * getConnexion : fonction de recuperation d'une connexion au serveur LDAP
     */
    public function getConnexionID() {
        try {
            return ldap_connect($this->ldapHost, $this->ldapPort);
        } catch (Exception $exc) {
            throw new UsernameNotFoundException('Impossible de se connecter au serveur Acive Directory !');
        }
    }

    /**
     * authenticate : authentification des utilisateurs dans l'active directory
     * 
     * @param type $username
     * @param type $password
     * @return type
     * @throws Exception 
     */
    public function authenticate($username, $password, $result = false) {

        $idConnexion = $this->getConnexionID();
        // rupture de la connexion
        try {
            $result = ldap_bind($idConnexion, $username, $password);
            ldap_unbind($idConnexion);
        } catch (Exception $exc) {
            $this->get('session')->getFlashBag()->add('ko',"nom d'utlisateur ou mot de asse incorrect");
            throw new UsernameNotFoundException('Impossible de se connecter l\'annuaire !');
        } finally {
            return $result;
        }
    }

    /**
     * getADUserByUsername : fonction de recuperation des information de l'utilisateur dans l'annuaire
     * 
     *      à partir du nom d'utilisateur
     * 
     */
    public function getADUserByUsername($username) {

        $result = null;
        $ds = $this->getConnexionID();

        $login = $username.'@ecobank.group' ;
        
        ldap_bind($ds, $this->ldapUser, $this->ldapPassword);

        $r = ldap_search($ds, $this->ldapPath, 'sAMAccountName=' . $login);

        if ($r) {
            $result = ldap_get_entries($ds, $r);
        }
        
       var_dump($result);exit; 
        ldap_unbind($ds);

        return $result;
    }

   

    /**
     * getDBUserByUsername:fonction de recuperation de l'utilisteur de la base de données à partir
     * 
     *      du nom d'utilisateur
     * 
     * @param type $username
     * 
     * @return Utilisateur
     */
    public function getDBUserByUsername($username) {
        $utiisateur = $this->em->getTable(entityBundle . 'Utilisateur')
                ->findByUsername($username);

        return $utiisateur;
    }

    
    public function getContainer() {
        return $this->container;
    }
}

?>
