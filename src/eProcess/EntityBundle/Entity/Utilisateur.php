<?php

namespace eProcess\EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * Utilisateur
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="eProcess\EntityBundle\Entity\UtilisateurTable")
 */
class Utilisateur  implements UserInterface, EquatableInterface
{
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=50)
     */
    private $username;
    
    
    
    private $password;
    private $providerKey;
    private $salt;
    
    private $roles;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=50)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=100)
     */
    private $prenom;

    
    /**
     * @var boolean
     *
     * @ORM\Column(name="isActif", type="boolean")
     */
    private $isActif;


    /**
     * @var integer
     *
     * @ORM\Column(name="creerPar", type="integer")
     */
    private $creerPar;

    /**
     * @var integer
     *
     * @ORM\Column(name="activerPar", type="integer", nullable=true)
     */
    private $activerPar;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreation", type="datetime")
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateActivation", type="datetime", nullable=true)
     */
    private $dateActivation;

    /**
    * @ORM\ManyToOne(targetEntity="Departement", inversedBy="utilisateurs")
    * @ORM\JoinColumn(name="departement_id", referencedColumnName="id", nullable=true)
    */
    private $departement;

    /**
    * @ORM\ManyToOne(targetEntity="Profile", inversedBy="utilisateurs")
    * @ORM\JoinColumn(name="profile_id", referencedColumnName="id", nullable=true)
    */
    private $profile;
    
    /**
    * @ORM\OneToMany(targetEntity="Transfert", mappedBy="envoyeur")
    */
    private $expeditions;
    
    
    /**
    * @ORM\OneToMany(targetEntity="Transfert", mappedBy="recepteur")
    */
    private $receptions;
    
     /**
    * @ORM\OneToMany(targetEntity="ApprobationBon", mappedBy="utilisateur", cascade={"persist"})
    */
    private $approbationBons; 

    /**
    * Constructor
    */
    public function __construct( $username='', $password='', $providerKey='', $salt='', $roles=array())
    {
        $this->username = $username;
        $this->password = $password;
        $this->isActif = false ;
        $this->dateCreation = new \DateTime() ;
        $this->providerKey = $providerKey ;
        $this->salt = $salt;
        $this->roles = $roles;
    }
    

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return Utilisateur
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Utilisateur
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Utilisateur
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set isActif
     *
     * @param boolean $isActif
     *
     * @return Utilisateur
     */
    public function setIsActif($isActif)
    {
        $this->isActif = $isActif;

        return $this;
    }

    /**
     * Get isActif
     *
     * @return boolean
     */
    public function getIsActif()
    {
        return $this->isActif;
    }

    /**
     * Set creerPar
     *
     * @param integer $creerPar
     *
     * @return Utilisateur
     */
    public function setCreerPar($creerPar)
    {
        $this->creerPar = $creerPar;

        return $this;
    }

    /**
     * Get creerPar
     *
     * @return integer
     */
    public function getCreerPar()
    {
        return $this->creerPar;
    }

    /**
     * Set activerPar
     *
     * @param integer $activerPar
     *
     * @return Utilisateur
     */
    public function setActiverPar($activerPar)
    {
        $this->activerPar = $activerPar;

        return $this;
    }

    /**
     * Get activerPar
     *
     * @return integer
     */
    public function getActiverPar()
    {
        return $this->activerPar;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Utilisateur
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set dateActivation
     *
     * @param \DateTime $dateActivation
     *
     * @return Utilisateur
     */
    public function setDateActivation($dateActivation)
    {
        $this->dateActivation = $dateActivation;

        return $this;
    }

    /**
     * Get dateActivation
     *
     * @return \DateTime
     */
    public function getDateActivation()
    {
        return $this->dateActivation;
    }

    /**
     * Set departement
     *
     * @param \eProcess\EntityBundle\Entity\Departement $departement
     *
     * @return Utilisateur
     */
    public function setDepartement(\eProcess\EntityBundle\Entity\Departement $departement = null)
    {
        $this->departement = $departement;

        return $this;
    }

    /**
     * Get departement
     *
     * @return \eProcess\EntityBundle\Entity\Departement
     */
    public function getDepartement()
    {
        return $this->departement;
    }

    /**
     * Set profile
     *
     * @param \eProcess\EntityBundle\Entity\Profile $profile
     *
     * @return Utilisateur
     */
    public function setProfile(\eProcess\EntityBundle\Entity\Profile $profile = null)
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * Get profile
     *
     * @return \eProcess\EntityBundle\Entity\Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Add expedition
     *
     * @param \eProcess\EntityBundle\Entity\Transfert $expedition
     *
     * @return Utilisateur
     */
    public function addExpedition(\eProcess\EntityBundle\Entity\Transfert $expedition)
    {
        $this->expeditions[] = $expedition;

        return $this;
    }

    /**
     * Remove expedition
     *
     * @param \eProcess\EntityBundle\Entity\Transfert $expedition
     */
    public function removeExpedition(\eProcess\EntityBundle\Entity\Transfert $expedition)
    {
        $this->expeditions->removeElement($expedition);
    }

    /**
     * Get expeditions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getExpeditions()
    {
        return $this->expeditions;
    }

    /**
     * Add reception
     *
     * @param \eProcess\EntityBundle\Entity\Transfert $reception
     *
     * @return Utilisateur
     */
    public function addReception(\eProcess\EntityBundle\Entity\Transfert $reception)
    {
        $this->receptions[] = $reception;

        return $this;
    }

    /**
     * Remove reception
     *
     * @param \eProcess\EntityBundle\Entity\Transfert $reception
     */
    public function removeReception(\eProcess\EntityBundle\Entity\Transfert $reception)
    {
        $this->receptions->removeElement($reception);
    }

    /**
     * Get receptions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReceptions()
    {
        return $this->receptions;
    }

    public function eraseCredentials() {
        
    }

   
    public function getRoles()
    {
        return $this->roles = array('ROLE_USER');
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    function getProviderKey() {
        return $this->providerKey;
    }

    function setProviderKey($providerKey) {
        $this->providerKey = $providerKey;
    }

        
    
    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof WebserviceUser) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->salt !== $user->getSalt()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }
    
    
    public function __toString() {
        return $this->nom.' '.$this->prenom;
    }

    /**
     * Add approbationBon
     *
     * @param \eProcess\EntityBundle\Entity\ApprobationBon $approbationBon
     *
     * @return Utilisateur
     */
    public function addApprobationBon(\eProcess\EntityBundle\Entity\ApprobationBon $approbationBon)
    {
        $this->approbationBons[] = $approbationBon;

        return $this;
    }

    /**
     * Remove approbationBon
     *
     * @param \eProcess\EntityBundle\Entity\ApprobationBon $approbationBon
     */
    public function removeApprobationBon(\eProcess\EntityBundle\Entity\ApprobationBon $approbationBon)
    {
        $this->approbationBons->removeElement($approbationBon);
    }

    /**
     * Get approbationBons
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getApprobationBons()
    {
        return $this->approbationBons;
    }
}
