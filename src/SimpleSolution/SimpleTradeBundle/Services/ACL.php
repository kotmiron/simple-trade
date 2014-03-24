<?php

namespace SimpleSolution\SimpleTradeBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;

class ACL
{
    protected $em;
    protected $aclProvider;
    protected $securityContext;
    protected $user;
    protected $acl;
    
    public function __construct(EntityManager $em, $aclProvider, $securityContext)
    {
        $this->em = $em;
        $this->aclProvider = $aclProvider;
        $this->securityContext = $securityContext;
        $this->user = $securityContext->getToken()->getUser();
    }

    public function setPermissions($object, $mask=MaskBuilder::MASK_OWNER)
    {
        $securityIdentity = UserSecurityIdentity::fromAccount($this->user);
        $this->getObject($object, $securityIdentity);

        $objectAces = $this->acl->getObjectAces();
        $founded = 0;
        foreach ($objectAces as $index => $ace)
        {
            if ($securityIdentity->equals($ace->getSecurityIdentity()))
            {
                $founded++;
                try
                {
                    $this->acl->updateObjectAce($index, $mask);
                }
                catch (\OutOfBoundsException $exception)
                {
                    $this->acl->insertObjectAce($securityIdentity, $mask);
                }
            }
        }
        if ($founded == 0)
        {
            $this->acl->insertObjectAce($securityIdentity, $mask);
        }
        $this->aclProvider->updateAcl($this->acl);
    }

    public function removePermissions($object)
    {
        $securityIdentity = UserSecurityIdentity::fromAccount($this->user);
        $this->getObject($object, $securityIdentity);

        $objectAces = $this->acl->getObjectAces();
        foreach ($objectAces as $index => $ace)
        {
            if ($securityIdentity->equals($ace->getSecurityIdentity()))
            {
                $this->acl->deleteObjectAce($index);
            }
        }
        $this->aclProvider->updateAcl($this->acl);
    }
    
    public function checkPermissions($object, $mask)
    {
        if (false === $this->securityContext->isGranted($mask, $object))
        {
            throw new AccessDeniedException();
        }
    }
    
    public function isGranted($object, $mask)
    {
        return (false === $this->securityContext->isGranted($mask, $object));
    }
    
    public function getObject($object, $securityIdentity)
    {
        $objectIdentity = ObjectIdentity::fromDomainObject($object);
        try 
        {          
            $this->acl = $this->aclProvider->findAcl($objectIdentity, array($securityIdentity));
        }
        catch (AclNotFoundException $exception)
        {
            $this->acl = $this->aclProvider->createAcl($objectIdentity);
        }
    }
}
