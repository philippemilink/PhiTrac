<?php

namespace PhiTrac\ProjectBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ItemType extends AbstractType
{
     /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text')
            ->add('type', 'choice', array('choices' => array('BUG' => 'Bug', 'TODO' => 'To do'), 'expanded' => true))
            ->add('description', 'textarea', array('required' => false))
            ->add('status', 'choice', array('choices' => array('TODO' => 'To do', 'BEGUN' => 'Begun', 'IN_TEST' => 'In test', 'DONE' => 'Done')))
            ->add('priority', 'choice', array('choices' => array('NORMAL' => 'Normal', 'IMPORTANT' => 'Important', 'URGENT' => 'Urgent')))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PhiTrac\ProjectBundle\Entity\Item'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'phitrac_projectbundle_item';
    }
}
