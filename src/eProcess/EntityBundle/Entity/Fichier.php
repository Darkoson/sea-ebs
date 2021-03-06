<?php

namespace eProcess\EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use eProcess\SecurityBundle\Constantes\TypeFichier;

/**
 * Fichier
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="eProcess\EntityBundle\Entity\FichierTable")
 * @ORM\HasLifecycleCallbacks
 * 
 */
class Fichier {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var UploadedFile $fichier
     * @Assert\File(
     *     maxSize = "5M",
     * )
     * 
     */
    private $fichier;
    
     /**
     * @var integer
     *
     * @ORM\Column(name="isActif", type="integer", nullable= true)
     */
    private $isActif = 0;

    
    
    // On ajoute cet attribut pour y stocker le nom du fichier temporairement
    private $tempFilename;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=true)
     */
    private $type;

    /**
     * @var string $url
     * *
      @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var string $url
     * *
      @ORM\Column(name="extention", type="string", length=5, nullable=true)
     */
    private $extention;

    /**
     * Constructor
     */
    public function __construct($type = TypeFichier::Autres) {

        $this->type = $type;

        // par défaut, l'extension est pdf
        $this->extention = 'pdf';
    }

    // On modifie le setter de File, pour prendre en compte l'upload d'un fichier lorsqu'il en existe déjà un autre
    public function setFichier($file) {


        $this->fichier = $file;

//        var_dump($this->fichier) ;exit;
        if (null !== $this->url) {
            // On sauvegarde l'extension du fichier pour le supprimerplus tard
            $this->tempFilename = $this->url;
            // On réinitialise les valeurs des attributs url 
            $this->url = null;
        }
    }

    /**
     * preUpload : fonction qui attribue un nom au fichier qui est chargé
     *              et initialise certains champs de l'entité fichier.
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload() {
        // Si jamais il n'y a pas de fichier (champ facultatif) alors on ne fait rien
        if (null === $this->fichier) {
            return;
        }

        // on commence le nom du fichier en fonction de son type
        $this->generateFileName() ;
    }

    
    /**
     * generateFileName  : fonction de creation du nom de fichier et d'hydratation 
     *  de l'entite 
     * 
     */
    public function generateFileName() {
        $this->url = TypeFichier::readInitialeFichier($this->type);
        $date = new \DateTime();
        $this->url .= $date->format('YmdHis');
        $this->url .= md5(uniqid(mt_rand(), true));
        

       
        if (null !== $this->fichier) {
            // si nous somme dans le cas d'un upload, alors on met à jour les informations 
            //    sur l'extension
            $this->extention = $this->fichier->guessExtension();
        }
        
        $this->url .= '.' . $this->extention;
        
        return $this->url;
    }

    /**
     * upload : fonction qui deplace le fichier charger dans le repertoire correspondant
     *          tout en s'assurant que l'ancien fichier est supprimé au cas où il y en avait.
     *          Si aucun fichier n'est chargé alors on ne fait rien!
     * 
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload() {

        if (null === $this->fichier) {
            return;
        }

        // on verifie si l'ancien fichier que nous avons sauvergadé existe, 
        // si oui alors on le supprime avant de charger le nouveau 
        if (null !== $this->tempFilename) {
            $oldFic = $this->getUploadRootDir() . $this->tempFilename;

            if (file_exists($oldFic)) {
                unlink($oldFic);
            }
        }

        $this->fichier->move($this->getUploadRootDir(), $this->url);
    }

    /** 
     * preRemoveUpload : fonction qui met à jour le nom du fichier temporaire afin  de supprimer ce
     *                   ce fichier temporaire
     * @ORM\PreRemove()
     * 
     */
    public function preRemoveUpload() {
        // On sauvegarde temporairement le nom du fichier
        $this->tempFilename = $this->getUploadRootDir() . $this->url;
    }

    /**
     * removeUpload  : fonction qui supprime un fichier
     *                 le nom complet du fichier à supprimé doit être placé dans : tempFilename
     * @ORM\PostRemove()
     */
    public function removeUpload() {
        //  on utilise notre nom sauvegardé
        if (file_exists($this->tempFilename)) {
            // On supprime le fichier
            unlink($this->tempFilename);
        }
    }

    /**
     * getAbsolutePath  : fonction qui retourne le chemin absolue d'un fichier
     * 
     * @return string
     */
    public function getAbsolutePath() {
        return null === $this->url ? null : $this->getUploadRootDir() . '' . $this->url;
    }

    /**
     * getWebPath  :  fonction qui retourne le chemin relatif au web d'un fichier
     * 
     * @return type
     */
    public function getWebPath() {
        return null === $this->url ? null : $this->getUploadDir() . '' . $this->url;
    }

    /** 
     * getUploadRootDir  : fonction qui retourne le chemin absolue  du repertoire d'un fichier
     * 
     * @return string
     */
    public function getUploadRootDir() {
        // On retourne le chemin relatif vers le fichier pour notre codePHP
        $chemin = __DIR__ . '/../../../../web/' . $this->getUploadDir();
        if (!is_dir($chemin)) {
            mkdir($chemin, 0777, true);
        }

        return $chemin;
    }

    /**
     *  getUploadDir   : fonction qui retourne le chemin relatif au web  du repertoire d'un fichier
     * 
     * @return string
     */
    public function getUploadDir() {
        // On retourne le chemin relatif vers le fichier en fonction de son type
        return 'uploads/' . TypeFichier::readRepertoireFichier($this->type) . '/';
    }

    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return Fichier
     */
    public function setType($type) {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Fichier
     */
    public function setUrl($url) {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    public function getFichier() {
        return $this->fichier;
    }

    public function getTempFilename() {
        return $this->tempFilename;
    }

    public function setTempFilename($tempFilename) {
        $this->tempFilename = $tempFilename;
    }


    /**
     * Set extention
     *
     * @param string $extention
     *
     * @return Fichier
     */
    public function setExtention($extention)
    {
        $this->extention = $extention;

        return $this;
    }

    /**
     * Get extention
     *
     * @return string
     */
    public function getExtention()
    {
        $extension = $this->extention ; 
        if($this->fichier){
           $extension =  $this->fichier->guessExtension();
        }
        return $extension ;
    }

    /**
     * Set isActif
     *
     * @param integer $isActif
     *
     * @return Fichier
     */
    public function setIsActif($isActif)
    {
        $this->isActif = $isActif;

        return $this;
    }

    /**
     * Get isActif
     *
     * @return integer
     */
    public function getIsActif()
    {
        return $this->isActif;
    }
    
    
    public function isPdfFile() {
        return in_array(strtolower($this->extention) , array('pdf'));
    }
    public function isExcelFile() {
        return in_array(strtolower($this->extention), array('csv','xls','xlsx'));
    }
    public function isImageFile() {
        return in_array(strtolower($this->extention), array('jpeg','png','ico','jpg'));
    }
    
    
    public function __toString() {
        return $this->getAbsolutePath();
    }
}
