<?php

namespace eProcess\EntityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class AchatTraitement1Type extends AbstractType {

  
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
                ->add('ficDemande', new FichierType(), array('required' => false))
                ->add('ficProformat', new FichierType(), array('required' => false))
                ->add('ficApprobation', new FichierType(), array('required' => false))
                ->add('ficLivraison', new FichierType(), array('required' => false))
                ->add('ficFacture', new FichierType(), array('mapped' => false, 'required' => false))
                ->add('montant', 'text', array('attr' => array('required' => TRUE, 'placeholder' => 'saisir le montant', 'class' => ' numeric')))
                ->add('devise', 'currency', array('preferred_choices' => array('EUR', 'USD', 'XOF'), 'attr' => array('required' => TRUE, 'class' => ' chosen-select-element ')))

                ;
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
        return 'eprocess_entitybundle_achat_traitement';
    }

}
