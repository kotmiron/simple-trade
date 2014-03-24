<?php

namespace SimpleSolution\SimpleTradeBundle\Form\tariffs;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TariffsChangeForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('cost', 'text', array( 'label' => 'change_tariff_form.cost', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array( 'class' => 'validate[required]' ) ));
    }

    public function getName()
    {
        return 'tariffsChange';
    }

}