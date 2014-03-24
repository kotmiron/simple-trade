<?php

namespace SimpleSolution\SimpleTradeBundle\Form\sros;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SrosCreateForm extends AbstractType
{
    protected $regions;
    protected $srosTypes;

    public function __construct($regions, $srosTypes)
    {
        foreach($regions as $region)
        {
            $this->regions[$region->getId()] = $region->getTitle();
        }
        foreach($srosTypes as $srosType)
        {
            $this->srosTypes[$srosType->getId()] = $srosType->getTitle();
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('region', 'choice', array('choices' => $this->regions, 'label' => 'create_sro_from.region', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle'));
        $builder->add('srosType', 'choice', array('choices' => $this->srosTypes, 'label' => 'create_sro_from.srosType', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle'));
        $builder->add('login', 'text', array('label' => 'create_sro_from.login', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle'));
        $builder->add('password', 'password', array('label' => 'create_sro_from.password', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle'));
        $builder->add('name', 'text', array('label' => 'create_sro_from.name', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle'));
        $builder->add('email', 'email', array('label' => 'create_sro_from.email', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array('class'=>'validate[required,custom[email]]')));
        $builder->add('title', 'text', array('label' => 'create_sro_from.title', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle'));
    }

    public function getName()
    {
        return 'createForm';
    }
}