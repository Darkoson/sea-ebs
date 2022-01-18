<?php

namespace eProcess\EntityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FournisseurType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom','text',array('attr' => array('required' => TRUE , 'placeholder'=>'Nom','class'=>'form-control ' )))
            ->add('pays','country',  array('attr' => array('required' => TRUE, 'class'=>'form-control chosen-select-element ' )))
            ->add('languePrefere','text',array('required' => false ,'attr' => array( 'placeholder'=>'Langue préférée','class'=>'form-control ' )))
            ->add('typeOrganisation','text',array('attr' => array('required' => false , 'placeholder'=>'Type d\'organisation','class'=>'form-control ' )))
            ->add('telephone','text',array('required' => false ,'attr' => array( 'placeholder'=>'Numéro de téléphone','class'=>'form-control ' )))
            ->add('representant','text',array('required' => false ,'attr' => array( 'placeholder'=>'Représentant de l\'adresse ','class'=>'form-control ' )))
            ->add('boitePostale','text',array('required' => false ,'attr' => array( 'placeholder'=>'Boîte Postale ','class'=>'form-control ' )))
            ->add('adresse','text',array('required' => false ,'attr' => array( 'placeholder'=>'Adresse du Fournisseur','class'=>'form-control ' )))
            ->add('hasContrat','choice', array( 'choices' => array('NO' => 'NO', 'YES' => 'YES'),'attr' => array('required' => TRUE, 'class' => 'form-control chosen-select-element ')) )
             ->add('devisePrefere', 'currency', array('preferred_choices' => array('EUR', 'USD', 'XOF'),'attr' => array('required' => TRUE, 'class' => 'form-control chosen-select-element ')))
             ->add('modePaiement', 'text', array('required' => false ,'attr' => array( 'placeholder'=>'Mode de paiement du fournisseur','class'=>'form-control ' )))
             ->add('compteBancaire', 'text', array('required' => false ,'attr' => array( 'placeholder'=>'compte Bancaire du fournisseur','class'=>'form-control ' )))
             ->add('agence', 'text', array('required' => false ,'attr' => array( 'placeholder'=>'Agence du fournisseur','class'=>'form-control ' )))
             ->add('banque', 'text', array('required' => false ,'attr' => array( 'placeholder'=>'Banque du fournisseur','class'=>'form-control ' )))
            ->add('email','email', array('required' => false ,'attr' => array( 'placeholder'=>'email','class'=>'form-control ' )))
            ->add('fichier',  new FichierType(), array('required' => false))
            
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'eProcess\EntityBundle\Entity\Fournisseur'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'eprocess_entitybundle_fournisseur';
    }
}
