-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Lun 18 Juillet 2016 à 16:19
-- Version du serveur :  5.6.17
-- Version de PHP :  5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `tm_simulateur`
--

-- --------------------------------------------------------

--
-- Structure de la table `pack`
--

CREATE TABLE IF NOT EXISTS `pack` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `date_achat` datetime DEFAULT NULL,
  `montant` double NOT NULL DEFAULT '0',
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=406 ;

--
-- Contenu de la table `pack`
--

INSERT INTO `pack` (`id`, `id_user`, `date_achat`, `montant`, `date`) VALUES
(230, 1, '2016-05-27 01:36:00', 49.80087256, '2016-07-17 00:18:00'),
(231, 1, '2016-05-27 19:45:00', 49.00929047, '2016-07-17 00:18:00'),
(232, 1, '2016-05-27 19:45:00', 49.00929047, '2016-07-17 00:18:00'),
(233, 1, '2016-05-27 19:45:00', 49.00929047, '2016-07-17 00:18:00'),
(234, 1, '2016-05-27 19:45:00', 49.00929047, '2016-07-17 00:18:00'),
(235, 1, '2016-05-27 19:45:00', 49.00929047, '2016-07-17 00:18:00'),
(236, 1, '2016-05-27 19:45:00', 49.00929047, '2016-07-17 00:18:00'),
(237, 1, '2016-05-27 19:45:00', 49.00929047, '2016-07-17 00:18:00'),
(238, 1, '2016-05-27 19:45:00', 49.00929047, '2016-07-17 00:18:00'),
(239, 1, '2016-05-27 19:45:00', 49.00929047, '2016-07-17 00:18:00'),
(240, 1, '2016-05-27 19:45:00', 49.00929047, '2016-07-17 00:18:00'),
(241, 1, '2016-05-28 14:24:00', 48.17005718, '2016-07-17 00:18:00'),
(242, 1, '2016-05-31 01:59:00', 46.3166072, '2016-07-17 00:18:00'),
(243, 1, '2016-06-01 14:27:00', 44.69331358, '2016-07-17 00:18:00'),
(244, 1, '2016-06-03 01:06:00', 43.23486889, '2016-07-17 00:18:00'),
(245, 1, '2016-06-05 02:12:00', 41.18949177, '2016-07-17 00:18:00'),
(246, 1, '2016-06-06 19:46:00', 39.36746725, '2016-07-17 00:18:00'),
(247, 1, '2016-06-08 22:33:00', 37.20127585, '2016-07-17 00:18:00'),
(248, 1, '2016-06-10 00:17:00', 36.0928669, '2016-07-17 00:18:00'),
(249, 1, '2016-06-11 11:37:00', 34.49964593, '2016-07-17 00:18:00'),
(250, 1, '2016-06-12 22:46:00', 33.03850842, '2016-07-17 00:18:00'),
(251, 1, '2016-06-14 19:32:00', 31.04791751, '2016-07-17 00:18:00'),
(252, 1, '2016-06-16 00:09:00', 29.79992453, '2016-07-17 00:18:00'),
(253, 1, '2016-06-16 18:07:00', 28.96641021, '2016-07-17 00:18:00'),
(254, 1, '2016-06-17 12:20:00', 28.17172237, '2016-07-17 00:18:00'),
(255, 1, '2016-06-18 10:25:00', 27.19332331, '2016-07-17 00:18:00'),
(256, 1, '2016-06-19 13:34:00', 26.00489801, '2016-07-17 00:18:00'),
(257, 1, '2016-06-20 18:53:00', 24.72919016, '2016-07-17 00:18:00'),
(258, 1, '2016-06-21 23:56:00', 23.53828407, '2016-07-17 00:18:00'),
(259, 1, '2016-06-23 18:24:00', 22.04343391, '2016-07-17 00:18:00'),
(260, 1, '2016-06-24 17:09:00', 21.02330272, '2016-07-17 00:18:00'),
(261, 1, '2016-06-26 02:33:00', 20.1368039, '2016-07-17 00:18:00'),
(262, 1, '2016-06-27 14:19:00', 18.55850362, '2016-07-17 00:18:00'),
(263, 1, '2016-06-28 20:08:00', 17.94038788, '2016-07-17 00:18:00'),
(264, 1, '2016-06-30 01:10:00', 16.65799373, '2016-07-17 00:18:00'),
(265, 1, '2016-06-30 20:33:00', 15.81937685, '2016-07-17 00:18:00'),
(266, 1, '2016-07-01 23:29:00', 14.67323822, '2016-07-17 00:18:00'),
(267, 1, '2016-07-02 21:46:00', 13.70477822, '2016-07-17 00:18:00'),
(268, 1, '2016-07-04 11:17:00', 12.43019687, '2016-07-17 00:18:00'),
(269, 1, '2016-07-05 00:40:00', 11.90232027, '2016-07-17 00:18:00'),
(270, 1, '2016-07-06 01:09:00', 10.84625584, '2016-07-17 00:18:00'),
(271, 1, '2016-07-07 01:39:00', 9.79155367, '2016-07-17 00:18:00'),
(272, 1, '2016-07-07 22:05:00', 8.87017561, '2016-07-17 00:18:00'),
(273, 1, '2016-07-08 16:18:00', 8.12114901, '2016-07-17 00:18:00'),
(274, 1, '2016-07-09 22:29:00', 7.2649103, '2016-07-17 00:18:00'),
(275, 1, '2016-07-11 01:20:00', 6.07855472, '2016-07-17 00:18:00'),
(276, 1, '2016-07-11 18:37:00', 5.3343596, '2016-07-17 00:18:00'),
(277, 1, '2016-07-12 16:47:00', 4.3687156, '2016-07-17 00:18:00'),
(278, 1, '2016-07-13 19:18:00', 3.31209766, '2016-07-17 00:18:00'),
(279, 1, '2016-07-14 18:53:00', 2.34299752, '2016-07-17 00:18:00'),
(280, 1, '2016-07-15 18:25:00', 1.28549767, '2016-07-17 00:19:00'),
(281, 1, '2016-07-16 14:05:00', 0.40545273, '2016-07-17 00:19:00'),
(293, 1, NULL, 0.35575742, '2016-07-17 17:41:00'),
(297, 1, '2016-07-18 01:53:00', 0, '2016-07-18 01:53:00'),
(352, 2, '2016-05-27 01:36:00', 51.5157787, '2016-07-18 14:30:27'),
(353, 2, '2016-05-27 19:45:00', 50.72419661, '2016-07-18 14:30:27'),
(354, 2, '2016-05-27 19:45:00', 50.72419661, '2016-07-18 14:30:27'),
(355, 2, '2016-05-27 19:45:00', 50.72419661, '2016-07-18 14:30:27'),
(356, 2, '2016-05-27 19:45:00', 50.72419661, '2016-07-18 14:30:27'),
(357, 2, '2016-05-27 19:45:00', 50.72419661, '2016-07-18 14:30:27'),
(358, 2, '2016-05-27 19:45:00', 50.72419661, '2016-07-18 14:30:27'),
(359, 2, '2016-05-27 19:45:00', 50.72419661, '2016-07-18 14:30:27'),
(360, 2, '2016-05-27 19:45:00', 50.72419661, '2016-07-18 14:30:27'),
(361, 2, '2016-05-27 19:45:00', 50.72419661, '2016-07-18 14:30:27'),
(362, 2, '2016-05-27 19:45:00', 50.72419661, '2016-07-18 14:30:27'),
(363, 2, '2016-05-28 14:24:00', 49.88496332, '2016-07-18 14:30:27'),
(364, 2, '2016-05-31 01:59:00', 48.03151334, '2016-07-18 14:30:27'),
(365, 2, '2016-06-01 14:27:00', 46.40821972, '2016-07-18 14:30:27'),
(366, 2, '2016-06-03 01:06:00', 44.94977503, '2016-07-18 14:30:27'),
(367, 2, '2016-06-05 02:12:00', 42.90439791, '2016-07-18 14:30:27'),
(368, 2, '2016-06-06 19:46:00', 41.08237339, '2016-07-18 14:30:27'),
(369, 2, '2016-06-08 22:33:00', 38.91618199, '2016-07-18 14:30:27'),
(370, 2, '2016-06-10 00:17:00', 37.80777304, '2016-07-18 14:30:27'),
(371, 2, '2016-06-11 11:37:00', 36.21455207, '2016-07-18 14:30:27'),
(372, 2, '2016-06-12 22:46:00', 34.75341456, '2016-07-18 14:30:27'),
(373, 2, '2016-06-14 19:32:00', 32.76282365, '2016-07-18 14:30:27'),
(374, 2, '2016-06-16 00:09:00', 31.51483067, '2016-07-18 14:30:27'),
(375, 2, '2016-06-16 18:07:00', 30.68131635, '2016-07-18 14:30:27'),
(376, 2, '2016-06-17 12:20:00', 29.88662851, '2016-07-18 14:30:27'),
(377, 2, '2016-06-18 10:25:00', 28.90822945, '2016-07-18 14:30:27'),
(378, 2, '2016-06-19 13:34:00', 27.71980415, '2016-07-18 14:30:27'),
(379, 2, '2016-06-20 18:53:00', 26.4440963, '2016-07-18 14:30:27'),
(380, 2, '2016-06-21 23:56:00', 25.25319021, '2016-07-18 14:30:27'),
(381, 2, '2016-06-23 18:24:00', 23.75834005, '2016-07-18 14:30:27'),
(382, 2, '2016-06-24 17:09:00', 22.73820886, '2016-07-18 14:30:27'),
(383, 2, '2016-06-26 02:33:00', 21.85171004, '2016-07-18 14:30:27'),
(384, 2, '2016-06-27 14:19:00', 20.27340976, '2016-07-18 14:30:27'),
(385, 2, '2016-06-28 20:08:00', 19.65529402, '2016-07-18 14:30:27'),
(386, 2, '2016-06-30 01:10:00', 18.37289987, '2016-07-18 14:30:27'),
(387, 2, '2016-06-30 20:33:00', 17.53428299, '2016-07-18 14:30:27'),
(388, 2, '2016-07-01 23:29:00', 16.38814436, '2016-07-18 14:30:27'),
(389, 2, '2016-07-02 21:46:00', 15.41968436, '2016-07-18 14:30:27'),
(390, 2, '2016-07-04 11:17:00', 14.14510301, '2016-07-18 14:30:27'),
(391, 2, '2016-07-05 00:40:00', 13.61722641, '2016-07-18 14:30:27'),
(392, 2, '2016-07-06 01:09:00', 12.56116198, '2016-07-18 14:30:27'),
(393, 2, '2016-07-07 01:39:00', 11.50645981, '2016-07-18 14:30:27'),
(394, 2, '2016-07-07 22:05:00', 10.58508175, '2016-07-18 14:30:27'),
(395, 2, '2016-07-08 16:18:00', 9.83605515, '2016-07-18 14:30:27'),
(396, 2, '2016-07-09 22:29:00', 8.97981644, '2016-07-18 14:30:27'),
(397, 2, '2016-07-11 01:20:00', 7.79346086, '2016-07-18 14:30:27'),
(398, 2, '2016-07-11 18:37:00', 7.04926574, '2016-07-18 14:30:27'),
(399, 2, '2016-07-12 16:47:00', 6.08362174, '2016-07-18 14:30:27'),
(400, 2, '2016-07-13 19:18:00', 5.0270038, '2016-07-18 14:30:27'),
(401, 2, '2016-07-14 18:53:00', 4.05790366, '2016-07-18 14:30:27'),
(402, 2, '2016-07-15 18:25:00', 3.00040381, '2016-07-18 14:30:41'),
(403, 2, '2016-07-16 14:05:00', 2.12035887, '2016-07-18 14:30:41'),
(404, 2, '2016-07-17 09:12:00', 1.27829005, '2016-07-18 14:30:41'),
(405, 2, '2016-07-18 01:53:00', 0.56939396, '2016-07-18 14:30:41');

-- --------------------------------------------------------

--
-- Structure de la table `session`
--

CREATE TABLE IF NOT EXISTS `session` (
  `sess_id` char(40) NOT NULL,
  `sess_datas` text NOT NULL,
  `sess_ip` varchar(15) NOT NULL,
  `sess_expire` int(10) NOT NULL,
  `sess_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sess_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `session`
--

INSERT INTO `session` (`sess_id`, `sess_datas`, `sess_ip`, `sess_expire`, `sess_date`) VALUES
('8ncoufcqjs19sl348p287cb2p5', 'key|s:8:"7;vnQ6-A";@Formulaire_7;vnQ6-A_token|s:28:"21134578cc7e02e7883.96561055";@Formulaire_7;vnQ6-A_token_time|i:1468844000;auth|b:1;id|s:1:"2";pseudo|s:7:"test001";Pack_7;vnQ6-A_token|s:28:"21054578ce40f908216.09338660";Pack_7;vnQ6-A_token_time|i:1468851215;User_7;vnQ6-A_token|s:27:"6303578ce40f928866.49044405";User_7;vnQ6-A_token_time|i:1468851215;', '127.0.0.1', 1468854815, '2016-07-18 14:13:20');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(100) NOT NULL,
  `mdp` char(128) NOT NULL,
  `solde` double DEFAULT '0',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `salt` char(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `user`
--

INSERT INTO `user` (`id`, `login`, `mdp`, `solde`, `updated_at`, `salt`) VALUES
(1, 'chikken', '80e56060e723ffda2bf1ff557374fd2d94f178e95c5db86c9912fb780e6dd35e062359b8d86d4c88c5b8f2acf6d7b1ca40091a9206fa12288e8abdcae58238e4', 6.58, '2016-07-18 01:53:00', '@g6jMçèd6C'),
(2, 'test001', '80e56060e723ffda2bf1ff557374fd2d94f178e95c5db86c9912fb780e6dd35e062359b8d86d4c88c5b8f2acf6d7b1ca40091a9206fa12288e8abdcae58238e4', 6.58, '2016-07-18 01:53:00', '@g6jMçèd6C');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
