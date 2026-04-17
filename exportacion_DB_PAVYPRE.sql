CREATE DATABASE  IF NOT EXISTS `pavypre` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `pavypre`;
-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: localhost    Database: pavypre
-- ------------------------------------------------------
-- Server version	8.0.41

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `pv_clientes`
--

DROP TABLE IF EXISTS `pv_clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pv_clientes` (
  `id_cliente` varchar(6) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `direccion` varchar(50) NOT NULL,
  `email` varchar(30) DEFAULT NULL,
  `contrasena` varchar(255) NOT NULL,
  PRIMARY KEY (`id_cliente`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pv_clientes`
--

LOCK TABLES `pv_clientes` WRITE;
/*!40000 ALTER TABLE `pv_clientes` DISABLE KEYS */;
INSERT INTO `pv_clientes` (`id_cliente`, `nombre`, `telefono`, `direccion`, `email`, `contrasena`) VALUES ('CTE001','Obras Públicas del Estado','2222109100','Av. Juárez 3 Centro, Puebla','obras.publicas@puebla.gob.mx','$argon2id$v=19$m=65536,t=4,p=1$dDcxdTVUQXdWL2F3RnlYTw$1aR4G4+stcg/b4OJmj861C5pVd4j/HoCoUEu7FbCp4g'),('CTE002','Gustavo López','2221357921','Calle Reforma, Puebla','gustavo.lopez@gmail.com','$argon2id$v=19$m=65536,t=4,p=1$UGNPaFpZcE50eWd0ZFdPYg$P2cuJbST4yssBuLn0dgj5XxkMxh+J/TvqTcUdzR345o'),('CTE003','Carlos Hernández','2226543212','Blvd. Norte, Puebla','carlos.hernandez@gmail.com','$argon2id$v=19$m=65536,t=4,p=1$akJQUmJGS0NRNC42RWVtbA$QAHiUT7GbOx1f/rIJhZyjkeaAg5JPvpVVniqT+hIi3I'),('CTE004','Ana Gómez','2221247900','Col. La Paz, Puebla','ana.gomez@gmail.com','$argon2id$v=19$m=65536,t=4,p=1$UjFLanFLWm96bkhYQ3lTbg$Q/7+37nYzwg3BOTuMVgh14ZNagksE5MCS/dOwue2WY8'),('CTE005','Rodrigo Ramírez','2225698901','Zona Centro, Puebla','rodrigo.ramirez@gmail.com','$argon2id$v=19$m=65536,t=4,p=1$bFNDU3NaU0tialViNFlKeg$SGjKMI5iyr2C+vDGmBrnRN3G38uaI+z8HViQRhy20R8');
/*!40000 ALTER TABLE `pv_clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pv_cobros`
--

DROP TABLE IF EXISTS `pv_cobros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pv_cobros` (
  `id_cobro` int NOT NULL AUTO_INCREMENT,
  `fecha_pago` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `monto` decimal(10,2) NOT NULL,
  `tipo_pago` varchar(30) NOT NULL,
  `id_cliente` varchar(6) NOT NULL,
  `id_obra` varchar(6) NOT NULL,
  PRIMARY KEY (`id_cobro`),
  KEY `cbo_dpe_fk` (`id_cliente`,`id_obra`),
  KEY `idx_obra` (`id_obra`),
  KEY `idx_cliente` (`id_cliente`),
  CONSTRAINT `cbo_dpe_fk` FOREIGN KEY (`id_cliente`, `id_obra`) REFERENCES `pv_disposiciones` (`id_cliente`, `id_obra`)
) ENGINE=InnoDB AUTO_INCREMENT=144 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pv_cobros`
--

LOCK TABLES `pv_cobros` WRITE;
/*!40000 ALTER TABLE `pv_cobros` DISABLE KEYS */;
INSERT INTO `pv_cobros` (`id_cobro`, `fecha_pago`, `monto`, `tipo_pago`, `id_cliente`, `id_obra`) VALUES (72,'2015-06-20 17:00:00',338015.20,'Transferencia','CTE003','OBA001'),(73,'2015-08-29 17:00:00',241439.43,'Efectivo','CTE003','OBA001'),(74,'2015-11-24 17:00:00',241439.43,'Cheque','CTE003','OBA001'),(75,'2016-02-23 14:12:00',144863.66,'Transferencia','CTE003','OBA001'),(79,'2018-03-15 14:00:00',801310.04,'Transferencia','CTE001','OBA002'),(80,'2018-11-18 14:00:00',701146.28,'Cheque','CTE001','OBA002'),(81,'2019-09-27 23:00:00',500818.77,'Transferencia','CTE001','OBA002'),(82,'2020-07-10 13:20:00',476406.31,'Transferencia','CTE001','OBA003'),(83,'2020-09-01 13:20:00',340290.22,'Efectivo','CTE001','OBA003'),(84,'2020-11-09 13:20:00',340290.22,'Efectivo','CTE001','OBA003'),(85,'2021-01-21 01:00:00',204174.13,'Transferencia','CTE001','OBA003'),(89,'2017-01-27 18:00:00',548015.90,'Cheque','CTE002','OBA004'),(90,'2017-03-28 18:00:00',469727.91,'Transferencia','CTE002','OBA004'),(91,'2017-06-13 18:00:00',313151.94,'Efectivo','CTE002','OBA004'),(92,'2017-09-01 15:20:00',234863.96,'Transferencia','CTE002','OBA004'),(96,'2021-09-15 13:10:00',816963.04,'Transferencia','CTE004','OBA005'),(97,'2022-02-26 13:10:00',714842.66,'Efectivo','CTE004','OBA005'),(98,'2022-09-24 17:05:00',510601.90,'Transferencia','CTE004','OBA005'),(99,'2014-05-24 14:30:00',378668.50,'Transferencia','CTE002','OBA006'),(100,'2014-08-18 14:30:00',270477.50,'Cheque','CTE002','OBA006'),(101,'2014-12-07 14:30:00',270477.50,'Efectivo','CTE002','OBA006'),(102,'2015-04-16 13:00:00',162286.50,'Transferencia','CTE002','OBA006'),(106,'2016-08-25 18:30:00',794945.41,'Cheque','CTE001','OBA007'),(107,'2017-03-16 16:00:00',650409.88,'Transferencia','CTE001','OBA007'),(109,'2019-03-15 16:20:00',630590.45,'Transferencia','CTE005','OBA008'),(110,'2019-07-09 16:20:00',551766.65,'Efectivo','CTE005','OBA008'),(111,'2019-12-07 17:00:00',394119.03,'Transferencia','CTE005','OBA008'),(112,'2022-04-10 14:40:00',1079668.95,'Transferencia','CTE005','OBA009'),(113,'2022-10-19 14:40:00',719779.30,'Cheque','CTE005','OBA009'),(114,'2023-05-07 22:20:00',599816.09,'Transferencia','CTE005','OBA009'),(115,'2013-02-23 18:05:00',1046559.34,'Transferencia','CTE001','OBA010'),(116,'2014-01-23 20:10:00',697706.23,'Efectivo','CTE001','OBA010'),(118,'2021-07-05 15:10:00',492244.18,'Cheque','CTE003','OBA011'),(119,'2021-08-07 15:10:00',351602.99,'Transferencia','CTE003','OBA011'),(120,'2021-09-21 15:10:00',351602.99,'Efectivo','CTE003','OBA011'),(121,'2021-11-10 00:05:00',210961.79,'Transferencia','CTE003','OBA011'),(125,'2015-09-17 15:20:00',904972.04,'Transferencia','CTE002','OBA012'),(126,'2016-01-30 15:20:00',791850.54,'Efectivo','CTE002','OBA012'),(127,'2016-07-22 23:10:00',565607.53,'Transferencia','CTE002','OBA012'),(128,'2020-01-15 14:05:00',213708.96,'Transferencia','CTE002','OBA013'),(129,'2020-07-19 22:50:00',174852.78,'Cheque','CTE002','OBA013'),(131,'2017-07-27 16:40:00',944069.30,'Transferencia','CTE004','OBA014'),(132,'2017-10-21 16:40:00',826060.64,'Efectivo','CTE004','OBA014'),(133,'2018-02-12 22:30:00',590043.31,'Transferencia','CTE004','OBA014'),(134,'2015-02-20 18:10:00',770834.76,'Cheque','CTE004','OBA015'),(135,'2015-05-01 18:10:00',550596.26,'Transferencia','CTE004','OBA015'),(136,'2015-07-27 18:10:00',550596.26,'Efectivo','CTE004','OBA015'),(137,'2015-10-26 21:10:00',330357.75,'Transferencia','CTE004','OBA015'),(141,'2024-08-20 17:10:00',1412120.78,'Transferencia','CTE001','OBA016'),(142,'2025-03-24 17:10:00',1235605.69,'Efectivo','CTE001','OBA016'),(143,'2025-12-22 17:10:00',882575.49,'Transferencia','CTE001','OBA016');
/*!40000 ALTER TABLE `pv_cobros` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pv_disposiciones`
--

DROP TABLE IF EXISTS `pv_disposiciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pv_disposiciones` (
  `id_cliente` varchar(6) NOT NULL,
  `id_obra` varchar(6) NOT NULL,
  PRIMARY KEY (`id_cliente`,`id_obra`),
  KEY `dpe_oba_fk` (`id_obra`),
  CONSTRAINT `dpe_cte_fk` FOREIGN KEY (`id_cliente`) REFERENCES `pv_clientes` (`id_cliente`),
  CONSTRAINT `dpe_oba_fk` FOREIGN KEY (`id_obra`) REFERENCES `pv_obras` (`id_obra`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pv_disposiciones`
--

LOCK TABLES `pv_disposiciones` WRITE;
/*!40000 ALTER TABLE `pv_disposiciones` DISABLE KEYS */;
INSERT INTO `pv_disposiciones` (`id_cliente`, `id_obra`) VALUES ('CTE003','OBA001'),('CTE001','OBA002'),('CTE001','OBA003'),('CTE002','OBA004'),('CTE004','OBA005'),('CTE002','OBA006'),('CTE001','OBA007'),('CTE005','OBA008'),('CTE005','OBA009'),('CTE001','OBA010'),('CTE003','OBA011'),('CTE002','OBA012'),('CTE002','OBA013'),('CTE004','OBA014'),('CTE004','OBA015'),('CTE001','OBA016');
/*!40000 ALTER TABLE `pv_disposiciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pv_empleados`
--

DROP TABLE IF EXISTS `pv_empleados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pv_empleados` (
  `id_empleado` varchar(6) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `puesto` varchar(30) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `direccion` varchar(50) NOT NULL,
  `email` varchar(30) DEFAULT NULL,
  `salario` decimal(10,2) NOT NULL,
  `id_supervisor` varchar(6) DEFAULT NULL,
  `contrasena` varchar(255) NOT NULL,
  PRIMARY KEY (`id_empleado`),
  UNIQUE KEY `email` (`email`),
  KEY `epo_epo_fk` (`id_supervisor`),
  CONSTRAINT `epo_epo_fk` FOREIGN KEY (`id_supervisor`) REFERENCES `pv_empleados` (`id_empleado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pv_empleados`
--

LOCK TABLES `pv_empleados` WRITE;
/*!40000 ALTER TABLE `pv_empleados` DISABLE KEYS */;
INSERT INTO `pv_empleados` (`id_empleado`, `nombre`, `puesto`, `telefono`, `direccion`, `email`, `salario`, `id_supervisor`, `contrasena`) VALUES ('ADMIN1','Edgar Robles','Administrador','2214170881','Calle Domingo Arenas','edgarrobles076@gmail.com',5000.00,NULL,'$argon2id$v=19$m=65536,t=4,p=1$QktwWGJ5cXQwbnlLQXR3dA$y5yrlDqWx58UMGuyejRKYCkIIbblIT0TLoMFlSkoovw'),('EPO001','Gustavo Torres','Ingeniero Topógrafo','2223456789','Calle 5 de Febrero, Puebla','gustavo.torres@pavypre.com',6060.09,'EPO003','$argon2id$v=19$m=65536,t=4,p=1$V1dLWlViTjdyUUdOV1RXLg$9C4lzfLjJPuJ/gEyQEnzGNzN1dUwHP0F5/kgUsseHik'),('EPO002','Carlos García','Ingeniero Superintendente','222-2345678','Avenida Juárez, Puebla','carlos.garcia@pavypre.com',10300.92,NULL,'$argon2id$v=19$m=65536,t=4,p=1$N1RUZmdzbzBYV1VMRXc4Sg$J/KNya2ZlNHsnKNYpmhWp5rxbuwjhwqe6AA2myQLupU'),('EPO003','Ana Lopez','Ingeniero Residente','2224567890','Calle Revolución, Puebla','ana.lopez@pavypre.com',7031.46,'EPO002','$argon2id$v=19$m=65536,t=4,p=1$NVR2dHVMN2RXbmVXZ09wVA$KYUD4+5HnEgJzQc+a+hiVBk92SA4+8YKEHyigL94XbY'),('EPO004','Pedro Rodriguez','Sobrestante','2225678901','Calle 3 Sur, Puebla','pedro.rodriguez@pavypre.com',5893.00,'EPO003','$argon2id$v=19$m=65536,t=4,p=1$NWgvaGd2c1RGUUkuMmVrUA$YCbXZjHQiRljFwkmIg76G5XwfwE8/SgpT5qz25NrEAY'),('EPO005','Luis Martinez','Operador','2226789012','Avenida 16 de Septiembre, Puebla','luis.martinez@pavypre.com',4081.32,'EPO003','$argon2id$v=19$m=65536,t=4,p=1$c1hCcTFYVmhuS0lqdWYyWA$EwkI3DXqhYAVUAodqDRYaQfc3tLwvMxZ+Gjpp8SMDcc'),('EPO006','José Hernández','Rastrillero','2227890123','Calle 12 Oriente, Puebla','jose.hernandez@pavypre.com',3960.60,'EPO004','$argon2id$v=19$m=65536,t=4,p=1$WENYNUphL2F6TjVsWlEzaA$txhFUeUsdN0SqGRy/Ls4k+5kxT+1cy3D7TqwwKQR49I'),('EPO007','Luis Gomez','Sensorista','2228901234','Calle 15 Poniente, Puebla','luis.gomez@pavypre.com',5759.55,'EPO003','$argon2id$v=19$m=65536,t=4,p=1$RjV0WFhrdU56eUVqdnZzUQ$C+tezYijKLGcPWiD2l+d/hMyQnF3uSR+4yx+urYetg4'),('EPO008','Ricardo Torres','Tornillero','2229012345','Avenida San Martín, Puebla','ricardo.torres@pavypre.com',3490.78,'EPO004','$argon2id$v=19$m=65536,t=4,p=1$M01XYVR2Rjl0enluLlNHMg$7056N2ckMnoH2Q7kkix5Jk6HCeggrGn2kKrEv158X/k'),('EPO010','Sofía González','Obrero','2221234567','Calle 22 Sur, Puebla','sofia.gonzalez@pavypre.com',3633.33,'EPO004','$argon2id$v=19$m=65536,t=4,p=1$OEZDTWFUek9ITGlreGYuSA$ayzbIS7pQTz/yYpalvk+a5Wb33BAeTIH6cV5E0/r2Yw'),('EPO011','Julio Rodriguez','Velador','2222345678','Calle 25 Oriente, Puebla','julio.rodriguez@pavypre.com',3073.00,'EPO003','$argon2id$v=19$m=65536,t=4,p=1$VW5TRUw3VVJmZm80VlM0Ng$gnFK/JDoYOZ8liEYl5GPpms4W1nRl4KWGTo8ZDQb+Gw'),('EPO012','Mario Peralta','Personal de Seguridad','2223456789','Calle 17 Poniente, Puebla','mario.peralta@pavypre.com',3722.85,'EPO003','$argon2id$v=19$m=65536,t=4,p=1$RDZ2eFBqdXpOMmQ4bTR3Ng$V3hIXbBAT3oZ3RJt0hCm6TPEWBO/jc9mOQ9krkCLdn8'),('EPO013','Héctor Ramírez','Ingeniero Topógrafo','2226543210','Calle Reforma, Puebla','hector.ramirez@pavypre.com',6145.35,'EPO003','$argon2id$v=19$m=65536,t=4,p=1$Ly5xLkJJd3V0bm5oRWs1bQ$5f7jGCLEEGaNHFgc9qNhCAWWhCDCpc9QqqzV6YmJ32c'),('EPO014','Alberto Jiménez','Operador','2227654321','Avenida Juárez, Puebla','alberto.jimenez@pavypre.com',3521.14,'EPO004','$argon2id$v=19$m=65536,t=4,p=1$RnFPSkJ6aG9iSHFWYXkxVA$kykuJ4z7tHU8pjgrsyBjOeRMN5OqoLS0+tugbCDk7LA'),('EPO015','Fernanda Lopez','Operador','2228765432','Colonia El Carmen, Puebla','fernanda.lopez@pavypre.com',5190.79,'EPO003','$argon2id$v=19$m=65536,t=4,p=1$NVFzbTVDSWVSa0RKYkFyMA$94Aa/uyN7ETJOiDBeQR1VV8A1Vq/eaQBF91UBtSbRXQ'),('EPO016','Diego Castillo','Rastrillero','2229876543','Calle 10 Oriente, Puebla','diego.castillo@pavypre.com',3141.83,'EPO004','$argon2id$v=19$m=65536,t=4,p=1$WTdaNW1EOVFad0U1dTdSLg$tDTD2NR7ydQQ6OCk9u7eBoo9HZMVt8IXQihYFX+gX98'),('EPO017','Gabriela Méndez','Rastrillero','2220987654','Boulevard 5 de Mayo, Puebla','gabriela.mendez@pavypre.com',4108.88,'EPO003','$argon2id$v=19$m=65536,t=4,p=1$UTA3c3IwZTFGZ3JkMTRydQ$bcxYpaW704Pf9KMk+/TF7vDLEAGJwmoHPiBoikJ36n8'),('EPO018','Roberto Sánchez','Sensorista','2221357924','Calle 7 Norte, Puebla','roberto.sanchez@pavypre.com',3513.56,'EPO004','$argon2id$v=19$m=65536,t=4,p=1$ZUhNcy9zVVN1V1pWTEQvLw$vWp0GnDo6pf4O5Zn4J4bZRWy4Ba1pSIAmEts+PttrWo'),('EPO019','Alicia Ortega','Sensorista','2222468135','Colonia Las Ánimas, Puebla','alicia.ortega@pavypre.com',7207.60,'EPO003','$argon2id$v=19$m=65536,t=4,p=1$VWpKZERJeTc5bzhTY2lVZw$fIJOiauhAggwzZNz41fiFmn+zM8/oa+8ilti88X1fIA'),('EPO020','Ricardo Morales','Tornillero','2223579246','Calle 20 Sur, Puebla','ricardo.morales@pavypre.com',3339.18,'EPO003','$argon2id$v=19$m=65536,t=4,p=1$M2JnbGxzNDUxSVRnMEpYVQ$sf+PuG3HnnR36fGCQK+r8fBQ+Rd3IeMHWkBCoiqLVL4'),('EPO021','Marisol Rivas','Tornillero','2224681357','Zona Centro, Puebla','marisol.rivas@pavypre.com',3335.55,'EPO004','$argon2id$v=19$m=65536,t=4,p=1$Wk83YU1XSjdPR2pqS2dKRA$cMwwPDfOJYCG80e0gLZDIDVRBcRQsUUR9RC+nSzwBYQ'),('EPO022','Luis Fernández','Obrero','2225792468','Colonia La Paz, Puebla','luis.fernandez@pavypre.com',2504.88,'EPO003','$argon2id$v=19$m=65536,t=4,p=1$TmtDeHcyandzaWEwbHlBWg$Ef0TKbapz1+YDZz9Z+FmQiznIsHtdIwyaZ3OeTBEdyM'),('EPO023','Carmen Vázquez','Obrero','2226803579','Calle 3 Poniente, Puebla','carmen.vazquez@pavypre.com',3113.17,'EPO004','$argon2id$v=19$m=65536,t=4,p=1$WmVYcjBYOThIQS9BbGxOYw$vm8hEL23jkb9E+13SeD6yIgirDsPtA99rUvgRNx0FTg'),('EPO024','Óscar Torres','Obrero','2227914680','Calle 30 Oriente, Puebla','oscar.torres@pavypre.com',3347.40,'EPO004','$argon2id$v=19$m=65536,t=4,p=1$OFNUb21pbVE4c2lsb3RRUA$nRheaBgnrVRTsTp0ocsi43t69jUYfWq+tjY2FGFE32Y'),('EPO026','Tomás Gutiérrez','Velador','2229136802','Calle 4 Norte, Puebla','tomas.gutierrez@pavypre.com',2453.25,'EPO004','$argon2id$v=19$m=65536,t=4,p=1$Y1VPaDV1OVJKYm5PY1R6aQ$TkT1d/mLius9lym7S0gXsHBbZ+ItHEo/QeQrYwGVHBk'),('EPO027','Paola Castro','Velador','2220247913','Colonia Resurgimiento, Puebla','paola.castro@pavypre.com',2666.85,'EPO003','$argon2id$v=19$m=65536,t=4,p=1$M0lvaE44S1V3ejE1cFc0TQ$JDTklerTZ2433elyfjugX3sEyvJXTuy+bzm3mYt/joM'),('EPO028','Esteban Salazar','Personal de Seguridad','2221358024','Colonia Mayorazgo, Puebla','esteban.salazar@pavypre.com',4262.30,'EPO003','$argon2id$v=19$m=65536,t=4,p=1$aU5jb1FmdmJjNi4zcGtyeg$lF/S6xKEzvRJ0RUMuNivCj920qAvxRpQiDZSgMopWIQ'),('EPO029','Verónica Cruz','Personal de Seguridad','2222469135','Calle 18 Poniente, Puebla','veronica.cruz@pavypre.com',4540.10,'EPO004','$argon2id$v=19$m=65536,t=4,p=1$SnNieXJMQ1ZZRWNFZXFrWA$+dACTCGj4n+P0STDNbBT8bNbNksR2vy2cQm/06lUFcg');
/*!40000 ALTER TABLE `pv_empleados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pv_empleos_insumos`
--

DROP TABLE IF EXISTS `pv_empleos_insumos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pv_empleos_insumos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_insumo` varchar(6) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cantidad` int NOT NULL,
  `id_cliente` varchar(6) NOT NULL,
  `id_obra` varchar(6) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_insumo_obra` (`id_insumo`,`id_obra`),
  KEY `empl_dpe_fk` (`id_cliente`,`id_obra`),
  KEY `idx_obra` (`id_obra`),
  CONSTRAINT `empl_dpe_fk` FOREIGN KEY (`id_cliente`, `id_obra`) REFERENCES `pv_disposiciones` (`id_cliente`, `id_obra`),
  CONSTRAINT `empl_iso_fk` FOREIGN KEY (`id_insumo`) REFERENCES `pv_insumos` (`id_insumo`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pv_empleos_insumos`
--

LOCK TABLES `pv_empleos_insumos` WRITE;
/*!40000 ALTER TABLE `pv_empleos_insumos` DISABLE KEYS */;
INSERT INTO `pv_empleos_insumos` (`id`, `id_insumo`, `fecha`, `cantidad`, `id_cliente`, `id_obra`) VALUES (3,'ISO003','2015-07-20 15:47:00',19,'CTE003','OBA001'),(4,'ISO004','2015-06-16 13:59:00',301,'CTE003','OBA001'),(5,'ISO005','2015-06-17 18:15:00',60,'CTE003','OBA001'),(6,'ISO006','2015-10-27 13:15:00',111,'CTE003','OBA001'),(7,'ISO007','2015-06-17 17:03:00',28,'CTE003','OBA001'),(8,'ISO001','2018-03-19 13:00:00',1234,'CTE001','OBA002'),(9,'ISO002','2018-03-16 17:30:00',144,'CTE001','OBA002'),(13,'ISO006','2018-08-28 14:00:00',223,'CTE001','OBA002'),(14,'ISO007','2018-03-17 20:30:00',20,'CTE001','OBA002'),(15,'ISO001','2020-07-06 15:00:00',753,'CTE001','OBA003'),(16,'ISO002','2020-07-05 15:00:00',134,'CTE001','OBA003'),(17,'ISO003','2020-07-05 15:00:00',40,'CTE001','OBA003'),(18,'ISO004','2020-07-06 16:00:00',188,'CTE001','OBA003'),(19,'ISO005','2020-07-07 14:30:00',189,'CTE001','OBA003'),(23,'ISO003','2017-01-25 18:58:00',37,'CTE002','OBA004'),(24,'ISO004','2017-01-31 19:00:00',219,'CTE002','OBA004'),(25,'ISO005','2017-02-15 17:30:00',59,'CTE002','OBA004'),(26,'ISO006','2017-05-05 14:30:00',299,'CTE002','OBA004'),(27,'ISO007','2017-01-23 18:58:00',26,'CTE002','OBA004'),(28,'ISO001','2021-10-05 13:15:00',1677,'CTE004','OBA005'),(29,'ISO002','2021-09-13 13:33:00',278,'CTE004','OBA005'),(33,'ISO006','2022-04-15 16:15:00',69,'CTE004','OBA005'),(34,'ISO007','2021-09-10 14:10:00',6,'CTE004','OBA005'),(35,'ISO001','2015-03-21 14:30:00',515,'CTE002','OBA006'),(36,'ISO002','2014-06-11 14:36:00',476,'CTE002','OBA006'),(37,'ISO003','2014-06-12 16:45:00',28,'CTE002','OBA006'),(38,'ISO004','2014-07-01 16:10:00',229,'CTE002','OBA006'),(39,'ISO006','2014-05-20 14:40:00',159,'CTE002','OBA006'),(43,'ISO003','2016-08-28 18:49:00',37,'CTE001','OBA007'),(44,'ISO004','2016-10-05 20:15:00',311,'CTE001','OBA007'),(45,'ISO005','2016-09-10 15:10:00',119,'CTE001','OBA007'),(46,'ISO006','2017-01-08 16:40:00',100,'CTE001','OBA007'),(47,'ISO007','2016-08-21 14:30:00',12,'CTE001','OBA007'),(48,'ISO001','2019-07-29 14:30:00',778,'CTE005','OBA008'),(49,'ISO002','2019-03-11 17:20:00',154,'CTE005','OBA008'),(53,'ISO006','2019-05-17 15:30:00',109,'CTE005','OBA008'),(54,'ISO007','2019-03-10 16:20:00',7,'CTE005','OBA008'),(55,'ISO001','2022-04-05 19:00:00',1863,'CTE005','OBA009'),(56,'ISO002','2022-04-05 16:40:00',185,'CTE005','OBA009'),(57,'ISO003','2022-04-17 15:00:00',21,'CTE005','OBA009'),(58,'ISO004','2022-04-06 14:40:00',175,'CTE005','OBA009'),(59,'ISO005','2022-07-22 18:20:00',109,'CTE005','OBA009'),(63,'ISO002','2013-02-18 20:10:00',393,'CTE001','OBA010'),(64,'ISO003','2013-02-20 14:30:00',33,'CTE001','OBA010'),(65,'ISO004','2013-02-18 16:03:00',272,'CTE001','OBA010'),(66,'ISO005','2013-03-25 21:30:00',98,'CTE001','OBA010'),(67,'ISO006','2013-07-06 14:40:00',188,'CTE001','OBA010'),(68,'ISO007','2013-02-18 19:05:00',10,'CTE001','OBA010'),(69,'ISO001','2021-09-03 15:10:00',1337,'CTE003','OBA011'),(73,'ISO005','2021-06-30 15:25:00',220,'CTE003','OBA011'),(74,'ISO007','2021-07-01 15:30:00',67,'CTE003','OBA011'),(75,'ISO001','2015-12-12 15:00:00',978,'CTE002','OBA012'),(76,'ISO002','2015-09-12 16:20:00',149,'CTE002','OBA012'),(77,'ISO003','2015-09-12 15:20:00',34,'CTE002','OBA012'),(78,'ISO004','2015-09-13 15:00:00',272,'CTE002','OBA012'),(79,'ISO005','2015-09-15 17:40:00',192,'CTE002','OBA012'),(83,'ISO001','2017-12-12 16:40:00',1087,'CTE004','OBA014'),(84,'ISO002','2017-07-26 13:00:00',241,'CTE004','OBA014'),(85,'ISO003','2017-07-22 16:20:00',19,'CTE004','OBA014'),(86,'ISO004','2017-07-23 16:40:00',196,'CTE004','OBA014'),(87,'ISO005','2017-07-30 14:40:00',357,'CTE004','OBA014'),(88,'ISO007','2017-07-22 16:40:00',11,'CTE004','OBA014'),(89,'ISO001','2015-08-07 14:00:00',1092,'CTE004','OBA015'),(93,'ISO006','2015-02-16 13:40:00',172,'CTE004','OBA015'),(94,'ISO007','2015-02-15 20:10:00',24,'CTE004','OBA015'),(95,'ISO001','2025-03-06 16:15:00',982,'CTE001','OBA016'),(96,'ISO002','2024-08-16 15:58:00',665,'CTE001','OBA016'),(97,'ISO003','2024-08-15 18:30:00',28,'CTE001','OBA016'),(98,'ISO004','2024-08-15 17:40:00',146,'CTE001','OBA016'),(99,'ISO005','2024-09-05 14:10:00',111,'CTE001','OBA016');
/*!40000 ALTER TABLE `pv_empleos_insumos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pv_herramientas`
--

DROP TABLE IF EXISTS `pv_herramientas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pv_herramientas` (
  `id_herramienta` varchar(6) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `proveedor_id` int NOT NULL DEFAULT '0',
  `renta_semanal` decimal(10,2) NOT NULL,
  `imagen` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_herramienta`),
  KEY `fk_herramientas_proveedor` (`proveedor_id`),
  CONSTRAINT `fk_herramientas_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `pv_proveedores` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pv_herramientas`
--

LOCK TABLES `pv_herramientas` WRITE;
/*!40000 ALTER TABLE `pv_herramientas` DISABLE KEYS */;
INSERT INTO `pv_herramientas` (`id_herramienta`, `nombre`, `proveedor_id`, `renta_semanal`, `imagen`) VALUES ('FD2015','Perfiladora PM200',16,1890.12,'uploads/herramientas/perfilladoraPM200_1775354731.jpeg'),('HRA001','Extenedora de Asfaltos',16,6487.68,'uploads/herramientas/pavimentadora.jpeg'),('HRA002','Rodillo Tándem',16,3748.72,'uploads/herramientas/rodilloTandem.jpeg'),('HRA003','Compactador Neumático',17,5162.48,'uploads/herramientas/compactadorNeumatico.jpeg'),('HRA004','Barredora',17,3154.97,'uploads/herramientas/barredora.jpeg'),('HRA005','Fresadora',16,5765.96,'uploads/herramientas/perfiladora.jpeg'),('HRA006','Petrolizadora',18,3838.61,'uploads/herramientas/petrolizadora.jpeg'),('HRA007','Lowboy',18,2492.53,'uploads/herramientas/remolqueLowBoy.jpeg'),('HRA008','Compactador Vibratorio',17,3325.30,'uploads/herramientas/compactadorVibratorio.jpeg');
/*!40000 ALTER TABLE `pv_herramientas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pv_insumos`
--

DROP TABLE IF EXISTS `pv_insumos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pv_insumos` (
  `id_insumo` varchar(6) NOT NULL,
  `costo_unitario` decimal(10,2) NOT NULL,
  `proveedor_id` int NOT NULL DEFAULT '0',
  `tipo_material` varchar(50) NOT NULL,
  PRIMARY KEY (`id_insumo`),
  KEY `fk_insumos_proveedor` (`proveedor_id`),
  CONSTRAINT `fk_insumos_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `pv_proveedores` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pv_insumos`
--

LOCK TABLES `pv_insumos` WRITE;
/*!40000 ALTER TABLE `pv_insumos` DISABLE KEYS */;
INSERT INTO `pv_insumos` (`id_insumo`, `costo_unitario`, `proveedor_id`, `tipo_material`) VALUES ('ISO001',420.00,1,'Emulsión'),('ISO002',180.00,2,'Base Hidráulica'),('ISO003',534.45,3,'Lubricantes'),('ISO004',15.36,4,'Combustibles'),('ISO005',1440.02,5,'Mezcla Asfáltica'),('ISO006',1500.09,6,'Mezcla Asfáltica'),('ISO007',549.39,7,'Aceites');
/*!40000 ALTER TABLE `pv_insumos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pv_obras`
--

DROP TABLE IF EXISTS `pv_obras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pv_obras` (
  `id_obra` varchar(6) NOT NULL,
  `presupuesto_inicial` decimal(10,2) NOT NULL,
  `utilidad_neta` decimal(10,2) NOT NULL,
  `gasto_empleados` decimal(10,2) NOT NULL,
  `gasto_insumos` decimal(10,2) NOT NULL,
  `gasto_servicios` decimal(10,2) NOT NULL,
  `gasto_herramientas` decimal(10,2) NOT NULL,
  `fecha_inicio` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_fin` timestamp NULL DEFAULT NULL,
  `ubicacion` varchar(50) NOT NULL,
  PRIMARY KEY (`id_obra`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pv_obras`
--

LOCK TABLES `pv_obras` WRITE;
/*!40000 ALTER TABLE `pv_obras` DISABLE KEYS */;
INSERT INTO `pv_obras` (`id_obra`, `presupuesto_inicial`, `utilidad_neta`, `gasto_empleados`, `gasto_insumos`, `gasto_servicios`, `gasto_herramientas`, `fecha_inicio`, `fecha_fin`, `ubicacion`) VALUES ('OBA001',1069383.70,20000.00,409551.48,283072.02,2729.13,270405.07,'2015-06-15 17:00:00','2016-02-20 14:12:00','Blvd. Forjadores, Puebla'),('OBA002',2218548.75,48000.00,872587.75,889707.87,8647.85,232331.62,'2018-03-10 14:00:00','2019-09-25 23:00:00','Autopista Cuota Puebla-México, Puebla'),('OBA003',1203654.23,22800.00,440827.02,636809.46,3813.32,279711.09,'2020-07-05 13:20:00','2021-01-18 01:00:00','Avenida Central, Ecatepec, Estado de México'),('OBA004',1651306.28,92100.00,534461.48,570910.72,8016.44,452371.06,'2017-01-22 18:00:00','2017-08-29 15:20:00','Periférico Ecológico, Puebla'),('OBA005',2301728.25,34700.00,739582.76,861182.55,6944.72,434697.58,'2021-09-10 13:10:00','2022-09-22 17:05:00','Autopista Puebla-Orizaba, Veracruz'),('OBA006',1212748.73,15900.00,171197.06,558976.35,10189.87,341546.73,'2014-05-18 14:30:00','2015-04-12 13:00:00','Vía Morelos, Tlalnepantla, Estado de México'),('OBA007',1303651.67,21300.00,503954.47,352515.67,4521.03,584364.12,'2016-08-20 18:30:00','2017-03-15 16:00:00','Blvd. 5 de Mayo, Puebla'),('OBA008',1548234.74,73000.00,749601.83,521835.54,4659.85,300378.91,'2019-03-10 16:20:00','2019-12-05 17:00:00','Carretera Puebla-Atlixco, Puebla'),('OBA009',2660178.69,62000.00,634372.83,986633.63,6335.48,771922.40,'2022-04-05 14:40:00','2023-05-05 22:20:00','Av. 11 Sur, Puebla'),('OBA010',1963554.09,12600.00,632481.04,521187.55,9163.91,581433.07,'2013-02-18 18:05:00','2014-01-22 20:10:00','Carretera Federal Puebla-Tehuacán, Puebla'),('OBA011',1480045.35,89200.00,296531.22,915153.53,8468.56,236683.45,'2021-06-30 15:10:00','2021-11-07 00:05:00','Periférico Ecológico, Puebla'),('OBA012',2566995.00,32900.00,964897.03,736413.06,4483.35,556636.67,'2015-09-12 15:20:00','2016-07-20 23:10:00','Carretera Izúcar de Matamoros-Puebla, Puebla'),('OBA013',414216.86,72000.00,130059.71,0.00,11248.19,247253.84,'2020-01-10 14:05:00','2020-07-18 22:50:00','Av. Reforma, Puebla'),('OBA014',2336977.41,53000.00,694085.76,1033215.54,5778.99,627092.96,'2017-07-22 16:40:00','2018-02-10 22:30:00','Carretera San Martín Texmelucan-Puebla, Puebla'),('OBA015',2154749.32,79000.00,518160.23,729840.84,5561.61,948822.34,'2015-02-15 18:10:00','2015-10-23 21:10:00','Autopista México-Puebla, Puebla'),('OBA016',3455720.24,61200.00,1139865.05,709189.38,1455.64,1726545.10,'2024-08-15 17:10:00','2025-12-20 17:10:00','Carretera Federal 190, Cardel, Veracruz');
/*!40000 ALTER TABLE `pv_obras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pv_proveedores`
--

DROP TABLE IF EXISTS `pv_proveedores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pv_proveedores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pv_proveedores`
--

LOCK TABLES `pv_proveedores` WRITE;
/*!40000 ALTER TABLE `pv_proveedores` DISABLE KEYS */;
INSERT INTO `pv_proveedores` (`id`, `nombre`, `email`, `telefono`, `direccion`) VALUES (1,'EMAP','contacto@emap.gob.mx','222 315 4800','Av. Reforma 1625, Centro, Puebla, Pue.'),(2,'Materiales y Agregados S.A.','ventas@materialesagregados.mx','222 413 7090','Carr. Federal México-Puebla Km 118, San Martín Texmelucan, Pue.'),(3,'Lubricantes Industriales del Centro','ventas@lubricentro.mx','222 508 6230','Blvd. Norte 4520, Lomas de Angelópolis, Puebla, Pue.'),(4,'Pemex Distribuidora','distribuidora@pemex.com.mx','800 728 0000','Autopista México-Puebla Km 126, Huejotzingo, Pue.'),(5,'Asfaltos y Pavimentos Nacionales','operaciones@asfaltosnacionales.mx','222 774 3350','Calle 6 Norte 4100, Parque Industrial Puebla 2000, Puebla, Pue.'),(6,'Asfaltos y Agregados del Sur','contacto@asfaltosdelsur.com.mx','222 661 9210','Periferico Ecologico 3620, San Andres Cholula, Pue.'),(7,'Aceites y Lubricantes del Golfo','ventas@lubricantesgolfo.mx','229 935 4470','Blvd. Adolfo Ruiz Cortines 5800, Boca del Río, Ver.'),(8,'Transportes López','operaciones@translopez.mx','222 243 8810','Calle Hidalgo 88, Col. La Paz, Puebla, Pue.'),(9,'Logística Martínez','logisticamartinez@gmail.com','222 310 0540','Calle 5 de Mayo 230, Col. Centro, San Martín Texmelucan, Pue.'),(10,'Flotilla Express','contacto@flotillaexpress.mx','222 567 3390','Av. Tlaxcala 1990, Parque Industrial, Puebla, Pue.'),(11,'Movimientos Herrera','movimientosherrera@hotmail.com','222 481 7770','Av. 11 Sur 3400, Col. La Antigua, Puebla, Pue.'),(12,'Carga y Traslados S.A.','carga@cargatraslados.com.mx','222 609 4450','Blvd. Hermanos Serdan 5610, Puebla, Pue.'),(13,'Transporte Integral Ruiz','ruiz@transporteintegral.mx','222 730 2280','Calle 16 de Septiembre 710, Col. Huexotitla, Puebla, Pue.'),(14,'Distribuciones García','distribuciones.garcia@outlook.com','222 355 8620','Av. Juárez 1250, San Pedro Cholula, Pue.'),(15,'Flota Segura S.A.','contacto@flotasegura.mx','222 812 5560','Recta a Cholula 8820, Lomas de Angelópolis, Puebla, Pue.'),(16,'Arrendadora MACRESA','renta@macresa.com.mx','222 228 4130','Blvd. Valsequillo 2900, Xonaca, Puebla, Pue.'),(17,'Renta de Equipo Vial S.A.','cotizaciones@rentaequipovial.mx','222 496 6880','Calle Independencia 430, Parque Industrial Norte, Huejotzingo, Pue.'),(18,'Transportes y Maquinaria Sur','maquinaria@transmaqsur.mx','222 339 7150','Carr. Puebla-Atlixco Km 7.5, San Jerónimo Caleras, Puebla, Pue.');
/*!40000 ALTER TABLE `pv_proveedores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pv_requerimientos_servicios`
--

DROP TABLE IF EXISTS `pv_requerimientos_servicios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pv_requerimientos_servicios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_servicio` varchar(6) NOT NULL,
  `id_cliente` varchar(6) NOT NULL,
  `id_obra` varchar(6) NOT NULL,
  `kilometraje` decimal(10,2) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `req_dpe_fk` (`id_cliente`,`id_obra`),
  KEY `idx_obra` (`id_obra`),
  KEY `idx_servicio` (`id_servicio`),
  CONSTRAINT `req_dpe_fk` FOREIGN KEY (`id_cliente`, `id_obra`) REFERENCES `pv_disposiciones` (`id_cliente`, `id_obra`),
  CONSTRAINT `req_svo_fk` FOREIGN KEY (`id_servicio`) REFERENCES `pv_servicios` (`id_servicio`)
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pv_requerimientos_servicios`
--

LOCK TABLES `pv_requerimientos_servicios` WRITE;
/*!40000 ALTER TABLE `pv_requerimientos_servicios` DISABLE KEYS */;
INSERT INTO `pv_requerimientos_servicios` (`id`, `id_servicio`, `id_cliente`, `id_obra`, `kilometraje`, `fecha`) VALUES (3,'SVO004','CTE003','OBA001',11.73,'2015-06-16 13:12:42'),(4,'SVO004','CTE003','OBA001',13.39,'2015-06-15 13:19:15'),(5,'SVO005','CTE003','OBA001',12.32,'2015-06-17 21:30:00'),(6,'SVO005','CTE003','OBA001',15.98,'2015-06-20 18:10:00'),(7,'SVO001','CTE001','OBA002',30.43,'2018-03-11 18:00:00'),(8,'SVO001','CTE001','OBA002',42.72,'2018-04-21 18:00:00'),(9,'SVO001','CTE001','OBA002',17.19,'2018-01-10 14:15:00'),(13,'SVO004','CTE001','OBA002',19.75,'2018-03-15 16:20:00'),(14,'SVO005','CTE001','OBA002',25.98,'2018-06-22 22:45:00'),(15,'SVO006','CTE001','OBA002',20.55,'2018-04-21 15:45:00'),(16,'SVO007','CTE001','OBA002',26.46,'2018-04-18 19:15:00'),(17,'SVO002','CTE001','OBA003',31.79,'2020-07-05 15:45:00'),(18,'SVO002','CTE001','OBA003',21.27,'2021-11-11 19:55:00'),(19,'SVO003','CTE001','OBA003',24.94,'2020-07-08 16:45:00'),(23,'SVO001','CTE002','OBA004',19.35,'2017-01-27 15:30:00'),(24,'SVO002','CTE002','OBA004',26.11,'2017-01-24 14:15:00'),(25,'SVO003','CTE002','OBA004',26.60,'2017-01-25 17:20:00'),(26,'SVO003','CTE002','OBA004',36.94,'2017-12-05 15:30:00'),(27,'SVO004','CTE002','OBA004',17.79,'2017-01-24 18:00:00'),(28,'SVO005','CTE002','OBA004',19.83,'2017-01-30 20:45:00'),(29,'SVO007','CTE002','OBA004',20.84,'2017-01-25 18:00:00'),(33,'SVO002','CTE004','OBA005',34.91,'2022-03-20 00:20:00'),(34,'SVO003','CTE004','OBA005',22.80,'2021-09-15 14:00:00'),(35,'SVO006','CTE004','OBA005',25.11,'2021-09-12 18:00:00'),(36,'SVO006','CTE004','OBA005',17.21,'2021-07-08 13:00:00'),(37,'SVO007','CTE004','OBA005',19.96,'2021-09-13 18:00:00'),(38,'SVO008','CTE004','OBA005',19.96,'2021-09-18 22:20:00'),(39,'SVO001','CTE002','OBA006',33.94,'2015-11-28 20:10:00'),(43,'SVO005','CTE002','OBA006',53.61,'2014-05-20 18:00:00'),(44,'SVO005','CTE002','OBA006',7.95,'2014-09-19 18:00:00'),(45,'SVO006','CTE002','OBA006',31.48,'2014-05-19 16:00:00'),(46,'SVO007','CTE002','OBA006',31.43,'2014-05-20 19:45:00'),(47,'SVO007','CTE002','OBA006',18.81,'2014-03-12 16:45:00'),(48,'SVO008','CTE002','OBA006',19.51,'2014-09-19 21:20:00'),(49,'SVO001','CTE001','OBA007',11.60,'2016-08-22 18:00:00'),(53,'SVO004','CTE001','OBA007',8.66,'2016-08-21 18:00:00'),(54,'SVO004','CTE001','OBA007',16.97,'2016-06-15 14:30:00'),(55,'SVO005','CTE001','OBA007',15.58,'2016-08-21 17:15:00'),(56,'SVO008','CTE001','OBA007',33.47,'2017-04-30 18:50:00'),(57,'SVO001','CTE005','OBA008',13.08,'2019-03-11 18:00:00'),(58,'SVO002','CTE005','OBA008',25.54,'2019-03-14 16:15:00'),(59,'SVO003','CTE005','OBA008',20.99,'2019-01-18 15:15:00'),(63,'SVO006','CTE005','OBA008',19.67,'2019-03-17 21:40:00'),(64,'SVO007','CTE005','OBA008',17.46,'2019-03-11 20:00:00'),(65,'SVO001','CTE005','OBA009',27.30,'2022-04-05 13:30:00'),(66,'SVO002','CTE005','OBA009',30.43,'2022-04-08 13:15:00'),(67,'SVO003','CTE005','OBA009',34.49,'2023-05-05 17:45:00'),(68,'SVO004','CTE005','OBA009',19.08,'2022-02-14 14:05:00'),(69,'SVO005','CTE005','OBA009',23.01,'2023-05-08 17:30:00'),(73,'SVO003','CTE001','OBA010',36.45,'2013-02-18 21:15:00'),(74,'SVO004','CTE001','OBA010',20.82,'2013-02-18 18:00:00'),(75,'SVO004','CTE001','OBA010',18.08,'2013-12-03 18:45:00'),(76,'SVO005','CTE001','OBA010',19.06,'2013-02-21 15:20:00'),(77,'SVO006','CTE001','OBA010',32.06,'2014-10-17 22:40:00'),(78,'SVO007','CTE001','OBA010',22.56,'2013-02-18 18:00:00'),(79,'SVO007','CTE001','OBA010',32.73,'2013-12-03 18:00:00'),(83,'SVO002','CTE003','OBA011',21.98,'2021-07-03 14:30:00'),(84,'SVO004','CTE003','OBA011',12.40,'2021-06-30 18:00:00'),(85,'SVO004','CTE003','OBA011',45.48,'2021-11-20 18:00:00'),(86,'SVO004','CTE003','OBA011',19.81,'2021-12-29 18:00:00'),(87,'SVO004','CTE003','OBA011',19.84,'2021-04-12 13:25:00'),(88,'SVO005','CTE003','OBA011',27.26,'2021-06-30 14:00:00'),(89,'SVO006','CTE003','OBA011',24.90,'2021-07-06 23:20:00'),(93,'SVO004','CTE002','OBA012',22.69,'2016-07-23 21:30:00'),(94,'SVO005','CTE002','OBA012',14.20,'2017-04-23 02:15:00'),(95,'SVO006','CTE002','OBA012',23.16,'2017-06-13 04:15:00'),(96,'SVO007','CTE002','OBA012',26.84,'2016-07-20 22:45:00'),(97,'SVO001','CTE002','OBA013',45.10,'2020-07-07 18:00:00'),(98,'SVO001','CTE002','OBA013',17.80,'2021-06-11 18:00:00'),(99,'SVO003','CTE002','OBA013',37.87,'2020-08-22 18:00:00'),(103,'SVO005','CTE002','OBA013',54.63,'2020-08-09 18:00:00'),(104,'SVO006','CTE002','OBA013',41.94,'2020-08-23 18:00:00'),(105,'SVO007','CTE002','OBA013',38.84,'2020-07-31 18:00:00'),(106,'SVO001','CTE004','OBA014',37.12,'2018-12-24 17:50:00'),(107,'SVO002','CTE004','OBA014',32.89,'2017-07-22 15:30:00'),(108,'SVO003','CTE004','OBA014',23.08,'2017-07-25 14:45:00'),(109,'SVO005','CTE004','OBA014',29.08,'2018-02-10 21:00:00'),(113,'SVO002','CTE004','OBA015',29.78,'2016-08-15 22:45:00'),(114,'SVO004','CTE004','OBA015',28.39,'2015-10-23 20:45:00'),(115,'SVO006','CTE004','OBA015',22.87,'2015-01-08 13:30:00'),(116,'SVO007','CTE004','OBA015',26.77,'2015-02-18 16:20:00'),(117,'SVO002','CTE001','OBA016',8.59,'2024-08-15 18:00:00'),(118,'SVO007','CTE001','OBA016',16.57,'2024-08-15 18:00:00');
/*!40000 ALTER TABLE `pv_requerimientos_servicios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pv_reset_tokens`
--

DROP TABLE IF EXISTS `pv_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pv_reset_tokens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(320) NOT NULL,
  `token` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_token` (`token`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pv_reset_tokens`
--

LOCK TABLES `pv_reset_tokens` WRITE;
/*!40000 ALTER TABLE `pv_reset_tokens` DISABLE KEYS */;
INSERT INTO `pv_reset_tokens` (`id`, `email`, `token`, `expires_at`, `used`, `created_at`) VALUES (1,'edgarrobles076@gmail.com','86bfb5f0ffa4120bfe55263750d39903cd8c6b7c4eea56ccb47f1e90d46319e4','2026-04-14 03:46:09',0,'2026-04-13 19:16:09');
/*!40000 ALTER TABLE `pv_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pv_servicios`
--

DROP TABLE IF EXISTS `pv_servicios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pv_servicios` (
  `id_servicio` varchar(6) NOT NULL,
  `costo_kilometro` decimal(10,2) NOT NULL,
  `proveedor_id` int NOT NULL DEFAULT '0',
  `tipo_traslado` varchar(30) NOT NULL,
  PRIMARY KEY (`id_servicio`),
  KEY `fk_servicios_proveedor` (`proveedor_id`),
  CONSTRAINT `fk_servicios_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `pv_proveedores` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pv_servicios`
--

LOCK TABLES `pv_servicios` WRITE;
/*!40000 ALTER TABLE `pv_servicios` DISABLE KEYS */;
INSERT INTO `pv_servicios` (`id_servicio`, `costo_kilometro`, `proveedor_id`, `tipo_traslado`) VALUES ('SVO001',42.35,8,'Insumos'),('SVO002',52.31,9,'Empleados'),('SVO003',41.61,10,'Insumos'),('SVO004',49.52,11,'Empleados'),('SVO005',52.48,12,'Insumos'),('SVO006',42.51,13,'Empleados'),('SVO007',60.73,14,'Insumos'),('SVO008',58.05,15,'Empleados');
/*!40000 ALTER TABLE `pv_servicios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pv_trabajos_empleados`
--

DROP TABLE IF EXISTS `pv_trabajos_empleados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pv_trabajos_empleados` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_empleado` varchar(6) NOT NULL,
  `id_cliente` varchar(6) NOT NULL,
  `id_obra` varchar(6) NOT NULL,
  `fecha_adicion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_termino` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_empleado_obra` (`id_empleado`,`id_obra`),
  KEY `trab_dpe_fk` (`id_cliente`,`id_obra`),
  KEY `idx_obra` (`id_obra`),
  KEY `idx_empleado` (`id_empleado`),
  CONSTRAINT `trab_dpe_fk` FOREIGN KEY (`id_cliente`, `id_obra`) REFERENCES `pv_disposiciones` (`id_cliente`, `id_obra`),
  CONSTRAINT `trab_epo_fk` FOREIGN KEY (`id_empleado`) REFERENCES `pv_empleados` (`id_empleado`)
) ENGINE=InnoDB AUTO_INCREMENT=136 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pv_trabajos_empleados`
--

LOCK TABLES `pv_trabajos_empleados` WRITE;
/*!40000 ALTER TABLE `pv_trabajos_empleados` DISABLE KEYS */;
INSERT INTO `pv_trabajos_empleados` (`id`, `id_empleado`, `id_cliente`, `id_obra`, `fecha_adicion`, `fecha_termino`) VALUES (3,'EPO004','CTE003','OBA001','2015-06-16 15:26:31','2015-11-06 15:26:31'),(4,'EPO005','CTE003','OBA001','2015-06-16 16:26:31','2015-11-07 16:26:31'),(5,'EPO013','CTE003','OBA001','2015-06-16 14:34:50','2015-09-05 14:34:50'),(6,'EPO015','CTE003','OBA001','2015-06-16 16:53:43','2015-10-25 16:53:43'),(7,'EPO027','CTE003','OBA001','2015-06-15 13:19:15','2015-09-20 13:19:15'),(8,'EPO002','CTE001','OBA002','2018-03-10 15:40:11','2018-12-23 15:40:11'),(9,'EPO003','CTE001','OBA002','2018-03-10 16:00:00','2018-10-30 16:00:00'),(13,'EPO022','CTE001','OBA002','2018-03-11 14:40:11','2018-12-16 14:40:11'),(14,'EPO027','CTE001','OBA002','2018-03-15 14:40:11','2019-01-07 14:40:11'),(15,'EPO001','CTE001','OBA003','2020-07-06 14:00:00','2020-10-25 14:00:00'),(16,'EPO002','CTE001','OBA003','2020-07-05 13:40:00','2020-10-07 13:40:00'),(17,'EPO003','CTE001','OBA003','2020-07-06 14:00:00','2020-10-04 14:00:00'),(18,'EPO004','CTE001','OBA003','2020-07-06 14:00:00','2020-09-25 14:00:00'),(19,'EPO005','CTE001','OBA003','2020-07-06 14:00:00','2020-09-26 14:00:00'),(23,'EPO002','CTE002','OBA004','2017-01-22 13:40:42','2017-04-28 13:40:42'),(24,'EPO003','CTE002','OBA004','2017-01-22 15:15:32','2017-05-04 15:15:32'),(25,'EPO004','CTE002','OBA004','2017-01-22 16:39:55','2017-04-21 16:39:55'),(26,'EPO010','CTE002','OBA004','2017-01-23 16:39:55','2017-05-17 16:39:55'),(27,'EPO014','CTE002','OBA004','2017-01-22 16:10:48','2017-06-02 16:10:48'),(28,'EPO018','CTE002','OBA004','2017-01-22 15:15:32','2017-04-15 15:15:32'),(29,'EPO021','CTE002','OBA004','2017-01-23 16:39:55','2017-05-06 16:39:55'),(33,'EPO004','CTE004','OBA005','2021-09-11 14:00:00','2022-04-13 14:00:00'),(34,'EPO006','CTE004','OBA005','2021-09-11 14:00:00','2022-01-14 14:00:00'),(35,'EPO008','CTE004','OBA005','2021-09-11 14:00:00','2022-03-10 14:00:00'),(36,'EPO010','CTE004','OBA005','2021-09-11 14:00:00','2022-04-19 14:00:00'),(37,'EPO014','CTE004','OBA005','2021-09-13 14:00:00','2022-03-15 14:00:00'),(38,'EPO016','CTE004','OBA005','2021-09-14 14:00:00','2022-03-21 14:00:00'),(39,'EPO029','CTE004','OBA005','2021-09-11 14:00:00','2022-02-25 14:00:00'),(43,'EPO017','CTE002','OBA006','2014-05-18 14:57:58','2014-10-12 14:57:58'),(44,'EPO020','CTE002','OBA006','2014-04-09 15:01:26','2014-10-04 15:01:26'),(45,'EPO001','CTE001','OBA007','2016-08-20 16:41:11','2016-11-26 16:41:11'),(46,'EPO002','CTE001','OBA007','2016-08-21 16:33:35','2016-12-05 16:33:35'),(47,'EPO003','CTE001','OBA007','2016-08-20 16:41:11','2016-12-14 16:41:11'),(48,'EPO007','CTE001','OBA007','2016-08-20 16:41:11','2016-11-22 16:41:11'),(49,'EPO017','CTE001','OBA007','2016-08-22 13:17:17','2016-12-18 13:17:17'),(53,'EPO002','CTE005','OBA008','2019-03-10 13:24:30','2019-08-09 13:24:30'),(54,'EPO003','CTE005','OBA008','2019-03-10 14:15:31','2019-07-31 14:15:31'),(55,'EPO007','CTE005','OBA008','2019-03-10 14:15:31','2019-08-09 14:15:31'),(56,'EPO011','CTE005','OBA008','2019-03-10 15:36:04','2019-07-01 15:36:04'),(57,'EPO012','CTE005','OBA008','2019-03-13 16:29:11','2019-07-20 16:29:11'),(58,'EPO019','CTE005','OBA008','2019-03-13 16:29:11','2019-07-26 16:29:11'),(59,'EPO002','CTE005','OBA009','2022-04-05 13:30:00','2022-10-08 13:30:00'),(63,'EPO021','CTE005','OBA009','2022-04-06 13:40:00','2022-10-21 13:40:00'),(64,'EPO024','CTE005','OBA009','2022-04-06 13:40:00','2022-09-24 13:40:00'),(65,'EPO026','CTE005','OBA009','2022-04-07 13:40:00','2022-10-23 13:40:00'),(66,'EPO029','CTE005','OBA009','2022-04-06 13:40:00','2022-09-30 13:40:00'),(67,'EPO001','CTE001','OBA010','2013-03-01 15:21:25','2013-08-21 15:21:25'),(68,'EPO002','CTE001','OBA010','2013-02-18 15:34:51','2013-08-06 15:34:51'),(69,'EPO003','CTE001','OBA010','2013-02-19 14:57:05','2013-08-05 14:57:05'),(73,'EPO023','CTE001','OBA010','2013-02-18 15:27:03','2013-07-17 15:27:03'),(74,'EPO001','CTE003','OBA011','2021-06-30 16:03:59','2021-08-31 16:03:59'),(75,'EPO002','CTE003','OBA011','2021-06-30 16:03:59','2021-08-30 16:03:59'),(76,'EPO003','CTE003','OBA011','2021-06-30 14:59:20','2021-09-05 14:59:20'),(77,'EPO005','CTE003','OBA011','2021-07-01 14:00:00','2021-07-09 14:00:00'),(78,'EPO012','CTE003','OBA011','2021-07-15 14:03:58','2021-09-03 14:03:58'),(79,'EPO017','CTE003','OBA011','2021-07-01 14:00:00','2021-07-08 14:00:00'),(83,'EPO002','CTE002','OBA012','2015-09-12 13:20:00','2016-02-29 13:20:00'),(84,'EPO003','CTE002','OBA012','2015-09-12 14:00:00','2016-03-07 14:00:00'),(85,'EPO004','CTE002','OBA012','2015-09-13 14:00:00','2016-01-11 14:00:00'),(86,'EPO005','CTE002','OBA012','2015-09-13 14:00:00','2016-03-01 14:00:00'),(87,'EPO013','CTE002','OBA012','2015-09-13 14:00:00','2016-02-18 14:00:00'),(88,'EPO019','CTE002','OBA012','2015-09-14 14:00:00','2016-01-20 14:00:00'),(89,'EPO027','CTE002','OBA012','2015-09-14 14:00:00','2016-03-06 14:00:00'),(93,'EPO012','CTE002','OBA013','2020-01-10 13:58:03','2020-03-28 13:58:03'),(94,'EPO022','CTE002','OBA013','2020-01-10 13:58:03','2020-04-27 13:58:03'),(95,'EPO028','CTE002','OBA013','2020-01-11 16:13:09','2020-04-02 16:13:09'),(96,'EPO001','CTE004','OBA014','2017-07-23 14:00:00','2017-10-15 14:00:00'),(97,'EPO002','CTE004','OBA014','2017-07-22 13:40:00','2017-10-12 13:40:00'),(98,'EPO003','CTE004','OBA014','2017-07-22 14:00:00','2017-11-13 14:00:00'),(99,'EPO004','CTE004','OBA014','2017-07-22 14:00:00','2017-10-12 14:00:00'),(103,'EPO012','CTE004','OBA014','2017-07-22 14:00:00','2017-10-07 14:00:00'),(104,'EPO013','CTE004','OBA014','2017-07-22 14:00:00','2017-11-13 14:00:00'),(105,'EPO017','CTE004','OBA014','2017-07-22 14:00:00','2017-11-07 14:00:00'),(106,'EPO020','CTE004','OBA014','2017-07-22 14:00:00','2017-10-10 14:00:00'),(107,'EPO022','CTE004','OBA014','2017-07-23 14:00:00','2017-10-30 14:00:00'),(108,'EPO023','CTE004','OBA014','2017-07-22 14:00:00','2017-10-18 14:00:00'),(109,'EPO002','CTE004','OBA015','2015-02-15 13:10:00','2015-07-07 13:10:00'),(113,'EPO008','CTE004','OBA015','2015-02-15 14:10:00','2015-06-01 14:10:00'),(114,'EPO021','CTE004','OBA015','2015-02-16 14:10:00','2015-06-27 14:10:00'),(115,'EPO024','CTE004','OBA015','2015-02-16 14:10:00','2015-07-13 14:10:00'),(116,'EPO026','CTE004','OBA015','2015-02-16 14:10:00','2015-06-10 14:10:00'),(117,'EPO029','CTE004','OBA015','2015-02-16 14:10:00','2015-06-25 14:10:00'),(118,'EPO002','CTE001','OBA016','2024-08-15 13:10:00','2025-03-07 13:10:00'),(119,'EPO003','CTE001','OBA016','2024-08-15 13:10:00','2025-04-27 13:10:00'),(123,'EPO008','CTE001','OBA016','2024-08-16 13:10:00','2024-12-20 13:10:00'),(124,'EPO010','CTE001','OBA016','2024-08-16 13:10:00','2024-12-07 13:10:00'),(125,'EPO014','CTE001','OBA016','2024-08-16 13:10:00','2025-05-27 13:10:00'),(126,'EPO016','CTE001','OBA016','2024-08-16 13:10:00','2025-04-24 13:10:00'),(127,'EPO018','CTE001','OBA016','2024-08-16 13:10:00','2025-03-08 13:10:00'),(128,'EPO023','CTE001','OBA016','2024-08-16 13:10:00','2024-12-23 13:10:00'),(129,'EPO001','CTE001','OBA016','2024-08-15 06:00:00','2024-08-30 06:00:00'),(130,'EPO005','CTE001','OBA016','2024-08-15 06:00:00','2024-08-30 06:00:00'),(131,'EPO011','CTE001','OBA016','2025-11-19 06:00:00','2025-12-16 06:00:00'),(133,'EPO013','CTE001','OBA016','2024-08-15 06:00:00','2024-08-30 06:00:00'),(135,'EPO015','CTE003','OBA011','2021-07-05 06:00:00','2021-09-11 06:00:00');
/*!40000 ALTER TABLE `pv_trabajos_empleados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pv_usos_herramientas`
--

DROP TABLE IF EXISTS `pv_usos_herramientas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pv_usos_herramientas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_herramienta` varchar(6) NOT NULL,
  `id_cliente` varchar(6) NOT NULL,
  `id_obra` varchar(6) NOT NULL,
  `fecha_adicion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_termino` timestamp NULL DEFAULT NULL,
  `cantidad` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_herramienta_obra` (`id_herramienta`,`id_obra`),
  KEY `usos_dpe_fk` (`id_cliente`,`id_obra`),
  KEY `idx_obra` (`id_obra`),
  KEY `idx_herramienta` (`id_herramienta`),
  CONSTRAINT `usos_dpe_fk` FOREIGN KEY (`id_cliente`, `id_obra`) REFERENCES `pv_disposiciones` (`id_cliente`, `id_obra`),
  CONSTRAINT `usos_hra_fk` FOREIGN KEY (`id_herramienta`) REFERENCES `pv_herramientas` (`id_herramienta`)
) ENGINE=InnoDB AUTO_INCREMENT=129 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pv_usos_herramientas`
--

LOCK TABLES `pv_usos_herramientas` WRITE;
/*!40000 ALTER TABLE `pv_usos_herramientas` DISABLE KEYS */;
INSERT INTO `pv_usos_herramientas` (`id`, `id_herramienta`, `id_cliente`, `id_obra`, `fecha_adicion`, `fecha_termino`, `cantidad`) VALUES (3,'HRA003','CTE003','OBA001','2015-07-28 16:45:00','2015-09-23 16:45:00',2),(4,'HRA004','CTE003','OBA001','2015-08-05 19:20:00','2015-09-15 19:20:00',1),(5,'HRA005','CTE003','OBA001','2015-06-28 14:45:00','2015-09-23 14:45:00',1),(6,'HRA006','CTE003','OBA001','2015-08-25 20:00:00','2015-09-30 20:00:00',1),(7,'HRA007','CTE003','OBA001','2015-07-05 15:15:00','2015-08-30 15:15:00',3),(8,'HRA008','CTE003','OBA001','2015-07-12 13:30:00','2015-08-16 13:30:00',1),(9,'HRA001','CTE001','OBA002','2018-06-25 16:45:00','2018-10-05 16:45:00',1),(13,'HRA005','CTE001','OBA002','2018-05-20 13:30:00','2018-07-23 13:30:00',1),(14,'HRA006','CTE001','OBA002','2018-08-05 20:20:00','2018-09-17 20:20:00',1),(15,'HRA007','CTE001','OBA002','2018-06-05 15:15:00','2018-07-20 15:15:00',2),(16,'HRA008','CTE001','OBA002','2018-06-15 14:00:00','2018-07-16 14:00:00',2),(17,'HRA001','CTE001','OBA003','2020-08-10 16:00:00','2020-11-03 16:00:00',1),(18,'HRA002','CTE001','OBA003','2020-09-10 17:20:00','2020-11-18 17:20:00',1),(19,'HRA003','CTE001','OBA003','2020-08-20 13:30:00','2020-10-28 13:30:00',2),(23,'HRA007','CTE001','OBA003','2020-07-20 15:30:00','2020-09-18 15:30:00',2),(24,'HRA008','CTE001','OBA003','2020-07-30 14:15:00','2020-09-09 14:15:00',1),(25,'HRA001','CTE002','OBA004','2017-01-25 13:15:00','2017-04-20 13:15:00',1),(26,'HRA002','CTE002','OBA004','2017-02-20 14:30:00','2017-05-19 14:30:00',3),(27,'HRA003','CTE002','OBA004','2017-02-05 15:00:00','2017-04-26 15:00:00',1),(28,'HRA004','CTE002','OBA004','2017-03-01 19:00:00','2017-04-29 19:00:00',1),(29,'HRA005','CTE002','OBA004','2017-02-01 14:00:00','2017-05-01 14:00:00',2),(33,'HRA001','CTE004','OBA005','2021-09-20 14:45:00','2021-12-25 14:45:00',1),(34,'HRA002','CTE004','OBA005','2021-10-25 17:00:00','2022-01-31 17:00:00',2),(35,'HRA003','CTE004','OBA005','2021-10-10 16:30:00','2022-01-06 16:30:00',1),(36,'HRA004','CTE004','OBA005','2021-11-01 19:15:00','2022-01-09 19:15:00',1),(37,'HRA005','CTE004','OBA005','2021-09-15 13:30:00','2022-01-03 13:30:00',1),(38,'HRA006','CTE004','OBA005','2021-11-10 20:45:00','2022-01-01 20:45:00',1),(39,'HRA007','CTE004','OBA005','2021-10-05 15:15:00','2021-12-16 15:15:00',1),(43,'HRA003','CTE002','OBA006','2014-06-01 13:30:00','2014-09-05 13:30:00',2),(44,'HRA004','CTE002','OBA006','2014-06-25 19:45:00','2014-09-08 19:45:00',1),(45,'HRA005','CTE002','OBA006','2014-05-22 13:45:00','2014-08-09 13:45:00',1),(46,'HRA006','CTE002','OBA006','2014-07-05 20:10:00','2014-09-05 20:10:00',1),(47,'HRA007','CTE002','OBA006','2014-06-05 15:30:00','2014-07-20 15:30:00',3),(48,'HRA008','CTE002','OBA006','2014-06-15 14:15:00','2014-07-25 14:15:00',1),(49,'HRA001','CTE001','OBA007','2016-09-01 17:00:00','2016-12-12 17:00:00',2),(53,'HRA005','CTE001','OBA007','2016-09-01 14:00:00','2016-11-29 14:00:00',2),(54,'HRA006','CTE001','OBA007','2016-09-01 21:00:00','2016-11-20 21:00:00',1),(55,'HRA007','CTE001','OBA007','2016-09-01 15:00:00','2016-12-03 15:00:00',3),(56,'HRA008','CTE001','OBA007','2016-09-01 16:00:00','2016-12-21 16:00:00',2),(57,'HRA001','CTE005','OBA008','2019-07-03 18:14:00','2019-09-10 18:14:00',1),(58,'HRA002','CTE005','OBA008','2019-09-20 17:11:00','2019-10-27 17:11:00',3),(59,'HRA003','CTE005','OBA008','2019-08-01 16:05:00','2019-10-03 16:05:00',2),(63,'HRA007','CTE005','OBA008','2019-03-12 17:33:00','2019-06-28 17:33:00',1),(64,'HRA008','CTE005','OBA008','2019-06-18 15:33:00','2019-09-22 15:33:00',1),(65,'HRA001','CTE005','OBA009','2022-08-01 18:00:00','2023-01-12 18:00:00',1),(66,'HRA002','CTE005','OBA009','2022-11-22 13:35:00','2023-02-06 13:35:00',3),(67,'HRA003','CTE005','OBA009','2022-09-05 14:47:00','2023-01-15 14:47:00',2),(68,'HRA004','CTE005','OBA009','2022-10-18 16:03:00','2023-02-01 16:03:00',1),(69,'HRA005','CTE005','OBA009','2022-05-03 15:20:00','2022-10-05 15:20:00',2),(73,'HRA001','CTE001','OBA010','2013-07-22 18:58:00','2013-10-08 18:58:00',1),(74,'HRA002','CTE001','OBA010','2013-11-18 15:47:00','2013-12-23 15:47:00',3),(75,'HRA003','CTE001','OBA010','2013-08-30 13:38:00','2013-11-24 13:38:00',2),(76,'HRA004','CTE001','OBA010','2013-10-05 17:14:00','2013-12-06 17:14:00',1),(77,'HRA005','CTE001','OBA010','2013-03-20 15:00:00','2013-08-08 15:00:00',2),(78,'HRA006','CTE001','OBA010','2013-12-15 13:59:00','2014-01-06 13:59:00',1),(79,'HRA007','CTE001','OBA010','2013-04-25 14:12:00','2013-09-23 14:12:00',1),(83,'HRA003','CTE003','OBA011','2021-09-03 13:25:00','2021-10-01 13:25:00',2),(84,'HRA004','CTE003','OBA011','2021-09-20 17:08:00','2021-10-11 17:08:00',1),(85,'HRA005','CTE003','OBA011','2021-07-05 14:55:00','2021-09-13 14:55:00',2),(86,'HRA006','CTE003','OBA011','2021-10-19 14:40:00','2021-10-28 14:40:00',1),(87,'HRA007','CTE003','OBA011','2021-07-20 16:32:00','2021-09-09 16:32:00',1),(88,'HRA008','CTE003','OBA011','2021-08-04 15:41:00','2021-09-23 15:41:00',2),(89,'HRA001','CTE002','OBA012','2015-12-07 17:56:00','2016-03-17 17:56:00',1),(93,'HRA005','CTE002','OBA012','2015-09-25 14:33:00','2016-02-25 14:33:00',2),(94,'HRA006','CTE002','OBA012','2016-04-28 13:58:00','2016-06-07 13:58:00',1),(95,'HRA007','CTE002','OBA012','2015-10-13 15:47:00','2016-02-26 15:47:00',1),(96,'HRA008','CTE002','OBA012','2015-11-02 16:10:00','2016-03-29 16:10:00',2),(97,'HRA001','CTE002','OBA013','2020-04-07 13:42:00','2020-06-05 13:42:00',1),(98,'HRA002','CTE002','OBA013','2020-06-02 18:30:00','2020-06-21 18:30:00',3),(99,'HRA003','CTE002','OBA013','2020-04-30 14:11:00','2020-06-11 14:11:00',2),(103,'HRA007','CTE002','OBA013','2020-02-14 17:08:00','2020-05-13 17:08:00',1),(104,'HRA008','CTE002','OBA013','2020-03-02 16:27:00','2020-05-13 16:27:00',2),(105,'HRA001','CTE004','OBA014','2017-08-01 17:00:00','2017-10-28 17:00:00',2),(106,'HRA002','CTE004','OBA014','2017-08-01 20:00:00','2017-10-18 20:00:00',3),(107,'HRA003','CTE004','OBA014','2017-08-01 18:00:00','2017-10-31 18:00:00',2),(108,'HRA004','CTE004','OBA014','2017-08-01 19:00:00','2017-11-19 19:00:00',1),(109,'HRA005','CTE004','OBA014','2017-08-01 14:00:00','2017-11-03 14:00:00',2),(113,'HRA001','CTE004','OBA015','2015-03-01 17:00:00','2015-06-04 17:00:00',2),(114,'HRA002','CTE004','OBA015','2015-03-01 20:00:00','2015-07-06 20:00:00',3),(115,'HRA003','CTE004','OBA015','2015-03-01 18:00:00','2015-07-02 18:00:00',2),(116,'HRA004','CTE004','OBA015','2015-03-01 19:00:00','2015-06-06 19:00:00',1),(117,'HRA005','CTE004','OBA015','2015-03-01 14:00:00','2015-06-23 14:00:00',2),(118,'HRA006','CTE004','OBA015','2015-03-01 21:00:00','2015-06-06 21:00:00',1),(119,'HRA007','CTE004','OBA015','2015-03-01 15:00:00','2015-06-05 15:00:00',3),(123,'HRA003','CTE001','OBA016','2024-07-01 18:00:00','2025-03-24 18:00:00',2),(124,'HRA004','CTE001','OBA016','2024-07-01 19:00:00','2025-05-19 19:00:00',1),(125,'HRA005','CTE001','OBA016','2024-07-01 14:00:00','2025-04-03 14:00:00',2),(126,'HRA006','CTE001','OBA016','2024-07-01 21:00:00','2025-05-05 21:00:00',1),(127,'HRA007','CTE001','OBA016','2024-07-01 15:00:00','2025-04-10 15:00:00',3),(128,'HRA008','CTE001','OBA016','2024-07-01 16:00:00','2025-04-04 16:00:00',2);
/*!40000 ALTER TABLE `pv_usos_herramientas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'pavypre'
--
/*!50003 DROP PROCEDURE IF EXISTS `sp_generar_id` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
/* DELIMITER ;; */
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_generar_id`(
      IN  p_tabla   VARCHAR(64),
      IN  p_columna VARCHAR(64),
      IN  p_prefijo VARCHAR(10),
      OUT p_id      VARCHAR(20)
  )
BEGIN
      SET @_gen_prefijo = p_prefijo;
      SET @_gen_sql = CONCAT(
          'SELECT COALESCE(MAX(CAST(SUBSTRING(', p_columna, ', ',
          CHAR_LENGTH(p_prefijo) + 1,
          ') AS UNSIGNED)), 0) ',
          'INTO @_gen_ultimo ',
          'FROM ', p_tabla,
          ' WHERE ', p_columna, ' LIKE CONCAT(@_gen_prefijo, ''%'')'
      );
      PREPARE _gen_stmt FROM @_gen_sql;
      EXECUTE _gen_stmt;
      DEALLOCATE PREPARE _gen_stmt;
      SET p_id = CONCAT(p_prefijo, LPAD(CAST(@_gen_ultimo + 1 AS UNSIGNED), 3, '0'));
  END ;;
/* DELIMITER ; */
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-13 23:06:02
