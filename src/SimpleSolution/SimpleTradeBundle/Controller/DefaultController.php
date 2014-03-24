<?php

namespace SimpleSolution\SimpleTradeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SimpleSolution\SimpleTradeBundle\Annotations\Permissions;
use SimpleSolution\SimpleTradeBundle\Model\OffersModel;
use SimpleSolution\SimpleTradeBundle\Model\OffersContentModel;
use SimpleSolution\SimpleTradeBundle\Model\SessionsModel;
use SimpleSolution\SimpleTradeBundle\Model\UsersModel;
use SimpleSolution\SimpleTradeBundle\Model\UsersRolesModel;
use SimpleSolution\SimpleTradeBundle\Model\UsersCompaniesModel;
use SimpleSolution\SimpleTradeBundle\Model\RolesModel;
use SimpleSolution\SimpleTradeBundle\Model\RegionsModel;
use SimpleSolution\SimpleTradeBundle\Model\CompaniesModel;
use SimpleSolution\SimpleTradeBundle\Model\CompaniesContentModel;
use SimpleSolution\SimpleTradeBundle\Model\CompaniesStatusesModel;
use SimpleSolution\SimpleTradeBundle\Model\SrosModel;
use SimpleSolution\SimpleTradeBundle\Model\SrosCompaniesModel;
use SimpleSolution\SimpleTradeBundle\Model\AccountsModel;
use SimpleSolution\SimpleTradeBundle\Model\SkillsModel;
use SimpleSolution\SimpleTradeBundle\Model\MessagesModel;
use SimpleSolution\SimpleTradeBundle\Model\CompaniesSkillsModel;
use SimpleSolution\SimpleTradeBundle\Model\RequestsModel;
use SimpleSolution\SimpleTradeBundle\Model\AuctionsModel;
use SimpleSolution\SimpleTradeBundle\Entity\Companies;
use SimpleSolution\SimpleTradeBundle\Entity\Users;
use SimpleSolution\SimpleTradeBundle\Form\register\RegisterForm;
use SimpleSolution\SimpleTradeBundle\Form\users\UsersRestorePasswordForm;
use Symfony\Component\HttpFoundation\Response;
use SimpleSolution\SimpleTradeBundle\Consts\Constants;

class DefaultController extends Controller
{

    /**
     * @Route("", name="index")
     * @Permissions(perm="/")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $usersCompaniesModel = new UsersCompaniesModel($em, $session);

        $userCurrent = $this->get('security.context')->getToken()->getUser();

        if (!$userCurrent->isGod()) {
            $usersCompany = $usersCompaniesModel->findOneByUserId($userCurrent->getId());
            if ($usersCompany !== null) {
                $company = $usersCompany->getCompany();
                if ('PRE_REGISTRATION' === $company->getStatus()->getName()) {
                    $session->getFlashBag()->add('notice', 'Регистрация компании еще не подтверждена');
                } else {
                    if ('CUSTOMER' === $company->getType()->getName()) {
                        if ('BLOCKED' === $company->getStatus()->getName()) {
                            $this->get('security.context')->setToken(null);
                            $session->invalidate();
                            $session->getFlashBag()->add('notice', 'Компания заблокирована c комментарием "' . $company->getContent()->getComment() . '"');

                            return $this->redirect($this->generateUrl('public_index'));
                        }
                    } else if ('PERFORMER' === $company->getType()->getName()) {
                        $srosCompaniesModel = new SrosCompaniesModel($em, $session);
                        $srosCompanies = $srosCompaniesModel->findAllByCompanyIdAndStatus($company->getId(), 'ACTIVE');
                        if (count($srosCompanies) == 0) {
                            $this->get('security.context')->setToken(null);
                            $session->invalidate();
                            $session->getFlashBag()->add('notice', 'Компания полностью заблокирована c комментарием "' . $company->getContent()->getComment() . '"');

                            return $this->redirect($this->generateUrl('public_index'));
                        } else {
                            $srosCompanies = $srosCompaniesModel->findAllByCompanyIdAndStatus($company->getId(), 'BLOCKED');
                            if (count($srosCompanies) > 0) {
                                $comments = array( );
                                foreach( $srosCompanies as $sroCompany ) {
                                    $comments[ ] = $sroCompany->getComment();
                                }
                                $session->getFlashBag()->add('notice', 'Компания частично заблокирована с комментариями: ' . implode(', ', $comments));
                            }
                        }
                    }
                }
            }
        }

        $roles = $userCurrent->getSystemRoles();
        $roles = $roles ? $roles : array();
        if (in_array(Constants::ROLE_ADMIN, $roles)) {
            $companiesModel = new CompaniesModel($em, $session, $acl);
            $registerCount = count($companiesModel->findAllByStatus('PRE_REGISTRATION'));

            $auctionsModel = new AuctionsModel($em, $session, $acl);
            $auctionsCount = count($auctionsModel->findAllByStatus('PRE_PUBLIC'));

            $requestsModel = new RequestsModel($em, $session, $acl);
            $complaintsCount = $requestsModel->getCountByTypeAndStatus('COMPLAINT', 'NEW');

            return $this->render('SimpleTradeBundle:Default:index_admin.html.twig', array(
                    'registerCount' => $registerCount,
                    'auctionsCount' => $auctionsCount,
                    'complaintsCount' => $complaintsCount ));
        } else if ((in_array(Constants::ROLE_CUSTOMER, $roles)) || (in_array(Constants::ROLE_NOT_APPROVED_CUSTOMER, $roles))) {
            return $this->render('SimpleTradeBundle:Default:index_customer.html.twig', array( ));
        } else if ((in_array(Constants::ROLE_PERFORMER, $roles)) || (in_array(Constants::ROLE_NOT_APPROVED_PERFORMER, $roles))) {
            return $this->render('SimpleTradeBundle:Default:index_performer.html.twig', array( ));
        } else if (in_array(Constants::ROLE_SRO, $roles)) {
            return $this->render('SimpleTradeBundle:Default:index_sro.html.twig', array( ));
        } else {
            return $this->render('SimpleTradeBundle:Default:index.html.twig', array( ));
        }
    }

    /**
     * @Route("")
     * @Permissions(perm="/")
     * @Template()
     */
    public function headerAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');
        $usersRolesModel = new UsersRolesModel($em, $session);
        $usersCompaniesModel = new UsersCompaniesModel($em, $session);
        $accountsModel = new AccountsModel($em, $session, $acl);
        $companiesSkillsModel = new CompaniesSkillsModel($em, $session);
        $messagesModel = new MessagesModel($em, $session, $acl);

        $userCurrent = $this->get('security.context')->getToken()->getUser();
        if ($userCurrent == 'anon.') {
            return $this->redirect($this->generateUrl('login'));
        }

        $notices = $session->getFlashBag()->get('notice', array( ));
        $allMenu = array( 'users' => array( 'perm' => '/users', 'href' => 'users', 'title' => 'Все пользователи' ),
            'companies' => array( 'perm' => '/companies', 'href' => 'companies', 'title' => 'Компании' ),
            'users_roles' => array( 'perm' => '/users/roles', 'href' => 'users_roles', 'title' => 'Роли' ),
            //'auctions' => array( 'perm' => '/auctions', 'href' => 'auctions', 'title' => 'Аукционы' ),
            'cabinet' => array( 'perm' => '/cabinet', 'href' => 'cabinet', 'title' => 'Личный кабинет' ),
            'sros' => array( 'perm' => '/sros', 'href' => 'sros', 'title' => 'СРО' ),
            'sros_companies' => array( 'perm' => '/sros/companies', 'href' => 'sros_companies', 'title' => 'Мои компании' ),
            //'offers' => array( 'perm' => '/offers', 'href' => 'offers', 'title' => 'Заявки на аукционы' ),
            'templates' => array( 'perm' => '/templates', 'href' => 'templates', 'title' => 'Шаблоны документов' ),
            'requests' => array( 'perm' => '/requests', 'href' => 'requests', 'title' => 'Заявки администатору' ),
            'companies_managment' => array( 'perm' => '/companies/managment', 'href' => 'companies_managment', 'title' => 'Управление компаниями' ),
            'trade' => array( 'perm' => '/trade', 'href' => 'trade', 'title' => 'Торги' ),
            'messages' => array( 'perm' => '/messages', 'href' => 'messages', 'title' => 'Сообщения' ),
            'news' => array( 'perm' => '/news', 'href' => 'news', 'title' => 'Новости' ),
            'news_customer' => array( 'perm' => '/news/customer', 'href' => 'news_customer', 'title' => 'Новости' ),
            'news_performer' => array( 'perm' => '/news/performer', 'href' => 'news_performer', 'title' => 'Новости' ),
            'news_sro' => array( 'perm' => '/news/sro', 'href' => 'news_sro', 'title' => 'Новости' ),
            'requests_company' => array( 'perm' => '/requests/company', 'href' => 'requests_company', 'title' => 'Мои заявки администратору' ),
            'requests_sro' => array( 'perm' => '/requests/sro', 'href' => 'requests_sro', 'title' => 'Мои заявки администратору' ),
            'reports' => array( 'perm' => '/reports', 'href' => 'reports', 'title' => 'Отчеты' ),
            'requests_complaints_sro' => array( 'perm' => '/requests/complaints/sro', 'href' => 'requests_complaints_sro', 'title' => 'Жалобы на мои компании' ),
            'tarifs' => array( 'perm' => '/tariffs', 'href' => 'tariffs', 'title' => 'Тарифы' ),
        );
        if ($userCurrent->isGod()) {
            $ignored = array( 'sros_companies', 'cabinet', 'offers', 'requests', 'news_customer', 'news_performer', 'news_sro' );
            foreach( $ignored as $i ) {
                unset($allMenu[ $i ]);
            }
            return $this->render('SimpleTradeBundle::header.html.twig', array(
                    'menu' => $allMenu,
                    'company' => 'God company',
                    'user' => 'God user',
                    'money' => '0',
                    'roles' => 'GOD',
                    'notices' => $notices,
                    //'skills' => '',
                    'messagesCount' => $messagesModel->getCount($userCurrent),
                    'userObj' => $userCurrent
                    )
            );
        } else {
            $usersCompanies = $usersCompaniesModel->findOneByUserId($userCurrent->getId());
            if ($usersCompanies !== null) {
                $company = $usersCompanies->getCompany();
                $skills = $companiesSkillsModel->findSkillsByCompanyIdAsArray($company->getId());
                $companyName = $company->getContent()->getTitle();

                $account = $accountsModel->findByCompanyId($company->getId());
                if ($account) {
                    $money = $account->getContent()->getAccount();
                } else {
                    $money = 'error';
                }
            } else {
                $companyName = '';
                $skills = array( '' );
                $money = '';
            }
            $user = $userCurrent->getName();

            $roles = $usersRolesModel->findRolesByUserIdAsArray($userCurrent->getId());
            $menu = array( );
            foreach( $allMenu as $item ) {
                if ($userCurrent->canI($item[ 'perm' ])) {
                    array_push($menu, $item);
                }
            }
            return $this->render('SimpleTradeBundle::header.html.twig', array(
                    'menu' => $menu,
                    'company' => $companyName,
                    'user' => $user,
                    'money' => $money,
                    'roles' => implode(', ', $roles),
                    'messagesCount' => $messagesModel->getCount($userCurrent),
                    'notices' => $notices,
                    'userObj' => $userCurrent )
                    //'skills' => implode(', ', $skills)
            );
        }
    }

    /**
     * @Route("/skills", name="skills")
     * @Permissions(perm="/skills")
     * @Template()
     */
    public function skillsAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');

        $usersCompaniesModel = new UsersCompaniesModel($em, $session);
        $userCurrent = $this->get('security.context')->getToken()->getUser();
        $usersCompanies = $usersCompaniesModel->findOneByUserId($userCurrent->getId());
        $company = $usersCompanies->getCompany();

        $companiesSkillsModel = new CompaniesSkillsModel($em, $session);
        $skills = $companiesSkillsModel->findSkillsByCompanyIdAsArray($company->getId());

        return $this->render('SimpleTradeBundle:Default:skills.html.twig', array(
                'skills' => implode(', ', $skills),
                'company' => $company
            ));
    }

}
