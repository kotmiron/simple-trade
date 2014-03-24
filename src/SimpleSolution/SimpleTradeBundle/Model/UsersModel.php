<?php

namespace SimpleSolution\SimpleTradeBundle\Model;

use SimpleSolution\SimpleTradeBundle\Entity\Users;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

Class UsersModel
{
    protected $em;
    protected $session;
    protected $factory;
    protected $entityName;

    public function __construct(EntityManager $em, Session $session, $factory)
    {
        $this->em = $em;
        $this->session = $session;
        $this->factory = $factory;
        $this->entityName = 'SimpleSolution\SimpleTradeBundle\Entity\Users';
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

    public function findByEmail($email)
    {
        $object = $this->em->getRepository($this->entityName)->findOneBy(array( 'email' => $email ));
        return $object;
    }

    public function findByHash($hash)
    {
        $user = new Users();
        $salt = $user->getRestoreSalt();
        $objects = $this->em->getRepository($this->entityName)->findAll();
        foreach( $objects as $object ) {
            if ($hash === md5($salt . $object->getLogin() . $object->getPassword())) {
                return $object;
            }
        }
        return false;
    }

    public function findAllByCompanyId($id)
    {
        $objects = $this->em->getRepository('SimpleSolution\SimpleTradeBundle\Entity\UsersCompanies')->findBy(array( 'company' => $id ));
        $out = array( );
        foreach( $objects as $object ) {
            array_push($out, $object->getUser());
        }
        return $out;
    }

    public function findAllByCompanyIdAndRole($id, $role)
    {
        $query = $this->em->createQuery("
            SELECT uc, user
            FROM SimpleTradeBundle:UsersCompanies uc
            JOIN uc.user user
            JOIN user.usersRoles usersRoles
            JOIN usersRoles.role role
            WHERE uc.company=:company_id AND
            role.name=:role
            ");
        $query->setParameter('company_id', $id);
        $query->setParameter('role', $role);

        $objects = $query->getResult();

        $out = array( );
        foreach( $objects as $object ) {
            array_push($out, $object->getUser());
        }
        return $out;
    }

    public function findAllBySroId($id)
    {
        $objects = $this->em->getRepository('SimpleSolution\SimpleTradeBundle\Entity\UsersSros')->findBy(array( 'sro' => $id ));
        $out = array( );
        foreach( $objects as $object ) {
            array_push($out, $object->getUser());
        }
        return $out;
    }

    public function findParticipantsAuction($auctionId)
    {
        $query = $this->em->createQuery("
            SELECT o, c
            FROM SimpleTradeBundle:Offers o
            JOIN o.company c
            WHERE o.auction=:auction_id
            ");
        $query->setParameter('auction_id', $auctionId);
        $objectsOffers = $query->getResult();

        $companysId = array( );
        foreach( $objectsOffers as $object ) {
            $companysId[ ] = $object->getCompany()->getId();
        }

        $out = array( );

        if (count($companysId) > 0) {
            $query = $this->em->createQuery("
                SELECT uc, u
                FROM SimpleTradeBundle:UsersCompanies uc
                JOIN uc.user u
                WHERE uc.company IN (:companysId)");
            $query->setParameter('companysId', $companysId);
            $objectsUsersCompanies = $query->getResult();

            foreach ($objectsUsersCompanies as $value) {
                $out[] = $value->getUser();
            }
        }

        return $out;
    }

    public function findPerformers($regionId, $auctionId)
    {
        $query = $this->em->createQuery("
            SELECT ask, s
            FROM SimpleTradeBundle:AuctionsSkills ask
            JOIN ask.skill s
            WHERE ask.auction=:auction_id
            ");
        $query->setParameter('auction_id', $auctionId);
        $objectsASkills = $query->getResult();

        $auctionsSkills = array( );
        foreach( $objectsASkills as $object ) {
            $auctionsSkills[ ] = $object->getSkill()->getId();
        }
        sort($auctionsSkills);

        $query = $this->em->createQuery("
            SELECT uc, c, cs, u, s
            FROM SimpleTradeBundle:UsersCompanies uc
            JOIN uc.company c
            JOIN c.companiesSkills cs
            JOIN cs.skill s
            JOIN uc.user u
            WHERE u.region=:region_id
            ");
        $query->setParameter('region_id', $regionId);
        $objectsCSkills = $query->getResult();

        $out = array( );
        foreach( $objectsCSkills as $object ) {
            $companiesSkills = array( );
            $skills = $object->getCompany()->getCompaniesSkills();
            foreach( $skills as $skill ) {
                $companiesSkills[ ] = $skill->getSkill()->getId();
            }
            sort($companiesSkills);

            if (count(array_diff($auctionsSkills, $companiesSkills)) == 0) {
                $out[ ] = $object->getUser();
            }
        }

        return $out;
    }

    public function createFromForm($oEntity)
    {
        $password = $oEntity->getPassword();
        $oEntity->setPassword($password, $this->factory);
        $oEntity->setCreatedAt(new \DateTime());

        $this->em->persist($oEntity);
        $this->em->flush();

        $transaction = new \History_transaction($this->em, $this->session);
        $transaction->createTransaction('create', $this->entityName, $oEntity);
    }

    public function create($entity)
    {
        $object = new Users();

        $object->setLogin($entity[ 'login' ]);
        $object->setName($entity[ 'name' ]);
        $object->setEmail($entity[ 'email' ]);
        $object->setPassword($entity[ 'password' ], $this->factory);
        $object->setCreatedAt(new \DateTime());

        if (!is_null($entity[ 'region' ])) $object->setRegion($entity[ 'region' ]);

        if (isset($entity[ 'phone' ])) $object->setPhone($entity[ 'phone' ]);
        if (isset($entity[ 'position' ])) $object->setPhone($entity[ 'position' ]);
        if (isset($entity[ 'grounds' ])) $object->setPhone($entity[ 'grounds' ]);
        if (isset($entity[ 'isBlocked' ])) $object->setIsBlocked($entity[ 'isBlocked' ]);

        $this->em->persist($object);
        $this->em->flush();

        $transaction = new \History_transaction($this->em, $this->session);
        $transaction->createTransaction('create', $this->entityName, $object);

        return $object;
    }

    public function update($entity)
    {
        $object = $this->em->find($this->entityName, $entity[ 'id' ]);
        foreach( $entity as $key => $element ) {
            $method = 'set' . ucfirst($key);
            if ($key == 'password') $object->$method($element, $this->factory);
            else $object->$method($element);
        }

        $this->em->persist($object);
        $this->em->flush();

        $transaction = new \History_transaction($this->em, $this->session);
        $transaction->createTransaction('update', $this->entityName, $object);
    }

    public function restorePassword($oEntity)
    {
        $password = $oEntity->generatePassword();
        $oEntity->setPassword($password, $this->factory);
        $oEntity->setCreatedAt(new \DateTime());

        $transaction = new \History_transaction($this->em, $this->session);
        $transaction->createTransaction('update', $this->entityName, $oEntity);

        return $password;
    }

    public function removeByPK($id)
    {
        $object = $this->em->find($this->entityName, $id);

        $transaction = new \History_transaction($this->em, $this->session);
        $transaction->createTransaction('remove', $this->entityName, $object);
        $this->em->remove($object);
        $this->em->flush();
    }

    public function removeByCompanyId($id)
    {
        $objects = $this->em->getRepository($this->entityName)->findBy(array( 'company' => $id ));
        foreach( $objects as $object ) {
            $transaction = new \History_transaction($this->em, $this->session);
            $transaction->createTransaction('remove', $this->entityName, $object);
            $this->em->remove($object);
        }
        $this->em->flush();
    }

    public function removeBySroId($id)
    {
        $objects = $this->em->getRepository($this->entityName)->findBy(array( 'sro' => $id ));

        foreach( $objects as $object ) {
            $transaction = new \History_transaction($this->em, $this->session);
            $transaction->createTransaction('remove', $this->entityName, $object);
            $this->em->remove($object);
        }
        $this->em->flush();
    }

}
