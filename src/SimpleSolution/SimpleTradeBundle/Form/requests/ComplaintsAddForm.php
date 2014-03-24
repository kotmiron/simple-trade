<?php

namespace SimpleSolution\SimpleTradeBundle\Form\requests;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ComplaintsAddForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('text', 'textarea', array('label' => 'complaints_add_form.text', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array('class'=>'validate[required]')));
        $builder->add('files', 'collection', array(
            'type' => new FilesType(),
            'allow_add' => true,
            'by_reference' => false,
        ));
    }

    public function getName()
    {
        return 'complaintsAdd';
    }
}