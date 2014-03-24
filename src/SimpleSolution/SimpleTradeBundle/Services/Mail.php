<?php

namespace SimpleSolution\SimpleTradeBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;
use SimpleSolution\SimpleTradeBundle\Model\TemplatesModel;
use SimpleSolution\SimpleTradeBundle\Entity\Users as UsersEntity;

class Mail
{
    protected $em;
    protected $session;
    protected $factory;
    protected $container;
    protected $mailer;

    public function __construct(EntityManager $em, Session $session, $factory, $container, $mailer)
    {
        $this->em = $em;
        $this->session = $session;
        $this->factory = $factory;
        $this->container = $container;
        $this->mailer = $mailer;
    }

    public function sendByUser($id, $user, $data = array( ))
    {
        if ($user instanceof UsersEntity) {
            $users = array( $user );
        } elseif (true === is_array($user)) {
            $users = $user;
        } else {
            throw new \Exception('Something wrong with User.');
        }

        $template = $this->template($id);

        $attach = $this->attach($data);

        foreach( $users as $user ) {

            if (false === isset($data[ 'user' ])) {
                $data[ 'user' ] = $user;
            }

            $this->send($user->getEmail(), $this->render($template->getSubject(), $data), $this->render($template->getBody(), $data), $attach);
        }

        return true;
    }

    public function sendByEmail($id, $email, $data = array( ))
    {
        if (true === is_array($email)) {
            $emails = $email;
        } else {
            $emails = array( $email );
        }

        $template = $this->template($id);

        $attach = $this->attach($data);

        foreach( $emails as $email ) {

            if (false === isset($data[ 'email' ])) {
                $data[ 'email' ] = $email;
            }

            $this->send($email, $this->render($template->getSubject(), $data), $this->render($template->getBody(), $data), $attach);
        }

        return true;
    }

    protected function send($email, $subject, $body, $attach)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom('no-reply@simpletrade.ru')
            ->setTo($email)
            ->setBody($body, 'text/html');

        if ($attach) {
            foreach( $attach as $v ) {
                switch( $v[ 'type' ] ) {
                    case 'content':
                        $message->attach(\Swift_Attachment::newInstance($v[ 'data' ], $v[ 'filename' ], $v[ 'contentType' ]));
                        break;
                    case 'file':
                        $message->attach(\Swift_Attachment::fromPath($v[ 'filename' ], $v[ 'contentType' ]));
                        break;
                }
            }
        }

        $this->mailer->send($message);
    }

    protected function render($s, $a)
    {
        if ($this->container->hasParameter('base_url')) {
            $url = $this->container->getParameter('base_url');
        } else {
            $request = $this->container->get('request');

            $url = $request->getScheme() . '://' . $request->getHttpHost();
            if ($port = $request->getPort()) {
                $url .= ':' . $port;
            }
            $url .= $request->getBaseUrl();
        }

        $a[ '_baseurl' ] = $url;

        $twig_env = new \Twig_Environment(new \Twig_Loader_String());
        return $twig_env->render($s, $a);
    }

    protected function template($id)
    {
        $templatesModel = new TemplatesModel($this->em, $this->session, $this->factory);
        $template = $templatesModel->findByName($id);

        if (null === $template) {
            throw new \Exception('Something wrong with Template. Maybe it\'s not defined?');
        }

        return $template;
    }

    protected function attach($data)
    {
        $attach = null;

        if (isset($data[ 'attach' ])) {
            if (isset($data[ 'attach' ][ 'content' ])
                ||
                isset($data[ 'attach' ][ 'filename' ])
            ) {
                $a = array( $data[ 'attach' ] );
            } else {
                $a = $data;
            }

            foreach( $a as $v ) {
                if (isset($v[ 'content' ])) {
                    $attach[ ] = array(
                        'type' => 'content',
                        'data' => $v[ 'content' ],
                        'filename' => $v[ 'filename' ],
                        'contentType' => $v[ 'contentType' ],
                    );
                } elseif (isset($v[ 'filename' ])) {
                    $attach[ ] = array(
                        'type' => 'file',
                        'filename' => $v[ 'filename' ],
                        'contentType' => $v[ 'contentType' ],
                    );
                } else {
                    throw new \Exception('Something wrong with attach. Can\'t detect type.');
                }
            }
        }

        return $attach;
    }

}
