<?php

namespace SimpleSolution\SimpleTradeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SimpleSolution\SimpleTradeBundle\Annotations\Permissions;
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
use SimpleSolution\SimpleTradeBundle\Model\AuctionsModel;
use SimpleSolution\SimpleTradeBundle\Model\AccountsModel;
use SimpleSolution\SimpleTradeBundle\Model\TariffsModel;
use SimpleSolution\SimpleTradeBundle\Model\SkillsModel;
use SimpleSolution\SimpleTradeBundle\Model\CompaniesSkillsModel;
use SimpleSolution\SimpleTradeBundle\Model\TemplatesModel;
use SimpleSolution\SimpleTradeBundle\Model\DocumentsModel;
use SimpleSolution\SimpleTradeBundle\Model\DocumentsLinksModel;
use SimpleSolution\SimpleTradeBundle\Model\SrosTypesModel;
use SimpleSolution\SimpleTradeBundle\Model\NewsModel;
use SimpleSolution\SimpleTradeBundle\Model\CompaniesTypesModel;
use SimpleSolution\SimpleTradeBundle\Model\OffersModel;
use SimpleSolution\SimpleTradeBundle\Entity\Companies;
use SimpleSolution\SimpleTradeBundle\Entity\Users;
use SimpleSolution\SimpleTradeBundle\Form\register\RegisterForm;
use SimpleSolution\SimpleTradeBundle\Form\users\UsersRestorePasswordForm;
use SimpleSolution\SimpleTradeBundle\Consts\Constants;

class PublicController extends Controller
{

    /**
     * @Route("/public", name="public_index")
     * @Permissions(perm="/")
     * @Template("SimpleTradeBundle:Public:index.html.twig")
     */
    public function indexAction()
    {
        return array( );
    }

//    /**
//     * @Route("/public", name="public_index")
//     * @Permissions(perm="/")
//     * @Template("SimpleTradeBundle:Public:auctions.html.twig")
//     */
//    public function auctionsAction()
//    {
//        $em = $this->get('doctrine.orm.entity_manager');
//        $session = $this->get('session');
//        $acl = $this->get('acl');
//
//        $auctionsModel = new AuctionsModel($em, $session, $acl);
//        $offersModel = new OffersModel($em, $session, $acl);
//        $auctions = $auctionsModel->findByStatusesAndTypesWithContent(array( 'PUBLIC', 'REJECTED', 'NOT_TAKE_PLACE', 'STARTED', 'COMPLETED', 'CLOSED' ), array( 'AUCTION' ));
//
//        $offersCounts = array( );
//        foreach( $auctions as $auction ) {
//            $offersCounts[$auction->getId()] = $offersModel->getCountByAuctionId($auction->getId());
//        }
//
//        return array(
//            'auctions' => $auctions,
//            'offersCounts' => $offersCounts
//        );
//    }

    /**
     * @Route("/public/register", name="public_register")
     * @Permissions(perm="/register")
     * @Template()
     */
    public function registerAction()
    {
        $request = $this->get('request');
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');
        $factory = $this->get('security.encoder_factory');

        $srosModel = new SrosModel($em, $session, $acl);
        $sros = $srosModel->findAll();
        $srosArray = $srosModel->findAllAsArray();

        $regionsModel = new RegionsModel($em);
        $regions = $regionsModel->findAll();

        $skillsModel = new SkillsModel($em);
        $skills = $skillsModel->findAllAsArray();

        $form = $this->createForm(new RegisterForm($sros, $regions));
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $formData = $form->getData();

                $companiesModel = new CompaniesModel($em, $session, $acl);
                $companiesStatusesModel = new CompaniesStatusesModel($em);

                $rolesModel = new RolesModel($em);

                $usersRolesModel = new UsersRolesModel($em, $session);
                $usersModel = new UsersModel($em, $session, $factory);

                $usersCompaniesModel = new UsersCompaniesModel($em, $session);

                $srosCompaniesModel = new SrosCompaniesModel($em, $session);
                $documentsModel = new DocumentsModel($em, $session, $acl);
                $documentsLinksModel = new DocumentsLinksModel($em, $session);

                $preregisterStatus = $companiesStatusesModel->findOneByName('PRE_REGISTRATION');

                $accountsModel = new AccountsModel($em, $session, $acl);

                $companiesSkillsModel = new CompaniesSkillsModel($em, $session);

                $companiesTypesModel = new CompaniesTypesModel($em);

                $em->getConnection()->beginTransaction();
                try {
                    $region = $regionsModel->findByPK($formData[ 'region' ]);

                    $companyType = $companiesTypesModel->findOneByName($formData[ 'role' ]);

                    $company = $companiesModel->create(array(
                        'title' => $formData[ 'title' ],
                        'name' => $formData[ 'name' ],
                        'inn' => $formData[ 'inn' ],
                        'kpp' => $formData[ 'kpp' ],
                        'ogrn' => $formData[ 'ogrn' ],
                        'userName' => rtrim($formData[ 'lastname' ] . ' ' . $formData[ 'firstname' ] . ' ' . $formData[ 'patronymic' ]),
                        'email' => $formData[ 'email' ],
                        'position' => $formData[ 'position' ],
                        'grounds' => $formData[ 'grounds' ],
                        'phone' => $formData[ 'phone' ],
                        'region' => $region ), array(
                        'status' => $preregisterStatus,
                        'type' => $companyType ));

                    foreach( $formData[ 'files' ] as $value ) {
                        $document = $documentsModel->create(array( 'file' => $value->getFile(),
                            'title' => $value->getTitle(),
                            ), array( 'isActive' => 1 ));

                        $documentsLinksModel->create(array( 'document' => $document,
                            'owner' => 'COMPANY',
                            'ownerId' => $company->getId(),
                        ));
                    }

                    $user = $usersModel->create(array(
                        'login' => $formData[ 'login' ],
                        'name' => rtrim($formData[ 'lastname' ] . ' ' . $formData[ 'firstname' ] . ' ' . $formData[ 'patronymic' ]),
                        'password' => $formData[ 'password' ],
                        'email' => $formData[ 'email' ],
                        'position' => $formData[ 'position' ],
                        'grounds' => $formData[ 'grounds' ],
                        'phone' => $formData[ 'phone' ],
                        'region' => $region ));

                    $accountsModel->create(array( 'account' => 300,
                        'tariff' => null,
                        'changes' => 300,
                        'comment' => '' ), array( 'company' => $company ));

                    $role = $rolesModel->findOneByName('NOT_APPROVED_' . $formData[ 'role' ]);
                    $adminRole = $rolesModel->findOneByName('NOT_APPROVED_COMPANY_ADMIN');

                    $usersRolesModel->createFromObjects(array( 'user' => $user,
                        'role' => $role ));
                    $usersRolesModel->createFromObjects(array( 'user' => $user,
                        'role' => $adminRole ));

                    $usersCompaniesModel->createFromObjects(array( 'user' => $user,
                        'company' => $company ));

                    if ('PERFORMER' === $formData[ 'role' ]) {
                        $srosCompaniesModel->create(array( 'sro' => $formData[ 'sro' ],
                            'company' => $company->getId(),
                            'status' => 'PRE_REGISTRATION' ));

                        $attrsBySkill = array( );
                        $attrs = explode(',', $formData[ 'attributes' ]);
                        foreach( $attrs as $attr ) {
                            list($id, $main, $dangerous) = explode('|', $attr);
                            $attrsBySkill[ $id ][ 'main' ] = $main;
                            $attrsBySkill[ $id ][ 'dangerous' ] = $dangerous;
                        }

                        $skills = explode(',', $formData[ 'skills' ]);

                        foreach( $skills as $skill ) {
                            $companiesSkillsModel->create(array( 'company' => $company->getId(),
                                'skill' => $skill,
                                'isMain' => $attrsBySkill[ $skill ][ 'main' ],
                                'isDangerous' => $attrsBySkill[ $skill ][ 'dangerous' ]
                            ));
                        }
                    }

                    $em->getConnection()->commit();
                } catch( Exception $e ) {
                    $em->getConnection()->rollback();
                    $em->close();
                    throw $e;
                }

                /**
                 * Creating PDF
                 */
                $templatesModel = new TemplatesModel($em, $session, $factory);
                $template = $templatesModel->findByName(Constants::MAIL_2USER_REGISTRATION_AGREEMENT);

                $twig_env = new \Twig_Environment(new \Twig_Loader_String());
                $html = $twig_env->render($template->getBody(), $formData);

                $pdf = $this->get('knp_snappy.pdf')->getOutputFromHtml($html);

                /**
                 * Sending welcome email
                 */
                $this->get('mail')->sendByUser(Constants::MAIL_2USER_REGISTRATION, $user, array( 'user' => $user, 'password' => $formData[ 'password' ], 'attach' => array( 'content' => $pdf, 'filename' => 'agreement.pdf', 'contentType' => 'application/pdf', ), ));

                $session->getFlashBag()->add('notice', 'Пользователь успешно зарегистрирован.');

                $token = new UsernamePasswordToken($user, $user->getPassword(), 'secured_area', $user->getRoles());
                $session->set('_security_secured_area', serialize($token));

                /* @TODO
                 * баг, должно перекидывать на index,
                 * но отправка писем всё ломает и пользователь остается на public_index
                 */
                return $this->redirect($this->generateUrl('index'));
            }
        }
        return $this->render('SimpleTradeBundle:Public:register.html.twig', array(
                'form' => $form->createView(),
                'skills' => $skills,
                'sros' => $srosArray
            ));
    }

    /**
     * @Route("/public/restore", name="public_restore")
     * @Permissions(perm="/restore")
     * @Template()
     */
    public function restorePasswordFirstStepAction()
    {
        $request = $this->get('request');

        $form = $this->createForm(new UsersRestorePasswordForm(), new Users());
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->get('doctrine.orm.entity_manager');
                $session = $this->get('session');
                $factory = $this->get('security.encoder_factory');

                $usersModel = new UsersModel($em, $session, $factory);

                $user = $usersModel->findByEmail($form->getData()->getEmail());
                if ($user) {
                    $this->get('mail')->sendByUser(Constants::MAIL_2USER_PASSWORD_RESTORE, $user, array( 'user' => $user, ));

                    $session->getFlashBag()->add('notice', 'Письмо отправлено на почту');
                    return $this->redirect($this->generateUrl('public_index'));
                } else {
                    return $this->render('SimpleTradeBundle:Public:restore.html.twig', array(
                            'form' => $form->createView(),
                            'notice' => 'Пользователь не найден'
                        ));
                }
            }
        }
        return $this->render('SimpleTradeBundle:Public:restore.html.twig', array(
                'form' => $form->createView(),
                'notice' => ''
            ));
    }

    /**
     * @Route("/public/restore/{hash}", name="public_restore_second")
     * @Permissions(perm="/restore/hash")
     * @Template()
     */
    public function restorePasswordSecondStepAction($hash)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $factory = $this->get('security.encoder_factory');

        $usersModel = new UsersModel($em, $session, $factory);

        $user = $usersModel->findByHash($hash);
        if ($user) {
            $this->get('mail')->sendByUser(Constants::MAIL_2USER_NEW_PASSWORD, $user, array( 'user' => $user, 'password' => $usersModel->restorePassword($user), ));

            $session->getFlashBag()->add('notice', 'Письмо с новым паролем отправлено на почту');
        } else {
            $session->getFlashBag()->add('notice', 'Неправильный хеш для восстановления пароля');
        }
        return $this->redirect($this->generateUrl('public_index'));
    }

    /**
     * @Route("/public/price", name="public_price")
     * @Permissions(perm="/")
     * @Template("SimpleTradeBundle:Public:price.html.twig")
     */
    public function priceAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');

        $tariffsModel = new TariffsModel($em, $session);

        return array(
            'tariffs' => $tariffsModel->findAll()
        );
    }

    /**
     * @Route("/public/news", name="public_news")
     * @Permissions(perm="/")
     * @Template()
     */
    public function newsAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $session = $this->get('session');
        $acl = $this->get('acl');

        $newsModel = new NewsModel($em, $session, $acl);
        $news = $newsModel->findAllByPermission(Constants::NEWS_ANONYMOUS);
        return $this->render('SimpleTradeBundle:Public:news.html.twig', array(
                'news' => $news ));
    }

    /**
     * @Route("/public/specialists", name="public_specialists")
     * @Permissions(perm="/")
     * @Template("SimpleTradeBundle:Public:specialists.html.twig")
     */
    public function specialistsAction()
    {
        return array( );
    }

    /**
     * @Route("/public/courses", name="public_courses")
     * @Permissions(perm="/")
     * @Template("SimpleTradeBundle:Public:courses.html.twig")
     */
    public function coursesAction()
    {
        return array( );
    }

    /**
     * @Route("/public/investments", name="public_investments")
     * @Permissions(perm="/")
     * @Template("SimpleTradeBundle:Public:investments.html.twig")
     */
    public function investmentsAction()
    {
        return array( );
    }

}
