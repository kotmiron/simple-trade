<?php

namespace SimpleSolution\SimpleTradeBundle\Form\trade;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TradeSearchAuctionForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type', 'entity', array( 'required' => false, 'class' => 'SimpleTradeBundle:AuctionsTypes', 'property' => 'title', 'empty_value' => 'Форма торгов' ));
        $builder->add('code', 'text', array( 'required' => false, 'label' => 'trade_searchauction_form.code', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array( 'class' => 'validate[required]' ) ));
        $builder->add('title', 'text', array( 'required' => false, 'label' => 'trade_searchauction_form.title', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array( 'class' => 'validate[required]' ) ));
        $builder->add('region', 'entity', array( 'required' => false, 'label' => 'trade_searchauction_form.region', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'class' => 'SimpleTradeBundle:Regions', 'property' => 'title', 'empty_value' => 'Регион' ));

        $builder->add('is_extended', 'hidden', array('data' => false));
    }

    public function getName()
    {
        return 'tradeSearchAuction';
    }

}