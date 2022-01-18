<?php

namespace eProcess\EntityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Doctrine\ORM\EntityTable;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FactureType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateArrive','text',array('attr' => array('required' => TRUE , 'placeholder'=>'dd-mm-aaaa','class'=>'form-control datetime' )))
            ->add('referenceFournisseur','text',array('attr' => array('required' => TRUE , 'placeholder'=>'Référence du fournisseur','class'=>'form-control ' )))
            ->add('objet','text',array('attr' => array('required' => TRUE , 'placeholder'=>'Saisir l\'objet de la facture','class'=>'form-control' )))
            ->add('montant','text',array('attr' => array('required' => TRUE , 'placeholder'=>'le montant de la facture','class'=>'form-control numeric' )))
            ->add('devise','currency',array('attr' => array('required' => TRUE , 'class'=>'form-control chosen-select-element ' )))
            ->add('fournisseur','entity',array( 'attr' => array('required' => TRUE, 'class'=>'form-control chosen-select-element ' ),
                'class'=>'eProcessEntityBundle:Fournisseur',
                 'query_builder' => function (EntityTable $repository) { return $repository->createQueryBuilder('f')->where('f.isActif=1') ;}
                ) )
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
        return 'eprocess_entitybundle_facture';
    }
}
