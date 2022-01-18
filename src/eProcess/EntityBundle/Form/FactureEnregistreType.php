<?php

namespace eProcess\EntityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Doctrine\ORM\EntityTable;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FactureEnregistreType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('referenceFournisseur','text',array('attr' => array('required' => TRUE , 'placeholder'=>'Référence du fournisseur','class'=>'form-control ' )))
            ->add('objet','text',array('mapped'=>false ,'attr' => array('required' => TRUE , 'placeholder'=>'Saisir l\'objet de la facture','class'=>'form-control' )))
            ->add('montant','text',array('mapped'=>false ,'attr' => array('required' => TRUE , 'placeholder'=>'le montant de la facture','class'=>'form-control numeric' )))
//            ->add('bonCommande','text',array('attr' => array('required' => false , 'placeholder'=>'la référence du bon de commande','class'=>'form-control' )))
            ->add('devise','currency',array('mapped'=>false ,'attr' => array('required' => TRUE , 'class'=>'form-control chosen-select-element ' )))

        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'eProcess\EntityBundle\Entity\Facture'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'eprocess_entitybundle_facture_enreg';
    }
}
