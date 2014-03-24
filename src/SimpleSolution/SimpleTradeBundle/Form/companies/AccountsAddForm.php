<?php

namespace SimpleSolution\SimpleTradeBundle\Form\companies;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AccountsAddForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('money', 'text', array('label' => 'accounts_add_form.money', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array('class'=>'validate[required]')));
        $builder->add('comment', 'text', array('label' => 'accounts_add_form.comment', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false));
    }

    public function getName()
    {
        return 'accountsAdd';
    }
}