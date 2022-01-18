<?php

namespace eProcess\EntityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityTable;
use eProcess\SecurityBundle\Constantes\TypeFichier ;

class AchatType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
//                ->add('fic'.TypeFichier::Demande, 'file', array('label' => TypeFichier::readFichier(TypeFichier::Demande)))
//                ->add('fic'.TypeFichier::Proformat, 'file', array('label' => TypeFichier::readFichier(TypeFichier::Proformat)))
//                ->add('fic'.TypeFichier::Approbation, 'file', array('label' => TypeFichier::readFichier(TypeFichier::Approbation)))
                ->add('ficDemande', new FichierType())
//                ->add('ficDemande', 'file', array('label' => 'Demande (PDF)'))
                ->add('ficProformat', new FichierType())
//                ->add('ficProformat', 'file', array('label' => 'Proformat (PDF )'))
                ->add('ficApprobation', new FichierType())
//                ->add('ficApprobation', 'file', array('label' => 'Approbation (PDF )'))
                ->add('objet', 'text', array('attr' => array('required' => TRUE, 'placeholder' => 'Saisir l\'objet de la demande', 'class' => 'form-control')))
                ->add('montant', 'text', array('attr' => array('required' => TRUE, 'placeholder' => 'saisir le montant', 'class' => 'form-control numeric')))
                ->add('devise', 'currency', array('preferred_choices' => array('EUR', 'USD', 'XOF'),'attr' => array('required' => TRUE, 'class' => 'form-control chosen-select-element ')))
                ->add('bonCommande', 'entity', array('attr' => array('required' => false, 'class' => 'form-control chosen-select-element '),
                            'class' => 'eProcessEntityBundle:BonCommande',
                            'empty_value' => 'pas de bon',
                            'query_builder' => function (EntityTable $repository) {
                        return $repository->createQueryBuilder('b')
                                ->innerJoin('b.achat', 'a')
                                ->where('a.facture is null') ;
                    }  )) 
                ->add('departement', 'entity', array('attr' => array('required' => false, 'class' => 'form-control chosen-select-element '),
                            'class' => 'eProcessEntityBundle:Departement',
                            'query_builder' => function (EntityTable $repository) {
                        return $repository->createQueryBuilder('d');
                    }  ))
                ->add('fournisseur', 'entity', array('attr' => array('required' => true, 'class' => 'form-control chosen-select-element '),
                    'class' => 'eProcessEntityBundle:Fournisseur',
                    'query_builder' => function (EntityTable $repository) {
                        return $repository->createQueryBuilder('f');
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
        return 'eprocess_entitybundle_achat';
    }

}
