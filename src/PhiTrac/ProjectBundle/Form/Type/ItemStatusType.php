<?php

namespace PhiTrac\ProjectBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

class ItemStatusType extends ItemType
{
     /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->remove('title')
                ->remove('type')
                ->remove('description')
                ->remove('priority');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'phitrac_projectbundle_itemstatus';
    }
}
