<?php

namespace SimpleSolution\SimpleTradeBundle\Extension;

use Symfony\Component\HttpKernel\KernelInterface;

class WordTransform extends \Twig_Extension
{

    public function getFilters()
    {
        return array(
            'transform' => new \Twig_Filter_Method($this, 'transform')
        );
    }

    public function transform($text, $number)
    {
        switch( $text ) {
            case 'рубль':
                if ($number == 1) {
                    return 'рубль';
                } else if (($number > 1) && ($number <= 5)) {
                    return 'рубля';
                } else if (($number == 0) || ($number > 5)) {
                    return 'рублей';
                }
            case 'доллар США':
                if ($number == 1) {
                    return 'доллар США';
                } else if (($number > 1) && ($number <= 5)) {
                    return 'доллара США';
                } else if (($number == 0) || ($number > 5)) {
                    return 'долларов США';
                }
                break;
            default:
                return $text;
        }
    }

    function getName()
    {
        return 'transform_twig_extension';
    }

}
