<?php

namespace eProcess\EntityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityTable;

class AchatBonModifType extends AbstractType {

   
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                
                ->add('ficDemande', new FichierType(), array('required' => false))
                ->add('ficProformat', new FichierType(), array('required' => false))
                ->add('ficApprobationBudget', new FichierType(), array('required' => false))
                ->add('ficApprobationHier', new FichierType(), array('required' => false))
                ->add('devise', 'currency', array('preferred_choices' => array('EUR', 'USD', 'XOF'),'attr' => array( 'class' => 'form-control chosen-select-element dynamic')))

                ->add('fournisseur', 'entity', array('attr' => array('required' => true, 'class' => 'form-control chosen-select-element dynamic '),
                    'class' => 'eProcessEntityBundle:Fournisseur',
                    'query_builder' => function (EntityTable $repository) {
                        return $repository->createQueryBuilder('f')->where('f.isActif = 1');
                    }  )) ;
                
//                
//                ->add('ficDemande', new FichierType(), array('required' => false))
//                ->add('ficProformat', new FichierType(), array('required' => false))
//                ->add('ficApprobation', new FichierType(), array('required' => false))
//                ->add('objet', 'text', array('attr' => array('required' => TRUE, 'placeholder' => 'Re-saisir l\'objet de la demande', 'autocomplete' => 'on', 'class' => 'form-control dynamic')))
////                ->add('montant', 'text', array('attr' => array('required' => TRUE,'readonly' => TRUE, 'placeholder' => 'montant pas encore calculé', 'class' => 'form-control numeric dynamic')))
//                ->add('devise', 'currency', array('preferred_choices' => array('EUR', 'USD', 'XOF'),'attr' => array('required' => TRUE, 'class' => 'form-control chosen-select-element ')))
////                ->add('departement', 'entity', array('attr' => array('required' => false, 'class' => 'form-control chosen-select-element '),
////                            'class' => 'eProcessEntityBundle:Departement',
////                            'query_builder' => function (EntityTable $repository) {
////                        return $repository->createQueryBuilder('d');
////                    }  ))
//                ->add('fournisseur', 'entity', array('attr' => array('required' => true, 'class' => 'form-control chosen-select-element '),
//                    'class' => 'eProcessEntityBundle:Fournisseur',
//                    'query_builder' => function (EntityTable $repository) {
//                        return $repository->createQueryBuilder('f')->where('f.isActif=1');
//                    }  )) ;
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
        return 'eprocess_entitybundle_achat_bon';
    }

}
