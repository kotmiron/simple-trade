<?php

namespace SimpleSolution\SimpleTradeBundle\Form\news;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class NewsChangeForm extends AbstractType
{

    protected $selected;

    public function __construct($selected)
    {
        $this->selected = $selected;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text', array( 'label' => 'create_news_form.title', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array( 'class' => 'validate[required]' ) ));
        $builder->add('text', 'textarea', array( 'label' => 'create_news_form.text', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false ));
        $builder->add('show', 'choice', array(
           'label' => 'create_news_form.show', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle',
           'choices' => array('Подрядчик', 'Заказчик', 'СРО', 'Публичная'),
           'expanded' => true,
           'multiple' => true,
           'data' => $this->selected
        ));
        $builder->add('emailRepeat', 'checkbox', array( 'label' => 'create_news_form.emailRepeat', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false ));
    }

    public function getName()
    {
        return 'newsChange';
    }

}