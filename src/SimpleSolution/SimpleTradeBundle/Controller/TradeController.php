<?php

namespace SimpleSolution\SimpleTradeBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use SimpleSolution\SimpleTradeBundle\Annotations\Permissions;
use SimpleSolution\SimpleTradeBundle\Model\AccountsModel;
use SimpleSolution\SimpleTradeBundle\Model\AccountsTypesModel;
use SimpleSolution\SimpleTradeBundle\Model\AuctionsModel;
use SimpleSolution\SimpleTradeBundle\Model\AuctionsSkillsModel;
use SimpleSolution\SimpleTradeBundle\Model\AuctionsTypesModel;
use SimpleSolution\SimpleTradeBundle\Model\AuctionsStatusesModel;
use SimpleSolution\SimpleTradeBundle\Model\ClarificationsModel;
use SimpleSolution\SimpleTradeBundle\Model\ClarificationsTypesModel;
use SimpleSolution\SimpleTradeBundle\Model\CurrenciesModel;
use SimpleSolution\SimpleTradeBundle\Model\DocumentsModel;
use SimpleSolution\SimpleTradeBundle\Model\DocumentsLinksModel;
use SimpleSolution\SimpleTradeBundle\Model\BidsModel;
use SimpleSolution\SimpleTradeBundle\Model\CompaniesSkillsModel;
use SimpleSolution\SimpleTradeBundle\Model\UsersModel;
use SimpleSolution\SimpleTradeBundle\Model\UsersCompaniesModel;
use SimpleSolution\SimpleTradeBundle\Model\SrosCompaniesModel;
use SimpleSolution\SimpleTradeBundle\Model\OffersModel;
use SimpleSolution\SimpleTradeBundle\Model\OffersStatusesModel;
use SimpleSolution\SimpleTradeBundle\Model\RobotsModel;
use SimpleSolution\SimpleTradeBundle\Model\RegionsModel;
use SimpleSolution\SimpleTradeBundle\Model\TariffsModel;
use SimpleSolution\SimpleTradeBundle\Model\SkillsModel;
use SimpleSolution\SimpleTradeBundle\Model\ProtocolsModel;
use SimpleSolution\SimpleTradeBundle\Model\ProtocolsTypesModel;
use SimpleSolution\SimpleTradeBundle\Model\ProtocolsCompanyValuesModel;
use SimpleSolution\SimpleTradeBundle\Form\auctions\AuctionsAddForm;
use SimpleSolution\SimpleTradeBundle\Form\trade\TradeClarificationsAnswerForm;
use SimpleSolution\SimpleTradeBundle\Form\trade\TradeClarificationsAuctionForm;
use SimpleSolution\SimpleTradeBundle\Form\trade\TradeClarificationsResultsForm;
use SimpleSolution\SimpleTradeBundle\Form\trade\TradeSearchAuctionForm;
use SimpleSolution\SimpleTradeBundle\Form\trade\TradeProtocolAuctionResultsForm;
use SimpleSolution\SimpleTradeBundle\Form\trade\TradeOfferAddForm;
use SimpleSolution\SimpleTradeBundle\Consts\Constants;

class TradeController extends Controller
{
    const TIME_TO_END = 10;     // Время до окончания с момента последней ставки в минутах
    const TIME_AFTER_END = 10;  // Время после окончания в течение которого можно сделать мин. ставку

    /**
     * @Route("/trade", name="trade")
     * @Permissions(perm="/trade")
     * @Template("SimpleTradeBundle:Trade:index.html.twig")
     */

    public function tradeAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $request = $this->get('request');
        $acl = $this->get('acl');
        $userCurrent = $this->get('security.context')->getToken()->getUser();

        $auctionsModel = new AuctionsModel($em, $session, $acl);

        $form = $this->createForm(new TradeSearchAuctionForm());

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $auctions = $auctionsModel->findAuction($form->getData());
            }
        }
        if (!isset($auctions)) {
            $auctions = $auctionsModel->findByStatusesWithContent(array( 'PUBLIC', 'NOT_TAKE_PLACE', 'STARTED', 'COMPLETED', 'CLOSED' ));
        }

        $auctionsPublicDate = $auctionsModel->getAuctionsPublicDateAsArray($auctions);

        $auctionsAll = array( );
        foreach( $auctions as $value ) {
            $auctionsAll[ ] = array(
                'object' => $value,
                'public_date' => $auctionsPublicDate[ $value->getId() ],
                'company' => $value->getCompany()
            );
        }

        $tariffModel = new TariffsModel($em, $session);
        $tariff = $tariffModel->findOneByName('AUCTION_ADD');

        $usersCompaniesModel = new UsersCompaniesModel($em, $session);
        $usersCompanies = $usersCompaniesModel->findOneByUserId($userCurrent->getId());

        $accountsModel = new AccountsModel($em, $session, $acl);
        $money = 0;
        if ($usersCompanies !== null) {
            $company = $usersCompanies->getCompany();
            $account = $accountsModel->findByCompanyId($company->getId());
            if ($account) {
                $money = $account->getContent()->getAccount();
            }
        }

        return array(
            'auctions' => $auctionsAll,
            'form' => $form->createView(),
            'tariff' => $tariff,
            'money' => $money
        );
    }

    /**
     * @Route("/trade/header/{id}", requirements={"id" = "\d+"}, name="trade_header")
     * @Template("SimpleTradeBundle:Trade:header.html.twig")
     */
    public function tradeHeaderAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $auctionsModel = new AuctionsModel($em, $session, $acl);
        $auctionsSkillsModel = new AuctionsSkillsModel($em, $session);

        $auction = $auctionsModel->findByPK($id);
        if (is_null($auction)) {
            throw new \Exception('Auction not exist.');
        }

        $skills = $auctionsSkillsModel->findSkillsByAuctionIdAsArray($id);

        return array(
            'auction' => $auction,
            'skills' => $skills
        );
    }

    /**
     * @Route("/trade/notice/create", name="trade_notice_create")
     * @Permissions(perm="/trade/notice/create")
     * @Template("SimpleTradeBundle:Trade:notice_create.html.twig")
     */
    public function tradeNoticeCreateAction()
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
                        'company' => $company,
                        'status' => $prepublicStatus ));

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

                return $this->redirect($this->generateUrl('trade'));
            }
        }

        return array(
            'form' => $form->createView(),
            'skills' => $skills,
            'company' => $company
        );
    }

    /**
     * @Route("/trade/notice/{id}", requirements={"id" = "\d+"}, name="trade_notice")
     * @Permissions(perm="/trade/notice")
     * @Template("SimpleTradeBundle:Trade:notice.html.twig")
     */
    public function tradeNoticeAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');
        $auctionsModel = new AuctionsModel($em, $session, $acl);
        $tariffModel = new TariffsModel($em, $session);
        $accountsModel = new AccountsModel($em, $session, $acl);

        $auction = $auctionsModel->findByPK($id);
        if (!isset($auction)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $userCurrent = $this->get('security.context')->getToken()->getUser();

        $tariff = $tariffModel->findOneByName('OFFER_ADD');

        $usersCompaniesModel = new UsersCompaniesModel($em, $session);
        $usersCompanies = $usersCompaniesModel->findOneByUserId($userCurrent->getId());
        if ($usersCompanies) {
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

        return array(
            'auction' => $auction,
            'tariff' => $tariff,
            'money' => $money,
            'user' => $userCurrent,
            'time_to_change_expired' => $this->timeToChangeAuctionExpired($auction)
        );
    }

    private function timeToChangeAuctionExpired($auction)
    {
        $now = new \DateTime();

        if ($auction->getContent()->getTradingForm()->getName() === 'AUCTION') {
            $expired = $auction->getContent()->getEndOffer();
        } elseif ($auction->getContent()->getTradingForm()->getName() === 'REQUEST_PROPOSALS') {
            $expired = $auction->getContent()->getDatetimeToAcceptOffers();
        }

        return ($now->getTimestamp() > $expired->getTimestamp());
    }

    /**
     * @Route("/trade/notice/{id}/edit", requirements={"id" = "\d+"}, name="trade_notice_edit")
     * @Permissions(perm="/trade/notice/edit")
     * @Template("SimpleTradeBundle:Trade:notice_edit.html.twig")
     */
    public function tradeNoticeEditAction($id)
    {
        $acl = $this->get('acl');
        $request = $this->get('request');
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');

        $auctionsModel = new AuctionsModel($em, $session, $acl);
        $auctionsTypesModel = new AuctionsTypesModel($em);
        $auctionsSkillsModel = new AuctionsSkillsModel($em, $session);
        $regionsModel = new RegionsModel($em);
        $currenciesModel = new CurrenciesModel($em);
        $documentsLinksModel = new DocumentsLinksModel($em, $session);
        $skillsModel = new SkillsModel($em);

        $auction = $auctionsModel->findByPK($id);

        if (!isset($auction)) {
            throw new \Exception('Auction not found');
        }
        if ($this->timeToChangeAuctionExpired($auction)) {
            throw new \Exception('Time to edit expired');
        }

        $userCurrent = $this->get('security.context')->getToken()->getUser();

        $regions = $regionsModel->findAll();
        $currencies = $currenciesModel->findAll();
        $auctionsTypes = $auctionsTypesModel->findAll();
        $allSkills = $skillsModel->findAllAsArray();
        $auctionSkillsArray = $auctionsSkillsModel->findSkillsIdByAuctionIdAsArray($id);
        $files = $documentsLinksModel->findAllByOwner($auction->getId(), 'AUCTION');

        $form = $this->createForm(new AuctionsAddForm($regions, $currencies, $auctionsTypes), $auction->getContent());
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {

                $formData = $form->getData();

                $em->getConnection()->beginTransaction();
                try {
                    $region = $regionsModel->findByPK($formData->getRegionId());
                    $currency = $currenciesModel->findByPK($formData->getCurrencyId());
                    $tradingForm = $auctionsTypesModel->findByPK($formData->getTradingFormId());

                    $a = array(
                        'tradingForm' => $tradingForm,
                        'internetAddress' => $formData->getInternetAddress(),
                        'noticeNumber' => md5($formData->getTitle() . rand()),
                        'title' => $formData->getTitle(),
                        'noticeLink' => $formData->getNoticeLink(),
                        'noticePrintform' => $formData->getNoticePrintform(),
                        'nomenclature' => $formData->getNomenclature(),
                        'companyName' => $formData->getCompanyName(),
                        'region' => $region,
                        'mail' => $formData->getMail(),
                        'email' => $formData->getEmail(),
                        'phones' => $formData->getPhones(),
                        'name' => $formData->getName(),
                        'contactName' => $formData->getContactName(),
                        'startPrice' => $formData->getStartPrice(),
                        'endPrice' => $formData->getEndPrice(),
                        'currency' => $currency,
                        'deliverySize' => $formData->getDeliverySize(),
                        'deliveryPlace' => $formData->getDeliveryPlace(),
                        'deliveryPeriod' => $formData->getDeliveryPeriod(),
                        'info' => $formData->getInfo(),
                        'endOffer' => $formData->getEndOffer(),
                        'endConsideration' => $formData->getEndConsideration(),
                        'startAuction' => $formData->getStartAuction(),
                        'placeToAcceptOffers' => $formData->getPlaceToAcceptOffers(),
                        'datetimeToAcceptOffers' => $formData->getDatetimeToAcceptOffers(),
                    );

                    if (in_array(Constants::ROLE_ADMIN, $userCurrent->getSystemRoles())) {
                        // Если админ то просто правка. Без изменения статуса и юзера

                        $auction = $auctionsModel->update($id, $a, array(
                            'status' => $auction->getStatus(),
                            'user' => $auction->getUser(),
                            'company' => $auction->getCompany() ));
                    } else {
                        // Если не админ, то меняем статус
                        $auctionsStatusesModel = new AuctionsStatusesModel($em);
                        $prepublicStatus = $auctionsStatusesModel->findOneByName('PRE_PUBLIC');

                        $auction = $auctionsModel->update($id, $a, array(
                            'user' => $userCurrent,
                            'company' => $auction->getCompany(),
                            'status' => $prepublicStatus ));
                    }

                    $auctionsSkillsModel->removeByAuctionId($id);

                    $attrsBySkill = array( );
                    $attrs = explode(',', $formData->getAttributes());
                    foreach( $attrs as $attr ) {
                        list($auctionid, $main, $dangerous) = explode('|', $attr);
                        $attrsBySkill[ $auctionid ][ 'main' ] = $main;
                        //$attrsBySkill[ $id ][ 'dangerous' ] = $dangerous;
                    }

                    $skills = explode(',', $formData->getSkills());
                    foreach( $skills as $skill ) {
                        $auctionsSkillsModel->create(array(
                            'auction' => $id,
                            'skill' => $skill,
                            'isMain' => $attrsBySkill[ $skill ][ 'main' ]
                        ));
                    }

                    $documentsModel = new DocumentsModel($em, $session, $acl);
                    foreach( $formData->getFiles() as $value ) {
                        $document = $documentsModel->create(array(
                            'file' => $value->getFile(),
                            'title' => $value->getTitle(),
                            ), array( 'isActive' => 1 ));

                        $documentsLinksModel->create(array(
                            'document' => $document,
                            'owner' => 'AUCTION',
                            'ownerId' => $id,
                        ));
                    }

                    $deletedFiles = explode(',', $formData->getDeletedFiles());
                    foreach( $deletedFiles as $file ) {
                        $documentsLinksModel->removeByDocumentId($file);
                    }

                    if (in_array(Constants::ROLE_ADMIN, $userCurrent->getSystemRoles())) {
                        /**
                         * @TODO: Система рассылает сообщения (внутренние и письма) об изменении условий аукциона всем участникам
                         */
                    }

                    $em->getConnection()->commit();
                } catch( \Exception $e ) {
                    $em->getConnection()->rollback();
                    $em->close();
                    throw $e;
                }

                $session->getFlashBag()->add('notice', 'Аукцион изменён. Извещение отправлено администратору для проверки.');

                return $this->redirect($this->generateUrl('trade'));
            }
        }

        return array(
            'auction' => $auction,
            'form' => $form->createView(),
            'all_skills' => $allSkills,
            'auction_skills' => $auctionSkillsArray,
            'files' => $files
        );
    }

    /**
     * @Route("/trade/notice/{id}/refusal", requirements={"id" = "\d+"}, name="trade_notice_refusal")
     * @Permissions(perm="/trade/notice/refusal")
     * @Template()
     */
    public function tradeNoticeRefusalAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');
        $factory = $this->get('security.encoder_factory');
        $request = $this->get('request');

        $em->getConnection()->beginTransaction();
        try {

            $auctionsModel = new AuctionsModel($em, $session, $acl);
            $usersModel = new UsersModel($em, $session, $factory);
            $auctionsStatusesModel = new AuctionsStatusesModel($em);

            $auction = $auctionsModel->findByPK($id);

            if (!isset($auction)) {
                throw new \Exception('Auction not found');
            }
            if ($this->timeToChangeAuctionExpired($auction)) {
                throw new \Exception('Time to refusal expired');
            }

            $status = $auctionsStatusesModel->findOneByName('NOT_TAKE_PLACE');
            $content = $auction->getContent()->getAllFieldsAsArray();

            $auctionsModel->update($id, $content, array(
                'status' => $status,
                'user' => $auction->getUser(),
                'company' => $auction->getCompany() ));

            $comment = '';
            if ($request->request->get('comment', false) !== false) {
                $comment = $request->request->get('comment');
            }

            $performers = $usersModel->findParticipantsAuction($id);
            foreach( $performers as $performer ) {
                $this->get('mail')->sendByUser(
                    Constants::MAIL_2COMPANY_REFUSAL_AUCTION, $performer, array( 'user' => $performer, 'auction' => $auction, 'comment' => $comment )
                );
            }

            $em->getConnection()->commit();
        } catch( \Exception $e ) {
            $em->getConnection()->rollback();
            $em->close();
            throw $e;
        }

        $session->getFlashBag()->add('notice', 'Аукцион отменён. Разослано ' . count($performers) . ' писем');

        return $this->redirect($this->generateUrl('trade'));
    }

    /**
     * @Route("/trade/clarifications/auction/{id}/request", requirements={"id" = "\d+"}, name="trade_clarifications_auction_request")
     * @Permissions(perm="/trade/clarifications/auction/request")
     * @Template("SimpleTradeBundle:Trade:clarifications_auction_request.html.twig")
     */
    public function tradeClarificationsAuctionRequestAction($id)
    {
        $acl = $this->get('acl');
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $request = $this->get('request');

        $clarificationsModel = new ClarificationsModel($em, $session, $acl);
        $usersCompaniesModel = new UsersCompaniesModel($em, $session);

        $userCurrent = $this->get('security.context')->getToken()->getUser();
        $userCompany = $usersCompaniesModel->findOneByUserId($userCurrent->getId())->getCompany();

        $allClarifications = $clarificationsModel->findByAuctionId($id);

        $auctionsModel = new AuctionsModel($em, $session, $acl);
        $auction = $auctionsModel->findByPK($id);
        if (!isset($auction)) {
            return $this->redirect($this->generateUrl('login'));
        }

        $form = $this->createForm(new TradeClarificationsAuctionForm());

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {

                $formData = $form->getData();

                $em->getConnection()->beginTransaction();
                try {

                    $clarificationsTypesModel = new ClarificationsTypesModel($em);

                    $customer = $auction->getUser();
                    $type = $clarificationsTypesModel->findOneByName('REQUEST_TO_AUCTION');

                    $content = array(
                        'subject' => $formData[ 'subject' ],
                        'body' => $formData[ 'body' ]
                    );

                    $more = array(
                        'fromUser' => $userCurrent,
                        'toUser' => $customer,
                        'auction' => $auction,
                        'type' => $type,
                        'createdAt' => new \DateTime()
                    );

                    $clarification = $clarificationsModel->create($content, $more);

                    $documentsModel = new DocumentsModel($em, $session, $acl);
                    $documentsLinksModel = new DocumentsLinksModel($em, $session);

                    foreach( $formData[ 'files' ] as $value ) {
                        $document = $documentsModel->create(array( 'file' => $value->getFile(),
                            'title' => $value->getTitle(),
                            ), array( 'isActive' => 0 ));

                        $documentsLinksModel->create(array( 'document' => $document,
                            'owner' => 'CLARIFICATION',
                            'ownerId' => $clarification->getId(),
                        ));
                    }

                    $em->getConnection()->commit();

                    /**
                     * @TODO: Отправить сообщения заказчику
                     * @TODO: Отправить письмо заказчику
                     */
                    // $this->get('mail')->sendByTemplateId(Constants::MAIL_2USER_REGISTRATION_CONFIRMATION, $this->get('users')->findAllByCompanyIdAndRole($company->getId(), Constants::ROLE_COMPANY_ADMIN), array( 'company' => $company, ));
                } catch( \Exception $e ) {
                    $em->getConnection()->rollback();
                    $em->close();
                    throw $e;
                }

                $notice = $this->get('translator')->trans('notices.request_for_clarification_sent', array( ), 'SimpleSolutionSimpleTradeBundle');
                $session->getFlashBag()->add('notice', $notice);

                return $this->redirect($this->generateUrl('trade_clarifications_auction_request', array( 'id' => $id )));
            }
        }

        return array(
            'form' => $form->createView(),
            'user_company' => $userCompany,
            'all_clarifications' => $allClarifications,
            'auction' => $auction,
            'formAnswer' => $this->createForm(new TradeClarificationsAnswerForm())->createView()
        );
    }

    /**
     * @Route("/trade/clarification/{id}/file/{idFile}", requirements={"id" = "\d+", "idFile" = "\d+"}, name="trade_clarification_get_doc")
     * @Permissions(perm="/trade")
     * @Template()
     */
    public function tradeClarificationGetDocAction($id, $idFile)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');

        $documentsLinksModel = new DocumentsLinksModel($em, $session);

        $file = $documentsLinksModel->findDocument($idFile, $id, 'CLARIFICATION');

        if ($file) {

            $full = realpath($this->container->getParameter('uploads_documents_dir') . $file->getContent()->getFilename());

            if (is_file($full)) {
                $response = new Response();
                $response->headers->set('Content-Length', filesize($full));
                $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($full) . '"');
                $response->headers->set('Pragma', 'no-cache');
                $response->headers->set('Expires', '0');
                $response->setContent(file_get_contents($full));

                return $response;
            }
        }

        throw new \Exception('File not found.');
    }

    /**
     * @Route("/trade/offer/{id}", requirements={"id" = "\d+"}, name="trade_offer")
     * @Permissions(perm="/trade/offer")
     * @Template("SimpleTradeBundle:Trade:offer.html.twig")
     */
    public function tradeOfferAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');
        $request = $this->get('request');

        $auctionsModel = new AuctionsModel($em, $session, $acl);
        $auction = $auctionsModel->findByPK($id);
        if (!isset($auction)) {
            return $this->redirect($this->generateUrl('login'));
        }
        if ($auction->getContent()->getTradingForm()->getName() != 'AUCTION') {
            return $this->redirect($this->generateUrl('trade_notice', array( 'id' => $id )));
        }

        $userCurrent = $this->get('security.context')->getToken()->getUser();
        $usersCompaniesModel = new UsersCompaniesModel($em, $session);
        $usersCompany = $usersCompaniesModel->findOneByUserId($userCurrent->getId());
        $company = $usersCompany->getCompany();

        $offersModel = new OffersModel($em, $session, $acl);
        $offer = $offersModel->findOneByCompanyIdAndAuctionId($company->getId(), $id);

        $endOfferTime = $auction->getContent()->getEndOffer()->getTimestamp();
        $nowTime = time();
        $timeToEnd = $endOfferTime - $nowTime;

        $form = $this->createForm(new TradeOfferAddForm());
        if ($offer) {

            $documentsLinksModel = new DocumentsLinksModel($em, $session);
            $files = $documentsLinksModel->findAllByOwner($offer->getId(), 'OFFER');
        } else {

            if ($request->isMethod('POST')) {
                $form->bind($request);
                if ($form->isValid()) {
                    $formData = $form->getData();
                    $em->getConnection()->beginTransaction();
                    try {
                        $offersStatusesModel = new OffersStatusesModel($em);
                        $offerOnConsiderationStatus = $offersStatusesModel->findOneByName('ON_CONSIDERATION');

                        $offer = $offersModel->create(
                            array( ), array( 'company' => $company, 'auction' => $auction, 'status' => $offerOnConsiderationStatus )
                        );

                        $documentsModel = new DocumentsModel($em, $session, $acl);
                        $documentsLinksModel = new DocumentsLinksModel($em, $session);

                        foreach( $formData[ 'files' ] as $value ) {
                            $document = $documentsModel->create(array( 'file' => $value->getFile(),
                                'title' => $value->getTitle(),
                                ), array( 'isActive' => 1 ));

                            $documentsLinksModel->create(array( 'document' => $document,
                                'owner' => 'OFFER',
                                'ownerId' => $offer->getId(),
                            ));
                        }

                        $tariffModel = new TariffsModel($em, $session);
                        $tariff = $tariffModel->findOneByName('OFFER_ADD');

                        $accountsTypesModel = new AccountsTypesModel($em);
                        $removeType = $accountsTypesModel->findOneByName('REMOVE');

                        $accountsModel = new AccountsModel($em, $session, $acl);
                        $account = $accountsModel->findByCompanyId($company->getId());
                        $accountsModel->update($account->getId(), array( 'account' => $account->getContent()->getAccount() - $tariff->getCost(),
                            'tariff' => $tariff,
                            'changes' => abs($tariff->getCost()),
                            'comment' => ''
                            ), array( 'company' => $company,
                            'user' => $userCurrent,
                            'type' => $removeType )
                        );

                        $em->getConnection()->commit();
                    } catch( Exception $e ) {
                        $em->getConnection()->rollback();
                        $em->close();
                        throw $e;
                    }

                    $session->getFlashBag()->add('notice', 'Заявка успешно отправлена');

                    return $this->redirect($this->generateUrl('trade_offer', array( 'id' => $id )));
                }
            }

            $srosCompaniesModel = new SrosCompaniesModel($em, $session);
            $sros = $srosCompaniesModel->findAllByCompanyId($company->getId());
        }

        if ($usersCompany !== null) {
            $accountsModel = new AccountsModel($em, $session, $acl);
            $account = $accountsModel->findByCompanyId($company->getId());
            if ($account) {
                $money = $account->getContent()->getAccount();
            } else {
                $money = 0;
            }
        } else {
            $money = 0;
        }
        $tariffModel = new TariffsModel($em, $session);
        $tariff = $tariffModel->findOneByName('OFFER_ADD');

        $protocolsModel = new ProtocolsModel($em, $session, $acl);
        $protocol = $protocolsModel->getByAuctionIdWithDocuments($id, ProtocolsModel::REVIEW_OFFERS);
        $offersForProtocol = array( );
        if (isset($protocol)) {
            $offers = $offersModel->findAllByAuctionId($id);
            foreach( $offers as $offer ) {
                $title = $offer->getCompany()->getContent()->getTitle();
                if (isset($offersForProtocol[ $title ])) {
                    if ($offersForProtocol[ $title ]) {
                        continue;
                    }
                }
                $offersForProtocol[ $title ] = ($offer->getStatus()->getName() == 'ADMITTED_TO_PARTICIPATION') ? true : false;
            }
        }

        $usersCompanies = $usersCompaniesModel->findOneByUserId($auction->getUser()->getId());
        $auctionCompany = $usersCompanies->getCompany();

        return array(
            'auction' => $auction,
            'offer' => isset($offer) ? $offer : null,
            'files' => isset($files) ? $files : array( ),
            'company' => $company,
            'user' => $userCurrent,
            'form' => isset($form) ? $form->createView() : null,
            'sros' => isset($sros) ? $sros : array( ),
            'money' => $money,
            'tariff' => $tariff,
            'timeToEnd' => $timeToEnd,
            'protocol' => $protocol,
            'offersForProtocol' => $offersForProtocol,
            'auctionCompany' => $auctionCompany
        );
    }

    /**
     * @Route("/trade/offers/{id}", requirements={"id" = "\d+"}, name="trade_offers")
     * @Permissions(perm="/trade/offers")
     * @Template("SimpleTradeBundle:Trade:offers.html.twig")
     */
    public function tradeOffersAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');
        $request = $this->get('request');
        $userCurrent = $this->get('security.context')->getToken()->getUser();

        $auctionsModel = new AuctionsModel($em, $session, $acl);
        $clarificationsModel = new ClarificationsModel($em, $session, $acl);
        $protocolsModel = new ProtocolsModel($em, $session, $acl);

        $auction = $auctionsModel->findByPK($id);
        if (is_null($auction)) {
            throw new \Exception('Auction not exist.');
        }

        $offersModel = new OffersModel($em, $session, $acl);
        $offers = $offersModel->findAllByAuctionId($id);
        $ids = array( );
        foreach( $offers as $offer ) {
            $ids[ ] = $offer->getId();
        }

        $documentsLinksModel = new DocumentsLinksModel($em, $session);
        $files = $documentsLinksModel->findAllByOwners($ids, 'OFFER');

        $protocol = $protocolsModel->getByAuctionIdWithDocuments($id, ProtocolsModel::REVIEW_OFFERS);

        if (is_null($protocol) &&
            in_array(Constants::ROLE_CUSTOMER, $userCurrent->getSystemRoles())) {
            // Если это заказчик и нет протокола, надо вывести форму
            $protocolForm = $this->createForm(new TradeProtocolAuctionResultsForm());

            if ($request->isMethod('POST') && $request->request->get('protocol', false)) {
                $protocolForm->bind($request);
                if ($protocolForm->isValid()) {
                    $formData = $protocolForm->getData();

                    $em->getConnection()->beginTransaction();
                    try {

                        $protocolsTypesModel = new ProtocolsTypesModel($em);
                        $type = $protocolsTypesModel->findOneByName('REVIEW_OFFERS');

                        $content = array(
                            'placeView' => $formData[ 'placeView' ],
                            'datetimeStartView' => $formData[ 'datetimeStartView' ],
                            'datetimeEndView' => $formData[ 'datetimeEndView' ],
                            'fullName1' => $formData[ 'fullName1' ],
                            'fullName2' => $formData[ 'fullName2' ],
                            'fullName3' => $formData[ 'fullName3' ],
                            'position1' => $formData[ 'position1' ],
                            'position2' => $formData[ 'position2' ],
                            'position3' => $formData[ 'position3' ],
                        );

                        $more = array(
                            'user' => $userCurrent,
                            'auction' => $auction,
                            'type' => $type,
                            'createdAt' => new \DateTime()
                        );

                        $protocol = $protocolsModel->create($content, $more);

                        $documentsModel = new DocumentsModel($em, $session, $acl);
                        $documentsLinksModel = new DocumentsLinksModel($em, $session);

                        foreach( $formData[ 'files' ] as $value ) {
                            $document = $documentsModel->create(array(
                                'file' => $value->getFile(),
                                'title' => $value->getTitle(),
                                ), array( 'isActive' => 0 ));

                            $documentsLinksModel->create(array(
                                'document' => $document,
                                'owner' => 'PROTOCOL',
                                'ownerId' => $protocol->getId(),
                            ));
                        }

                        $em->getConnection()->commit();
                    } catch( \Exception $e ) {
                        $em->getConnection()->rollback();
                        $em->close();
                        throw $e;
                    }

                    /* @TODO
                     * Система рассылает уведомления всем компаниям-участникам
                     */

                    $session->getFlashBag()->add('notice', 'Протокол рассмотрения заявок на аукцион опубликован.');

                    return $this->redirect($this->generateUrl('trade_offers', array( 'id' => $id )));
                }
            }
        }

        $endOfferTime = $auction->getContent()->getEndOffer()->getTimestamp();
        $nowTime = time();
        $timeToEndOffer = $endOfferTime - $nowTime;

        $endConsiderationTime = $auction->getContent()->getEndConsideration()->getTimestamp();
        $timeToEndConsideration = $endConsiderationTime - $nowTime;

        $offersForProtocol = array( );
        foreach( $offers as $offer ) {
            $title = $offer->getCompany()->getContent()->getTitle();
            if (isset($offersForProtocol[ $title ])) {
                if ($offersForProtocol[ $title ]) {
                    continue;
                }
            }
            $offersForProtocol[ $title ] = ($offer->getStatus()->getName() == 'ADMITTED_TO_PARTICIPATION') ? true : false;
        }

        $usersCompaniesModel = new UsersCompaniesModel($em, $session);
        $usersCompanies = $usersCompaniesModel->findOneByUserId($auction->getUser()->getId());
        $auctionCompany = $usersCompanies->getCompany();

        return array(
            'auction' => $auction,
            'protocol' => $protocol,
            'protocol_form' => isset($protocolForm) ? $protocolForm->createView() : null,
            'timeToEndOffer' => $timeToEndOffer,
            'timeToEndConsideration' => $timeToEndConsideration,
            'offers' => $offers,
            'files' => $files,
            'offersForProtocol' => $offersForProtocol,
            'auctionCompany' => $auctionCompany
        );
    }

    /**
     * @Route("/trade/protocols/{id}/file/{idFile}", requirements={"id" = "\d+", "idFile" = "\d+"}, name="trade_protocols_get_doc")
     * @Permissions(perm="/trade")
     * @Template()
     */
    public function tradeProtocolsGetDocAction($id, $idFile)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');

        $documentsLinksModel = new DocumentsLinksModel($em, $session);

        $file = $documentsLinksModel->findDocument($idFile, $id, 'PROTOCOL');

        if ($file) {

            $full = realpath($this->container->getParameter('uploads_documents_dir') . $file->getContent()->getFilename());

            if (is_file($full)) {
                $response = new Response();
                $response->headers->set('Content-Length', filesize($full));
                $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($full) . '"');
                $response->headers->set('Pragma', 'no-cache');
                $response->headers->set('Expires', '0');
                $response->setContent(file_get_contents($full));

                return $response;
            }
        }

        throw new \Exception('File not found.');
    }

    /**
     * @Route("/trade/room/{id}", requirements={"id" = "\d+"}, name="trade_room")
     * @Permissions(perm="/trade/room")
     * @Template("SimpleTradeBundle:Trade:room.html.twig")
     */
    public function tradeRoomAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');
        $userCurrent = $this->get('security.context')->getToken()->getUser();

        $auctionsModel = new AuctionsModel($em, $session, $acl);
        $auctionsSkillsModel = new AuctionsSkillsModel($em, $session);
        $bidsModel = new BidsModel($em);
        $robotsModel = new RobotsModel($em, $session, $acl);

        $auction = $auctionsModel->findByPK($id);
        if (is_null($auction)) {
            throw new \Exception('Auction not exist.');
        }
        if ($auction->getStatus()->getName() === 'COMPLETED' && !$this->auctionCompletedNotPassedNMinutes($id)) {
            return $this->redirect($this->generateUrl('trade_room_results', array( 'id' => $id )));
        }
        if ($auction->getContent()->getTradingForm()->getName() != 'AUCTION') {
            return $this->redirect($this->generateUrl('trade_notice', array( 'id' => $id )));
        }

        $skills = $auctionsSkillsModel->findSkillsByAuctionIdAsArray($id);
        $bids = $bidsModel->findByAuctionIdAsArray($id, $userCurrent);

        $bestPrice = $bidsModel->getBestPriceByAuctionId($id);
        if (is_null($bestPrice)) {
            $bestPrice = $auction->getContent()->getStartPrice();
        }

        $lastPrice = $bidsModel->getAuctionLastPriceByUser($id, $userCurrent->getId());
        $position = $bidsModel->getPositionUser($id, $userCurrent->getId());
        $blocked = (floatval($lastPrice) === floatval($auction->getContent()->getEndPrice())) ? true : false;
        $robot = $robotsModel->findByUserIdAndAuctionId($userCurrent->getId(), $id);
        $timeToEnd = $this->getTimeToEnd($auction);

        return array(
            'access' => $this->checkAccessToAuction($id),
            'blocked' => $blocked,
            'robot' => $robot,
            'auction_id' => $id,
            'auction' => $auction,
            'skills' => $skills,
            'bids' => $bids,
            'best_price' => $bestPrice,
            'last_price' => $lastPrice,
            'position' => $position,
            'time_to_end' => $timeToEnd
        );
    }

    private function getTimeToEnd($auction)
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $bidsModel = new BidsModel($em);

        $timeToEnd = '00:00';
        if ($auction->getStatus()->getName() === 'STARTED') {
            $lastDateTime = $bidsModel->getDateTimeLastBidByAuctionId($auction->getId());
            if (is_null($lastDateTime)) {
                $lastDateTime = $auction->getContent()->getStartAuction();
            }
            $timeLast = $lastDateTime->getTimestamp();
            $timeNow = time();
            $timeEnd = $timeLast + (60 * self::TIME_TO_END);
            $timeStill = $timeEnd - $timeNow;
            if ($timeStill > 0) {
                $minutes = ( int ) floor($timeStill / 60);
                $seconds = $timeStill - ($minutes * 60);

                $minutes = $minutes < 10 ? '0' . $minutes : $minutes;
                $seconds = $seconds < 10 ? '0' . $seconds : $seconds;

                $timeToEnd = $minutes . ':' . $seconds;
            }
        }

        return array(
            'status' => $auction->getStatus()->getName(),
            'time' => $timeToEnd
        );
    }

    /**
     * @Route("/trade/room/update/{id}", requirements={"id" = "\d+"}, name="trade_room_update")
     * @Permissions(perm="/trade/room")
     * @Template()
     */
    public function tradeRoomUpdateAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $em->getConnection()->beginTransaction();
        try {

            $session = $this->get('session');
            $acl = $this->get('acl');
            $userCurrent = $this->get('security.context')->getToken()->getUser();

            $bidsModel = new BidsModel($em);
            $auctionsModel = new AuctionsModel($em, $session, $acl);
            $robotsModel = new RobotsModel($em, $session, $acl);

            $auction = $auctionsModel->findByPK($id);
            $bidsArr = $bidsModel->findByAuctionIdAsArray($id, $userCurrent);

            $bestPrice = $bidsModel->getBestPriceByAuctionId($id);
            if (is_null($bestPrice)) {
                $bestPrice = $auction->getContent()->getStartPrice();
            }

            $position = $bidsModel->getPositionUser($id, $userCurrent->getId());
            $timeToEnd = $this->getTimeToEnd($auction);
            $lastPrice = $bidsModel->getAuctionLastPriceByUser($id, $userCurrent->getId());
            $robot = $robotsModel->findByUserIdAndAuctionId($userCurrent->getId(), $id);

            $answer = array(
                'bids' => $bidsArr,
                'best_price' => $bestPrice,
                'last_price' => $lastPrice,
                'position' => $position,
                'time_to_end' => $timeToEnd,
                'robot_is_active' => is_object($robot) ? $robot->getIsActive() : false,
                'access' => $this->checkAccessToAuction($id),
            );

            $em->getConnection()->commit();
        } catch( \Exception $e ) {
            $em->getConnection()->rollback();
            $em->close();
            $answer = array( 'error' => $e->getMessage() );
        }

        $response = new Response(json_encode($answer));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/trade/room/sendprice/{id}", requirements={"id" = "\d+"}, name="trade_room_sendprice")
     * @Method({"POST"})
     * @Permissions(perm="/trade/room")
     * @Template()
     */
    public function tradeRoomSendpriceAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $em->getConnection()->beginTransaction();
        try {

            $request = $this->get('request');

            $price = $request->get('price', false);

            if ($price === false) {
                throw new \Exception('No data!');
            }
            if (!$this->checkAccessToAuction($id)) {
                throw new \Exception('No access to the auction!');
            }

            $price = floatval($price);

            $session = $this->get('session');
            $acl = $this->get('acl');
            $userCurrent = $this->get('security.context')->getToken()->getUser();

            $bidsModel = new BidsModel($em);
            $usersCompaniesModel = new UsersCompaniesModel($em, $session);
            $auctionsModel = new AuctionsModel($em, $session, $acl);

            $auction = $auctionsModel->findByPK($id);

            $oldBestPrice = $bidsModel->getBestPriceByAuctionId($id);
            if (is_null($oldBestPrice)) {
                $oldBestPrice = $auction->getContent()->getStartPrice();
            }
            $oldBestPrice = floatval($oldBestPrice);

            $lastPrice = $bidsModel->getAuctionLastPriceByUser($id, $userCurrent->getId());

            if ($this->auctionCompletedNotPassedNMinutes($id) &&
                $price !== floatval($auction->getContent()->getEndPrice())) {
                // Если аукцион закончился, но не прошло 10и минут и цена не равна минимальной
                throw new \Exception('auction_completed_not_price_not_minimal');
            }
            if (floatval($lastPrice) === floatval($auction->getContent()->getEndPrice())) {
                // Юзер уже выставил минимальную цену контракта
                throw new \Exception('minimal_price_saved');
            }
            if (($price > $oldBestPrice) ||
                ($price === $oldBestPrice && $oldBestPrice !== floatval($auction->getContent()->getEndPrice()))) {
                // Если цена больше последней цены контракта
                throw new \Exception('more_recent_prices');
            }
            if ($price < floatval($auction->getContent()->getEndPrice())) {
                // Если цена ниже минимальной цены контракта
                throw new \Exception('price_below_minimum');
            }

            $company = $usersCompaniesModel->findOneByUserId($userCurrent->getId())->getCompany();

            $bidEntity = $bidsModel->create(array(
                'current_bid' => $oldBestPrice - $price,
                'best_price' => $price,
                'user' => $userCurrent,
                'company' => $company,
                'auction' => $auction,
                'robot' => null
                ));

            $blocked = ($price === floatval($auction->getContent()->getEndPrice())) ? true : false;
            $timeToEnd = $this->getTimeToEnd($auction);
            $bids = $bidsModel->findByAuctionIdAsArray($id, $userCurrent);
            $position = $bidsModel->getPositionUser($id, $userCurrent->getId());

            $answer = array(
                'bids' => $bids,
                'best_price' => $price,
                'blocked' => $blocked,
                'time_to_end' => $timeToEnd,
                'position' => $position
            );

            $em->getConnection()->commit();
        } catch( \Exception $e ) {
            $em->getConnection()->rollback();
            $em->close();
            $answer = array( 'error' => $e->getMessage() );
        }

        $response = new Response(json_encode($answer));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    private function checkAccessToAuction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');
        $userCurrent = $this->get('security.context')->getToken()->getUser();

        $auctionsModel = new AuctionsModel($em, $session, $acl);
        $offersModel = new OffersModel($em, $session, $acl);
        $usersCompaniesModel = new UsersCompaniesModel($em, $session);

        $company = $usersCompaniesModel->findOneByUserId($userCurrent->getId())->getCompany();
//        $companyUsers = $usersCompaniesModel->findAllUsersByCompanyId($company->getId());
//        $companyUsersIds = array( );
//        foreach( $companyUsers as $user ) {
//            $companyUsersIds[ ] = $user->getId();
//        }

        if (!$offersModel->checkByCompanyIdAuctionIdStatus($company->getId(), $id, 'ADMITTED_TO_PARTICIPATION')) {
            // Если нет подтверждённой заявки
            return false;
        }

        if ($auctionsModel->findByPK($id)->getStatus()->getName() === 'STARTED') {
            // Если аукцион начался
            return true;
        }
        if ($this->auctionCompletedNotPassedNMinutes($id)) {
            // Если аукцион закончился и прошло не более 10-и минут
            return true;
        }

        return false;
    }

    private function auctionCompletedNotPassedNMinutes($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');
        $userCurrent = $this->get('security.context')->getToken()->getUser();

        $auctionsModel = new AuctionsModel($em, $session, $acl);
        $auction = $auctionsModel->findByPK($id);
        $status = $auction->getStatus()->getName();
        $dateTime = $auction->getCreatedAt();

        $auctionTimestamp = $dateTime->getTimestamp();
        $nowTimestamp = time();

        if (($nowTimestamp - $auctionTimestamp) <= self::TIME_AFTER_END * 60 && $auction->getStatus()->getName() === 'COMPLETED') {
            return true;
        }

        return false;
    }

    /**
     * @Route("/trade/robot/start/{id}", requirements={"id" = "\d+"}, name="trade_robot_start")
     * @Permissions(perm="/trade/room")
     * @Method({"POST"})
     * @Template()
     */
    public function tradeRobotStartAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $em->getConnection()->beginTransaction();
        try {

            if (!$this->checkAccessToAuction($id)) {
                throw new \Exception('No access to the auction!');
            }
            if ($this->auctionCompletedNotPassedNMinutes($id)) {
                throw new \Exception('The auction was over!');
            }

            $request = $this->get('request');

            $bid = $request->request->get('bid', false);
            $minimum = $request->request->get('minimum', false);

            if ($bid === false || $minimum === false) {
                throw new \Exception('No data!');
            }

            $bid = floatval($bid);
            $minimum = floatval($minimum);

            $session = $this->get('session');
            $acl = $this->get('acl');
            $userCurrent = $this->get('security.context')->getToken()->getUser();

            $auctionsModel = new AuctionsModel($em, $session, $acl);
            $bidsModel = new BidsModel($em);
            $robotsModel = new RobotsModel($em, $session, $acl);
            $usersCompaniesModel = new UsersCompaniesModel($em, $session);

            $auction = $auctionsModel->findByPK($id);
            $company = $usersCompaniesModel->findOneByUserId($userCurrent->getId())->getCompany();
            $robot = $robotsModel->findByUserIdAndAuctionId($userCurrent->getId(), $id);

            $bestPrice = $bidsModel->getBestPriceByAuctionId($id);
            if (is_null($bestPrice)) {
                $bestPrice = $auction->getContent()->getStartPrice();
            }
            $bestPrice = floatval($bestPrice);

            if ($minimum < floatval($auction->getContent()->getEndPrice())) {
                throw new \Exception('minimum_very_small');
            }

            if ($minimum >= $bestPrice) {
                throw new \Exception('minimum_very_big');
            }

            $content = array(
                'bid_size' => $bid,
                'deadline' => $minimum,
                'bid_time' => null
            );

            $more = array(
                'user' => $userCurrent,
                'auction' => $auction,
                'company' => $company,
                'createdAt' => new \DateTime(),
                'isActive' => true,
            );

            if (is_null($robot)) {
                // Робота нет, надо создать
                $robotsModel->create($content, $more);
            } else {
                // Робот есть, надо обновить
                $robotsModel->update($robot->getId(), $content, $more);
            }

            $em->getConnection()->commit();

            $status = 'ok';
        } catch( \Exception $e ) {
            $em->getConnection()->rollback();
            $em->close();
            $status = $e->getMessage();
        }

        $response = new Response(json_encode(array(
                    'status' => $status
                )));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/trade/robot/stop/{id}", requirements={"id" = "\d+"}, name="trade_robot_stop")
     * @Permissions(perm="/trade/room")
     * @Template()
     */
    public function tradeRobotStopAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $em->getConnection()->beginTransaction();
        try {

            if (!$this->checkAccessToAuction($id)) {
                throw new \Exception('No access to the auction!');
            }
            if ($this->auctionCompletedNotPassedNMinutes($id)) {
                throw new \Exception('The auction was over!');
            }

            $session = $this->get('session');
            $acl = $this->get('acl');
            $userCurrent = $this->get('security.context')->getToken()->getUser();

            $robotsModel = new RobotsModel($em, $session, $acl);

            $robot = $robotsModel->findByUserIdAndAuctionId($userCurrent->getId(), $id);

            if (is_null($robot)) {
                throw new \Exception('no_robot');
            }
            if ($robot->getIsActive() === false) {
                throw new \Exception('robot_not_active');
            }

            $more = array(
                'user' => $userCurrent,
                'auction' => $robot->getAuction(),
                'company' => $robot->getCompany(),
                'createdAt' => new \DateTime(),
                'isActive' => false,
            );

            $robotsModel->update($robot->getId(), null, $more);

            $em->getConnection()->commit();

            $status = 'ok';
        } catch( \Exception $e ) {
            $em->getConnection()->rollback();
            $em->close();
            $status = $e->getMessage();
        }

        $response = new Response(json_encode(array(
                    'status' => $status
                )));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/trade/room/{id}/results", requirements={"id" = "\d+"}, name="trade_room_results")
     * @Permissions(perm="/trade/room")
     * @Template("SimpleTradeBundle:Trade:room_results.html.twig")
     */
    public function tradeRoomResultsAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');
        $request = $this->get('request');
        $userCurrent = $this->get('security.context')->getToken()->getUser();

        $auctionsModel = new AuctionsModel($em, $session, $acl);
        $clarificationsModel = new ClarificationsModel($em, $session, $acl);
        $protocolsModel = new ProtocolsModel($em, $session, $acl);

        $auction = $auctionsModel->findByPK($id);

        if (is_null($auction)) {
            throw new \Exception('Auction not exist.');
        }
        if ($auction->getStatus()->getName() !== 'COMPLETED') {
            return $this->redirect($this->generateUrl('trade_room', array( 'id' => $id )));
        }

        $bidsModel = new BidsModel($em);
        $results = $bidsModel->getResultsAuctionAsArray($id);
        $history = $bidsModel->getHistoryAuction($id, $results[ 0 ][ 'bid' ]);

        $protocol = $protocolsModel->getByAuctionIdWithDocuments($id, ProtocolsModel::AUCTION_RESULTS);

        if (is_null($protocol) && in_array(Constants::ROLE_CUSTOMER, $userCurrent->getSystemRoles())) {
            // Если это заказчик и нет протокола, надо вывести форму
            $protocolForm = $this->createForm(new TradeProtocolAuctionResultsForm());

            if ($request->isMethod('POST') && $request->request->get('protocol', false)) {
                $protocolForm->bind($request);
                if ($protocolForm->isValid()) {
                    $formData = $protocolForm->getData();

                    $em->getConnection()->beginTransaction();
                    try {

                        $protocolsTypesModel = new ProtocolsTypesModel($em);
                        $type = $protocolsTypesModel->findOneByName('AUCTION_RESULTS');

                        $content = array(
                            'placeView' => $formData[ 'placeView' ],
                            'datetimeStartView' => $formData[ 'datetimeStartView' ],
                            'datetimeEndView' => $formData[ 'datetimeEndView' ],
                            'fullName1' => $formData[ 'fullName1' ],
                            'fullName2' => $formData[ 'fullName2' ],
                            'fullName3' => $formData[ 'fullName3' ],
                            'position1' => $formData[ 'position1' ],
                            'position2' => $formData[ 'position2' ],
                            'position3' => $formData[ 'position3' ],
                        );

                        $more = array(
                            'user' => $userCurrent,
                            'auction' => $auction,
                            'type' => $type,
                            'createdAt' => new \DateTime()
                        );

                        $protocol = $protocolsModel->create($content, $more);

                        $protocolsCompanyValuesModel = new ProtocolsCompanyValuesModel($em);
                        foreach( $results as $value ) {
                            $protocolsCompanyValuesModel->create(array(
                                'company' => $value[ 'bid' ]->getCompany(),
                                'value' => $value[ 'position' ],
                                'protocolContent' => $protocol->getContent()
                            ));
                        }

                        $documentsModel = new DocumentsModel($em, $session, $acl);
                        $documentsLinksModel = new DocumentsLinksModel($em, $session);

                        foreach( $formData[ 'files' ] as $value ) {
                            $document = $documentsModel->create(array(
                                'file' => $value->getFile(),
                                'title' => $value->getTitle(),
                                ), array( 'isActive' => 0 ));

                            $documentsLinksModel->create(array(
                                'document' => $document,
                                'owner' => 'PROTOCOL',
                                'ownerId' => $protocol->getId(),
                            ));
                        }

                        $em->getConnection()->commit();
                    } catch( \Exception $e ) {
                        $em->getConnection()->rollback();
                        $em->close();
                        throw $e;
                    }

                    $session->getFlashBag()->add('notice', 'Протокол подведения итогов аукциона опубликован.');

                    return $this->redirect($this->generateUrl('trade_room_results', array( 'id' => $id )));
                }
            }
        }

        $clarification = $clarificationsModel->getByAuctionIdAndUserId($id, $userCurrent->getId(), ClarificationsModel::AUCTION_RESULTS);

        if (is_null($clarification) && in_array(Constants::ROLE_PERFORMER, $userCurrent->getSystemRoles())) {
            $clarificationForm = $this->createForm(new TradeClarificationsResultsForm(false));

            if ($request->isMethod('POST') && $request->request->get('clarifications', false)) {
                $clarificationForm->bind($request);
                if ($clarificationForm->isValid()) {

                    $formData = $clarificationForm->getData();

                    $em->getConnection()->beginTransaction();
                    try {

                        $clarificationsModel = new ClarificationsModel($em, $session, $acl);
                        $clarificationsTypesModel = new ClarificationsTypesModel($em);

                        $type = $clarificationsTypesModel->findOneByName('REQUEST_AUCTION_RESULTS');

                        $content = array(
                            'subject' => '',
                            'body' => $formData[ 'body' ]
                        );

                        $more = array(
                            'fromUser' => $userCurrent,
                            'toUser' => null,
                            'auction' => $auction,
                            'type' => $type,
                            'createdAt' => new \DateTime()
                        );

                        $clarification = $clarificationsModel->create($content, $more);

                        $em->getConnection()->commit();

                        /**
                         * @TODO: Система отправляет сообщения администраторам
                         */
                    } catch( \Exception $e ) {
                        $em->getConnection()->rollback();
                        $em->close();
                        throw $e;
                    }

                    $session->getFlashBag()->add('notice', 'Запрос разьяснения результатов аукциона отправлен.');

                    return $this->redirect($this->generateUrl('trade_room_results', array( 'id' => $id )));
                }
            }
        }

        $usersCompaniesModel = new UsersCompaniesModel($em, $session);
        $usersCompanies = $usersCompaniesModel->findOneByUserId($userCurrent->getId());
        $userCompany = $usersCompanies->getCompany();

        return array(
            'clarification' => $clarification,
            'clarification_form' => isset($clarificationForm) ? $clarificationForm->createView() : false,
            'protocol' => $protocol,
            'protocol_form' => isset($protocolForm) ? $protocolForm->createView() : false,
            'results' => $results,
            'history' => $history,
            'auction' => $auction,
            'user_company' => $userCompany,
        );
    }

    /**
     * @Route("/trade/clarifications/results/{id}/answer", requirements={"id" = "\d+"}, name="trade_clarifications_results_answer")
     * @Permissions(perm="/trade/clarifications/results/answer")
     * @Template("SimpleTradeBundle:Trade:clarifications_results_answer.html.twig")
     */
    public function tradeClarificationsResultsAnswerAction($id)
    {
        $acl = $this->get('acl');
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $request = $this->get('request');

        $auctionsModel = new AuctionsModel($em, $session, $acl);
        $auction = $auctionsModel->findByPK($id);

        if (is_null($auction)) {
            throw new \Exception('Auction not exist.');
        }

        $clarificationsModel = new ClarificationsModel($em, $session, $acl);

        $allClarifications = $clarificationsModel->findByAuctionId($id, ClarificationsModel::AUCTION_RESULTS);

        $forms = array( );
        $formsView = array( );
        foreach( $allClarifications as $value ) {
            if (!$value[ 'answer' ]) {
                $requestId = $value[ 'request' ][ 'request' ]->getId();

                $forms[ $requestId ] = $this->createForm(new TradeClarificationsResultsForm());
                $forms[ $requestId ]->get('for_id')->setData($value[ 'request' ][ 'request' ]->getId());
                $formsView[ $requestId ] = $forms[ $requestId ]->createView();
            }
        }

        if ($request->isMethod('POST')) {
            $postData = $request->request->get('tradeClarifications');

            $form = $forms[ $postData[ 'for_id' ] ];
            $form->bind($request);

            if ($form->isValid()) {

                $formData = $form->getData();

                $em->getConnection()->beginTransaction();
                try {

                    $clarificationsModel = new ClarificationsModel($em, $session, $acl);
                    $clarificationsTypesModel = new ClarificationsTypesModel($em);

                    $type = $clarificationsTypesModel->findOneByName('ANSWER_AUCTION_RESULTS');
                    $userCurrent = $this->get('security.context')->getToken()->getUser();
                    $requestClarification = $allClarifications[ $postData[ 'for_id' ] ][ 'request' ][ 'request' ];

                    $content = array(
                        'subject' => '',
                        'body' => $formData[ 'body' ]
                    );

                    $more = array(
                        'fromUser' => $userCurrent,
                        'toUser' => $requestClarification->getFromUser(),
                        'auction' => $requestClarification->getAuction(),
                        'type' => $type,
                        'createdAt' => new \DateTime()
                    );

                    $clarification = $clarificationsModel->create($content, $more);

                    $clarificationsModel->update($requestClarification->getId(), array(), array(
                        'fromUser' => $requestClarification->getFromUser(),
                        'toUser' => $requestClarification->getToUser(),
                        'clarification' => $clarification,
                        'auction' => $requestClarification->getAuction(),
                        'type' => $requestClarification->getType()
                        )
                    );

                    $documentsModel = new DocumentsModel($em, $session, $acl);
                    $documentsLinksModel = new DocumentsLinksModel($em, $session);

                    foreach( $formData[ 'files' ] as $value ) {
                        $document = $documentsModel->create(array(
                            'file' => $value->getFile(),
                            'title' => $value->getTitle(),
                            ), array( 'isActive' => 0 ));

                        $documentsLinksModel->create(array(
                            'document' => $document,
                            'owner' => 'CLARIFICATION',
                            'ownerId' => $clarification->getId(),
                        ));
                    }

                    $em->getConnection()->commit();
                } catch( \Exception $e ) {
                    $em->getConnection()->rollback();
                    $em->close();
                    throw $e;
                }

                $session->getFlashBag()->add('notice', 'Ответ на запрос разьяснения отправлен.');

                return $this->redirect($this->generateUrl('trade_clarifications_results_answer', array( 'id' => $id )));
            }

            $formsView[ $postData[ 'for_id' ] ] = $form->createView();
        }

        return array(
            'auction' => $auction,
            'all_clarifications' => $allClarifications,
            'forms' => $formsView
        );
    }

    /**
     * @Route("/trade/clarifications/auction/answer/{id}", requirements={"id" = "\d+"}, name="trade_clarifications_auction_answer")
     * @Permissions(perm="/trade/clarifications/auction/answer")
     * @Template("SimpleTradeBundle:Trade:clarifications_auction_answer.html.twig")
     */
    public function tradeClarificationsAuctionAnswerAction($id)
    {
        $request = $this->get('request');
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $clarificationsModel = new ClarificationsModel($em, $session, $acl);
        $requestClarification = $clarificationsModel->findByPK($id);

        if (is_null($requestClarification)) {
            throw new \Exception('Auction not exist.');
        }

        $form = $this->createForm(new TradeClarificationsAnswerForm());
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $formData = $form->getData();
                $em->getConnection()->beginTransaction();
                try {
                    $clarificationsTypesModel = new ClarificationsTypesModel($em);

                    $type = $clarificationsTypesModel->findOneByName('ANSWER_TO_AUCTION');
                    $userCurrent = $this->get('security.context')->getToken()->getUser();

                    $content = array(
                        'subject' => '',
                        'body' => $formData[ 'body' ]
                    );

                    $more = array(
                        'fromUser' => $userCurrent,
                        'toUser' => $requestClarification->getFromUser(),
                        'auction' => $requestClarification->getAuction(),
                        'type' => $type,
                        'createdAt' => new \DateTime()
                    );
                    $clarification = $clarificationsModel->create($content, $more);

                    $clarificationsModel->update($id, array(), array(
                        'fromUser' => $requestClarification->getFromUser(),
                        'toUser' => $requestClarification->getToUser(),
                        'clarification' => $clarification,
                        'auction' => $requestClarification->getAuction(),
                        'type' => $requestClarification->getType()
                        )
                    );

                    $documentsModel = new DocumentsModel($em, $session, $acl);
                    $documentsLinksModel = new DocumentsLinksModel($em, $session);

                    foreach( $formData[ 'files' ] as $value ) {
                        $document = $documentsModel->create(array(
                            'file' => $value->getFile(),
                            'title' => $value->getTitle(),
                            ), array( 'isActive' => 1 ));

                        $documentsLinksModel->create(array(
                            'document' => $document,
                            'owner' => 'CLARIFICATION',
                            'ownerId' => $clarification->getId(),
                        ));
                    }

                    $em->getConnection()->commit();
                } catch( Exception $e ) {
                    $em->getConnection()->rollback();
                    $em->close();
                    throw $e;
                }
                $session->getFlashBag()->add('notice', 'Ответ на запрос разьяснения отправлен.');

                return $this->redirect($this->generateUrl('trade_clarifications_auction_request', array( 'id' => $requestClarification->getAuction()->getId() )));
            }
        }
        else
        {
            return $this->redirect($this->generateUrl('trade_clarifications_auction_request', array( 'id' => $requestClarification->getAuction()->getId() )));
        }
    }

}