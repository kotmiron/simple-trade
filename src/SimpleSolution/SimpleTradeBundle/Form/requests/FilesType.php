<?php

namespace SimpleSolution\SimpleTradeBundle\Form\requests;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class FilesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', 'file');
        $builder->add('title', 'text', array('label' => 'Название'));
    }


public function getDefaultOptions(array $options)
{
    return array(
        'data_class' => 'SimpleSolution\SimpleTradeBundle\Entity\DocumentsContent',
    );
}


    public function getName()
    {
        return 'simplesolution_simpletradebundle_filestype';
    }
}