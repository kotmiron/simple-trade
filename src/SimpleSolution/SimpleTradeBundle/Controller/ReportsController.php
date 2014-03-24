<?php

namespace SimpleSolution\SimpleTradeBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SimpleSolution\SimpleTradeBundle\Annotations\Permissions;
use SimpleSolution\SimpleTradeBundle\Model\AccountsModel;
use SimpleSolution\SimpleTradeBundle\Model\RegionsModel;
use SimpleSolution\SimpleTradeBundle\Model\SrosTypesModel;
use SimpleSolution\SimpleTradeBundle\Model\SrosModel;
use SimpleSolution\SimpleTradeBundle\Model\TemplatesModel;
use SimpleSolution\SimpleTradeBundle\Form\reports\ReportsSearchFinanceForm;
use SimpleSolution\SimpleTradeBundle\Consts\Constants;

class ReportsController extends Controller
{
    private $controllerName = "Reports";

    /**
     * @Route("/reports", name="reports")
     * @Permissions(perm="/reports")
     * @Template()
     */
    public function reportsAction()
    {
        return $this->render('SimpleTradeBundle:Reports:index.html.twig', array(
                'user' => $this->get('security.context')->getToken()->getUser()
            ));
    }

    /**
     * @Route("/reports/finance", name="reports_finance")
     * @Permissions(perm="/reports/finance")
     * @Template()
     */
    public function reportsFinanceAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');
        $request = $this->get('request');

        $regionsModel = new RegionsModel($em);
        $regions = $regionsModel->findAll();

        $srosTypesModel = new SrosTypesModel($em, $session);
        $srosTypes = $srosTypesModel->findAll();

        $srosModel = new SrosModel($em, $session, $acl);
        $srosArray = $srosModel->findAllAsArray();

        $form = $this->createForm(new ReportsSearchFinanceForm($srosTypes, $regions));

        $accountsModel = new AccountsModel($em, $session, $acl);
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $accounts = $accountsModel->findAccounts($form->getData());
            }
        }
        if (!isset($accounts)) {
            $accounts = $accountsModel->findAllHistory();
        }

        return $this->render('SimpleTradeBundle:Reports:finance.html.twig', array(
                'accounts' => $accounts,
                'user' => $this->get('security.context')->getToken()->getUser(),
                'form' => $form->createView(),
                'sros' => $srosArray
            ));
    }

    /**
     * @Route("/reports/finance/download")
     * @Template()
     */
    public function reportsFinanceDownloadAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');
        $request = $this->get('request');
        $factory = $this->get('security.encoder_factory');

        if ($request->isMethod('POST')) {
            $post = $this->get('request')->request->all();
            $post = array_pop($post);
            unset($post[ '_token' ]);

            $accountsModel = new AccountsModel($em, $session, $acl);
            $accounts = $accountsModel->findAccounts($post);

            $templatesModel = new TemplatesModel($em, $session, $factory);
            $template = $templatesModel->findByName(Constants::FINANCE_REPORT);

            $twig_env = new \Twig_Environment(new \Twig_Loader_String());
            $html = $twig_env->render($template->getBody(), array( 'accounts' => $accounts ));

            return new Response(
                    $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
                    200,
                    array(
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'attachment; filename="file.pdf"'
                    )
            );
        }
        exit;
    }

}
