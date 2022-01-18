<?php

namespace eProcess\EntityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AchatTraitement2Type extends AbstractType {

 
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
//        var_dump() ;exit;

        $builder
                ->add('ficDemande', new FichierType(), array('required' => false))
                ->add('ficProformat', new FichierType(), array('required' => false))
                ->add('ficApprobation', new FichierType(), array('required' => false))
                ->add('ficLivraison', new FichierType(), array('required' => false))
                ->add('ficFacture', new FichierType(), array('mapped' => false, 'required' => false))
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
