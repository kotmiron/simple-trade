<?php

namespace SimpleSolution\SimpleTradeBundle\Form;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class UTCDateTimeTransformer implements DataTransformerInterface
{

    // Из базы в то, что на форме
    public function transform($datetime)
    {
        if (null === $datetime) {
            return null;
        }

        $datetime->setTimezone(new \DateTimeZone('Europe/Moscow'));

        return $datetime;
    }

    // Из формы в то, что в базе
    public function reverseTransform($datetime)
    {
        if (null === $datetime) {
            return null;
        }

        $dateTimeZoneUTC = new \DateTimeZone("UTC");
        $dateTimeZoneMoscow = new \DateTimeZone("Europe/Moscow");

        $dateTimeUTC = new \DateTime("now", $dateTimeZoneUTC);
        $dateTimeMoscow = new \DateTime("now", $dateTimeZoneMoscow);

        $timeOffset = $dateTimeZoneMoscow->getOffset($dateTimeUTC);

        $new = new \DateTime('@'.($datetime->getTimestamp()-$timeOffset));

        return $new;
    }

}