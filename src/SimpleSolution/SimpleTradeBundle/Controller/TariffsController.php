<?php

namespace SimpleSolution\SimpleTradeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SimpleSolution\SimpleTradeBundle\Annotations\Permissions;
use SimpleSolution\SimpleTradeBundle\Model\TariffsModel;
use SimpleSolution\SimpleTradeBundle\Form\tariffs\TariffsChangeForm;

class TariffsController extends Controller
{

    /**
     * @Route("/tariffs", name="tariffs")
     * @Permissions(perm="/tariffs")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');

        $tariffsModel = new TariffsModel($em, $session);
        $tariffs = $tariffsModel->findAll();

        return $this->render('SimpleTradeBundle:Tariffs:index.html.twig', array(
                'tariffs' => $tariffs,
                'user' => $this->get('security.context')->getToken()->getUser()
            ));
    }

    /**
     * @Route("/tariffs/change/{id}")
     * @Permissions(perm="/tariffs/change")
     * @Template()
     */
    public function tariffsChangeAction($id)
    {
        if (!is_numeric($id)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $request = $this->get('request');
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');

        $tariffsModel = new TariffsModel($em, $session);
        $tariff = $tariffsModel->findByPK($id);
        if (!isset($tariff)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $form = $this->createForm(new TariffsChangeForm(), $tariff);
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $em->getConnection()->beginTransaction();
                try {
                    $tariffsModel->updateFromObject($tariff);

                    $em->getConnection()->commit();
                } catch( Exception $e ) {
                    $em->getConnection()->rollback();
                    $em->close();
                    throw $e;
                }
                $session->getFlashBag()->add('notice', 'Тариф успешно изменен');

                return $this->redirect($this->generateUrl('tariffs'));
            }
        }
        return $this->render('SimpleTradeBundle:Tariffs:change.html.twig', array(
                'form' => $form->createView(),
                'user' => $this->get('security.context')->getToken()->getUser(),
                'tariff' => $tariff
            ));
    }
}
