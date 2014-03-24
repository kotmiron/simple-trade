<?php

namespace SimpleSolution\SimpleTradeBundle\Model;
use SimpleSolution\SimpleTradeBundle\Entity\AuctionsContent;
use Doctrine\ORM\EntityManager;

Class AuctionsContentModel
{
    protected $em;
    protected $entityName;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->entityName = 'SimpleSolution\SimpleTradeBundle\Entity\AuctionsContent';
    }

    public function findAll()
    {
        $objects = $this->em->getRepository($this->entityName)->findAll();
        return $objects;
    }

    public function findByPK($id)
    {
        $object = $this->em->find($this->entityName, $id);
        return $object;
    }

    public function create($entity)
    {
        $object = new AuctionsContent();
        $object->setTradingForm($entity['tradingForm']);
        $object->setInternetAddress($entity['internetAddress']);
        $object->setNoticeNumber($entity['noticeNumber']);
        $object->setTitle($entity['title']);
        $object->setNoticeLink($entity['noticeLink']);
        $object->setNoticePrintform($entity['noticePrintform']);
        $object->setNomenclature($entity['nomenclature']);
        $object->setCompanyName($entity['companyName']);
        $object->setRegion($entity['region']);
        $object->setMail($entity['mail']);
        $object->setEmail($entity['email']);
        $object->setPhones($entity['phones']);
        $object->setName($entity['name']);
        $object->setContactName($entity['contactName']);
        $object->setStartPrice($entity['startPrice']);
        $object->setEndPrice($entity['endPrice']);
        $object->setCurrency($entity['currency']);
        $object->setDeliverySize($entity['deliverySize']);
        $object->setDeliveryPlace($entity['deliveryPlace']);
        $object->setDeliveryPeriod($entity['deliveryPeriod']);
        $object->setInfo($entity['info']);
        $object->setEndOffer($entity['endOffer']);
        $object->setEndConsideration($entity['endConsideration']);
        $object->setStartAuction($entity['startAuction']);
        $object->setPlaceToAcceptOffers($entity['placeToAcceptOffers']);
        $object->setDatetimeToAcceptOffers($entity['datetimeToAcceptOffers']);

        $this->em->persist($object);
        $this->em->flush();
        return $object;
    }

    public function removeByPK($id)
    {
        $object = $this->em->find($this->entityName, $id);
        $this->em->remove($object);
        $this->em->flush();
    }
}
