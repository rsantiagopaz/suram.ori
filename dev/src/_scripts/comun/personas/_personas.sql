# phpMyAdmin SQL Dump
# version 2.5.6
# http://www.phpmyadmin.net
#
# Servidor: localhost
# Tiempo de generación: 13-08-2004 a las 19:03:36
# Versión del servidor: 3.23.54
# Versión de PHP: 4.3.3
# 
# Base de datos : `intrasde`
# 

# --------------------------------------------------------

#
# Estructura de tabla para la tabla `_personas`
#

CREATE TABLE `_personas` (
  `persona_id` char(5) NOT NULL default '',
  `persona_tipo` char(1) NOT NULL default '',
  `persona_dni` int(8) NOT NULL default '0',
  `persona_cuil` int(11) NOT NULL default '0',
  `persona_cuit` int(11) NOT NULL default '0',
  `persona_nombre` char(100) NOT NULL default '',
  `persona_domicilio` char(100) NOT NULL default '',
  `persona_sexo` char(1) NOT NULL default '',
  `persona_nacionalidad` char(20) NOT NULL default '',
  `persona_estadocivil` char(1) NOT NULL default '',
  `persona_instruccion` char(20) NOT NULL default '',
  `persona_clase` int(4) NOT NULL default '0',
  `localidad_id` char(5) NOT NULL default '',
  `SYSusuario` char(20) NOT NULL default '',
  `SYSusuario_carga_fecha` date NOT NULL default '0000-00-00',
  `SYSusuario_carga_hora` time NOT NULL default '00:00:00',
  PRIMARY KEY  (`persona_id`),
  KEY `persona_tipo` (`persona_tipo`,`persona_dni`,`persona_cuil`,`persona_cuit`),
  KEY `SYSusuario` (`SYSusuario`),
  FULLTEXT KEY `SYSusuario_2` (`SYSusuario`),
  KEY `SYSusuario_fecha_carga` (`SYSusuario_carga_fecha`)
) TYPE=MyISAM;

#
# Volcar la base de datos para la tabla `_personas`
#

INSERT INTO `_personas` VALUES ('EM213', '-', 11555888, 0, 0, 'kdjslgnjkad', 'sdkdjfnadkjf', '-', '', '', '', 2, '12345', 'jorge', '2004-08-02', '17:19:25');
INSERT INTO `_personas` VALUES ('2', 'F', 22242901, 2147483647, 0, 'MITRE JORGE FABIAN', 'ALVEAR 325', 'M', 'ARGENTINO', 'C', 'UNIVERSITARIO', 1971, '00901', 'jorge', '0000-00-00', '00:00:00');
INSERT INTO `_personas` VALUES ('QYXP9', '-', 55555555, 0, 0, 'QUINTO', 'ASDFSA', '-', '', '', '', 2, '12345', 'pablo', '2004-07-22', '21:44:04');
INSERT INTO `_personas` VALUES ('2T5HA', '-', 44444444, 0, 0, 'CUARTO', 'ASDFA', '-', '', '', '', 2, '12345', 'pablo', '2004-07-22', '21:41:44');
INSERT INTO `_personas` VALUES ('IZGHA', '-', 22222222, 0, 0, 'SEGUNDO', 'AA', '-', '', '', '', 2, '12345', 'pablo', '2004-07-22', '21:39:46');
INSERT INTO `_personas` VALUES ('K3DUT', '-', 11111111, 0, 0, 'PRIMERO', 'AAA', '-', '', '', '', 2, '12345', 'pablo', '2004-07-22', '21:37:15');
INSERT INTO `_personas` VALUES ('EMW3Z', 'F', 21632362, 2147483647, 0, 'LOPEZ PABLO', 'SU CASA', 'M', 'ARGENTINO', '', 'SECUNDARIA', 2, '00901', 'jorge', '2004-07-22', '20:29:20');
INSERT INTO `_personas` VALUES ('3', 'J', 0, 2022242901, 2022242901, 'EMPRESA S.A.', '', '', '', '', '', 0, '', 'jorge', '2004-07-22', '00:00:00');
INSERT INTO `_personas` VALUES ('CXW3H', 'F', 18300556, 2147483647, 0, 'LUNA WALTER', 'C202 1422', 'M', 'ARGENTINO', '', 'SECUNDARIA COMPLETA', 2, '12345', 'jorge', '2004-07-14', '19:24:15');
INSERT INTO `_personas` VALUES ('1RSL1', 'F', 0, 0, 0, 'yyy', 'yy', 'M', '', '', '', 2, '12345', 'pablo', '2004-07-14', '19:14:12');
INSERT INTO `_personas` VALUES ('ETUAA', '-', 66666666, 0, 0, 'SEXTO', 'SAFAS', '-', '', '', '', 2, '12345', 'pablo', '2004-07-22', '21:46:26');
INSERT INTO `_personas` VALUES ('O44R7', '-', 88888888, 0, 0, 'OCTAVO', 'SDFSA', '-', '', '', '', 2, '12345', 'pablo', '2004-07-22', '21:49:58');
INSERT INTO `_personas` VALUES ('BD7SX', '-', 99999999, 0, 0, 'NOVENO', 'SDFA', '-', '', '', '', 2, '12345', 'pablo', '2004-07-22', '21:54:28');
INSERT INTO `_personas` VALUES ('KU70A', '-', 10101010, 0, 0, 'DECIMO', 'DSAF', '-', '', '', '', 2, '12345', 'pablo', '2004-07-22', '21:54:56');
INSERT INTO `_personas` VALUES ('219F1', '-', 12121212, 0, 0, 'FIRST', 'SDSDF', '-', '', '', '', 2, '12345', 'pablo', '2004-07-22', '22:10:45');
INSERT INTO `_personas` VALUES ('TUNWQ', '-', 13131313, 0, 0, 'SECOND', 'SDFA', '-', '', '', '', 2, '12345', 'pablo', '2004-07-22', '22:14:34');
INSERT INTO `_personas` VALUES ('QP6U5', '-', 14141414, 0, 0, 'Lopez Sebastian', 'SDFAS', '-', '', '', '', 2, '12345', 'pablo', '2004-07-22', '22:19:05');
INSERT INTO `_personas` VALUES ('025G7', '-', 15151515, 0, 0, 'NOVENO', 'DOMICILIO DE NOVENO', '-', '', '', '', 2, '12345', 'pablo', '2004-09-23', '15:52:35');
INSERT INTO `_personas` VALUES ('IBGDH', 'F', 44123123, 0, 0, 'MITRE CANDELA', 'ALVEAR', 'F', 'ARGENTINA', '', '', 2, '12345', 'jorge', '2004-09-23', '16:49:38');
INSERT INTO `_personas` VALUES ('BJLDF', 'F', 12345678, 0, 0, 'LLANOS JUAN', 'ADFS', 'M', '', '', '', 2, '12345', 'jorge', '2004-09-23', '16:59:55');
INSERT INTO `_personas` VALUES ('J4I7S', '-', 33333333, 0, 0, 'sdfg', 'sdfg', '-', '', '', '', 2, '12345', 'jorge', '2004-09-23', '17:05:20');
INSERT INTO `_personas` VALUES ('36K8F', '-', 85214793, 0, 0, 'sdfgsdfgd', 'sdgsdfg', '-', '', '', '', 2, '12345', 'jorge', '2004-09-23', '17:07:17');
INSERT INTO `_personas` VALUES ('W6AAH', '-', 85748585, 0, 0, 'asdfasdfasdfas', 'asdf', '-', '', '', '', 2, '12345', 'jorge', '2004-09-23', '17:10:02');
INSERT INTO `_personas` VALUES ('TBES3', '-', 87521456, 0, 0, 'sdfgsdgsdñklsdfg', 'sldfg q', '-', '', '', '', 2, '12345', 'jorge', '2004-09-23', '17:12:47');
INSERT INTO `_personas` VALUES ('B0M42', '-', 85554793, 0, 0, 'sdfg sdfg', 'sdfg', '-', '', '', '', 2, '12345', 'jorge', '2004-09-23', '17:14:16');
INSERT INTO `_personas` VALUES ('M0R2S', '-', 333, 33, 33, '33', '33', '-', '33', '', '', 2, '12345', 'pablo', '2004-09-23', '19:13:26');
INSERT INTO `_personas` VALUES ('YC8U4', '-', 66, 66, 66, '66', '66', '-', '66', '', '66', 2, '12345', 'pablo', '2004-09-23', '19:15:18');
INSERT INTO `_personas` VALUES ('REO81', '-', 89, 89, 89, '89', '89', '-', '89', '', '89', 2, '12345', 'pablo', '2004-09-23', '19:17:07');
INSERT INTO `_personas` VALUES ('2KJ10', 'F', 17340626, 2147483647, 0, 'ZAVALETA HUGO', 'CORDOBA 669', 'M', 'ARGENTINA', '', 'INGENIERO', 2, '12345', 'jorge', '2004-09-24', '10:25:27');
INSERT INTO `_personas` VALUES ('KGNM0', '-', 79797979, 0, 0, 'rrr', 'rrr', '-', '', '', '', 2, '12345', 'pablo', '2004-09-24', '23:34:32');
INSERT INTO `_personas` VALUES ('2K0JW', '-', 40123123, 0, 0, 'LO QUE SEA', 'KHGJHGHJ', '-', '', '', '', 2, '12345', 'jorge', '2004-07-27', '18:44:41');
