<?php

namespace SimpleSolution\SimpleTradeBundle\Form\sros;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CompaniesBlockForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('comment', 'textarea', array('label' => 'companies_block_form.comment', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array('class'=>'validate[required]')));
    }

    public function getName()
    {
        return 'companiesBlock';
    }
}