-- MySQL dump 10.13  Distrib 8.0.42, for Win64 (x86_64)
--
-- Host: localhost    Database: univesp_pi
-- ------------------------------------------------------
-- Server version	8.0.42

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
-- Table structure for table `tab_ordem_servico`
--

DROP TABLE IF EXISTS `tab_ordem_servico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tab_ordem_servico` (
  `OS_ID` bigint NOT NULL,
  `OS_SOLICITANTE` varchar(100) NOT NULL,
  `OS_STATUS` varchar(50) NOT NULL,
  `OS_TAG_MAQ` varchar(50) NOT NULL,
  `OS_LINHA` varchar(50) NOT NULL,
  `OS_CLASSIFICACAO` varchar(50) NOT NULL,
  `OS_RECEBIDO_POR` varchar(50) DEFAULT NULL,
  `OS_SITUACAO` varchar(50) NOT NULL,
  `OS_MANUTENTOR` varchar(100) DEFAULT NULL,
  `OS_DATA_PROMESSA` datetime DEFAULT NULL,
  `OS_DATA_SOLICITACAO` datetime NOT NULL,
  `OS_DATA_INICIO` datetime DEFAULT NULL,
  `OS_DATA_FIM` datetime DEFAULT NULL,
  `OS_DESC_PEDIDO` varchar(5000) DEFAULT NULL,
  `OS_GRAVIDADE` int NOT NULL,
  `OS_URGENCIA` int NOT NULL,
  `OS_TENDENCIA` int NOT NULL,
  `OS_SOMA_GUT` int NOT NULL,
  `OS_ASSINATURA` varchar(100) DEFAULT NULL,
  `OS_OBSERVACOES` varchar(5000) DEFAULT NULL,
  `OS_HORAS_PLAN` decimal(10,2) DEFAULT NULL,
  `OS_AVALIACAO_COMENTARIO` varchar(5000) DEFAULT NULL,
  `OS_AVALIACAO_NOTA` int DEFAULT NULL,
  `OS_ID_OP` int DEFAULT NULL,
  PRIMARY KEY (`OS_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tab_ordem_servico`
--

LOCK TABLES `tab_ordem_servico` WRITE;
/*!40000 ALTER TABLE `tab_ordem_servico` DISABLE KEYS */;
INSERT INTO `tab_ordem_servico` VALUES (1,'AMANDA FRAGNAN','Aberto','EXT001','LINHA VINIL - 1','MECANICA',NULL,'Linha parada',NULL,NULL,'2025-05-08 18:59:08',NULL,NULL,'TESTE O.S',5,5,5,15,NULL,NULL,NULL,NULL,NULL,9),(2,'AMANDA FRAGNAN','Aberto','EXT003','LINHA VINIL - 2','PREDIAL',NULL,'Linha segue em atuação',NULL,NULL,'2025-05-08 19:08:03',NULL,NULL,'TESTE 2 O.S',4,4,4,12,NULL,NULL,NULL,NULL,NULL,9),(3,'AMANDA FRAGNAN','Aberto','EXT001','LINHA VINIL - 3','MECANICA',NULL,'Linha segue em atuação',NULL,NULL,'2025-05-08 19:10:21',NULL,NULL,'TESTE 3 O.S',3,3,3,9,NULL,NULL,NULL,NULL,NULL,9);
/*!40000 ALTER TABLE `tab_ordem_servico` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-17 16:53:05
