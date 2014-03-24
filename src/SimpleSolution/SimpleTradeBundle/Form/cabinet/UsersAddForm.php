<?php

namespace SimpleSolution\SimpleTradeBundle\Form\cabinet;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UsersAddForm extends AbstractType
{
    private $roles;

    public function __construct($roles)
    {
        $this->roles = $roles;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('login', 'text', array('label' => 'users_add_form.login', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle'));
        $builder->add('password', 'text', array('label' => 'users_add_form.password', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle'));
        $builder->add('name', 'text', array('label' => 'users_add_form.name', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle'));
        $builder->add('email', 'email', array('label' => 'users_add_form.email', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array('class'=>'validate[required,custom[email]]')));

        $builder->add('roles', 'choice', array(
           'choices' => $this->roles,
           'expanded' => true,
           'multiple' => true,
        ));
    }

    public function getName()
    {
        return 'usersAdd';
    }
}