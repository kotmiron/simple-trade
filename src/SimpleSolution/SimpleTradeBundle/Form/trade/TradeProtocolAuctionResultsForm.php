<?php

namespace SimpleSolution\SimpleTradeBundle\Form\trade;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use SimpleSolution\SimpleTradeBundle\Form\trade\FilesType;

class TradeProtocolAuctionResultsForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $utcTransformer = new \SimpleSolution\SimpleTradeBundle\Form\UTCDateTimeTransformer();
        $transformer = new \SimpleSolution\SimpleTradeBundle\Form\DateTimeStringToAppStringTransformer();

        $builder->add(
            $builder->create('datetimeStartView', 'datetime', array( 'label' => 'protocol_auction_results_form.datetimeStartView', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false, 'widget' => 'single_text' ))
                ->addModelTransformer($utcTransformer)
                ->addViewTransformer($transformer)
        );

        $builder->add(
            $builder->create('datetimeEndView', 'datetime', array( 'label' => 'protocol_auction_results_form.datetimeEndView', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false, 'widget' => 'single_text' ))
                ->addModelTransformer($utcTransformer)
                ->addViewTransformer($transformer)
        );

        $builder->add('placeView', 'text', array( 'label' => 'protocol_auction_results_form.placeView', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array( 'class' => 'validate[required]' ) ));
        $builder->add('fullName1', 'text', array( 'label' => 'protocol_auction_results_form.fullName', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array( 'class' => 'validate[required]' ) ));
        $builder->add('fullName2', 'text', array( 'label' => 'protocol_auction_results_form.fullName', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array( 'class' => 'validate[required]' ) ));
        $builder->add('fullName3', 'text', array( 'label' => 'protocol_auction_results_form.fullName', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array( 'class' => 'validate[required]' ) ));
        $builder->add('position1', 'text', array( 'label' => 'protocol_auction_results_form.position', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array( 'class' => 'validate[required]' ) ));
        $builder->add('position2', 'text', array( 'label' => 'protocol_auction_results_form.position', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array( 'class' => 'validate[required]' ) ));
        $builder->add('position3', 'text', array( 'label' => 'protocol_auction_results_form.position', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array( 'class' => 'validate[required]' ) ));

        $builder->add('files', 'collection', array(
            'type' => new FilesType(),
            'allow_add' => true,
            'by_reference' => false,
        ));
    }

    public function getName()
    {
        return 'protocol';
    }

}