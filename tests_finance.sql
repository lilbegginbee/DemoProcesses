-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 16, 2014 at 01:15 PM
-- Server version: 5.5.35
-- PHP Version: 5.3.10-1ubuntu3.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `tests_finance`
--

-- --------------------------------------------------------

--
-- Table structure for table `acl_groups`
--

DROP TABLE IF EXISTS `acl_groups`;
CREATE TABLE IF NOT EXISTS `acl_groups` (
  `id_group` char(10) NOT NULL,
  `about` varchar(200) NOT NULL,
  PRIMARY KEY (`id_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Список групп пользователей';

--
-- Dumping data for table `acl_groups`
--

INSERT INTO `acl_groups` (`id_group`, `about`) VALUES
('admin', 'Группа для управляющих системой'),
('api', 'Группа для взаимодействия с железом.'),
('dealer', 'Группа для дилеров компании (которые распространяют аппараты в других регионах)'),
('guest', 'Посетитель демонстрационных страниц'),
('person', 'Обследуемые'),
('report', 'Группа для мониторящих здоровье '),
('school', 'Кабинет школы (директор, звучи, учителя, медсёстры)'),
('store', 'Группа для работников склада'),
('system', 'Группа для мэйнтейнеров системы (кеш почистить)');

-- --------------------------------------------------------

--
-- Table structure for table `acl_groups_roles`
--

DROP TABLE IF EXISTS `acl_groups_roles`;
CREATE TABLE IF NOT EXISTS `acl_groups_roles` (
  `id_group` char(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `id_role` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_role`,`id_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Соответствия роль ->группа';

--
-- Dumping data for table `acl_groups_roles`
--

INSERT INTO `acl_groups_roles` (`id_group`, `id_role`) VALUES
('guest', 0),
('person', 10),
('school', 20),
('school', 21),
('school', 22),
('school', 23),
('school', 24),
('school', 25),
('school', 26),
('school', 27),
('school', 28),
('admin', 30),
('admin', 31),
('report', 40),
('store', 50),
('dealer', 60),
('system', 100),
('api', 110);

-- --------------------------------------------------------

--
-- Table structure for table `acl_groups_roles_extra`
--

DROP TABLE IF EXISTS `acl_groups_roles_extra`;
CREATE TABLE IF NOT EXISTS `acl_groups_roles_extra` (
  `id_group` char(20) NOT NULL,
  `id_role` int(10) NOT NULL,
  PRIMARY KEY (`id_group`,`id_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `acl_groups_roles_extra`
--

INSERT INTO `acl_groups_roles_extra` (`id_group`, `id_role`) VALUES
('api', 28);

-- --------------------------------------------------------

--
-- Table structure for table `acl_privileges`
--

DROP TABLE IF EXISTS `acl_privileges`;
CREATE TABLE IF NOT EXISTS `acl_privileges` (
  `id_privilege` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `privilege` varchar(50) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id_privilege`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Перечень привилегий Acl' AUTO_INCREMENT=5 ;

--
-- Dumping data for table `acl_privileges`
--

INSERT INTO `acl_privileges` (`id_privilege`, `privilege`, `description`) VALUES
(1, 'create', 'Создание'),
(2, 'delete', 'Удаление'),
(3, 'update', 'Редактирование'),
(4, 'read', 'Чтение/просмотр');

-- --------------------------------------------------------

--
-- Table structure for table `acl_resources`
--

DROP TABLE IF EXISTS `acl_resources`;
CREATE TABLE IF NOT EXISTS `acl_resources` (
  `id_resource` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `resource` varchar(50) NOT NULL,
  `id_group` enum('api','guest','admin','school','report','person','store','dealer','system') NOT NULL,
  `description` text,
  PRIMARY KEY (`id_resource`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Ресурсы Acl' AUTO_INCREMENT=44 ;

--
-- Dumping data for table `acl_resources`
--

INSERT INTO `acl_resources` (`id_resource`, `resource`, `id_group`, `description`) VALUES
(1, 'page_dashboard', 'school', 'Стартовая страница для кабинета директора школы'),
(2, 'page_dashboard', 'report', 'Страница дэшборда'),
(3, 'page_report_1', 'report', 'Страница первого отчёта'),
(4, 'page_report_2', 'report', 'Страница отчёта'),
(5, 'page_school_info', 'school', 'Страница информации о школе'),
(8, 'page_report_3', 'report', 'Соответствие половозрастным нормам по системам'),
(18, 'mainpage', 'guest', 'Показывать стартовую ( главную ) страницу'),
(19, 'dashboard', 'person', 'главная страница персонального кабинета'),
(20, 'dashboard', 'admin', 'Главная страница для администратора'),
(21, 'page_report_4', 'report', 'Страница отчёта «Параметры систем организма»'),
(22, 'dashboard', 'store', 'Стартовая страница для работника склада'),
(23, 'dashboard', 'dealer', 'Стартовая страница для представителя дилера'),
(24, 'dashboard', 'system', 'Дэшборд для управления системой (кеширование и т.п.)'),
(25, 'page_personal', 'school', 'Список персонала школы'),
(26, 'monitoring_ou', 'school', 'Мониторинг ОУ'),
(27, 'tests', 'school', 'Проведение тестов'),
(28, 'psych_tests', 'school', 'Психологическое тестирование'),
(29, 'personal_view', 'school', 'Просматривать информацию о пользователи из списка персонала'),
(30, 'personal_edit', 'school', 'Редактирование, удаление пользователя'),
(31, 'personal_add', 'school', 'добавление персонала в школу'),
(32, 'personals_view', 'school', 'список персонала'),
(33, 'reports', 'school', 'Отчёты по школе'),
(34, 'Monitoring2', 'school', 'Второй мониторинг ОУ'),
(36, 'school_monitoring', 'school', 'Отчёты по мониторингу'),
(38, 'monitoring_ou_reports', 'school', 'Мониторинг образовательных учреждений'),
(39, 'Monitoring_reports_school_psy', 'school', 'Отчёты по психологическому тестированию'),
(40, 'tests_results', 'school', 'Резульаты тестирования'),
(41, 'tests_new_session', 'school', 'Новая сессия'),
(42, 'tests_modules', 'school', 'Упаравление модулями'),
(43, 'tests_classrooms', 'school', 'Управление аудиториями');

-- --------------------------------------------------------

--
-- Table structure for table `acl_resources_privileges`
--

DROP TABLE IF EXISTS `acl_resources_privileges`;
CREATE TABLE IF NOT EXISTS `acl_resources_privileges` (
  `id_resource` int(10) unsigned NOT NULL DEFAULT '0',
  `id_privilege` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_privilege`,`id_resource`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `acl_roles`
--

DROP TABLE IF EXISTS `acl_roles`;
CREATE TABLE IF NOT EXISTS `acl_roles` (
  `id_role` tinyint(4) NOT NULL DEFAULT '0',
  `title` char(30) DEFAULT NULL,
  `status` enum('active','disabled') NOT NULL DEFAULT 'active',
  `caption` char(30) DEFAULT NULL,
  PRIMARY KEY (`id_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `acl_roles`
--

INSERT INTO `acl_roles` (`id_role`, `title`, `status`, `caption`) VALUES
(0, 'guest', 'active', NULL),
(10, 'person', 'active', NULL),
(20, 'school_director', 'active', NULL),
(21, 'school_headteacher_uvr', 'disabled', NULL),
(22, 'school_headteacher_vr', 'disabled', NULL),
(23, 'school_headteacher_umr', 'disabled', NULL),
(24, 'school_headteacher_xoz', 'disabled', NULL),
(25, 'school_psych', 'active', NULL),
(26, 'school_fizruk', 'disabled', NULL),
(27, 'school_teacher', 'disabled', NULL),
(28, 'school_nurse', 'active', NULL),
(30, 'admin', 'active', NULL),
(31, 'methodist', 'active', NULL),
(40, 'reporter', 'active', NULL),
(50, 'store', 'active', NULL),
(60, 'dealer', 'active', NULL),
(100, 'system', 'active', NULL),
(110, 'api', 'active', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `acl_roles_access`
--

DROP TABLE IF EXISTS `acl_roles_access`;
CREATE TABLE IF NOT EXISTS `acl_roles_access` (
  `id_role` tinyint(4) NOT NULL,
  `resource` varchar(50) NOT NULL,
  `privilege` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_role`,`resource`,`privilege`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Доступ ролей к ресурсам Acl';

--
-- Dumping data for table `acl_roles_access`
--

INSERT INTO `acl_roles_access` (`id_role`, `resource`, `privilege`) VALUES
(20, 'device', ''),
(20, 'personal', ''),
(21, 'personal', 'statistic');

-- --------------------------------------------------------

--
-- Table structure for table `acl_roles_privileges`
--

DROP TABLE IF EXISTS `acl_roles_privileges`;
CREATE TABLE IF NOT EXISTS `acl_roles_privileges` (
  `id_role` int(10) unsigned NOT NULL DEFAULT '0',
  `id_resource` char(50) NOT NULL DEFAULT '',
  `id_privilege` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_role`,`id_resource`,`id_privilege`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `acl_roles_resources`
--

DROP TABLE IF EXISTS `acl_roles_resources`;
CREATE TABLE IF NOT EXISTS `acl_roles_resources` (
  `id_role` tinyint(4) NOT NULL,
  `resource` char(50) NOT NULL,
  PRIMARY KEY (`id_role`,`resource`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Доступ ролей к ресурсам Acl';

--
-- Dumping data for table `acl_roles_resources`
--

INSERT INTO `acl_roles_resources` (`id_role`, `resource`) VALUES
(20, 'Monitoring2'),
(20, 'monitoring_ou'),
(20, 'monitoring_ou_reports'),
(20, 'Monitoring_reports_school_psy'),
(20, 'page_personal'),
(20, 'page_school_info'),
(20, 'personals_view'),
(20, 'personal_add'),
(20, 'personal_edit'),
(20, 'personal_view'),
(20, 'reports'),
(20, 'school_monitoring'),
(20, 'Отчёты по мониторингу'),
(25, 'monitoring_psy'),
(25, 'Monitoring_reports_school_psy'),
(25, 'tests'),
(25, 'tests_classrooms'),
(25, 'tests_modules'),
(25, 'tests_new_session'),
(25, 'tests_results');

-- --------------------------------------------------------

--
-- Table structure for table `acl_users_access`
--

DROP TABLE IF EXISTS `acl_users_access`;
CREATE TABLE IF NOT EXISTS `acl_users_access` (
  `id_user` int(11) NOT NULL,
  `resource` varchar(50) NOT NULL,
  `privilege` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_user`,`resource`,`privilege`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Перечень привилегий доступа отдельных пользователей';

--
-- Dumping data for table `acl_users_access`
--

INSERT INTO `acl_users_access` (`id_user`, `resource`, `privilege`) VALUES
(100001, 'personal', 'create');

-- --------------------------------------------------------

--
-- Table structure for table `acl_users_privileges`
--

DROP TABLE IF EXISTS `acl_users_privileges`;
CREATE TABLE IF NOT EXISTS `acl_users_privileges` (
  `id_user` int(10) unsigned NOT NULL,
  `id_resource` int(10) unsigned NOT NULL,
  `id_privilege` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_user`,`id_resource`,`id_privilege`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `acl_users_resources`
--

DROP TABLE IF EXISTS `acl_users_resources`;
CREATE TABLE IF NOT EXISTS `acl_users_resources` (
  `id_user` int(11) NOT NULL,
  `id_resource` char(50) NOT NULL,
  PRIMARY KEY (`id_user`,`id_resource`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Перечень привилегий доступа отдельных пользователей';

-- --------------------------------------------------------

--
-- Table structure for table `processes`
--

DROP TABLE IF EXISTS `processes`;
CREATE TABLE IF NOT EXISTS `processes` (
  `id_process` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `id_owner` int(10) unsigned NOT NULL,
  `add_time` datetime NOT NULL,
  `start_time` datetime DEFAULT NULL COMMENT 'Время первого старта процесса',
  `change_time` datetime DEFAULT NULL COMMENT 'Время крайних манипуляций со статусами процесса',
  `end_time` datetime DEFAULT NULL COMMENT 'Время завершения процесса',
  `full_time` int(10) unsigned NOT NULL COMMENT 'Полное время выполнения процесса в секундах',
  `progress_update_time` int(10) unsigned DEFAULT NULL COMMENT 'Время последнего обновления прогресса',
  `progress_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Сколько секунд процесс выполнялся',
  `status` enum('init','progress','wait','done') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'init',
  `is_removed` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_process`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=20 ;

--
-- Dumping data for table `processes`
--

INSERT INTO `processes` (`id_process`, `title`, `id_owner`, `add_time`, `start_time`, `change_time`, `end_time`, `full_time`, `progress_update_time`, `progress_time`, `status`, `is_removed`) VALUES
(11, '300 секунд', 4, '2014-04-15 13:53:33', '2014-04-15 13:53:40', '2014-04-15 15:43:37', NULL, 300, 1397562656, 464, 'done', 1),
(12, '120 сек', 4, '2014-04-15 14:30:33', '2014-04-15 14:30:37', '2014-04-15 15:38:10', NULL, 120, 1397562484, 1397562609, 'done', 1),
(13, '360 сек', 4, '2014-04-15 15:08:43', '2014-04-15 15:36:05', '2014-04-15 15:52:30', NULL, 360, 1397563671, 1073, 'done', 1),
(14, '120 sec2', 4, '2014-04-15 16:24:14', '2014-04-15 16:24:59', '2014-04-15 16:24:59', NULL, 120, 1397564825, 126, 'done', 1),
(15, '200 sec', 4, '2014-04-16 08:16:14', '2014-04-16 08:16:23', '2014-04-16 09:11:04', NULL, 200, 1397625115, 200, 'done', 1),
(16, '120 sec', 4, '2014-04-16 09:10:45', '2014-04-16 09:11:01', '2014-04-16 09:11:01', NULL, 120, 1397625182, 121, 'done', 1),
(17, '600 sec', 4, '2014-04-16 09:10:55', '2014-04-16 09:24:15', '2014-04-16 09:24:15', NULL, 600, 1397626455, 600, 'done', 0),
(18, '100 sec admin', 1, '2014-04-16 09:32:50', '2014-04-16 09:33:17', '2014-04-16 09:33:17', NULL, 100, 1397626498, 101, 'done', 0),
(19, '500 sec', 10, '2014-04-16 10:56:55', '2014-04-16 10:56:58', '2014-04-16 11:09:56', NULL, 500, 1397632192, 87, 'wait', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID пользователя',
  `name` varchar(200) DEFAULT NULL COMMENT 'Имя пользователя',
  `login` varchar(30) DEFAULT NULL COMMENT 'Логин пользователя',
  `password` char(32) NOT NULL COMMENT 'Пароль пользователя',
  `password_changed_date` datetime DEFAULT NULL COMMENT 'Пароль пользователя',
  `id_role` tinyint(3) unsigned NOT NULL COMMENT 'ID роли пользователя',
  `post` varchar(100) NOT NULL COMMENT 'Должность пользователя',
  `group` enum('user','school','admin','analytic','dealer','store','person','api','system','sub_admin') NOT NULL COMMENT 'Группа пользователя',
  `id_organisation` int(11) NOT NULL COMMENT 'ID подразделения для staff и student',
  `last_login` datetime NOT NULL COMMENT 'Дата последнего входа',
  `status` enum('inactive','active','block','delete') NOT NULL DEFAULT 'inactive',
  `created` datetime DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `flagAuthorizedContacts` varchar(20) DEFAULT NULL,
  `token` varchar(32) NOT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `Index 5` (`login`),
  KEY `group` (`group`),
  KEY `organization` (`id_organisation`),
  KEY `status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Общая таблица пользователей системы: персона и обследуемых' AUTO_INCREMENT=11 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `name`, `login`, `password`, `password_changed_date`, `id_role`, `post`, `group`, `id_organisation`, `last_login`, `status`, `created`, `email`, `phone`, `flagAuthorizedContacts`, `token`) VALUES
(1, 'Administrator', 'admin', '9bf8b46a765bc19171b4d0bdcffdfa39', NULL, 30, '', 'admin', 0, '2014-04-16 13:14:34', 'active', NULL, NULL, NULL, NULL, ''),
(4, NULL, 'timur', '9bf8b46a765bc19171b4d0bdcffdfa39', NULL, 10, '', 'person', 0, '2014-04-16 10:57:06', 'active', NULL, 'timur@semerenko.ru', NULL, NULL, '7ccedc25d6b74deeb5688b43643c1d87');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
