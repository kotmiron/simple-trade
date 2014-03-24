<?php

namespace SimpleSolution\SimpleTradeBundle\Model;

use SimpleSolution\SimpleTradeBundle\Entity\RequestsSkills;
use Doctrine\ORM\EntityManager;

Class RequestsSkillsModel
{
    protected $em;
    protected $entityName;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->entityName = 'SimpleSolution\SimpleTradeBundle\Entity\RequestsSkills';
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

    public function findAllSROByRequestIdAndCompanyId($request_id, $company_id)
    {
        $query = $this->em->createQuery("
            SELECT rs, skill, parent, type, sro
            FROM SimpleTradeBundle:RequestsSkills rs
            JOIN rs.skill skill
            JOIN skill.parent parent
            JOIN parent.type type
            JOIN type.sros sro
            JOIN sro.companies companies
            WHERE rs.request=:request_id
            AND companies.company=:company_id
                ");
        $query->setParameter('request_id', $request_id);
        $query->setParameter('company_id', $company_id);

        $objects = $query->getResult();

        $out = array( );
        foreach( $objects as $object ) {
            foreach( $object->getSkill()->getParent()->getType()->getSros() as $sro ) {
                array_push($out, $sro);
            }
        }
        return $out;
    }

    public function findAllSkillsByRequestId($id)
    {
        $query = $this->em->createQuery("
            SELECT rs
            FROM SimpleTradeBundle:RequestsSkills rs
            WHERE rs.request=:request_id");
        $query->setParameter('request_id', $id);

        return $query->getResult();
    }

    public function findSkillsByRequestIdAsArray($id)
    {
        $query = $this->em->createQuery("
            SELECT rs, s
            FROM SimpleTradeBundle:RequestsSkills rs
            JOIN rs.skill s
            WHERE rs.request=:request_id");
        $query->setParameter('request_id', $id);

        $objects = $query->getResult();
        $out = array( );
        foreach( $objects as $object ) {
            $out[ $object->getId() ] = $object->getSkill()->getTitle();
        }
        return $out;
    }

    public function findSkillsByRequestIdAsArrayOfIds($id)
    {
        $query = $this->em->createQuery("
            SELECT rs, s
            FROM SimpleTradeBundle:RequestsSkills rs
            JOIN rs.skill s
            WHERE rs.request=:request_id");
        $query->setParameter('request_id', $id);

        $objects = $query->getResult();
        $out = array( );
        foreach( $objects as $object ) {
            $id = $object->getSkill()->getId();
            $out[ $id ] = $id . '|' . ( int ) $object->getIsMain() . '|' . ( int ) $object->getIsDangerous();
        }
        return $out;
    }

    public function create($entity)
    {
        $object = new RequestsSkills();
        $object->setIsMain($entity[ 'isMain' ]);
        $object->setIsDangerous($entity[ 'isDangerous' ]);
        $skill = $this->em->find('SimpleSolution\SimpleTradeBundle\Entity\Skills', $entity[ 'skill' ]);
        $object->setSkill($skill);
        $request = $this->em->find('SimpleSolution\SimpleTradeBundle\Entity\Requests', $entity[ 'request' ]);
        $object->setRequest($request);

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

    public function removeByRequestId($id)
    {
        $objects = $this->em->getRepository($this->entityName)->findBy(array( 'request' => $id ));
        foreach( $objects as $object ) {
            $this->em->remove($object);
        }
        $this->em->flush();
    }

}
?>
