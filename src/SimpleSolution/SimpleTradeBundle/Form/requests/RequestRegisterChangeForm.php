<?php

namespace SimpleSolution\SimpleTradeBundle\Form\requests;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RequestRegisterChangeForm extends AbstractType
{
    protected $regions;
    protected $selected;
    protected $skills;
    protected $attributes;

    public function __construct($regions, $selected = '', $skills = '', $attributes = '')
    {
        foreach($regions as $region)
        {
            $this->regions[$region->getId()] = $region->getTitle();
        }
        $this->selected = $selected;
        $this->skills = $skills;
        $this->attributes = $attributes;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text', array('label' => 'change_company.title', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array('class'=>'validate[required]')));
        $builder->add('name', 'text', array('label' => 'change_company.name', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array('class'=>'validate[required]')));
        $builder->add('region_id', 'choice', array('choices' => $this->regions, 'label' => 'change_company.region', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'data' => $this->selected));

        $builder->add('inn', 'text', array('label' => 'change_company.inn', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array('class'=>'validate[required]')));
        $builder->add('kpp', 'text', array('label' => 'change_company.kpp', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false ));
        $builder->add('ogrn', 'text', array('label' => 'change_company.ogrn', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false));

        $builder->add('email', 'email', array('label' => 'change_company.email', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array('class'=>'validate[required,custom[email]]')));
        $builder->add('phone', 'text', array('label' => 'change_company.phone', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array('class'=>'validate[required]')));
        $builder->add('position', 'text', array('label' => 'change_company.position', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false));
        $builder->add('grounds', 'text', array('label' => 'change_company.grounds', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false));
        $builder->add('userName', 'text', array('label' => 'change_company.userName', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array('class'=>'validate[required]')));

        $builder->add('files', 'collection', array(
            'type' => new FilesType(),
            'allow_add' => true,
            'by_reference' => false,
        ));
        $builder->add('deletedFiles', 'hidden', array('required' => false));
        $builder->add('skills', 'hidden', array('required' => false, 'data' => $this->skills ));
        $builder->add('attributes', 'hidden', array('required' => false, 'data' => $this->attributes ));
    }

    public function getName()
    {
        return 'requestRegisterChange';
    }
}