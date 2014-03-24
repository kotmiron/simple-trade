SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


--
-- Дамп данных таблицы `roles`
--

INSERT INTO `roles` (`id`, `name`, `title`) VALUES
(1, 'CUSTOMER', 'Заказчик'),
(2, 'PERFORMER', 'Строительная компания'),
(3, 'ADMIN', 'Администратор'),
(4, 'SRO', 'СРО'),
(5, 'COMPANY_ADMIN', 'Администратор компании'),
(6, 'MODERATOR', 'Модератор'),
(7, 'NOT_APPROVED_CUSTOMER', 'Не подтвержденный заказчик'),
(8, 'NOT_APPROVED_PERFORMER', 'Не подтвержденная строительная компания'),
(9, 'NOT_APPROVED_COMPANY_ADMIN', 'Не подтвержденный администратор компании');

INSERT INTO `roles_primitives` (`id`, `path`) VALUES
(1, '/companies'),
(2, '/companies/change'),
(3, '/companies/remove'),
(4, '/users'),
(5, '/users/show'),
(6, '/users/add'),
(7, '/users/change'),
(8, '/users/remove'),
(9, '/users/roles'),
(10, '/users/roles/add'),
(11, '/users/roles/change'),
(12, '/users/roles/remove'),
(13, '/users/admin/add'),
(14, '/users/systemuser/add'),
(15, '/auctions'),
(16, '/auctions/add'),
(17, '/auctions/change'),
(18, '/auctions/remove'),
(19, '/'),
(20, '/cabinet'),
(21, '/admin'),
(22, '/sros/companies'),
(23, '/sros/companies/change'),
(24, '/sros/companies/block'),
(25, '/sros/companies/active'),
(26, '/cabinet/companies'),
(27, '/cabinet/companies/change'),
(28, '/cabinet/user/change'),
(29, '/auctions/approve'),
(30, '/auctions/reject'),
(31, '/auctions/show'),
(32, '/offers'),
(33, '/offers/add'),
(34, '/offers/remove'),
(35, '/requests'),
(36, '/requests/register'),
(37, '/requests/register/show'),
(38, '/requests/register/change'),
(39, '/requests/register/approve'),
(40, '/requests/register/reject'),
(41, '/requests/skills'),
(42, '/requests/skills/show'),
(43, '/requests/skills/change'),
(44, '/requests/skills/approve'),
(45, '/requests/skills/reject'),
(46, '/requests/auctions'),
(47, '/requests/auctions/show'),
(48, '/requests/auctions/change'),
(49, '/requests/auctions/approve'),
(50, '/requests/auctions/reject'),
(51, '/offers/show'),
(52, '/offers/allow'),
(53, '/offers/reject'),
(54, '/companies/managment'),
(55, '/companies/managment/add'),
(56, '/cabinet/companies/documents'),
(57, '/cabinet/companies/documents/remove'),
(58, '/companies/block'),
(59, '/companies/active'),
(60, '/cabinet/users'),
(61, '/cabinet/users/add'),
(62, '/cabinet/users/change'),
(63, '/cabinet/users/remove'),
(64, '/trade'),
(65, '/sros'),
(66, '/sros/add'),
(67, '/sros/change'),
(68, '/sros/remove'),
(69, '/sros/users'),
(70, '/sros/users/add'),
(71, '/sros/users/remove'),
(72, '/news'),
(73, '/news/add'),
(74, '/news/change'),
(75, '/news/remove'),
(76, '/news/publish'),
(77, '/news/depublish'),
(78, '/messages'),
(79, '/news/performer'),
(80, '/news/customer'),
(81, '/news/sro'),
(82, '/cabinet/companies/change/skills'),
(83, '/sros/companies/change/skills'),
(84, '/requests/company'),
(85, '/requests/sro'),
(86, '/requests/block'),
(87, '/requests/block/show'),
(88, '/requests/block/change'),
(89, '/requests/block/approve'),
(90, '/requests/block/reject'),
(91, '/reports'),
(92, '/reports/finance'),
(93, '/cabinet/sros'),
(94, '/cabinet/sros/enter'),
(95, '/cabinet/sros/exit'),
(96, '/cabinet/sros/show'),
(97, '/requests/sros'),
(98, '/requests/sros/show'),
(99, '/requests/sros/change'),
(100, '/requests/sros/approve'),
(101, '/requests/sros/reject'),
(102, '/skills'),
(103, '/trade/notice'),
(104, '/trade/notice/edit'),
(105, '/trade/notice/refusal'),
(106, '/trade/clarifications/auction/request'),
(107, '/trade/offer'),
(108, '/trade/room'),
(109, '/requests/complaints'),
(110, '/requests/complaints/show'),
(111, '/requests/complaints/change'),
(112, '/requests/complaints/approve'),
(113, '/requests/complaints/reject'),
(114, '/requests/complaints/sro'),
(115, '/requests/complaints/sro/show'),
(116, '/requests/complaints/sro/change'),
(117, '/requests/complaints/sro/approve'),
(118, '/requests/complaints/sro/reject'),
(119, '/requests/complaints/add'),
(120, '/offers/company'),
(121, '/trade/clarifications/results/answer'),
(122, '/tariffs'),
(123, '/tariffs/change'),
(124, '/users/block'),
(125, '/users/unblock'),
(126, '/offers/getdoc'),
(127, '/trade/offers'),
(128, '/trade/notice/create'),
(129, '/trade/clarifications/auction/request/add'),
(130, '/trade/clarifications/auction/answer');

INSERT INTO `roles_roles_primitives` (`primitive_id`, `role_id`) VALUES
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(31, 1),
(32, 1),
(51, 1),
(52, 1),
(53, 1),
(15, 2),
(19, 2),
(20, 2),
(64, 2),
(103, 2),
(104, 2),
(105, 2),
(106, 2),
(107, 2),
(108, 2),
(1, 3),
(2, 3),
(3, 3),
(4, 3),
(5, 3),
(6, 3),
(7, 3),
(8, 3),
(9, 3),
(10, 3),
(11, 3),
(12, 3),
(14, 3),
(15, 3),
(19, 3),
(21, 3),
(19, 4),
(22, 4),
(23, 4),
(24, 4),
(25, 4),
(26, 5),
(27, 5),
(28, 5),
(28, 2),
(29, 3),
(30, 3),
(31, 2),
(32, 2),
(33, 2),
(33, 1),
(34, 2),
(35, 3),
(36, 3),
(37, 3),
(38, 3),
(39, 3),
(40, 3),
(41, 3),
(42, 3),
(43, 3),
(44, 3),
(45, 3),
(46, 3),
(47, 3),
(48, 3),
(49, 3),
(50, 3),
(54, 3),
(55, 3),
(64, 3),
(103, 3),
(104, 3),
(121, 3),
(56, 5),
(57, 5),
(58, 3),
(59, 3),
(60, 5),
(61, 5),
(62, 5),
(63, 5),
(64, 5),
(103, 1),
(104, 1),
(105, 1),
(106, 5),
/*(107, 1),*/
(108, 1),
(65, 3),
(66, 3),
(67, 3),
(68, 3),
(69, 3),
(70, 3),
(71, 3),
(72, 6),
(73, 6),
(74, 6),
(75, 6),
(76, 6),
(77, 6),
(19, 6),
(79, 2),
(80, 1),
(81, 4),
(82, 5),
(83, 4),
(84, 5),
(85, 4),
(86, 3),
(87, 3),
(88, 3),
(89, 3),
(90, 3),
(91, 3),
(92, 3),
(93, 5),
(94, 5),
(95, 5),
(96, 5),
(97, 3),
(98, 3),
(99, 3),
(100, 3),
(101, 3),
(102, 1),
(102, 2),
(102, 5),
(19, 7),
(20, 7),
(28, 7),
(56, 7),
(57, 7),
(102, 7),
(19, 8),
(20, 8),
(28, 8),
(56, 8),
(57, 8),
(102, 8),
(19, 9),
(20, 9),
(27, 9),
(28, 9),
(56, 9),
(57, 9),
(102, 9),
(109, 3),
(110, 3),
(111, 3),
(112, 3),
(113, 3),
(114, 4),
(115, 4),
(116, 4),
(117, 4),
(118, 4),
(119, 1),
(119, 2),
(120, 1),
(120, 2),
(122, 3),
(123, 3),
(124, 3),
(125, 3),
(126, 1),
(126, 2),
(126, 3),
(127, 1),
(128, 1),
(129, 2),
(130, 1);

INSERT INTO `companies_statuses` (`id`, `name`, `title`) VALUES
(1, 'PRE_REGISTRATION', 'Предварительная регистрация'),
(2, 'BLOCKED', 'Блокирован'),
(3, 'ACTIVE', 'Активен'),
(4, 'REJECTED', 'Отклонена регистрация');

INSERT INTO `offers_statuses` (`id`, `name`, `title`) VALUES
(1, 'ON_CONSIDERATION', 'На рассмотрении'),
(2, 'ADMITTED_TO_PARTICIPATION', 'Допущена до участия'),
(3, 'REJECTED', 'Отклонена'),
(4, 'LOST', 'Проиграла'),
(5, 'WON', 'Выиграла'),
(6, 'CONCLUDED_CONTRACT', 'Заключен контракт'),
(7, 'REVOKED_BY_BUILDER', 'Отозвана строителем');

INSERT INTO `regions` (`id`, `title`) VALUES
(1, 'Санкт-Петербург'),
(2, 'Москва');

INSERT INTO `currencies` (`id`, `title`) VALUES
(1, 'RUR'),
(2, 'USD');

INSERT INTO `auctions_types` (`id`, `title`, `name`) VALUES
(1, 'Аукцион', 'AUCTION'),
(2, 'Запрос предложений', 'REQUEST_PROPOSALS');

INSERT INTO `protocols_types` (`id`, `title`, `name`) VALUES
(1, 'Протокол подведения итогов аукциона', 'AUCTION_RESULTS'),
(2, 'Протокол рассмотрения заявок на участие в аукционе', 'REVIEW_OFFERS');

INSERT INTO `clarifications_types` (`id`, `title`, `name`) VALUES
(1, 'Запрос разьяснения к аукциону', 'REQUEST_TO_AUCTION'),
(2, 'Разъяснение к аукциону', 'ANSWER_TO_AUCTION'),
(3, 'Запрос разьяснения результатов аукциона', 'REQUEST_AUCTION_RESULTS'),
(4, 'Разъяснение результатов аукциона', 'ANSWER_AUCTION_RESULTS');

INSERT INTO `accounts_types` (`id`, `title`, `name`) VALUES
(1, 'Добавление', 'ADD'),
(2, 'Списание', 'REMOVE');

INSERT INTO `tariffs` (`id`, `title`, `name`, `service`, `cost`) VALUES
(1, 'подача заявки на аукцион', 'OFFER_ADD', '/offers/add', 300.0),
(2, 'создание аукциона', 'AUCTION_ADD', '/auctions/add', 200.0);

INSERT INTO `auctions_statuses` (`id`, `name`, `title`) VALUES
(1, 'PRE_PUBLIC', 'Предварительная публикация'),
(2, 'PUBLIC', 'Опубликован'),
(3, 'REJECTED', 'Отклонен'),
(4, 'NOT_TAKE_PLACE', 'Аукцион не состоялся'),
(5, 'STARTED', 'Аукцион начался'),
(6, 'COMPLETED', 'Аукцион завершен'),
(7, 'CLOSED', 'Аукцион закрыт'),
(8, 'ARCHIVE', 'Архивный');

INSERT INTO `sros_types` (`id`, `title`) VALUES
(1, 'СРОС'),
(2, 'СРОИ'),
(3, 'СРОП');

INSERT INTO `news_statuses` (`id`, `name`, `title`) VALUES
(1, 'PRE_PUBLIC', 'Предварительная публикация'),
(2, 'PUBLIC', 'Опубликован'),
(3, 'NOT_PUBLIC', 'Не публикован');

INSERT INTO `companies_types` (`id`, `name`, `title`) VALUES
(1, 'CUSTOMER', 'Заказчик'),
(2, 'PERFORMER', 'Строительная компания');

INSERT INTO `requests_types` (`id`, `name`, `title`) VALUES
(1, 'BLOCK', 'Блокировка компании'),
(2, 'SKILLS', 'Изменение квалификации'),
(3, 'ENTER_SRO', 'Вступление в СРО'),
(4, 'COMPLAINT', 'Жалоба на компанию'),
(5, 'COMPLAINT_SRO', 'Жалоба на компанию');

INSERT INTO `requests_statuses` (`id`, `name`, `title`) VALUES
(1, 'NEW', 'Не рассмотрена'),
(2, 'APPROVED', 'Подтверждена'),
(3, 'REJECTED', 'Отклонена');

INSERT INTO `properties` (`id`, `key`, `value`, `comment`) VALUES
(1, 'minutesToComplete', '10', 'minutes to complete auction after last bid'),
(2, 'minutesToClose', '10', 'minutes to close auction after complete (time for last offer)'),
(3, 'hoursToInputAuctionProtocol', '24', 'hours to input auction protocol after auctions completed (before close)'),
(4, 'hoursToProcessAuction', '24', 'hours to make auction PUBLIC or REJECTED'),
(5, 'daysToArchive', '1', 'days to move auction to archive after closing'),
(6, 'numberOfWinners', '5', 'number of win offers after auction closing'),
(7, 'preStartNotificationHours', '12', 'send notification about auction start at N hours before start'),
(8, 'preEndOfferNotificationHours', '12', 'send notification about endOfferTime at N hours before'),
(9, 'preEndConsiderationNotificationHours', '24', 'send notification if theres offers ON_CONSIDERATION at N hours before endConsideration time'),
(10, 'daysToPay', '30', 'days for company to pay for registration'),
(11, 'penaltiesToNotice', '2', 'penalties for company, when notification to admin will be sent'),
(12, 'senderAddress', 'testsimplesolution@gmail.com', 'mail of software, that will be signed into "from" field of email'),
(13, 'templatesLink', 'http://simpletrade.artvisio.com/app_dev.php/public', 'link to the messages templates'),
(14, 'adminAddress', 'dmitriy.aleksandrov@simple-solution.biz', 'admin address to send notifications'),
(15, 'daysToProcessRequest', '1', 'days to do something with new request, created for admin'),
(16, 'minutesBetweenBids', '2', 'minutes between robots bids');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
