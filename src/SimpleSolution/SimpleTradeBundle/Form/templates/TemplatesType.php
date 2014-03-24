<?php

namespace SimpleSolution\SimpleTradeBundle\Form\templates;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use SimpleSolution\SimpleTradeBundle\Consts\Constants;

class TemplatesType extends AbstractType
{
    protected $created;

    public function __construct($created = array( ))
    {
        $this->created = $created;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choises = array( );
        for( $i = 1; $i <= Constants::MAIL_TOTAL; $i++ ) {
            if (!isset($this->created[ $i ])) {
                $choises[ $i ] = 'mail.types.' . $i;
            }
        }

        $builder
            ->add('name', 'choice', array(
                'label' => 'Тип',
                'choices' => $choises,
                'data' => $options[ 'data' ]->getName(),
            ))
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
