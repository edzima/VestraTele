mysql -u root -p$MYSQL_ROOT_PASSWORD -e \
"
CREATE DATABASE  IF NOT EXISTS '$DB_DBNAME' /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE '$DB_DBNAME';
-- MySQL dump 10.13  Distrib 8.0.18, for macos10.14 (x86_64)
--
-- Host: localhost    Database: dev_local
-- ------------------------------------------------------
-- Server version	8.0.16

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
-- Table structure for table 'accident_typ'
--

DROP TABLE IF EXISTS 'accident_typ';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'accident_typ' (
  'id' int(11) NOT NULL AUTO_INCREMENT,
  'name' varchar(45) NOT NULL,
  PRIMARY KEY ('id')
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'answer_typ'
--

DROP TABLE IF EXISTS 'answer_typ';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'answer_typ' (
  'id' int(11) NOT NULL AUTO_INCREMENT,
  'name' varchar(45) DEFAULT NULL,
  PRIMARY KEY ('id')
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'article'
--

DROP TABLE IF EXISTS 'article';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'article' (
  'id' int(11) NOT NULL AUTO_INCREMENT,
  'title' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'slug' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'body' text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'status' smallint(6) NOT NULL,
  'category_id' int(11) DEFAULT NULL,
  'author_id' int(11) DEFAULT NULL,
  'updater_id' int(11) DEFAULT NULL,
  'published_at' int(11) DEFAULT NULL,
  'created_at' int(11) DEFAULT NULL,
  'updated_at' int(11) DEFAULT NULL,
  PRIMARY KEY ('id'),
  KEY 'fk_article_author' ('author_id'),
  KEY 'fk_article_updater' ('updater_id'),
  KEY 'fk_article_category' ('category_id'),
  CONSTRAINT 'fk_article_author' FOREIGN KEY ('author_id') REFERENCES 'user' ('id') ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT 'fk_article_category' FOREIGN KEY ('category_id') REFERENCES 'article_category' ('id') ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT 'fk_article_updater' FOREIGN KEY ('updater_id') REFERENCES 'user' ('id') ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'article_category'
--

DROP TABLE IF EXISTS 'article_category';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'article_category' (
  'id' int(11) NOT NULL AUTO_INCREMENT,
  'title' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'slug' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'comment' text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  'parent_id' int(11) DEFAULT NULL,
  'status' smallint(6) NOT NULL,
  'created_at' int(11) DEFAULT NULL,
  'updated_at' int(11) DEFAULT NULL,
  PRIMARY KEY ('id'),
  KEY 'fk_article_category_section' ('parent_id'),
  CONSTRAINT 'fk_article_category_section' FOREIGN KEY ('parent_id') REFERENCES 'article_category' ('id') ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'article_tag'
--

DROP TABLE IF EXISTS 'article_tag';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'article_tag' (
  'article_id' int(11) NOT NULL,
  'tag_id' int(11) NOT NULL,
  PRIMARY KEY ('article_id','tag_id'),
  KEY 'fk_tag-tag_id-tag-id' ('tag_id'),
  CONSTRAINT 'fk_tag-article_id-article-id' FOREIGN KEY ('article_id') REFERENCES 'article' ('id') ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT 'fk_tag-tag_id-tag-id' FOREIGN KEY ('tag_id') REFERENCES 'tag' ('id') ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'auth_assignment'
--

DROP TABLE IF EXISTS 'auth_assignment';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'auth_assignment' (
  'item_name' varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'user_id' varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'created_at' int(11) DEFAULT NULL,
  PRIMARY KEY ('item_name','user_id'),
  CONSTRAINT 'auth_assignment_ibfk_1' FOREIGN KEY ('item_name') REFERENCES 'auth_item' ('name') ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'auth_item'
--

DROP TABLE IF EXISTS 'auth_item';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'auth_item' (
  'name' varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'type' int(11) NOT NULL,
  'description' text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  'rule_name' varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  'data' text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  'created_at' int(11) DEFAULT NULL,
  'updated_at' int(11) DEFAULT NULL,
  PRIMARY KEY ('name'),
  KEY 'rule_name' ('rule_name'),
  KEY 'idx-auth_item-type' ('type'),
  CONSTRAINT 'auth_item_ibfk_1' FOREIGN KEY ('rule_name') REFERENCES 'auth_rule' ('name') ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'auth_item_child'
--

DROP TABLE IF EXISTS 'auth_item_child';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'auth_item_child' (
  'parent' varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'child' varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY ('parent','child'),
  KEY 'child' ('child'),
  CONSTRAINT 'auth_item_child_ibfk_1' FOREIGN KEY ('parent') REFERENCES 'auth_item' ('name') ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT 'auth_item_child_ibfk_2' FOREIGN KEY ('child') REFERENCES 'auth_item' ('name') ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'auth_rule'
--

DROP TABLE IF EXISTS 'auth_rule';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'auth_rule' (
  'name' varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'data' text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  'created_at' int(11) DEFAULT NULL,
  'updated_at' int(11) DEFAULT NULL,
  PRIMARY KEY ('name')
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'calendar_news'
--

DROP TABLE IF EXISTS 'calendar_news';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'calendar_news' (
  'id' int(11) NOT NULL AUTO_INCREMENT,
  'news' varchar(255) NOT NULL,
  'agent_id' int(11) NOT NULL,
  'start' date DEFAULT NULL,
  'end' date NOT NULL,
  PRIMARY KEY ('id'),
  KEY 'agent_id' ('agent_id'),
  CONSTRAINT 'calendar_agent_fk' FOREIGN KEY ('agent_id') REFERENCES 'user' ('id') ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1356 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'campaign'
--

DROP TABLE IF EXISTS 'campaign';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'campaign' (
  'id' int(11) NOT NULL AUTO_INCREMENT,
  'name' varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY ('id'),
  UNIQUE KEY 'name' ('name')
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'comment'
--

DROP TABLE IF EXISTS 'comment';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'comment' (
  'id' int(11) NOT NULL AUTO_INCREMENT,
  'entity' char(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'entityId' int(11) NOT NULL,
  'content' text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'parentId' int(11) DEFAULT NULL,
  'level' smallint(6) NOT NULL DEFAULT '1',
  'createdBy' int(11) NOT NULL,
  'updatedBy' int(11) NOT NULL,
  'relatedTo' varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'url' text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  'status' smallint(6) NOT NULL DEFAULT '1',
  'createdAt' int(11) NOT NULL,
  'updatedAt' int(11) NOT NULL,
  PRIMARY KEY ('id'),
  KEY 'idx-Comment-entity' ('entity'),
  KEY 'idx-Comment-status' ('status')
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'connexion'
--

DROP TABLE IF EXISTS 'connexion';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'connexion' (
  'id' int(11) NOT NULL AUTO_INCREMENT,
  'name' varchar(16) NOT NULL,
  PRIMARY KEY ('id')
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'issue'
--

DROP TABLE IF EXISTS 'issue';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'issue' (
  'id' int(11) NOT NULL AUTO_INCREMENT,
  'created_at' timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  'updated_at' timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  'date' timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  'agent_id' int(11) NOT NULL,
  'client_first_name' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'client_surname' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'client_phone_1' varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'client_phone_2' varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  'client_email' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  'client_city_id' int(11) NOT NULL,
  'client_subprovince_id' int(11) DEFAULT NULL,
  'client_city_code' varchar(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'client_street' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'victim_first_name' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  'victim_surname' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  'victim_phone' varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  'victim_email' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  'victim_city_id' int(11) DEFAULT NULL,
  'victim_subprovince_id' int(11) DEFAULT NULL,
  'victim_city_code' varchar(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  'victim_street' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  'details' text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  'provision_type' smallint(6) NOT NULL,
  'provision_value' decimal(10,2) DEFAULT NULL,
  'provision_base' decimal(10,2) DEFAULT NULL,
  'stage_id' int(11) NOT NULL,
  'type_id' int(11) NOT NULL,
  'entity_responsible_id' int(11) NOT NULL,
  'archives_nr' varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  'payed' tinyint(1) NOT NULL DEFAULT '0',
  'old_id' int(11) DEFAULT NULL,
  'lawyer_id' int(11) NOT NULL,
  'tele_id' int(11) DEFAULT NULL,
  'accident_at' timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  'stage_change_at' datetime DEFAULT NULL,
  'status_change_at' datetime DEFAULT NULL,
  PRIMARY KEY ('id'),
  KEY 'fk_issue_client_city' ('client_city_id'),
  KEY 'fk_issue_victim_city' ('victim_city_id'),
  KEY 'fk_issue_entity_responsible' ('entity_responsible_id'),
  KEY 'fk_issue_stage' ('stage_id'),
  KEY 'fk_issue_type' ('type_id'),
  KEY 'fk_issue_client_agent' ('agent_id'),
  KEY 'fk_issue_lawyer' ('lawyer_id'),
  KEY 'fk_issue_tele' ('tele_id'),
  CONSTRAINT 'fk_issue_client_agent' FOREIGN KEY ('agent_id') REFERENCES 'user' ('id') ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT 'fk_issue_client_city' FOREIGN KEY ('client_city_id') REFERENCES 'miasta' ('id') ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT 'fk_issue_entity_responsible' FOREIGN KEY ('entity_responsible_id') REFERENCES 'issue_entity_responsible' ('id') ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT 'fk_issue_lawyer' FOREIGN KEY ('lawyer_id') REFERENCES 'user' ('id') ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT 'fk_issue_stage' FOREIGN KEY ('stage_id') REFERENCES 'issue_stage' ('id') ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT 'fk_issue_tele' FOREIGN KEY ('tele_id') REFERENCES 'user' ('id'),
  CONSTRAINT 'fk_issue_type' FOREIGN KEY ('type_id') REFERENCES 'issue_type' ('id') ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT 'fk_issue_victim_city' FOREIGN KEY ('victim_city_id') REFERENCES 'miasta' ('id') ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6950 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'issue_entity_responsible'
--

DROP TABLE IF EXISTS 'issue_entity_responsible';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'issue_entity_responsible' (
  'id' int(11) NOT NULL AUTO_INCREMENT,
  'name' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY ('id'),
  UNIQUE KEY 'name' ('name')
) ENGINE=InnoDB AUTO_INCREMENT=294 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'issue_entity_responsible_details'
--

DROP TABLE IF EXISTS 'issue_entity_responsible_details';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'issue_entity_responsible_details' (
  'city_id' int(11) NOT NULL,
  'entity_id' int(11) NOT NULL,
  'phone' varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  'bank_transfer_at' datetime DEFAULT NULL,
  'direct_at' datetime DEFAULT NULL,
  PRIMARY KEY ('city_id','entity_id'),
  KEY 'fk_issue_entity_responsible_details_entity' ('entity_id'),
  CONSTRAINT 'fk_issue_entity_responsible_details_city' FOREIGN KEY ('city_id') REFERENCES 'miasta' ('id') ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT 'fk_issue_entity_responsible_details_entity' FOREIGN KEY ('entity_id') REFERENCES 'issue_entity_responsible' ('id') ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'issue_meet'
--

DROP TABLE IF EXISTS 'issue_meet';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'issue_meet' (
  'id' int(11) NOT NULL AUTO_INCREMENT,
  'type_id' int(11) NOT NULL,
  'phone' varchar(20) DEFAULT NULL,
  'client_name' varchar(20) NOT NULL,
  'client_surname' varchar(30) DEFAULT NULL,
  'tele_id' int(11) DEFAULT NULL,
  'agent_id' int(11) DEFAULT NULL,
  'created_at' timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  'updated_at' timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  'date_at' datetime DEFAULT NULL,
  'details' text,
  'status' int(11) NOT NULL,
  'city_id' int(11) DEFAULT NULL,
  'sub_province_id' int(11) DEFAULT NULL,
  'street' varchar(50) DEFAULT NULL,
  'campaign_id' int(11) DEFAULT NULL,
  'date_end_at' datetime DEFAULT NULL,
  'email' varchar(255) DEFAULT NULL,
  PRIMARY KEY ('id'),
  KEY 'fk_issue_meet_tele' ('tele_id'),
  KEY 'fk_issue_meet_agent' ('agent_id'),
  KEY 'fk_issue_meet_type' ('type_id'),
  KEY 'fk_issue_meet_city' ('city_id'),
  KEY 'fk_issue_meet_campaign' ('campaign_id'),
  CONSTRAINT 'fk_issue_meet_agent' FOREIGN KEY ('agent_id') REFERENCES 'user' ('id') ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT 'fk_issue_meet_campaign' FOREIGN KEY ('campaign_id') REFERENCES 'campaign' ('id') ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT 'fk_issue_meet_city' FOREIGN KEY ('city_id') REFERENCES 'miasta' ('id') ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT 'fk_issue_meet_tele' FOREIGN KEY ('tele_id') REFERENCES 'user' ('id') ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT 'fk_issue_meet_type' FOREIGN KEY ('type_id') REFERENCES 'issue_type' ('id') ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1311 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'issue_note'
--

DROP TABLE IF EXISTS 'issue_note';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'issue_note' (
  'id' int(11) NOT NULL AUTO_INCREMENT,
  'issue_id' int(11) NOT NULL,
  'user_id' int(11) NOT NULL,
  'title' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'description' text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'created_at' timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  'updated_at' timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  'type' smallint(6) DEFAULT NULL,
  PRIMARY KEY ('id'),
  KEY 'fk_issue_note_user' ('user_id'),
  KEY 'fk_issue_note_issue' ('issue_id'),
  CONSTRAINT 'fk_issue_note_issue' FOREIGN KEY ('issue_id') REFERENCES 'issue' ('id') ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT 'fk_issue_note_user' FOREIGN KEY ('user_id') REFERENCES 'user' ('id') ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=130061 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'issue_pay'
--

DROP TABLE IF EXISTS 'issue_pay';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'issue_pay' (
  'id' int(11) NOT NULL AUTO_INCREMENT,
  'issue_id' int(11) NOT NULL,
  'pay_at' timestamp NULL DEFAULT NULL,
  'value' decimal(10,2) NOT NULL,
  'deadline_at' datetime NOT NULL,
  'transfer_type' smallint(6) NOT NULL,
  'vat' decimal(5,2) NOT NULL,
  'status' int(11) NOT NULL,
  'calculation_id' int(11) NOT NULL,
  PRIMARY KEY ('id'),
  KEY 'fk_issue_pay_issue' ('issue_id')
) ENGINE=InnoDB AUTO_INCREMENT=1191 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'issue_pay_calculation'
--

DROP TABLE IF EXISTS 'issue_pay_calculation';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'issue_pay_calculation' (
  'issue_id' int(11) NOT NULL,
  'value' decimal(10,2) NOT NULL,
  'details' text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  'created_at' timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  'updated_at' timestamp NULL DEFAULT NULL,
  'id' int(11) NOT NULL AUTO_INCREMENT,
  'type' smallint(6) NOT NULL,
  'payment_at' datetime DEFAULT NULL,
  'provider_id' int(11) NOT NULL,
  'provider_type' int(11) NOT NULL,
  PRIMARY KEY ('id'),
  KEY 'fk_issue_pay_calculation_issue' ('issue_id'),
  CONSTRAINT 'fk_issue_pay_calculation_issue' FOREIGN KEY ('issue_id') REFERENCES 'issue' ('id') ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=511 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'issue_stage'
--

DROP TABLE IF EXISTS 'issue_stage';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'issue_stage' (
  'id' int(11) NOT NULL AUTO_INCREMENT,
  'name' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'short_name' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'posi' int(11) unsigned NOT NULL DEFAULT '0',
  'days_reminder' int(11) DEFAULT NULL,
  PRIMARY KEY ('id'),
  UNIQUE KEY 'name' ('name'),
  UNIQUE KEY 'short_name' ('short_name')
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'issue_stage_type'
--

DROP TABLE IF EXISTS 'issue_stage_type';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'issue_stage_type' (
  'type_id' int(11) NOT NULL,
  'stage_id' int(11) NOT NULL,
  KEY 'issue_stage_type_type' ('type_id'),
  KEY 'issue_stage_type_stage' ('stage_id'),
  CONSTRAINT 'issue_stage_type_stage' FOREIGN KEY ('stage_id') REFERENCES 'issue_stage' ('id') ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT 'issue_stage_type_type' FOREIGN KEY ('type_id') REFERENCES 'issue_type' ('id') ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'issue_type'
--

DROP TABLE IF EXISTS 'issue_type';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'issue_type' (
  'id' int(11) NOT NULL AUTO_INCREMENT,
  'name' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'short_name' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'provision_type' smallint(6) NOT NULL DEFAULT '1',
  'vat' decimal(5,2) NOT NULL,
  'meet' tinyint(1) DEFAULT NULL,
  PRIMARY KEY ('id'),
  UNIQUE KEY 'name' ('name'),
  UNIQUE KEY 'short_name' ('short_name')
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'issue_user'
--

DROP TABLE IF EXISTS 'issue_user';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'issue_user' (
  'type' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'issue_id' int(11) NOT NULL,
  'user_id' int(11) NOT NULL,
  PRIMARY KEY ('type','issue_id','user_id'),
  KEY 'fk_issue_person_user' ('user_id'),
  KEY 'fk_issue_person_issue' ('issue_id'),
  CONSTRAINT 'fk_issue_person_issue' FOREIGN KEY ('issue_id') REFERENCES 'issue' ('id') ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT 'fk_issue_person_user' FOREIGN KEY ('user_id') REFERENCES 'user' ('id') ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'key_storage_item'
--

DROP TABLE IF EXISTS 'key_storage_item';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'key_storage_item' (
  'key' varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'value' text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'comment' text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  PRIMARY KEY ('key'),
  UNIQUE KEY 'idx_key_storage_item_key' ('key')
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'log'
--

DROP TABLE IF EXISTS 'log';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'log' (
  'id' bigint(20) NOT NULL AUTO_INCREMENT,
  'level' int(11) DEFAULT NULL,
  'category' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  'log_time' double DEFAULT NULL,
  'prefix' text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  'message' text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  PRIMARY KEY ('id'),
  KEY 'idx_log_level' ('level'),
  KEY 'idx_log_category' ('category')
) ENGINE=InnoDB AUTO_INCREMENT=181886 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'menu'
--

DROP TABLE IF EXISTS 'menu';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'menu' (
  'id' int(11) NOT NULL AUTO_INCREMENT,
  'url' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'label' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'parent_id' int(11) DEFAULT NULL,
  'status' smallint(6) NOT NULL,
  'sort_index' int(11) DEFAULT NULL,
  PRIMARY KEY ('id'),
  KEY 'parent' ('parent_id'),
  CONSTRAINT 'parent' FOREIGN KEY ('parent_id') REFERENCES 'menu' ('id') ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'miasta'
--

DROP TABLE IF EXISTS 'miasta';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'miasta' (
  'id' int(5) NOT NULL AUTO_INCREMENT,
  'name' varchar(31) DEFAULT NULL,
  'wojewodztwo_id' int(2) DEFAULT NULL,
  'powiat_id' int(2) DEFAULT NULL,
  PRIMARY KEY ('id'),
  KEY 'miasta_wojewodztwo_id_fk' ('wojewodztwo_id'),
  CONSTRAINT 'miasta_wojewodztwo_id_fk' FOREIGN KEY ('wojewodztwo_id') REFERENCES 'wojewodztwa' ('id')
) ENGINE=InnoDB AUTO_INCREMENT=103461 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'migration'
--

DROP TABLE IF EXISTS 'migration';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'migration' (
  'version' varchar(180) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  'apply_time' int(11) DEFAULT NULL,
  PRIMARY KEY ('version')
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'page'
--

DROP TABLE IF EXISTS 'page';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'page' (
  'id' int(11) NOT NULL AUTO_INCREMENT,
  'title' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'slug' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'description' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  'keywords' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  'body' text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'status' smallint(6) NOT NULL,
  'created_at' int(11) DEFAULT NULL,
  'updated_at' int(11) DEFAULT NULL,
  PRIMARY KEY ('id')
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'powiaty'
--

DROP TABLE IF EXISTS 'powiaty';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'powiaty' (
  'id' int(2) NOT NULL,
  'wojewodztwo_id' int(2) NOT NULL,
  'name' varchar(23) DEFAULT NULL,
  PRIMARY KEY ('id','wojewodztwo_id'),
  KEY 'wojewodztwo_id' ('wojewodztwo_id'),
  CONSTRAINT 'powiaty_wojewodztwo_id_fk' FOREIGN KEY ('wojewodztwo_id') REFERENCES 'wojewodztwa' ('id')
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'provision'
--

DROP TABLE IF EXISTS 'provision';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'provision' (
  'id' int(11) NOT NULL AUTO_INCREMENT,
  'pay_id' int(11) NOT NULL,
  'to_user_id' int(11) NOT NULL,
  'from_user_id' int(11) DEFAULT NULL,
  'value' decimal(10,2) NOT NULL,
  'type_id' int(11) NOT NULL,
  'hide_on_report' tinyint(1) DEFAULT NULL,
  PRIMARY KEY ('id'),
  KEY 'fk_provision_pay' ('pay_id'),
  KEY 'fk_provision_from_user' ('from_user_id'),
  KEY 'fk_provision_to_user' ('to_user_id'),
  KEY 'fk_provision_type' ('type_id'),
  CONSTRAINT 'fk_provision_from_user' FOREIGN KEY ('from_user_id') REFERENCES 'user' ('id') ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT 'fk_provision_pay' FOREIGN KEY ('pay_id') REFERENCES 'issue_pay' ('id') ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT 'fk_provision_to_user' FOREIGN KEY ('to_user_id') REFERENCES 'user' ('id') ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT 'fk_provision_type' FOREIGN KEY ('type_id') REFERENCES 'provision_type' ('id') ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2747 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'provision_type'
--

DROP TABLE IF EXISTS 'provision_type';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'provision_type' (
  'id' int(11) NOT NULL AUTO_INCREMENT,
  'name' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'value' decimal(10,2) NOT NULL,
  'date_from' timestamp NULL DEFAULT NULL,
  'date_to' timestamp NULL DEFAULT NULL,
  'only_with_tele' tinyint(1) DEFAULT NULL,
  'is_default' tinyint(1) DEFAULT NULL,
  'data' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  'is_percentage' tinyint(1) DEFAULT '1',
  PRIMARY KEY ('id')
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'provision_user'
--

DROP TABLE IF EXISTS 'provision_user';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'provision_user' (
  'from_user_id' int(11) NOT NULL,
  'to_user_id' int(11) NOT NULL,
  'type_id' int(11) NOT NULL,
  'value' decimal(10,2) NOT NULL,
  PRIMARY KEY ('from_user_id','to_user_id','type_id'),
  KEY 'fk_provision_user_to_user' ('to_user_id'),
  KEY 'fk_provision_user_type' ('type_id'),
  CONSTRAINT 'fk_provision_user_from_user' FOREIGN KEY ('from_user_id') REFERENCES 'user' ('id') ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT 'fk_provision_user_to_user' FOREIGN KEY ('to_user_id') REFERENCES 'user' ('id') ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT 'fk_provision_user_type' FOREIGN KEY ('type_id') REFERENCES 'provision_type' ('id') ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'tag'
--

DROP TABLE IF EXISTS 'tag';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'tag' (
  'id' int(11) NOT NULL AUTO_INCREMENT,
  'frequency' int(11) NOT NULL,
  'name' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'slug' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'comment' text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  PRIMARY KEY ('id')
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'terc'
--

DROP TABLE IF EXISTS 'terc';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'terc' (
  'id' int(11) NOT NULL AUTO_INCREMENT,
  'WOJ' int(2) DEFAULT NULL,
  'POW' int(2) DEFAULT NULL,
  'GMI' int(2) DEFAULT NULL,
  'name' varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  PRIMARY KEY ('id'),
  KEY 'WOJ' ('WOJ'),
  KEY 'POW' ('POW'),
  CONSTRAINT 'terc_woj_fk' FOREIGN KEY ('WOJ') REFERENCES 'wojewodztwa' ('id')
) ENGINE=InnoDB AUTO_INCREMENT=2529 DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'user'
--

DROP TABLE IF EXISTS 'user';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'user' (
  'id' int(11) NOT NULL AUTO_INCREMENT,
  'username' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'auth_key' varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'access_token' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  'password_hash' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'email' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  'status' smallint(6) NOT NULL,
  'ip' varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  'typ_work' char(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  'created_at' int(11) DEFAULT NULL,
  'updated_at' int(11) DEFAULT NULL,
  'action_at' int(11) DEFAULT NULL,
  'boss' int(11) DEFAULT NULL,
  'old_id' int(11) DEFAULT NULL,
  PRIMARY KEY ('id')
) ENGINE=InnoDB AUTO_INCREMENT=552 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'user_profile'
--

DROP TABLE IF EXISTS 'user_profile';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'user_profile' (
  'user_id' int(11) NOT NULL AUTO_INCREMENT,
  'firstname' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  'lastname' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  'birthday' int(11) DEFAULT NULL,
  'avatar_path' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  'gender' smallint(1) DEFAULT NULL,
  'website' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  'other' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  'phone' varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY ('user_id'),
  CONSTRAINT 'fk_user' FOREIGN KEY ('user_id') REFERENCES 'user' ('id') ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=552 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'user_provision'
--

DROP TABLE IF EXISTS 'user_provision';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'user_provision' (
  'id' int(11) NOT NULL AUTO_INCREMENT,
  'user_id' int(11) NOT NULL,
  'source_user_id' int(11) DEFAULT NULL,
  'provision_value' decimal(10,2) NOT NULL,
  'provision_value_tele' decimal(10,2) NOT NULL,
  'provision_type' smallint(6) NOT NULL,
  PRIMARY KEY ('id'),
  KEY 'fk_user_provision_user' ('user_id'),
  KEY 'fk_user_provision_source_user' ('source_user_id'),
  CONSTRAINT 'fk_user_provision_source_user' FOREIGN KEY ('source_user_id') REFERENCES 'user' ('id') ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT 'fk_user_provision_user' FOREIGN KEY ('user_id') REFERENCES 'user' ('id') ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table 'wojewodztwa'
--

DROP TABLE IF EXISTS 'wojewodztwa';
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE 'wojewodztwa' (
  'id' int(2) NOT NULL,
  'name' varchar(19) DEFAULT NULL,
  PRIMARY KEY ('id')
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping events for database 'dev_local'
--

--
-- Dumping routines for database 'dev_local'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-08-25 14:24:48
"