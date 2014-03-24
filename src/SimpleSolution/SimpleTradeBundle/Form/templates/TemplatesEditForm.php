<?php

namespace SimpleSolution\SimpleTradeBundle\Form\templates;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use SimpleSolution\SimpleTradeBundle\Consts\Constants;

class TemplatesEditForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'hidden', array( 'label' => 'Тип',))
            ->add('subject', 'text', array( 'label' => 'Заголовок' ))
            ->add('body', 'textarea', array( 'label' => 'Содержание', 'attr' => array( 'style' => 'width:300px;height:200px' ) ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SimpleSolution\SimpleTradeBundle\Entity\Templates'
        ));
    }

    public function getName()
    {
        return 'simplesolution_simpletradebundle_templatestype';
    }

}
