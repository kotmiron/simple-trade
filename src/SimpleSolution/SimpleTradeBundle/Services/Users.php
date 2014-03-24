<?php

namespace SimpleSolution\SimpleTradeBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;
use SimpleSolution\SimpleTradeBundle\Model\UsersModel;

class Users
{
    protected $em;
    protected $session;
    protected $factory;
    protected $model;

    public function __construct(EntityManager $em, Session $session, $factory)
    {
        $this->em = $em;
        $this->session = $session;
        $this->factory = $factory;

        $this->model = new UsersModel($this->em, $this->session, $this->factory);
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array( array( $this->model, $name ), $arguments );
    }

}
