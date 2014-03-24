<?php

namespace SimpleSolution\SimpleTradeBundle\Model;

use Doctrine\ORM\EntityManager;
use SimpleSolution\SimpleTradeBundle\Entity\Bids;

Class BidsModel
{
    protected $em;
    protected $entityName;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->entityName = 'SimpleSolution\SimpleTradeBundle\Entity\Bids';
    }

    public function findAll()
    {
        return $this->em->getRepository($this->entityName)->findAll();
    }

    public function findByPK($id)
    {
        return $this->em->find($this->entityName, $id);
    }

    public function findByAuctionId($id)
    {
        //return $this->em->getRepository($this->entityName)->findBy(array( 'auction' => $id ));

        $query = $this->em->createQuery("
            SELECT b, u, c, a
            FROM SimpleTradeBundle:Bids b
            LEFT JOIN b.user u
            LEFT JOIN b.company c
            LEFT JOIN b.auction a
            WHERE b.auction = :id_auction")->setParameter('id_auction', $id);

        return $query->getResult();
    }

    public function findByAuctionIdAsArray($id, $currUser)
    {
        $bids = $this->findByAuctionId($id);

        $out = array( );
        $users = array( );
        $you = null;
        $number = 1;
        foreach( $bids as $key => $value ) {

            if (!isset($users[ $value->getUser()->getLogin() ])) {
                $users[ $value->getUser()->getLogin() ] = $number;
                if ($value->getUser() === $currUser) {
                    $you = $number;
                }
                $number++;
            }

            $out[ ] = array(
                'user' => $users[ $value->getUser()->getLogin() ],
                'best_price' => $value->getBestPrice(),
                'time' => $value->getBidTime()->setTimezone(new \DateTimeZone('Europe/Moscow'))->format('H:i:s')
            );
        }
        return array(
            'list' => $out,
            'you' => $you
        );
    }

    public function getHistoryAuction($id, $winnerBid)
    {
        $bids = $this->findByAuctionId($id);

        $out = array( );
        $userNumbers = array( );
        $number = 1;
        foreach( $bids as $key => $value ) {

            if (!isset($userNumbers[ $value->getUser()->getId() ])) {
                $userNumbers[ $value->getUser()->getId() ] = $number;
                $number++;
            }

            $out[ ] = array(
                'user_number' => $userNumbers[ $value->getUser()->getId() ],
                'bid' => $value,
                'is_winner' => $winnerBid === $value
            );
        }

        return $out;
    }

    public function getResultsAuctionAsArray($idAuction)
    {
        $bids = $this->findByAuctionId($idAuction);

        $number = 1;
        $usersMax = array( );
        $userNumbers = array( );
        foreach( $bids as $value ) {
            if (!isset($userNumbers[ $value->getUser()->getId() ])) {
                $userNumbers[ $value->getUser()->getId() ] = $number;
                $number++;
            }
            $usersMax[ $value->getUser()->getId() ] = $value;
        }

        usort($usersMax, array( "SimpleSolution\SimpleTradeBundle\Model\BidsModel", "sortBids" ));

        $result = array( );
        foreach( $usersMax as $key => $value ) {
            $result[] = array(
                'bid' => $value,
                'position' => $key + 1
            );
        }

        return $result;
    }

    static function sortBids($a, $b)
    {
        // Сперва сравниваем предложенные цены
        if ($a->getBestPrice() < $b->getBestPrice()) {
            return -1;
        } elseif ($a->getBestPrice() > $b->getBestPrice()) {
            return 1;
        }

        // Если предложенные цены равны, сравниваем время (чем раньше тем выше)
        $aTimestamp = $a->getBidTime()->getTimestamp();
        $bTimestamp = $b->getBidTime()->getTimestamp();

        if ($aTimestamp > $bTimestamp) {
            return 1;
        } elseif ($aTimestamp < $bTimestamp) {
            return -1;
        }
        return 0;
    }

    public function getDateTimeLastBidByAuctionId($id)
    {
        $lastBid = $this->em->getRepository($this->entityName)->findBy(
            array( 'auction' => $id ), array( 'bidTime' => 'DESC' ), 1
        );

        if (!empty($lastBid)) {
            return $lastBid[ 0 ]->getBidTime();
        }
        return null;
    }

    public function getBestPriceByAuctionId($id)
    {
        $query = $this->em->createQuery("
            SELECT MIN(b.bestPrice) as bestPrice
            FROM SimpleTradeBundle:Bids b
            WHERE b.auction = :id")->setParameter('id', $id);

        return $query->getSingleScalarResult();
    }

    public function getAuctionLastPriceByUser($idAuction, $idUser)
    {
        $query = $this->em->createQuery("
            SELECT MIN(b.bestPrice) as bestPrice
            FROM SimpleTradeBundle:Bids b
            WHERE b.auction = :id_auction
            AND b.user = :id_user")->setParameter('id_auction', $idAuction)->setParameter('id_user', $idUser);

        return $query->getSingleScalarResult();
    }

    public function getPositionUser($idAuction, $idUser)
    {
        $bids = $this->findByAuctionId($idAuction);

        $usersMax = array( );
        foreach( $bids as $value ) {
            $usersMax[ $value->getUser()->getId() ] = $value;
        }

        usort($usersMax, array( "SimpleSolution\SimpleTradeBundle\Model\BidsModel", "sortBids" ));

        foreach( $usersMax as $key => $value ) {
            if ($value->getUser()->getId() === $idUser) {
                return $key + 1;
            }
        }

        return null;
    }

    public function create($entity)
    {
        $bid = new Bids();

        $bid->setCurrentBid($entity[ 'current_bid' ]);
        $bid->setBestPrice($entity[ 'best_price' ]);
        $bid->setUser($entity[ 'user' ]);
        $bid->setCompany($entity[ 'company' ]);
        $bid->setAuction($entity[ 'auction' ]);
        $bid->setRobot($entity[ 'robot' ]);

        $this->em->persist($bid);
        $this->em->flush();

        return $bid;
    }

    //public function getResults
}
