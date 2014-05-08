<?php

namespace PhiTrac\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
     /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('name')
            ->add('firstname')
            ->add('password', 'password')
            ->add('roles', 'choice', array('mapped' => false, 'choices' => array('ROLE_TESTER' => 'Tester', 'ROLE_DEV' => 'Developer', 'ROLE_ADMIN' => 'Administrator')))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PhiTrac\UserBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'phitrac_userbundle_user';
    }
}
