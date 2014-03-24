<?php

namespace SimpleSolution\SimpleTradeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SimpleSolution\SimpleTradeBundle\Annotations\Permissions;
use SimpleSolution\SimpleTradeBundle\Model\AuctionsModel;
use SimpleSolution\SimpleTradeBundle\Model\AuctionsContentModel;
use SimpleSolution\SimpleTradeBundle\Model\AuctionsStatusesModel;
use SimpleSolution\SimpleTradeBundle\Model\AuctionsTypesModel;
use SimpleSolution\SimpleTradeBundle\Model\RegionsModel;
use SimpleSolution\SimpleTradeBundle\Model\CurrenciesModel;
use SimpleSolution\SimpleTradeBundle\Model\DocumentsModel;
use SimpleSolution\SimpleTradeBundle\Model\DocumentsLinksModel;
use SimpleSolution\SimpleTradeBundle\Model\UsersModel;
use SimpleSolution\SimpleTradeBundle\Model\TariffsModel;
use SimpleSolution\SimpleTradeBundle\Model\AccountsModel;
use SimpleSolution\SimpleTradeBundle\Model\AccountsTypesModel;
use SimpleSolution\SimpleTradeBundle\Model\SkillsModel;
use SimpleSolution\SimpleTradeBundle\Model\AuctionsSkillsModel;
use SimpleSolution\SimpleTradeBundle\Model\OffersModel;
use SimpleSolution\SimpleTradeBundle\Model\UsersCompaniesModel;
use SimpleSolution\SimpleTradeBundle\Entity\Auctions;
use SimpleSolution\SimpleTradeBundle\Form\auctions\AuctionsAddForm;
use SimpleSolution\SimpleTradeBundle\Consts\Constants;

class AuctionsController extends Controller
{
    private $controllerName = "Auctions";

    /**
     * @Route("/auctions", name="auctions")
     * @Permissions(perm="/auctions")
     * @Template()
     */
    public function auctionsAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $auctionsModel = new AuctionsModel($em, $session, $acl);

        $userCurrent = $this->get('security.context')->getToken()->getUser();
        $auctions = $auctionsModel->findAll();

        $tariffModel = new TariffsModel($em, $session);
        $tariff = $tariffModel->findOneByName('AUCTION_ADD');

        $usersCompaniesModel = new UsersCompaniesModel($em, $session);
        $usersCompanies = $usersCompaniesModel->findOneByUserId($userCurrent->getId());

        $accountsModel = new AccountsModel($em, $session, $acl);
        if ($usersCompanies !== null) {
            $company = $usersCompanies->getCompany();
            $account = $accountsModel->findByCompanyId($company->getId());
            if ($account) {
                $money = $account->getContent()->getAccount();
            } else {
                $money = 0;
            }
        } else {
            $money = 0;
        }

        return $this->render('SimpleTradeBundle:Auctions:list.html.twig', array(
                'auctions' => $auctions,
                'user' => $userCurrent,
                'tariff' => $tariff,
                'money' => $money
            ));
    }

    /**
     * @Route("/auctions/show/{id}")
     * @Permissions(perm="/auctions/show")
     * @Template()
     */
    public function auctionsShowAction($id)
    {
        if (!is_numeric($id)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');
        $auctionsModel = new AuctionsModel($em, $session, $acl);
        $tariffModel = new TariffsModel($em, $session);
        $accountsModel = new AccountsModel($em, $session, $acl);
        $usersCompaniesModel = new UsersCompaniesModel($em, $session);

        $auction = $auctionsModel->findByPK($id);
        if (!isset($auction)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $userCurrent = $this->get('security.context')->getToken()->getUser();

        $tariff = $tariffModel->findOneByName('OFFER_ADD');
        $usersCompanies = $usersCompaniesModel->findOneByUserId($userCurrent->getId());
        $company = $usersCompanies->getCompany();
        $customerCompany = $usersCompaniesModel->findOneByUserId($auction->getUser())->getCompany();

        if ($usersCompanies !== null) {
            $account = $accountsModel->findByCompanyId($company->getId());
            if ($account) {
                $money = $account->getContent()->getAccount();
            } else {
                $money = 0;
            }
        } else {
            $money = 0;
        }

        if ($userCurrent->canI('/offers')) {
            $offersModel = new OffersModel($em, $session, $acl);
            $offers = $offersModel->findAllByAuctionId($id);
        }

        return $this->render('SimpleTradeBundle:Auctions:show.html.twig', array(
                'auction' => $auction,
                'tariff' => $tariff,
                'money' => $money,
                'user' => $userCurrent,
                'offers' => isset($offers) ? $offers : false,
                'company' => $customerCompany,
            ));
    }

    /**
     * @Route("/auctions/add")
     * @Permissions(perm="/auctions/add")
     * @Template()
     */
    public function auctionsAddAction()
    {
        $request = $this->get('request');
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');

        $auctionsTypesModel = new AuctionsTypesModel($em);
        $regionsModel = new RegionsModel($em);
        $currenciesModel = new CurrenciesModel($em);
        $skillsModel = new SkillsModel($em);
        $usersCompaniesModel = new UsersCompaniesModel($em, $session);
        $userCurrent = $this->get('security.context')->getToken()->getUser();
        $usersCompanies = $usersCompaniesModel->findOneByUserId($userCurrent->getId());
        $company = $usersCompanies->getCompany();

        $selected = array( 'companyName' => $company->getContent()->getTitle(),
            'email' => $company->getContent()->getEmail(),
            'phones' => $company->getContent()->getPhone(),
            'userName' => $company->getContent()->getUserName()
        );
        $regions = $regionsModel->findAll();
        $currencies = $currenciesModel->findAll();
        $auctionsTypes = $auctionsTypesModel->findAll();
        $skills = $skillsModel->findAllAsArray();

        $form = $this->createForm(new AuctionsAddForm($regions, $currencies, $auctionsTypes, $selected));
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {

                $session = $this->get('session');
                $acl = $this->get('acl');

                $formData = $form->getData();

                $em->getConnection()->beginTransaction();
                try {
                    $region = $regionsModel->findByPK($formData[ 'regionId' ]);
                    $currency = $currenciesModel->findByPK($formData[ 'currencyId' ]);
                    $tradingForm = $auctionsTypesModel->findByPK($formData[ 'tradingFormId' ]);

                    $auctionsStatusesModel = new AuctionsStatusesModel($em);
                    $prepublicStatus = $auctionsStatusesModel->findOneByName('PRE_PUBLIC');

                    $userCurrent = $this->get('security.context')->getToken()->getUser();
                    /**
                     * Create auction
                     */
                    $a = array(
                        'tradingForm' => $tradingForm,
                        'internetAddress' => $formData[ 'internetAddress' ],
                        'noticeNumber' => md5($formData[ 'title' ] . rand()),
                        'title' => $formData[ 'title' ],
                        'noticeLink' => $formData[ 'noticeLink' ],
                        'noticePrintform' => $formData[ 'noticePrintform' ],
                        'nomenclature' => $formData[ 'nomenclature' ],
                        'companyName' => $formData[ 'companyName' ],
                        'region' => $region,
                        'mail' => $formData[ 'mail' ],
                        'email' => $formData[ 'email' ],
                        'phones' => $formData[ 'phones' ],
                        'name' => $formData[ 'name' ],
                        'contactName' => $formData[ 'contactName' ],
                        'startPrice' => $formData[ 'startPrice' ],
                        'endPrice' => $formData[ 'endPrice' ],
                        'currency' => $currency,
                        'deliverySize' => $formData[ 'deliverySize' ],
                        'deliveryPlace' => $formData[ 'deliveryPlace' ],
                        'deliveryPeriod' => $formData[ 'deliveryPeriod' ],
                        'info' => $formData[ 'info' ],
                        'endOffer' => $formData[ 'endOffer' ],
                        'endConsideration' => $formData[ 'endConsideration' ],
                        'startAuction' => $formData[ 'startAuction' ],
                        'placeToAcceptOffers' => $formData[ 'placeToAcceptOffers' ],
                        'datetimeToAcceptOffers' => $formData[ 'datetimeToAcceptOffers' ],
                    );

                    $auctionsModel = new AuctionsModel($em, $session, $acl);

                    $auction = $auctionsModel->create($a, array(
                        'user' => $userCurrent,
                        'status' => $prepublicStatus,
                        'company' => $company ));

                    $auctionsSkillsModel = new AuctionsSkillsModel($em, $session);

                    $attrsBySkill = array( );
                    $attrs = explode(',', $formData[ 'attributes' ]);
                    foreach( $attrs as $attr ) {
                        list($id, $main, $dangerous) = explode('|', $attr);
                        $attrsBySkill[ $id ][ 'main' ] = $main;
                        //$attrsBySkill[ $id ][ 'dangerous' ] = $dangerous;
                    }

                    $skills = explode(',', $formData[ 'skills' ]);

                    foreach( $skills as $skill ) {
                        $auctionsSkillsModel->create(array( 'auction' => $auction->getId(),
                            'skill' => $skill,
                            'isMain' => $attrsBySkill[ $skill ][ 'main' ]
                        ));
                    }

                    $documentsModel = new DocumentsModel($em, $session, $acl);
                    $documentsLinksModel = new DocumentsLinksModel($em, $session);

                    foreach( $formData[ 'files' ] as $value ) {
                        $document = $documentsModel->create(array(
                            'file' => $value->getFile(),
                            'title' => $value->getTitle(),
                            ), array( 'isActive' => 1 ));

                        $documentsLinksModel->create(array(
                            'document' => $document,
                            'owner' => 'AUCTION',
                            'ownerId' => $auction->getId(),
                        ));
                    }

                    $em->getConnection()->commit();
                } catch( \Exception $e ) {
                    $em->getConnection()->rollback();
                    $em->close();
                    throw $e;
                }

                $session->getFlashBag()->add('notice', 'Аукцион успешно создан. Извещение отправлено администратору для проверки.');

                return $this->redirect($this->generateUrl('auctions'));
            }
        }
        return $this->render('SimpleTradeBundle:Auctions:add.html.twig', array(
                'form' => $form->createView(),
                'skills' => $skills,
                'company' => $company
            ));
    }

    /**
     * @Route("/auctions/approve/{auctionId}")
     * @Permissions(perm="/auctions/approve")
     * @Template()
     */
    public function auctionsApproveAction($auctionId)
    {
        if (!is_numeric($auctionId)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $factory = $this->get('security.encoder_factory');
        $acl = $this->get('acl');
        $auctionsModel = new AuctionsModel($em, $session, $acl);

        $auction = $auctionsModel->findByPK($auctionId);
        if (!isset($auction)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em->getConnection()->beginTransaction();
        try {
            $userCurrent = $this->get('security.context')->getToken()->getUser();

            $usersCompaniesModel = new UsersCompaniesModel($em, $session);
            $company = $usersCompaniesModel->findOneByUserId($auction->getUser()->getId())->getCompany();

            $tariffModel = new TariffsModel($em, $session);
            $tariff = $tariffModel->findOneByName('AUCTION_ADD');

            $accountsTypesModel = new AccountsTypesModel($em);
            $removeType = $accountsTypesModel->findOneByName('REMOVE');

            $accountsModel = new AccountsModel($em, $session, $acl);
            $account = $accountsModel->findByCompanyId($company->getId());
            $accountsModel->update($account->getId(), array( 'account' => $account->getContent()->getAccount() - $tariff->getCost(),
                'tariff' => $tariff,
                'changes' => $tariff->getCost(),
                'comment' => ''
                ), array( 'company' => $company,
                'user' => $userCurrent,
                'type' => $removeType
                )
            );

            $auctionsStatusesModel = new AuctionsStatusesModel($em);
            $publicStatus = $auctionsStatusesModel->findOneByName('PUBLIC');
            $content = $auction->getContent()->getAllFieldsAsArray();

            $auctionsModel->update($auctionId, $content, array( 'status' => $publicStatus ));

            $em->getConnection()->commit();
        } catch( Exception $e ) {
            $em->getConnection()->rollback();
            $em->close();
            throw $e;
        }

        $usersModel = new UsersModel($em, $session, $factory);

        /* @TODO
         * рассылать подрядчикам не только emailы но и внутренние сообщения
         */
        $performers = $usersModel->findPerformers($auction->getContent()->getRegion()->getId(), $auction->getId());
        foreach( $performers as $performer ) {
            $this->get('mail')->sendByUser(Constants::MAIL_2COMPANY_NEW_AUCTION, $performer, array( 'user' => $performer, 'auction' => $auction, ));
        }

        $session->getFlashBag()->add('notice', 'Аукцион успешно одобрен. Разослано ' . count($performers) . ' писем');

        return $this->redirect($this->generateUrl('requests_auctions'));
    }

    /**
     * @Route("/auctions/reject/{auctionId}")
     * @Permissions(perm="/auctions/reject")
     * @Template()
     */
    public function auctionsRejectAction($auctionId)
    {
        if (!is_numeric($auctionId)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');
        $auctionsModel = new AuctionsModel($em, $session, $acl);

        $auction = $auctionsModel->findByPK($auctionId);
        if (!isset($auction)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em->getConnection()->beginTransaction();
        try {
            $userCurrent = $this->get('security.context')->getToken()->getUser();

            $usersCompaniesModel = new UsersCompaniesModel($em, $session);
            $company = $usersCompaniesModel->findOneByUserId($auction->getUser()->getId())->getCompany();

            $auctionsStatusesModel = new AuctionsStatusesModel($em);
            $rejectStatus = $auctionsStatusesModel->findOneByName('REJECTED');
            $content = $auction->getContent()->getAllFieldsAsArray();

            $auctionsModel->update($auctionId, $content, array( 'status' => $rejectStatus ));
            $em->getConnection()->commit();
        } catch( Exception $e ) {
            $em->getConnection()->rollback();
            $em->close();
            throw $e;
        }


        $session->getFlashBag()->add('notice', 'Аукцион успешно отклонен');

        return $this->redirect($this->generateUrl('auctions'));
    }

}
