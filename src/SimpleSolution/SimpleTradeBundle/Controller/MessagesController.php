<?php

namespace SimpleSolution\SimpleTradeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SimpleSolution\SimpleTradeBundle\Annotations\Permissions;
use SimpleSolution\SimpleTradeBundle\Model\MessagesModel;
use SimpleSolution\SimpleTradeBundle\Model\UsersModel;

class MessagesController extends Controller
{

    /**
     * @Route("/messages", name="messages")
     * @Permissions(perm="/messages")
     * @Template()
     */
    public function indexAction()
    {
//        $this->addAction();

        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $messagesModel = new MessagesModel($em, $session, $acl);

        $messages = $messagesModel->findAll();

        return $this->render('SimpleTradeBundle:Messages:index.html.twig', array( 'messages' => $messages ));
    }

    /**
     * @Route("/messages/create")
     * @Permissions(perm="/messages/create")
     * @Template()
     */
    public function createAction()
    {
        $request = $this->get('request');

        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $messagesModel = new MessagesModel($em, $session, $acl);

        if ($request->isMethod('POST')) {
            $form = $this->createForm(new MessagesCreateForm());
            $form->bind($request);

            if ($form->isValid()) {
                $em->getConnection()->beginTransaction();
                try {

                    $users_model = new UsersModel($em, $session, $acl);

                    $from_user = $users_model->findByPK(1);
                    $to_user = $users_model->findByPK(2);

                    $messagesModel->create(array(
                        'subject' => 'subject',
                        'body' => 'test',
                        ), array(
                        'fromUser' => $from_user,
                        'toUser' => $to_user,
                        )
                    );
                    $em->getConnection()->commit();
                } catch( Exception $e ) {
                    $em->getConnection()->rollback();
                    $em->close();
                    throw $e;
                }
            }
        }
    }

}
