<?php

namespace SimpleSolution\SimpleTradeBundle\Form\register;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegisterForm extends AbstractType
{
    protected $sros;
    protected $regions;

    public function __construct($sros, $regions)
    {
        foreach($sros as $sro)
        {
            $this->sros[$sro->getId()] = $sro->getContent()->getTitle();
        }
        foreach($regions as $region)
        {
            $this->regions[$region->getId()] = $region->getTitle();
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('region', 'choice', array('choices' => $this->regions, 'label' => 'form.region', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle'));
        $builder->add('role', 'choice', array('choices' => array('PERFORMER' => 'Строительная компания', 'CUSTOMER' => 'Заказчик'), 'label' => 'form.role', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array('class'=>'validate[required]')));
        $builder->add('sro', 'hidden', array('label' => 'form.sro', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle'));
        $builder->add('title', 'text', array('label' => 'form.title', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array('class'=>'validate[required]')));
        $builder->add('name', 'text', array('label' => 'form.name', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array('class'=>'validate[required,minSize[3]]')));
        $builder->add('login', 'text', array('label' => 'form.login', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array('class'=>'validate[required]')));
        $builder->add('password', 'repeated', array(
                'type' => 'password',
                'options' => array('translation_domain' => 'SimpleSolutionSimpleTradeBundle'),
                'first_options' => array('label' => 'form.password', 'attr' => array('class'=>'validate[required]')),
                'second_options' => array('label' => 'form.password_confirmation', 'attr' => array('class'=>'validate[required,equals[registerCompany_password_first]]')),
        ));

        $builder->add('inn', null, array(
                    'label' => 'form.inn',
                    'translation_domain' => 'SimpleSolutionSimpleTradeBundle',
                    'attr' => array('class'=>'validate[required,minSize[10],maxSize[12],custom[onlyNumber]]')
                  ));
        $builder->add('kpp', null, array('label' => 'form.kpp', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false ));
        $builder->add('ogrn', null, array('label' => 'form.ogrn', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false));
        $builder->add('lastname', null, array('label' => 'form.lastname', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array('class'=>'validate[required,maxSize[64]]')));
        $builder->add('firstname', null, array('label' => 'form.firstname', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array('class'=>'validate[required,maxSize[64]]')));
        $builder->add('patronymic', null, array('label' => 'form.patronymic', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false, 'attr' => array('class'=>'validate[maxSize[64]]')));
        $builder->add('position', null, array('label' => 'form.position', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false));
        $builder->add('grounds', null, array('label' => 'form.grounds', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'required' => false));
        $builder->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array('class'=>'validate[required,custom[email]]')));
        $builder->add('phone', null, array('label' => 'form.phone', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle', 'attr' => array('class'=>'validate[required]')));

        $builder->add('files', 'collection', array(
            'type' => new FilesType(),
            'allow_add' => true,
            'by_reference' => false,
        ));
        $builder->add('skills', 'hidden', array('required' => false ));
        $builder->add('attributes', 'hidden', array('required' => false ));
    }

    public function getName()
    {
        return 'registerCompany';
    }
}