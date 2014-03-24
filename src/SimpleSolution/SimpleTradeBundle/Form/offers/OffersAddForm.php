<?php

namespace SimpleSolution\SimpleTradeBundle\Form\offers;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class OffersAddForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /* FIX ME */
        $builder->add('filename1', 'file', array('label' => 'create_offers_form.filename', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false));
        $builder->add('filename2', 'file', array('label' => 'create_offers_form.filename', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false));
        $builder->add('filename3', 'file', array('label' => 'create_offers_form.filename', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false));
        $builder->add('filename4', 'file', array('label' => 'create_offers_form.filename', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false));
        $builder->add('filename5', 'file', array('label' => 'create_offers_form.filename', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false));
    }

    public function getName()
    {
        return 'offersAdd';
    }
}