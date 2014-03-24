<?php

namespace SimpleSolution\SimpleTradeBundle\Form\cabinet;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CabinetCompaniesDocumentsChangeForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text', array('label' => 'form.title', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle'));
        $builder->add('file', 'file', array('label' => 'form.filename', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false));
    }

    public function getName()
    {
        return 'cabinetCompaniesDocumentsChangeCompany';
    }
}