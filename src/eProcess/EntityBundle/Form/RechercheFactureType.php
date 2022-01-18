<?php

namespace eProcess\EntityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use eProcess\SecurityBundle\Constantes\TypeDate;

class RechercheFactureType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('devise', 'currency', array(
                    'preferred_choices' => array('EUR', 'USD', 'XOF'),
                    'attr' => array('class' => 'form-control chosen-select-element '),
                    'required' => false,
                    'placeholder' => 'aucune devise',
                    'empty_data' => null
                        ) )
                ->add('typeDate', 'choice', array(
                    'choices'=>  TypeDate::getTypeDateFacture(),
                    'attr' => array('required' => TRUE, 'class' => 'form-control chosen-select-element ')))
                 ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => null
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'form_recherche_facture';
    }

}
