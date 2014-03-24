SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

INSERT INTO `sessions` (`id`, `data`, `time`) VALUES
('a60rnqv7u6r5l5q0eu9fau9h23', 'X3NmMl9hdHRyaWJ1dGVzfGE6Mjp7czozNDoiX3NlY3VyaXR5LnNlY3VyZWRfYXJlYS50YXJnZXRfcGF0aCI7czozNDoiaHR0cDovLzEyNy4wLjAuMTo4MDgwL2FwcF9kZXYucGhwLyI7czoyMjoiX3NlY3VyaXR5X3NlY3VyZWRfYXJlYSI7czo4NjE6IkM6NzQ6IlN5bWZvbnlcQ29tcG9uZW50XFNlY3VyaXR5XENvcmVcQXV0aGVudGljYXRpb25cVG9rZW5cVXNlcm5hbWVQYXNzd29yZFRva2VuIjo3NzM6e2E6Mzp7aTowO047aToxO3M6MTI6InNlY3VyZWRfYXJlYSI7aToyO3M6NzI0OiJhOjQ6e2k6MDtPOjQxOiJTeW1mb255XENvbXBvbmVudFxTZWN1cml0eVxDb3JlXFVzZXJcVXNlciI6Nzp7czo1MToiAFN5bWZvbnlcQ29tcG9uZW50XFNlY3VyaXR5XENvcmVcVXNlclxVc2VyAHVzZXJuYW1lIjtzOjM6ImdvZCI7czo1MToiAFN5bWZvbnlcQ29tcG9uZW50XFNlY3VyaXR5XENvcmVcVXNlclxVc2VyAHBhc3N3b3JkIjtzOjM6ImdvZCI7czo1MDoiAFN5bWZvbnlcQ29tcG9uZW50XFNlY3VyaXR5XENvcmVcVXNlclxVc2VyAGVuYWJsZWQiO2I6MTtzOjYwOiIAU3ltZm9ueVxDb21wb25lbnRcU2VjdXJpdHlcQ29yZVxVc2VyXFVzZXIAYWNjb3VudE5vbkV4cGlyZWQiO2I6MTtzOjY0OiIAU3ltZm9ueVxDb21wb25lbnRcU2VjdXJpdHlcQ29yZVxVc2VyXFVzZXIAY3JlZGVudGlhbHNOb25FeHBpcmVkIjtiOjE7czo1OToiAFN5bWZvbnlcQ29tcG9uZW50XFNlY3VyaXR5XENvcmVcVXNlclxVc2VyAGFjY291bnROb25Mb2NrZWQiO2I6MTtzOjQ4OiIAU3ltZm9ueVxDb21wb25lbnRcU2VjdXJpdHlcQ29yZVxVc2VyXFVzZXIAcm9sZXMiO2E6MTp7aTowO3M6MTA6IlJPTEVfQURNSU4iO319aToxO2I6MTtpOjI7YToxOntpOjA7Tzo0MToiU3ltZm9ueVxDb21wb25lbnRcU2VjdXJpdHlcQ29yZVxSb2xlXFJvbGUiOjE6e3M6NDc6IgBTeW1mb255XENvbXBvbmVudFxTZWN1cml0eVxDb3JlXFJvbGVcUm9sZQByb2xlIjtzOjEwOiJST0xFX0FETUlOIjt9fWk6MzthOjA6e319Ijt9fSI7fV9zZjJfZmxhc2hlc3xhOjA6e31fc2YyX21ldGF8YTozOntzOjE6InUiO2k6MTM1NTY4NTU4MjtzOjE6ImMiO2k6MTM1NTY4NTUxODtzOjE6ImwiO3M6NDoiMzYwMCI7fQ==', '1355685582');

INSERT INTO `users` (`id`, `region_id`, `login`, `email`, `name`, `password`, `salt`, `created_at`, `is_god`) VALUES
(1, null, 'god', 'god@simpletrade.ru', 'Суперпользователь', 'c8749ef1a3dd56f822cd4bd63a5ba58f4968c3a5', 'uq5ym+d77.yy_tt-tjnexrk83938.pgo1x44,b0*c+gxq', NOW(), '1'),
(2, 1, 'sro', 'sro@simpletrade.ru', 'Иван Андреевич Петров', '9b05eb18436c484f527b8fe2b95cf8b7e1e65ab7', 'h55u0,=b4hkb1m+87w-wn.*=m5i_f_*x2+la,km-,+0,l', NOW(), '0'),
(3, null, 'admin', 'admin@simpletrade.ru', 'Петр Александрович Иванов', '3a3cc72a0f69e9624e813f2d29544c131e92a829', 'hnk_a76=uq-urfs2vkcz2lmvl4wsqxo1e2_o83n*skpc.', NOW(), '0'),
(4, 1, 'customer', 'customer@simpletrade.ru', 'Евгений Владимирович Смирнов', '6507683fa8f5a50e17ad102d30142b30dc2271af', 'dkgh*cxz9n.9353mq+hq9c5hexff8.9ldp_0,t34a_bc1', NOW(), '0'),
(5, 1, 'performer', 'performer@simpletrade.ru', 'Владимир Николаевич Васильев', 'c0b44be6cd477994c840e352cee9332dafea0b9e', 'fpn.sxlh++9o3o7nv8k0jsij296-z*i8l=28vmpvmxcoe', NOW(), '0'),
(6, null, 'moderator', 'moderator@simpletrade.ru', 'Роман Владимирович Глебов', '0aa919b996b23d122202a395016c3d8e5f54e3c3', '_5eo2uz*=_+i5e,zcyv_pihidj_708l7c.udom0mj9-nn', NOW(), '0');

INSERT INTO `users_roles` (`user_id`, `role_id`) VALUES
(2, 4),
(3, 3),
(3, 6),
(4, 7),
(4, 9),
(5, 8),
(5, 9),
(6, 6);

INSERT INTO `sros_content` (`id`, `title`, `email`) VALUES
(1, 'testSRO', 'sro@simpletrade.ru');
INSERT INTO `sros` (`id`, `content_id`, `created_at`, `type_id`) VALUES
(1, 1, NOW(), 1);
INSERT INTO `transactions` (`id`, `session_id`, `created_at`, `action_name`, `action_entity`, `action_table`, `action_id`) VALUES
(1, 'a60rnqv7u6r5l5q0eu9fau9h23', NOW(), 'create', 'SimpleSolution\SimpleTradeBundle\Entity\Sros', 'Sros', 1);
INSERT INTO `sros_versions` (`id`, `content_id`, `type_id`, `created_at`, `group_id`, `transaction_id`) VALUES
(1, 1, 1, NOW(), 1, 1);
INSERT INTO `users_sros` (`id`, `user_id`, `sro_id`) VALUES
(1, 2, 1);
INSERT INTO `sros` (`id`, `content_id`, `created_at`, `type_id`) VALUES
(2, 1, NOW(), 2);
INSERT INTO `sros` (`id`, `content_id`, `created_at`, `type_id`) VALUES
(3, 1, NOW(), 3);
INSERT INTO `sros` (`id`, `content_id`, `created_at`, `type_id`) VALUES
(4, 1, NOW(), 3);

INSERT INTO `companies_content` (`id`, `title`, `name`, `email`, `phone`, `user_name`, `inn`, `region_id`) VALUES
(1, 'ООО Ромашка', 'Ромашка', 'customer@simpletrade.ru', '89095554433', 'Евгений Владимирович Смирнов', '0123456789', 1);
INSERT INTO `companies` (`id`, `content_id`, `created_at`, `type_id`, `status_id`) VALUES
(1, 1, NOW(), 1, 1);
INSERT INTO `transactions` (`id`, `session_id`, `created_at`, `action_name`, `action_entity`, `action_table`, `action_id`) VALUES
(2, 'a60rnqv7u6r5l5q0eu9fau9h23', NOW(), 'create', 'SimpleSolution\SimpleTradeBundle\Entity\Companies', 'Companies', 1);
INSERT INTO `companies_versions` (`id`, `content_id`, `type_id`, `status_id`, `created_at`, `group_id`, `transaction_id`) VALUES
(1, 1, 1, 1, NOW(), 1, 2);
INSERT INTO `users_companies` (`user_id`, `company_id`) VALUES
(4, 1);

INSERT INTO `companies_content` (`id`, `title`, `name`, `email`, `phone`, `user_name`, `inn`, `region_id`) VALUES
(2, 'ООО Фиалка', 'Фиалка', 'performer@simpletrade.ru', '89095554433', 'Владимир Николаевич Васильев', '0123456789', 2);
INSERT INTO `companies` (`id`, `content_id`, `created_at`, `type_id`, `status_id`) VALUES
(2, 2, NOW(), 2, 1);
INSERT INTO `transactions` (`id`, `session_id`, `created_at`, `action_name`, `action_entity`, `action_table`, `action_id`) VALUES
(3, 'a60rnqv7u6r5l5q0eu9fau9h23', NOW(), 'create', 'SimpleSolution\SimpleTradeBundle\Entity\Companies', 'Companies', 2);
INSERT INTO `companies_versions` (`id`, `content_id`, `type_id`, `status_id`, `created_at`, `group_id`, `transaction_id`) VALUES
(2, 2, 2, 1, NOW(), 2, 3);
INSERT INTO `users_companies` (`user_id`, `company_id`) VALUES
(5, 2);

INSERT INTO `sros_companies` (`status_id`, `company_id`, `sro_id`) VALUES
(1, 2, 1);

INSERT INTO `accounts_content` (`id`, `tariff_id`, `account`) VALUES
(1, 1, 300.0);
INSERT INTO `accounts` (`id`, `content_id`, `created_at`, `company_id`) VALUES
(1, 1, NOW(), 1);
INSERT INTO `transactions` (`id`, `session_id`, `created_at`, `action_name`, `action_entity`, `action_table`, `action_id`) VALUES
(4, 'a60rnqv7u6r5l5q0eu9fau9h23', NOW(), 'create', 'SimpleSolution\SimpleTradeBundle\Entity\Accounts', 'Accounts', 1);
INSERT INTO `accounts_versions` (`id`, `content_id`, `company_id`, `created_at`, `group_id`, `transaction_id`) VALUES
(1, 1, 1, NOW(), 1, 4);

INSERT INTO `accounts_content` (`id`, `tariff_id`, `account`) VALUES
(2, 1, 300.0);
INSERT INTO `accounts` (`id`, `content_id`, `created_at`, `company_id`) VALUES
(2, 2, NOW(), 2);
INSERT INTO `transactions` (`id`, `session_id`, `created_at`, `action_name`, `action_entity`, `action_table`, `action_id`) VALUES
(5, 'a60rnqv7u6r5l5q0eu9fau9h23', NOW(), 'create', 'SimpleSolution\SimpleTradeBundle\Entity\Accounts', 'Accounts', 2);
INSERT INTO `accounts_versions` (`id`, `content_id`, `company_id`, `created_at`, `group_id`, `transaction_id`) VALUES
(2, 2, 2, NOW(), 2, 5);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
