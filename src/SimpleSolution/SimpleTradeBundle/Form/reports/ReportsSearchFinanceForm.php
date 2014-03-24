<?php

namespace SimpleSolution\SimpleTradeBundle\Form\reports;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ReportsSearchFinanceForm extends AbstractType
{
    protected $srosTypes;
    protected $regions;

    public function __construct($srosTypes, $regions)
    {
        foreach( $srosTypes as $sroTypes ) {
            $this->srosTypes[ $sroTypes->getId() ] = $sroTypes->getTitle();
        }
        foreach( $regions as $region ) {
            $this->regions[ $region->getId() ] = $region->getTitle();
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('role', 'choice', array( 'choices' => array( 'PERFORMER' => 'Строительная компания', 'CUSTOMER' => 'Заказчик' ), 'label' => 'reports_search_form.role', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false ));
        $builder->add('name', 'text', array( 'label' => 'reports_search_form.name', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false ));
        $builder->add('inn', 'text', array( 'label' => 'reports_search_form.inn', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false ));
        $builder->add('region', 'choice', array( 'choices' => $this->regions, 'label' => 'reports_search_form.region', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false ));
        $builder->add('sro', 'hidden', array('label' => 'reports_search_form.sro', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false));
        $builder->add('sroType', 'choice', array('choices' => $this->srosTypes, 'label' => 'reports_search_form.sroType', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false));
        $builder->add(
            $builder->create('startPeriod', 'datetime', array( 'label' => 'reports_search_form.startPeriod', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false, 'widget' => 'single_text' ))
        );
        $builder->add(
            $builder->create('endPeriod', 'datetime', array( 'label' => 'reports_search_form.endPeriod', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false, 'widget' => 'single_text' ))
        );

    }

    public function getName()
    {
        return 'reportsSearchFinance';
    }

}