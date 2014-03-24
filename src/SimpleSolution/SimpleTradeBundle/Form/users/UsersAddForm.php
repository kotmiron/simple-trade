<?php

namespace SimpleSolution\SimpleTradeBundle\Form\users;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UsersAddForm extends AbstractType
{
    protected $permissions;
    protected $regions;

    public function __construct($permissions, $regions)
    {
        $this->permissions = $permissions;
        foreach($regions as $region)
        {
            $this->regions[$region->getId()] = $region->getTitle();
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('regionId', 'choice', array('choices' => $this->regions, 'label' => 'users_add_form.region', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle'));
        $builder->add('login', 'text', array('label' => 'users_add_form.login', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle'));
        $builder->add('password', 'text', array('label' => 'users_add_form.password', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle'));
        $builder->add('name', 'text', array('label' => 'users_add_form.name', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle'));

        $builder->add('email', 'email', array('label' => 'users_add_form.email', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array('class'=>'validate[required,custom[email]]')));
        $builder->add('permissions', 'choice', array(
           'choices' => $this->permissions,
           'expanded' => true,
           'multiple' => true,
           'label' => 'users_add_form.permissions',
           'translation_domain' => 'SimpleSolutionSimpleTradeBundle'
        ));
    }

    public function getName()
    {
        return 'usersAdd';
    }
}