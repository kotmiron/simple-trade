<?php

namespace SimpleSolution\SimpleTradeBundle\Form;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class DateTimeStringToAppStringTransformer implements DataTransformerInterface
{

    // Из базы в то, что на форме
    public function transform($datetime)
    {
        if ($datetime == '') {
            return '';
        }

        $new = new \DateTime($datetime);

        return $new->format('d-m-Y H:i');
    }

    // Из формы в то, что в базе
    public function reverseTransform($datetime)
    {
        return $datetime;
    }

}