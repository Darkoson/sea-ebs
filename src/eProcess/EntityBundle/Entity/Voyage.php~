<?php

namespace eProcess\EntityBundle\Entity;

use eProcess\SecurityBundle\Constantes\Status;
use Doctrine\ORM\Mapping as ORM;


/**
 * Voyage
 * @ORM\Table()
 * @ORM\Entity()
 */

class Voyage {

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
     * @ORM\Column(name="dateDepart", type="datetime", nullable=true)
     */
    private $dateDepart;

    /**
     * @var string
     *
     * @ORM\Column(name="villeDepart", type="string", length=25)
     */
    private $villeDepart;

    /**
     * @var string
     *
     * @ORM\Column(name="dateArrive", type="datetime", nullable=true)
     */
    private $dateArrive;

    /**
     * @var string
     *
     * @ORM\Column(name="villeArrive", type="string", length=25)
     */
    private $villeArrive;

    /**
     * @var string
     *
     * @ORM\Column(name="vol", type="string", length=30)
     */
    private $vol;
    
    /**
    * @ORM\OneToOne(targetEntity="AchatDepartement", inversedBy="voyage", cascade={"persist","remove"})
    */
    private $achatDepartement;
    


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
     * Set dateDepart
     *
     * @param \DateTime $dateDepart
     *
     * @return Voyage
     */
    public function setDateDepart($dateDepart)
    {
        $this->dateDepart = $dateDepart;

        return $this;
    }

    /**
     * Get dateDepart
     *
     * @return \DateTime
     */
    public function getDateDepart()
    {
        return $this->dateDepart;
    }

    /**
     * Set villeDepart
     *
     * @param string $villeDepart
     *
     * @return Voyage
     */
    public function setVilleDepart($villeDepart)
    {
        $this->villeDepart = $villeDepart;

        return $this;
    }

    /**
     * Get villeDepart
     *
     * @return string
     */
    public function getVilleDepart()
    {
        return $this->villeDepart;
    }

    /**
     * Set dateArrive
     *
     * @param \DateTime $dateArrive
     *
     * @return Voyage
     */
    public function setDateArrive($dateArrive)
    {
        $this->dateArrive = $dateArrive;

        return $this;
    }

    /**
     * Get dateArrive
     *
     * @return \DateTime
     */
    public function getDateArrive()
    {
        return $this->dateArrive;
    }

    /**
     * Set villeArrive
     *
     * @param string $villeArrive
     *
     * @return Voyage
     */
    public function setVilleArrive($villeArrive)
    {
        $this->villeArrive = $villeArrive;

        return $this;
    }

    /**
     * Get villeArrive
     *
     * @return string
     */
    public function getVilleArrive()
    {
        return $this->villeArrive;
    }

    /**
     * Set vol
     *
     * @param string $vol
     *
     * @return Voyage
     */
    public function setVol($vol)
    {
        $this->vol = $vol;

        return $this;
    }

    /**
     * Get vol
     *
     * @return string
     */
    public function getVol()
    {
        return $this->vol;
    }

    /**
     * Set achatDepartement
     *
     * @param \eProcess\EntityBundle\Entity\AchatDepartement $achatDepartement
     *
     * @return Voyage
     */
    public function setAchatDepartement(\eProcess\EntityBundle\Entity\AchatDepartement $achatDepartement = null)
    {
        $this->achatDepartement = $achatDepartement;

        return $this;
    }

    /**
     * Get achatDepartement
     *
     * @return \eProcess\EntityBundle\Entity\AchatDepartement
     */
    public function getAchatDepartement()
    {
        return $this->achatDepartement;
    }
}
