<?php

namespace PhiTrac\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectEditType extends ProjectType
{
     /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->remove('icon');
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'phitrac_projectbundle_projectedit';
    }
}
