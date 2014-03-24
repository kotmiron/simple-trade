<?php

namespace SimpleSolution\SimpleTradeBundle\Form\users;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UsersRolesAddForm extends AbstractType
{
    protected $roles;

    public function __construct($roles)
    {
        $this->roles = $roles;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text');
        $builder->add('name', 'text');
        $builder->add('roles', 'choice', array(
           'choices' => $this->roles,
           'expanded' => true,
           'multiple' => true,
        ));
    }

    public function getName()
    {
        return 'usersRolesAdd';
    }
}