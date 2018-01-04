
-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 18-03-2016 a las 19:03:19
-- Versión del servidor: 10.0.20-MariaDB-log
-- Versión de PHP: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `u429736889_factu`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE IF NOT EXISTS `cliente` (
  `rif` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `RAZON_SOCIAL` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `DIRECCION` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `FECHA_VALOR` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`rif`),
  UNIQUE KEY `rif` (`rif`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente_telefono`
--

CREATE TABLE IF NOT EXISTS `cliente_telefono` (
  `rif` varchar(12) COLLATE utf8_unicode_ci DEFAULT NULL,
  `TELEFONO` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `FECHA_VALOR` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedido`
--

CREATE TABLE IF NOT EXISTS `detalle_pedido` (
  `num_invoice` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `descripcion` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `precio_unit` decimal(10,2) DEFAULT NULL,
  `sub_total` decimal(10,2) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `num_part` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_pedido` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=51 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa`
--

CREATE TABLE IF NOT EXISTS `empresa` (
  `rif` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `RAZON_SOCIAL` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `DIRECCION` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `logo` blob,
  `FECHA_VALOR` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type_logo` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`rif`),
  UNIQUE KEY `rif` (`rif`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa_telefono`
--

CREATE TABLE IF NOT EXISTS `empresa_telefono` (
  `rif` varchar(12) COLLATE utf8_unicode_ci DEFAULT NULL,
  `TELEFONO` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `FECHA_VALOR` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE IF NOT EXISTS `pedidos` (
  `num_orden` int(11) NOT NULL,
  `fecha_emision` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `cliente_rif` varchar(12) COLLATE utf8_unicode_ci DEFAULT NULL,
  `empresa_rif` varchar(12) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sub_total` decimal(10,2) DEFAULT NULL,
  `sale_tax` decimal(10,2) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `freight` decimal(10,2) DEFAULT NULL,
  `handling` decimal(10,2) DEFAULT NULL,
  `restocking` decimal(10,2) DEFAULT NULL,
  `total_sale` decimal(10,2) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `num_invoice` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
