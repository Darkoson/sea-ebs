<?php

namespace eProcess\EntityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityTable;


class AchatReceptionType extends AbstractType {

    
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
       
        $builder
                ->add('objet', 'text', array('attr' => array('required' => TRUE, 'autocomplete' => TRUE,  'placeholder' => 'Saisir l\'objet de la demande', 'class' => 'form-control')))
                ->add('montant', 'text', array('attr' => array('required' => TRUE, 'placeholder' => 'saisir le montant', 'class' => 'form-control numeric')))
                ->add('devise', 'currency', array('preferred_choices' => array('EUR', 'USD', 'XOF'),'attr' => array('required' => TRUE, 'class' => 'form-control chosen-select-element ')))
                ->add('fournisseur', 'entity', array('attr' => array('required' => true, 'class' => 'form-control chosen-select-element '),
                    'class' => 'eProcessEntityBundle:Fournisseur',
                    'query_builder' => function (EntityTable $repository) {
                        return $repository->createQueryBuilder('f')->where('f.isActif=1');
                    }  )) ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'eProcess\EntityBundle\Entity\Achat'
        ));
    }

    
    /**
     * @return string
     */
    public function getName() {
        return 'eprocess_entitybundle_achat_reception';
    }

}
