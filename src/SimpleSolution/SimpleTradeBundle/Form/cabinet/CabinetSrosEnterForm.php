<?php

namespace SimpleSolution\SimpleTradeBundle\Form\cabinet;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CabinetSrosEnterForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('sro', 'hidden', array( 'label' => 'form.sro', 'translation_domain' => 'SimpleSolutionSimpleTradeBundle' ));

        $builder->add('files', 'collection', array(
            'type' => new FilesType(),
            'allow_add' => true,
            'by_reference' => false,
        ));
    }

    public function getName()
    {
        return 'cabinetSrosEnterForm';
    }

}