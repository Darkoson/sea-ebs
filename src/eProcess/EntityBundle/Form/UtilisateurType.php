<?php

namespace eProcess\EntityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UtilisateurType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text', array('attr' => array('required' => TRUE, 'placeholder' => 'nom d\'utilisateur de active directory', 'class' => 'form-control')))
            ->add('nom', 'text', array('attr' => array('required' => TRUE, 'placeholder' => 'Saisir le nom', 'class' => 'form-control')))
            ->add('prenom', 'text', array('attr' => array('required' => TRUE, 'placeholder' => 'Saisir le prénom', 'class' => 'form-control')))
             ->add('departement', 'entity', array('attr' => array('required' => false, 'class' => 'form-control chosen-select-element '),
                            'class' => 'eProcessEntityBundle:Departement',
//                            'query_builder' => function (EntityTable $repository) {
//                        return $repository->createQueryBuilder('d');
//                    } 
                    ))
                ->add('profile', 'entity', array('attr' => array('required' => true, 'class' => 'form-control chosen-select-element '),
                    'class' => 'eProcessEntityBundle:Profile',
//                    'query_builder' => function (EntityTable $repository) {
//                        return $repository->createQueryBuilder('p');
//                    }  
                    )) 
                
              
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'eProcess\EntityBundle\Entity\Utilisateur'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'eprocess_entitybundle_utilisateur';
    }
}
