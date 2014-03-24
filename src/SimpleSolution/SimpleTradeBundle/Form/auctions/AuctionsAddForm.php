<?php

namespace SimpleSolution\SimpleTradeBundle\Form\auctions;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use SimpleSolution\SimpleTradeBundle\Form\cabinet\FilesType;

class AuctionsAddForm extends AbstractType
{
    protected $regions;
    protected $currencies;
    protected $auctionsTypes;
    protected $selected;

    public function __construct($regions, $currencies, $auctionsTypes, $selected = array( ))
    {
        foreach( $regions as $region ) {
            $this->regions[ $region->getId() ] = $region->getTitle();
        }
        foreach( $currencies as $currency ) {
            $this->currencies[ $currency->getId() ] = $currency->getTitle();
        }
        foreach( $auctionsTypes as $type ) {
            $this->auctionsTypes[ $type->getId() ] = $type->getTitle();
        }
        if (!empty($selected)) {
            $this->selected = $selected;
        } else {
            $this->selected = array(
                'companyName' => null,
                'email' => null,
                'phones' => null,
                'userName' => null
            );
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $utcTransformer = new \SimpleSolution\SimpleTradeBundle\Form\UTCDateTimeTransformer();
        $transformer = new \SimpleSolution\SimpleTradeBundle\Form\DateTimeStringToAppStringTransformer();

        $builder->add('regionId', 'choice', array( 'choices' => $this->regions, 'label' => 'create_auctions_form.region', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle' ));
        $builder->add('currencyId', 'choice', array( 'choices' => $this->currencies, 'label' => 'create_auctions_form.currency', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle' ));
        $builder->add('tradingFormId', 'choice', array( 'choices' => $this->auctionsTypes, 'label' => 'create_auctions_form.tradingForm', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle' ));
        $builder->add('internetAddress', 'text', array( 'label' => 'create_auctions_form.internetAddress', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array( 'class' => 'validate[required]' ) ));
        $builder->add('title', 'text', array( 'label' => 'create_auctions_form.title', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array( 'class' => 'validate[required]' ) ));
        $builder->add('noticeLink', 'text', array( 'label' => 'create_auctions_form.noticeLink', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array( 'class' => 'validate[required]' ) ));
        $builder->add('noticePrintform', 'text', array( 'label' => 'create_auctions_form.noticePrintform', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array( 'class' => 'validate[required]' ) ));
        $builder->add('nomenclature', 'text', array( 'label' => 'create_auctions_form.nomenclature', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array( 'class' => 'validate[required]' ) ));
        $builder->add('companyName', 'text', array( 'label' => 'create_auctions_form.companyName', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array( 'class' => 'validate[required,minSize[3]]' ), 'read_only' => true, 'data' => $this->selected[ 'companyName' ] ));
        $builder->add('mail', 'text', array( 'label' => 'create_auctions_form.mail', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array( 'class' => 'validate[required]' ) ));
        $builder->add('email', 'email', array( 'label' => 'create_auctions_form.email', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array( 'class' => 'validate[required,custom[email]]' ), 'data' => $this->selected[ 'email' ] ));
        $builder->add('phones', 'textarea', array( 'label' => 'create_auctions_form.phones', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array( 'class' => 'validate[required]' ), 'data' => $this->selected[ 'phones' ] ));
        $builder->add('name', 'text', array( 'label' => 'create_auctions_form.name', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array( 'class' => 'validate[required]' ) ));
        $builder->add('contactName', 'text', array( 'label' => 'create_auctions_form.contactName', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array( 'class' => 'validate[required]' ), 'data' => $this->selected[ 'userName' ] ));
        $builder->add('startPrice', 'text', array( 'required' => false, 'label' => 'create_auctions_form.startPrice', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle' ));
        $builder->add('endPrice', 'text', array( 'required' => false, 'label' => 'create_auctions_form.endPrice', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle' ));
        $builder->add('deliverySize', 'text', array( 'label' => 'create_auctions_form.deliverySize', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle' ));
        $builder->add('deliveryPlace', 'text', array( 'label' => 'create_auctions_form.deliveryPlace', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle' ));
        $builder->add('deliveryPeriod', 'text', array( 'label' => 'create_auctions_form.deliveryPeriod', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle' ));
        $builder->add('info', 'textarea', array( 'label' => 'create_auctions_form.info', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle' ));

        $builder->add(
            $builder->create('endOffer', 'datetime', array( 'label' => 'create_auctions_form.endOffer', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false, 'widget' => 'single_text' ))
                ->addModelTransformer($utcTransformer)
                ->addViewTransformer($transformer)
        );

        $builder->add(
            $builder->create('endConsideration', 'datetime', array( 'label' => 'create_auctions_form.endConsideration', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false, 'widget' => 'single_text' ))
                ->addModelTransformer($utcTransformer)
                ->addViewTransformer($transformer)
        );

        $builder->add(
            $builder->create('startAuction', 'datetime', array( 'label' => 'create_auctions_form.startAuction', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false, 'widget' => 'single_text' ))
                ->addModelTransformer($utcTransformer)
                ->addViewTransformer($transformer)
        );

        $builder->add('placeToAcceptOffers', 'text', array( 'required' => false, 'label' => 'create_auctions_form.placeToAcceptOffers', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle' ));

        $builder->add(
            $builder->create('datetimeToAcceptOffers', 'datetime', array( 'label' => 'create_auctions_form.datetimeToAcceptOffers', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false, 'widget' => 'single_text' ))
                ->addModelTransformer($utcTransformer)
                ->addViewTransformer($transformer)
        );


        $builder->add('skills', 'hidden', array( 'required' => false ));
        $builder->add('attributes', 'hidden', array( 'required' => false ));

        $builder->add('files', 'collection', array(
            'type' => new FilesType(),
            'allow_add' => true,
            'by_reference' => false,
        ));
        $builder->add('deletedFiles', 'hidden', array( 'required' => false ));
    }

    public function getName()
    {
        return 'auctionsAdd';
    }

}