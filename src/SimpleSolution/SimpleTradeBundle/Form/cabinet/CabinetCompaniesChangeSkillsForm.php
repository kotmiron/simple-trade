<?php

namespace SimpleSolution\SimpleTradeBundle\Form\cabinet;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CabinetCompaniesChangeSkillsForm extends AbstractType
{
    protected $skills;
    protected $attributes;

    public function __construct($skills = '', $attributes = '')
    {
        $this->skills = $skills;
        $this->attributes = $attributes;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('skills', 'hidden', array('required' => false, 'data' => $this->skills ));
        $builder->add('attributes', 'hidden', array('required' => false, 'data' => $this->attributes ));

        $builder->add('files', 'collection', array(
            'type' => new FilesType(),
            'allow_add' => true,
            'by_reference' => false,
        ));
        $builder->add('deletedFiles', 'hidden', array('required' => false));
    }

    public function getName()
    {
        return 'cabinetCompaniesChangeSkills';
    }
}