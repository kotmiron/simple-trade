<?php

namespace SimpleSolution\SimpleTradeBundle\Form\requests;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RequestRegisterApproveForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('files', 'collection', array(
            'type' => new FilesType(),
            'allow_add' => true,
            'by_reference' => false,
        ));
    }

    public function getName()
    {
        return 'requestRegisterApprove';
    }
}