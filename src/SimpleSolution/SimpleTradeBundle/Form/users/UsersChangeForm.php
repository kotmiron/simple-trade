<?php

namespace SimpleSolution\SimpleTradeBundle\Form\users;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UsersChangeForm extends AbstractType
{
    protected $permissions;
    protected $roles;
    protected $regions;
    protected $selected;

    public function __construct($permissions, $regions, $roles, $selected = array( ))
    {
        $this->permissions = $permissions;
        $this->roles = $roles;

        foreach( $regions as $region ) {
            $this->regions[ $region->getId() ] = $region->getTitle();
        }

        if (!empty($selected)) {
            $this->selected = $selected;
        } else {
            $this->selected = array(
                'region' => null
            );
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('regionId', 'choice', array( 'choices' => $this->regions, 'label' => 'change_company.region', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'data' => $this->selected[ 'region' ] ));

        $builder->add('login', 'text', array( 'label' => 'users_add_form.login', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle' ));
        $builder->add('password', 'text', array( 'data' => '', 'label' => 'users_add_form.password', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false, 'data' => '' ));
        $builder->add('name', 'text', array( 'label' => 'users_add_form.name', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle' ));
        $builder->add('phone', 'text', array( 'label' => 'users_add_form.phone', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false ));
        $builder->add('position', 'text', array( 'label' => 'users_add_form.position', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false ));
        $builder->add('grounds', 'text', array( 'label' => 'users_add_form.grounds', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false ));

        $builder->add('email', 'email', array( 'label' => 'users_add_form.email', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array( 'class' => 'validate[required,custom[email]]' ) ));

        $builder->add('permissions', 'choice', array(
            'choices' => $this->permissions,
            'expanded' => true,
            'multiple' => true,
            'label' => 'users_add_form.permissions',
            'translation_domain' => 'SimpleSolutionSimpleTradeBundle',
            'data' => $this->roles
        ));
    }

    public function getName()
    {
        return 'usersChange';
    }

}