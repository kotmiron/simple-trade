<?php

namespace SimpleSolution\SimpleTradeBundle\Model;
use SimpleSolution\SimpleTradeBundle\Entity\Roles;
use Doctrine\ORM\EntityManager;

Class RolesModel
{
    protected $em;
    protected $entityName;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->entityName = 'SimpleSolution\SimpleTradeBundle\Entity\Roles';
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

    public function findOneByName($name)
    {
        $object = $this->em->getRepository($this->entityName)->findOneBy(array('name' => $name));
        return $object;
    }

    public function findAllAsArray()
    {
        $query = $this->em->createQuery("
            SELECT r, rp
            FROM SimpleTradeBundle:Roles r
            JOIN r.roles_primitives rp");

        $out = array();
        foreach ($query->getResult() as $role) {
            $out[$role->getId()] = $role->getTitle().'('.implode(' + ', $role->getRolesPrimitivesAsArrayOfStrings()).')';
        }

        return $out;
    }

    public function findAllAsMultiArray()
    {
        $query = $this->em->createQuery("
            SELECT r, rp
            FROM SimpleTradeBundle:Roles r
            JOIN r.roles_primitives rp");

        $out = array();
        foreach ($query->getResult() as $role) {
            $out[$role->getId()] = array(
                'title' => $role->getTitle(),
                'primitives' => $role->getRolesPrimitivesAsArrayOfStrings()
            );
        }

        return $out;
    }

    public function createFromForm($oEntity)
    {
        $this->em->persist($oEntity);
        $this->em->flush();
    }

    public function create($entity)
    {
        $object = new Roles();
        $object->setTitle($entity['title']);
        $object->setName($entity['name']);

        if (isset($entity['roles_primitives'])) {
            $object->setRolesPrimitives($entity['roles_primitives']);
        }

        $this->em->persist($object);
        $this->em->flush();
    }

    public function update($id, $entity)
    {
        $object = $this->findByPK($id);
        $object->setTitle($entity['title']);
        $object->setName($entity['name']);

        if (isset($entity['roles_primitives'])) {
            $object->setRolesPrimitives($entity['roles_primitives']);
        }

        $this->em->persist($object);
        $this->em->flush();
    }

    public function removeByPK($id)
    {
        $object = $this->em->find($this->entityName, $id);
        $this->em->remove($object);
        $this->em->flush();
    }
}
?>
