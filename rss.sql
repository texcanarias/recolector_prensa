-- phpMyAdmin SQL Dump
-- version 4.1.11
-- http://www.phpmyadmin.net
--
-- Servidor: d368.dinaserver.com
-- Tiempo de generación: 03-11-2016 a las 15:35:32
-- Versión del servidor: 5.5.52-0+deb7u1-log
-- Versión de PHP: 5.4.45-0+deb7u5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `rss`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rss_base_item`
--

CREATE TABLE IF NOT EXISTS `rss_base_item` (
  `Id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Fuente` varchar(50) COLLATE utf8_general_ci NOT NULL,
  `FechaPublicacion` datetime NOT NULL,
  `Url` varchar(100) COLLATE utf8_general_ci NOT NULL,
  `Titulo` varchar(255) COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rss_base_item_rel_tag`
--

CREATE TABLE IF NOT EXISTS `rss_base_item_rel_tag` (
  `rss_base_item_Id` bigint(20) unsigned NOT NULL,
  `rss_base_tag_Id` bigint(20) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rss_base_tag`
--

CREATE TABLE IF NOT EXISTS `rss_base_tag` (
  `Id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(255) COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `rss_base_item_rel_tag`
--
ALTER TABLE `rss_base_item_rel_tag`
  ADD CONSTRAINT `rss_base_item_rel_tag_ibfk_1` FOREIGN KEY (`rss_base_item_Id`) REFERENCES `rss_base_item` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rss_base_item_rel_tag_ibfk_2` FOREIGN KEY (`rss_base_tag_Id`) REFERENCES `rss_base_tag` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
