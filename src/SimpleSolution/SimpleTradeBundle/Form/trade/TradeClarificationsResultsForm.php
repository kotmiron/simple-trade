<?php

namespace SimpleSolution\SimpleTradeBundle\Form\trade;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use SimpleSolution\SimpleTradeBundle\Form\trade\FilesType;

class TradeClarificationsResultsForm extends AbstractType
{
    private $withFiles;

    public function __construct($withFiles = true)
    {
        $this->withFiles = $withFiles;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('body', 'textarea', array( 'label' => 'trade_clarifications_form.body', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array( 'class' => 'validate[required]' ) ));
        if ($this->withFiles) {
            $builder->add('files', 'collection', array(
                'type' => new FilesType(),
                'allow_add' => true
            ));
        }
        $builder->add('for_id', 'hidden');
    }

    public function getName()
    {
        return 'clarifications';
    }

}