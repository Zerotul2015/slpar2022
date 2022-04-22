-- MySQL dump 10.13  Distrib 8.0.26, for Linux (x86_64)
--
-- Host: localhost    Database: app
-- ------------------------------------------------------
-- Server version	8.0.26

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bath_style`
--

DROP TABLE IF EXISTS `bath_style`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bath_style` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bath_style_id_uindex` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bath_style`
--

LOCK TABLES `bath_style` WRITE;
/*!40000 ALTER TABLE `bath_style` DISABLE KEYS */;
INSERT INTO `bath_style` VALUES (1,'123'),(4,'456');
/*!40000 ALTER TABLE `bath_style` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer`
--

DROP TABLE IF EXISTS `customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `mail` varchar(255) DEFAULT NULL,
  `pass` varchar(255) DEFAULT NULL,
  `status` varchar(32) DEFAULT NULL COMMENT 'Статус покупателя(active, blocked и т.п)',
  `note_hidden` text,
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_id_uindex` (`id`),
  UNIQUE KEY `customer_mail_uindex` (`mail`),
  UNIQUE KEY `customer_phone_uindex` (`phone`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer`
--

LOCK TABLES `customer` WRITE;
/*!40000 ALTER TABLE `customer` DISABLE KEYS */;
INSERT INTO `customer` VALUES (1,'Костя','+79234815343','kostyalinks@gmail.com',NULL,'active',NULL,0),(2,'Сергей Петрович Юмашин',NULL,'test',NULL,'active',NULL,1);
/*!40000 ALTER TABLE `customer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_company`
--

DROP TABLE IF EXISTS `customer_company`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_company` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned DEFAULT NULL,
  `inn` varchar(12) DEFAULT NULL,
  `kpp` varchar(9) DEFAULT NULL,
  `name` text,
  `note` text COMMENT 'Произвольный текст. Здесь покупатель может что то пометить для себя.',
  `note_hidden` text COMMENT 'Не доступно для покупателя. Используется менеджерами для заметок по контрагенту.',
  `deleted` tinyint(1) DEFAULT '0' COMMENT 'контрагент не удаляется что бы не сбить заказы с ним, но для покупателя он скрыт',
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_company_id_uindex` (`id`),
  KEY `customer_company_customer_id_fk` (`customer_id`),
  CONSTRAINT `customer_company_customer_id_fk` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_company`
--

LOCK TABLES `customer_company` WRITE;
/*!40000 ALTER TABLE `customer_company` DISABLE KEYS */;
INSERT INTO `customer_company` VALUES (1,1,'1234567890',NULL,'ООО \"Тестовая\"','тест публичного комментария','тест  скрытого комментария',0),(4,1,'1324567890',NULL,'ООО СТК',NULL,NULL,0),(5,1,'0987604531',NULL,'ООО \"ВСС\"',NULL,NULL,0);
/*!40000 ALTER TABLE `customer_company` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_methods`
--

DROP TABLE IF EXISTS `delivery_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_methods` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `price` int DEFAULT NULL,
  `sum_for_free` int unsigned DEFAULT NULL,
  `enable` tinyint(1) DEFAULT NULL,
  `protected` tinyint(1) DEFAULT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_methods`
--

LOCK TABLES `delivery_methods` WRITE;
/*!40000 ALTER TABLE `delivery_methods` DISABLE KEYS */;
INSERT INTO `delivery_methods` VALUES (1,'Самовывоз','г. Кемерово, ул. Ю.Двужильного 7 к2, отд. 114,  магазин \"Печкин дом\"',0,NULL,1,1),(3,'Доставка транспортной компанией','При заказе свыше 15 000 р доставка до терминала ТК без(с)платно',400,15000,1,NULL),(7,'Доставка по городу','Только для города Кемерово',500,15000,1,NULL);
/*!40000 ALTER TABLE `delivery_methods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `discounts`
--

DROP TABLE IF EXISTS `discounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `discounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `active` tinyint(1) DEFAULT NULL,
  `time_start` int DEFAULT NULL,
  `time_end` int DEFAULT NULL,
  `value` int DEFAULT NULL,
  `unit` varchar(6) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `composition` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `discounts`
--

LOCK TABLES `discounts` WRITE;
/*!40000 ALTER TABLE `discounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `discounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gallery_category`
--

DROP TABLE IF EXISTS `gallery_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gallery_category` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `folder` varchar(255) DEFAULT NULL,
  `category_description` text,
  `seo` text,
  `connection_products` text,
  `connection_category_products` text,
  `connection_services` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gallery_category_folder_uindex` (`folder`),
  UNIQUE KEY `gallery_category_url_uindex` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gallery_category`
--

LOCK TABLES `gallery_category` WRITE;
/*!40000 ALTER TABLE `gallery_category` DISABLE KEYS */;
/*!40000 ALTER TABLE `gallery_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gallery_image`
--

DROP TABLE IF EXISTS `gallery_image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gallery_image` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `gallery_category` text,
  `image` varchar(255) DEFAULT NULL,
  `connection_products` text,
  `connection_category_products` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gallery_image`
--

LOCK TABLES `gallery_image` WRITE;
/*!40000 ALTER TABLE `gallery_image` DISABLE KEYS */;
/*!40000 ALTER TABLE `gallery_image` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `position` varchar(32) DEFAULT NULL,
  `items` text,
  `priority` int DEFAULT NULL,
  `enable` tinyint(1) DEFAULT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu`
--

LOCK TABLES `menu` WRITE;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` VALUES (11,'Новое меню','header','[{\"title\":\"Новая ссылка1\",\"typeItem\":\"custom\",\"value\":\"23423\",\"children\":null},{\"title\":\"Новая ссылка4\",\"typeItem\":\"custom\",\"value\":\"tyrty\",\"children\":null},{\"title\":\"Новая ссылка\",\"typeItem\":\"custom\",\"value\":null,\"children\":null}]',0,1);
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer` bigint unsigned DEFAULT NULL,
  `customer_company` bigint unsigned DEFAULT NULL,
  `payment_method` bigint unsigned DEFAULT NULL,
  `delivery_method` bigint unsigned DEFAULT NULL,
  `products` text,
  `status` bigint unsigned DEFAULT NULL,
  `priceOrder` int DEFAULT NULL,
  `priceDelivery` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_id_uindex` (`id`),
  KEY `order_orders_status_id_fk` (`status`),
  KEY `order_delivery_methods_id_fk` (`delivery_method`),
  KEY `order_payment_methods_id_fk` (`payment_method`),
  KEY `orders_customer_company_id_fk` (`customer_company`),
  KEY `orders_customer_id_fk` (`customer`),
  CONSTRAINT `order_delivery_methods_id_fk` FOREIGN KEY (`delivery_method`) REFERENCES `delivery_methods` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `order_orders_status_id_fk` FOREIGN KEY (`status`) REFERENCES `orders_status` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `order_payment_methods_id_fk` FOREIGN KEY (`payment_method`) REFERENCES `payment_methods` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `orders_customer_company_id_fk` FOREIGN KEY (`customer_company`) REFERENCES `customer_company` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `orders_customer_id_fk` FOREIGN KEY (`customer`) REFERENCES `customer` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders_status`
--

DROP TABLE IF EXISTS `orders_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders_status` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `send_admin` tinyint(1) DEFAULT NULL,
  `description` text,
  `message_mail` text,
  `message_mail_admin` text,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders_status`
--

LOCK TABLES `orders_status` WRITE;
/*!40000 ALTER TABLE `orders_status` DISABLE KEYS */;
INSERT INTO `orders_status` VALUES (1,'Новый заказ',1,NULL,'Ваш заказ №[id] успешно получен. В ближайшее время с вами свяжется наш менеджер для уточнение и согласования деталей по вашему заказу.','Новый заказ №[id] от [name]. [link.details]'),(2,'Ожидает оплаты',NULL,NULL,'Ваш заказ №[id] ожидает оплаты. <a href=\"[link.pay]\">Оплатить</a>',NULL),(3,'Оплачен',1,NULL,'Ваш заказ №[id] оплачен.','Заказ №[id]  оплачен.'),(4,'Отправлен',0,NULL,'Ваш заказ №[id] отправлен.',NULL);
/*!40000 ALTER TABLE `orders_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `page`
--

DROP TABLE IF EXISTS `page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `page` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `style` varchar(32) DEFAULT NULL,
  `images` text,
  `folder` varchar(255) DEFAULT NULL,
  `seo` text,
  `description` text,
  `content` text,
  `url` varchar(255) NOT NULL,
  `integrated` tinyint(1) DEFAULT NULL,
  `url_integrated` varchar(255) DEFAULT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `id` (`id`),
  KEY `category` (`category_id`),
  CONSTRAINT `page_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `page_category` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page`
--

LOCK TABLES `page` WRITE;
/*!40000 ALTER TABLE `page` DISABLE KEYS */;
INSERT INTO `page` VALUES (1,'Контакты',NULL,'default',NULL,'5ef3186531449fldr1592989797','{\"title\":\"Обратная связь\",\"keywords\":\"Обратная связь, написать, связаться\",\"description\":\"Оставьте нам сообщение на интересующий Вас вопрос. Наши специалисты ответят Вам в ближайшее время\"}',NULL,'<div id=\"feedback\">\n<p>ООО \"ТРИУМФ\" <br />ИНН 4205323472, КПП 420501001<br />650024, г.Кемерово, ул. Двужильного, 7-114,&nbsp;</p>\n<p>тел. (3842) 48-02-32<br />сот. тел. 8-961-722-1122</p>\n<p>Электронная почта: <a href=\"mailto:pd@kamin42.ru\">info@kamin42.ru</a></p>\n<p>График работы:<br />Пн: Выходной;<br />Вт: с 9:00 до 19:00;<br />Ср: с 9:00 до 19:00;<br />Чт: с 9:00 до 19:00;<br />Пт: с 9:00 до 19:00;<br />Сб: с 9:00 до 19:00;<br />Вс: с 9:00 до 17:00;</p>\n<p>Также Вы можете оставить сообщение в форме обратной связи и мы свяжемся с Вами в ближайшее время:</p>\n<feedback-form>Форма обратной связи</feedback-form></div>','kontakty',0,NULL,'2021-05-02 17:34:53'),(8,'Доставка',NULL,'default',NULL,'5f52142ca050ffldr1599214636','{\"title\":\"Доставка \",\"keywords\":\"Доставка, доставка товара, доставка кемерово, доставка печи, доставка камина, \",\"description\":\"Информация о возможных видах доставки из интернет-магазина Камин42. При покупке на сумму от 20 000 рублей доставляем заказ по городу Кемерово бесплатно\"}',NULL,'<p><strong>Доставка Вашего товара из нашего интернет-магазина, - это просто!</strong></p>\n<p><strong>Доставка по городу Кемерово осуществляется:</strong></p>\n<ul>\n<li>Самовывозом с Торгового рынка &laquo;Привоз&raquo;, 2 корпус, 114 отдел, магазин &laquo;Камин42&raquo;;</li>\n<li>Наёмным автотранспортом с нашей погрузкой, стоимость от 600 до 1300 рублей, в зависимости от района;</li>\n</ul>\n<p><strong>Доставка в пригороды Кемерова:</strong></p>\n<ul>\n<li>Наёмным автотранспортом с нашей погрузкой исходя из тарифов:<br /><br /><strong>&nbsp;</strong></li>\n</ul>\n<p><strong>Доставка в другие города Кемеровской области:</strong></p>\n<ul>\n<li>Наёмным автотранспортом с нашей погрузкой исходя из тарифов:</li>\n</ul>\n<p><strong>&nbsp;</strong></p>\n<p><strong>Доставка в регионы РФ:</strong></p>\n<p><strong>Транспортными компаниями на Ваш выбор:</strong></p>\n<p><strong>Деловые линии<br /></strong>8&nbsp;800&nbsp;100-80-00<br /><a href=\"http://www.dellin.ru/calculator\">www.dellin.ru/calculator</a><br />www.dellin.ru/tracker</p>\n<p><strong>ЖелДорЭкспедиция<br /></strong>8&nbsp;800&nbsp;100-55-05<br /><a href=\"http://www.jde.ru/calc\">www.jde.ru/calc&nbsp;</a></p>\n<p><strong>КИТ (КАШАЛОТ)<br /></strong>www.tk-kit.ru/calculator</p>\n<p><strong>Энергия<br /></strong>8&nbsp;800&nbsp;700-70-00<br />nrg-tk.ru/kalkulyator.html<strong>&nbsp;</strong></p>\n<p><strong>ОБРАЩАЕМ ВАШЕ ВНИМАНИЕ:</strong></p>\n<p><strong>Среднее время доставки в ближайшие регионы 1-3 дня, в отдаленные &ndash; около 7-10 дней</strong>.&nbsp;Обращаем ваше внимание, что <strong>хранение</strong> прибывшего груза в ТК осуществляется <strong>беЗплатно в течение двух рабочих дней.</strong></p>\n<p><strong>ВАЖНО!</strong> Обязательным условием при отправке груза через транспортную компанию является внесение паспортных данных получателя! Вы можете выбрать транспортную компанию самостоятельно или обратиться за помощью к менеджеру нашей компании. Для самостоятельного расчета стоимости доставки в регионы рекомендуем сервис c6v.ru</p>\n<ul>\n<li><strong>При сдаче груза в транспортную компанию груз фотографируется</strong>&nbsp;и в ТТН (товарно-транспортная накладная) пишется, что груз принят без повреждений. Бывает, что сотрудники некоторых транспортных компаний пишут в ТТН груз принят с повреждениями, чтобы снять с себя ответственность при возможном повреждении груза в процессе его перевозки. В таких случаях мы предлагаем сотруднику транспортной компании совместно вскрыть упаковку товара, проверить его целостность, зафиксировать фотосъемкой и в ТТН отметить что товар принят без повреждений, если сотрудник транспортной компании отказывается от досмотра груза и настаивает на приёме груза с повреждениями, то мы не отправляем груз этой транспортной компанией.</li>\n<li>При оформлении груза к перевозке в обязательном порядке оформляется страхование груза согласно сопроводительным документам.</li>\n<li>При порче груза по вине грузоперевозчика, собственнику груза (получателю) со стороны ТК возмещается ущерб в размере полной стоимости испорченной части товара.</li>\n<li>Поле передачи вашего заказа в ТК мы проинформируем Вас СМС сообщением, указав номер ТТН (товарно-транспортная накладная) для отслеживания груза.</li>\n</ul>\n<p><strong>Правила приёмки груза у транспортной компании:</strong></p>\n<p>Наша компания сотрудничает с надёжными транспортными компаниями (ТК), которые выполняют свою работу максимально ответственно, быстро и аккуратно, в соответствии со всеми предъявляемыми требованиями нашего магазина, так, чтобы клиент остался всем доволен. Однако нельзя забывать, что в работе любой транспортной компании, как и везде, не исключен так называемый \"человеческий фактор\", ведь никто не может быть застрахован от ошибок персонала грузоперевозчика.</p>\n<p><strong>Итак:</strong></p>\n<ol>\n<li>ЕСЛИ ВЫ СОМНЕВАЕТЕСЬ, ХОТЯ БЫ НЕМНОГО, В ЦЕЛОСТНОСТИ ДОСТАВЛЕННОГО ЗАКАЗА, НИ ПРИ КАКИХ УСЛОВИЯХ <strong>НЕ ПОДПИСЫВАЙТЕ ДОКУМЕНТЫ БЕЗ ПРЕДВАРИТЕЛЬНОГО ОСМОТРА ГРУЗА</strong>. В ОБЯЗАТЕЛЬНОМ ПОРЯДКЕ ПРОВЕРЬТЕ СВОИ ПОДОЗРЕНИЯ НА МЕСТЕ ПОЛУЧЕНИЯ ГРУЗА В ПРИСУТСТВИИ СОТРУДНИКА ТРАНСПОРТНОЙ КОМПАНИИ.</li>\n<li>ЕСЛИ ЖЕ ПОВРЕЖДЕНИЯ ГРУЗА ОБНАРУЖАТСЯ УЖЕ ПОСЛЕ ПОДПИСАНИЯ ТТН И ПОКИДАНИЯ ГРУЗОМ ТЕРРИТОРИИ ТРАНСПОРТНОЙ КОМПАНИИ, ПРЕТЕНЗИИ НЕ БУДУТ ПРИНЯТЫ НИ ПРИ КАКИХ ОБСТОЯТЕЛЬСТВАХ!</li>\n</ol>\n<p><strong>Алгоритм Ваших действий при приёме груза на транспортной компании:</strong></p>\n<ol>\n<li>Сверить количество мест по факту с указанным в ТТН (товарно-транспортной накладной). Если количество мест не совпадает, произведите вскрытие груза и прием товаров по позициям, а также в обязательном порядке составьте акт о расхождении мест в присутствии сотрудников ТК.</li>\n<li>Проверьте визуальную целостность внешней упаковки. Она не должна иметь сквозных дыр, повреждений и помятостей, следов перескотчевания (на заводских упаковках недопустимы следы &laquo;двойного скотча&raquo;). Наличие непонятной или подозрительной упаковки также является условием для составления акта о нарушениях и отказе в приеме товара. Обратите внимание, что наша компания не занимается дополнительной упаковкой груза (товар может быть упакован только в заводскую упаковку)! При любых нарушениях сфотографируйте товар или сделайте видеозапись вскрытия грузового места в присутствии сотрудников ТК, составляйте Акт, в котором опишите обнаруженные нарушения, такие, как повреждения, отсутствие или подмена товара и т.д. В последствии сделанные Вами фотографии и видео станут самыми объективными и неопровержимыми аргументами зафиксированного нарушения.</li>\n<li>Проверьте вес и объем товара по товарно-транспортной накладной (ТТН). Если есть возможность (при приеме на терминале) требуйте перевешивания и замера объема груза в Вашем присутствии. При несовпадении веса или объема порядок действий такой же, как и при прочих нарушениях - обязательно в присутствии представителя ТК производите вскрытие груза и прием товара по позициям согласно накладной и упаковочным листам, составляйте акт о расхождении веса или объема.</li>\n</ol>\n<p>Обратите внимание, что один экземпляр Акта с оригинальной печатью ТК и подписью сотрудника ТК остается у Вас (грузополучателя). Транспортная компания, занимающаяся доставкой, считает свои обязательства перед получателем выполненными, если грузополучатель не составит акт о нарушениях и распишется в отсутствии претензий в товарно-транспортных документах. Напоминает, что акт должен быть составлен при получении груза с участием представителя ТК и грузополучателя, а также подписанного с обеих сторон. Акт обязательно должен быть заверен синей печатью ТК.</p>\n<p>В случае если сотрудник ТК отказывается подписывать акт, ни в коем случае&nbsp;<strong>НЕ ПРИНИМАЙТЕ ГРУЗ!</strong>&nbsp;<strong>Обязательно сделайте фото или видеозапись груза на складе ТК</strong>, постарайтесь узнать данные сотрудника, который отказался подписать или принять акт, потребуйте озвучивания причины отказа, и в тот же день срочно свяжитесь с нами. Копии записей и акта о нарушениях вышлите на нашу электронную почту:&nbsp;<u><a href=\"mailto:zakaz@kamin42.ru\">zakaz@kamin42.ru</a></u>. Вы можете рассчитывать на помощь наших специалистов, и мы готовы представить Вам все дополнительные документы, которые будут необходимы для решения возникших проблем.</p>','dostavka',0,NULL,'2021-05-02 17:34:53'),(12,'Оплата',NULL,NULL,NULL,NULL,'{\"title\":\"Оплата\",\"keywords\":\"Оплата\",\"description\":\"Оплата\"}',NULL,'<p>Для физических лиц:</p>\n\n<p>Оплата банковскими картами (Master Card, Visa) или наличными в фирменном магазине по адресу:  Кемерово, ул. Двужильного 7 к2 - 114, Печной центр «Печкин дом»</p>\n\n<p>Оплата в безналичной форме на расчетный счет ООО «ТРИУМФ»  по предварительно выставленной квитанции. Обратите внимание, что возможно взимание комиссии банком за перемещение денежных средств.</p>\n\n<p>Оплата переводом на банковскую карту компании. Обратите внимание, что при банковском переводе возможно взимание комиссии банком за перемещение денежных средств.</p>\n\n<p>Яндекс.Касса (Банковские карты, Яндекс.Деньги, QIWI Wallet, Сбербанк Онлайн, Альфа-Клик, интернет-банк Промсвязьбанка, МasterPass, Кредитование).</p>\n\n<p>Для юридических лиц:<br />\nОплата в безналичной форме на расчетный счет ООО «ТРИУМФ» по предварительно выставленному счету.<br />\nОплата заказа производится согласно выписанному счету, который мы отправим по указанному вами номеру факса или адресу электронной почты. Оплату необходимо произвести в течение 5-ти банковских дней. Если в течение этого времени средства для оплаты счета не будут переведены, он будет считаться недействительным, а заказ аннулированным. После оплаты заказа сообщите нам об этом по электронной почте:  В сообщении укажите: дату, сумму оплаты, номер заказа, название организации, Ф. И. О. и номер платежного поручения.<br />\nРеквизиты:<br />\nБанк получателя: АО «Кемсоцинбанк»<br />\nИНН: 4205323472<br />\nБИК: 043207720<br />\nКПП: 420501001<br />\nР/счет: 40702810100000001281<br />\nК/счет: 30101810600000000720<br />\nНазначение платежа: для зачисления на карту № 4276 4400 1207 196<br />\nИнформация о продавце:<br />\nООО «ТРИУМФ»<br />\nЮридический адрес: Кемерово, ул. Двужильного 7 отдел 114, (рынок «Привоз).<br />\nТелефоны: 8 (384-2) 49-56-49 (Кемерово);</p>\n\n<p> </p>\n\n<p>Предоплата за товар под заказ</p>\n\n<p>Если выбранного Вами товара не оказалось в наличии в Кемерово, Вы можете оформить его покупку под заказ указанными ниже способами и мы изготовим его в течение 7-14 дней.</p>\n\n<p><br />\nДля Кемерово и Кемеровской области:</p>\n\n<p>1) Наличие и срок поставки товара обсуждаются с менеджером интернет-магазина.</p>\n\n<p>2) Оплата за заказной товар осуществляется любым способом, указанным в разделе «Оплата товара», и в размере:</p>\n\n<p>— не менее 50% на товар собственного производства;</p>\n\n<p>3) Если Вы передумали покупать заказанный товар, убедительная просьба: проинформируйте нас по телефону 8 (3842) 49-56-49 или отправьте сообщение по почте: obereg@izlesuvestimo.ru</p>\n\n<p> </p>\nДля регионов России и других стран:\n\n<p>1) Сроки поставки обсуждаются со специалистом интернет-магазина при уточнении деталей заказа.</p>\n\n<p>2) Оформление и доставка заказа осуществляется после оплаты стоимости товара в размере 100 %</p>','oplata',NULL,NULL,'2021-05-02 17:34:53'),(15,'О нас',NULL,NULL,NULL,NULL,'{\"title\":\"О нас\",\"keywords\":\"заказать изделие из металла, купить декор из металла, кемеровское производство, изделия из металла в Кемерово\",\"description\":\"о компании ИзЛесуВестимо\"}',NULL,'<p>«Когда мы окружаем себя хорошими людьми, красивыми вещами и добрыми мыслями, - то жизнь начинает меняться в лучшую сторону!»</p>\n\n<p>Создание интересных изделий, как и душевных интерьеров, всегда требует фантазии, трудолюбия и настойчивости и тот, кто упорно стремится к своей цели, не обращая внимания на трудности, с каждым летом становясь мудрее и сильнее, непременно добивается искомого и желаемого результата!</p>\n\n<p><strong><span style=\"color:#e74c3c\">Излесувестимо</span></strong>, - это результат упорства и трудолюбия, активного поиска и кропотливой разработки большой и разносторонней ассортиментной линейки производимой продукции! Настал час и сегодня мы рады представить Вам плод наших стараний, – большой ассортимент предметов декора, ландшафта, мебели и объектов среды на который мы гордо ставим свой знак качества, - свой логотип!</p>\n\n<p><strong><span style=\"color:#e74c3c\">Наша продукция</span></strong>  объединяет в себе всё необходимое в качестве, надежности, дизайне, удобстве и прямой функциональности, но не содержит ничего лишнего в стоимости!</p>\n\n<p><strong><span style=\"color:#e74c3c\">Надеемся и уверены</span></strong>, что приобретая нашу продукцию для своих интерьеров и ландшафтов, Вы добьётесь желаемых результатов и ощущений неповторимости, теплоты и уюта!</p>\n\n<p><span style=\"color:#e74c3c\"><strong>Торговые организации</strong></span> смогут не только привлечь нашей продукцией новых покупателей, но и иметь хорошие наценки и достойную прибыль!</p>\n\n<p><strong><span style=\"color:#e74c3c\">Мы хотим</span></strong>, - чтобы наши изделия не только долго служили своим хозяевам по своему назначению, но и в какой-то степени создавали хорошее настроение, поскольку они состоят не только из метала, дерева, стекла и кожи, - самое главное, - в каждом из них находится теплота наших рук, доброта наших сердец, наша русская смекалка и выдумка!</p>\n\n<p><span style=\"color:#e74c3c\"><u><strong>Наши изделия, - ИзЛесуВестимо!</strong></u></span></p>','o-nas',NULL,NULL,'2021-05-02 17:34:53'),(16,'Просмотр заказов',NULL,'green',NULL,NULL,'{\"title\":\"Просмотр заказов\",\"keywords\":\"просмотр заказов, посмотреть мои заказы\",\"description\":\"Просмотр ваших заказов и покупок на нашем сайте,\"}',NULL,NULL,'prosmotr-zakazov',1,'/orders/preview/','2021-05-02 17:34:53'),(25,'Подарочный сертификат',NULL,'orange','[]',NULL,'{\"title\":\"Подарочный сертификат\",\"keywords\":\"подарочный сертификат, купить подарочный сертификат, что подарить, \",\"description\":\"Подарочный сертификат даёт возможность выбрать подарок самому.\"}',NULL,'<p>Не знаете, что подарить? Подарите возможность выбрать подарок самому!</p>\n\n<p>Им можно воспользоваться для оплаты любого товара из нашего каталога.</p>','podarochnyi-sertifikat',1,'/certificate/buy','2021-05-02 00:00:00'),(27,'Таблицы стоимости дымоходов',NULL,'default','[]','5f092866c08e4fldr1594435686','{\"title\":\"Таблицы ориентировочной стоимости дымоходов КАМИН42\",\"description\":\"Таблицы ориентировочной стоимости дымоходов\",\"keywords\":\"расчет дымохода, цена дымохода, купить дымоход, как установить дымоход сендвич,\"}',NULL,NULL,'tablicy-stoimosti-dymokhodov',0,NULL,'2021-05-02 00:00:00'),(28,'Схемы монтажа',NULL,'default','[]','5f0928b9d9d55fldr1594435769','{\"title\":\"Схемы монтажа модульных дымоходов и теплообменников\",\"description\":\"Схемы монтажа модульных дымоходов и теплообменников\",\"keywords\":\"дымоход, купить дымоход сендвич, схемы монтажа дымохода\"}',NULL,'<p>&nbsp;</p>\n<ul>\n<li><a href=\"/page/ustroistvo-prokhoda-perekrytiia-dlia-sendvich-truby\">СХЕМА УСТРОЙСТВА ПЕРЕКРЫТИЯ ДЛЯ СЭНДВИЧ-ТРУБЫ</a></li>\n<li>СХЕМА УСТАНОВКИ ТЕПЛООБМЕННИКА В БАНЕ</li>\n</ul>','skhemy-montazha',0,NULL,'2021-05-02 00:00:00');
/*!40000 ALTER TABLE `page` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `page_blocks`
--

DROP TABLE IF EXISTS `page_blocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `page_blocks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `intro` text,
  `pages` text,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page_blocks`
--

LOCK TABLES `page_blocks` WRITE;
/*!40000 ALTER TABLE `page_blocks` DISABLE KEYS */;
/*!40000 ALTER TABLE `page_blocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `page_category`
--

DROP TABLE IF EXISTS `page_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `page_category` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name_short` varchar(255) DEFAULT 'без названия',
  `name_full` varchar(255) DEFAULT NULL,
  `description` text,
  `seo` text,
  `parent` bigint DEFAULT '0',
  `priority` int NOT NULL DEFAULT '0',
  `url` varchar(255) NOT NULL,
  `integrated` tinyint(1) DEFAULT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page_category`
--

LOCK TABLES `page_category` WRITE;
/*!40000 ALTER TABLE `page_category` DISABLE KEYS */;
INSERT INTO `page_category` VALUES (2,'Новости','Новости',NULL,'{\"description\":\"\",\"title\":\"\"}',0,0,'novosti',0,'2021-05-03 00:00:00');
/*!40000 ALTER TABLE `page_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_methods`
--

DROP TABLE IF EXISTS `payment_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_methods` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `settings` text,
  `enable` tinyint(1) DEFAULT NULL,
  `protected` tinyint(1) DEFAULT NULL,
  `protected_name` varchar(255) DEFAULT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_methods`
--

LOCK TABLES `payment_methods` WRITE;
/*!40000 ALTER TABLE `payment_methods` DISABLE KEYS */;
INSERT INTO `payment_methods` VALUES (1,'Курьеру при получении (только наличный расчет)','Заказ оплачивается при получении курьеру, только наличный расчет',NULL,1,0,NULL),(2,'На расчетный счет','На расчетный счет организации безналичным платежом. Счета выставляем без НДС',NULL,1,NULL,NULL),(3,'Наличными или картой в кассе магазина','Заказ оплачивается наличными или картой в кассе магазина',NULL,1,0,NULL),(4,'Онлайн банковской картой VISA, Mastercard, Maestro, Мир, JCB','Оплата банковскими картами VISA, Mastercard, Maestro, Мир, JCB.',NULL,1,1,'Яндекс.Касса');
/*!40000 ALTER TABLE `payment_methods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_yandex`
--

DROP TABLE IF EXISTS `payment_yandex`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_yandex` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `secret_key` varchar(255) DEFAULT NULL,
  `shop_id` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `payment_yandex_id_uindex` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_yandex`
--

LOCK TABLES `payment_yandex` WRITE;
/*!40000 ALTER TABLE `payment_yandex` DISABLE KEYS */;
INSERT INTO `payment_yandex` VALUES (1,'live_xFVgZwVuOurbNY4wlq7WusMu0kx-21hzqWcUutGMlbQ','735705');
/*!40000 ALTER TABLE `payment_yandex` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `article` varchar(255) DEFAULT NULL,
  `specifications` text,
  `description` text,
  `seo` text,
  `images` text,
  `image_main` varchar(255) DEFAULT NULL,
  `videos` text,
  `attachments` text,
  `folder` varchar(255) DEFAULT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `label_id` bigint unsigned DEFAULT NULL,
  `manufacturer_id` bigint unsigned DEFAULT NULL,
  `unit_id` bigint unsigned DEFAULT NULL,
  `dimensions` text,
  `stock_status_id` bigint unsigned DEFAULT NULL COMMENT '0 - отсутствует\n1 - в наличии\n2 - под заказ',
  `url` varchar(255) NOT NULL,
  `product_options` text,
  `price` decimal(10,2) DEFAULT NULL,
  `price_old` decimal(10,2) DEFAULT NULL,
  `price_on_request` tinyint(1) DEFAULT NULL,
  `priority` int DEFAULT '0',
  `enable` tinyint(1) DEFAULT '1',
  `products_related` text,
  `bath_style_id` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `url` (`url`),
  UNIQUE KEY `products_article_uindex` (`article`),
  KEY `plantations` (`manufacturer_id`),
  KEY `unit` (`unit_id`),
  KEY `category` (`category_id`),
  KEY `manufacturer` (`manufacturer_id`),
  KEY `label` (`label_id`),
  KEY `stock_status` (`stock_status_id`),
  CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `product_category` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `product_ibfk_2` FOREIGN KEY (`manufacturer_id`) REFERENCES `product_manufacturer` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `product_ibfk_3` FOREIGN KEY (`unit_id`) REFERENCES `product_unit` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `product_ibfk_4` FOREIGN KEY (`label_id`) REFERENCES `product_label` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `product_ibfk_5` FOREIGN KEY (`stock_status_id`) REFERENCES `product_stock_status` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25684 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product`
--

LOCK TABLES `product` WRITE;
/*!40000 ALTER TABLE `product` DISABLE KEYS */;
/*!40000 ALTER TABLE `product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_category`
--

DROP TABLE IF EXISTS `product_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_category` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint unsigned DEFAULT '0',
  `priority` int NOT NULL DEFAULT '0',
  `name` varchar(128) NOT NULL,
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  `seo` text,
  `folder` varchar(255) DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `is_custom` tinyint(1) DEFAULT NULL,
  `custom_link` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `url` (`url`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=682 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_category`
--

LOCK TABLES `product_category` WRITE;
/*!40000 ALTER TABLE `product_category` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_label`
--

DROP TABLE IF EXISTS `product_label`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_label` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT NULL,
  `icon` varchar(32) DEFAULT NULL,
  `color` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_label`
--

LOCK TABLES `product_label` WRITE;
/*!40000 ALTER TABLE `product_label` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_label` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_manufacturer`
--

DROP TABLE IF EXISTS `product_manufacturer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_manufacturer` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text,
  `folder` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=357 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_manufacturer`
--

LOCK TABLES `product_manufacturer` WRITE;
/*!40000 ALTER TABLE `product_manufacturer` DISABLE KEYS */;
INSERT INTO `product_manufacturer` VALUES (224,'Энергофлекс',NULL,NULL,'61b5a82bc7e6e2.43305984fldr1639295019','energofleks'),(225,'ЭВАН',NULL,NULL,'61b5a82bca4d75.38373148fldr1639295019','evan'),(226,'Формат',NULL,NULL,'61b5a82bcc03f0.69065462fldr1639295019','format'),(227,'Теплолюкс',NULL,NULL,'61b5a82bcd6b82.34822954fldr1639295019','teploliuks'),(228,'СантехМастерГель',NULL,NULL,'61b5a82bcee532.55884921fldr1639295019','santekhmastergel'),(229,'РЭКО',NULL,NULL,'61b5a82bd04db8.32906415fldr1639295019','reko'),(230,'РусНИТ',NULL,NULL,'61b5a82bd1ae54.05451604fldr1639295019','rusnit'),(231,'РУВИНИЛ',NULL,NULL,'61b5a82bd31e93.68349399fldr1639295019','ruvinil'),(232,'РОСМА',NULL,NULL,'61b5a82bd48854.82071305fldr1639295019','rosma'),(233,'РММС',NULL,NULL,'61b5a82bd9e045.03336902fldr1639295019','rmms'),(234,'Политэк',NULL,NULL,'61b5a82bdb53d8.64459617fldr1639295019','politek'),(235,'Подольск кабель',NULL,NULL,'61b5a82bdcc3c5.33102017fldr1639295019','podolsk-kabel'),(236,'Нептун',NULL,NULL,'61b5a82bde7ef5.75341965fldr1639295019','neptun'),(237,'ЛЕМАКС',NULL,NULL,'61b5a82bdfea76.72863221fldr1639295019','lemaks'),(238,'КЗТО',NULL,NULL,'61b5a82be15d37.02335685fldr1639295019','kzto'),(239,'Джилекс',NULL,NULL,'61b5a82be2c571.47587455fldr1639295019','dzhileks'),(240,'Грота',NULL,NULL,'61b5a82be43610.94872467fldr1639295019','grota'),(241,'ГАЛЛОП',NULL,NULL,'61b5a82be5ad04.52184069fldr1639295019','gallop'),(242,'АНИОН',NULL,NULL,'61b5a82be71912.72344321fldr1639295019','anion'),(243,'Акватек',NULL,NULL,'61b5a82be89204.28357640fldr1639295019','akvatek'),(244,'Аквасторож',NULL,NULL,'61b5a82bea3860.21883171fldr1639295019','akvastorozh'),(245,'Акваконтроль',NULL,NULL,'61b5a82bec6817.39724950fldr1639295019','akvakontrol'),(246,'ZONT',NULL,NULL,'61b5a82befbd69.04204158fldr1639295019','zont'),(247,'ZETKAMA',NULL,NULL,'61b5a82bf2c782.37673336fldr1639295019','zetkama'),(248,'Wieland',NULL,NULL,'61b5a82c00bbf0.15529847fldr1639295020','wieland'),(249,'Weishaupt',NULL,NULL,'61b5a82c021c78.49468433fldr1639295020','weishaupt'),(250,'Watts',NULL,NULL,'61b5a82c0395e5.00526339fldr1639295020','watts'),(251,'Warme',NULL,NULL,'61b5a82c04fc38.81222919fldr1639295020','warme'),(252,'Walraven',NULL,NULL,'61b5a82c066295.05767908fldr1639295020','walraven'),(253,'VTS',NULL,NULL,'61b5a82c07c408.73039532fldr1639295020','vts'),(254,'Viessmann',NULL,NULL,'61b5a82c092512.28403655fldr1639295020','viessmann'),(255,'Viega',NULL,NULL,'61b5a82c0a8b32.78481931fldr1639295020','viega'),(256,'VIADRUS',NULL,NULL,'61b5a82c0bedc9.20191979fldr1639295020','viadrus'),(257,'VARMANN',NULL,NULL,'61b5a82c0d6582.40046187fldr1639295020','varmann'),(258,'Vaillant',NULL,NULL,'61b5a82c0ec9b5.73604672fldr1639295020','vaillant'),(259,'UPONOR',NULL,NULL,'61b5a82c103148.47073371fldr1639295020','uponor'),(260,'UNIPAK',NULL,NULL,'61b5a82c119069.54255727fldr1639295020','unipak'),(261,'UNIGB',NULL,NULL,'61b5a82c12edb1.67494839fldr1639295020','unigb'),(262,'TORK',NULL,NULL,'61b5a82c1454b8.45914713fldr1639295020','tork'),(263,'TIEMME',NULL,NULL,'61b5a82c15c198.97411674fldr1639295020','tiemme'),(264,'THERMO',NULL,NULL,'61b5a82c174cd8.56839634fldr1639295020','thermo'),(265,'Thermex',NULL,NULL,'61b5a82c18b8c1.33223812fldr1639295020','thermex'),(266,'Thermagent',NULL,NULL,'61b5a82c1a2571.82543697fldr1639295020','thermagent'),(267,'Thermaflex',NULL,NULL,'61b5a82c1b8736.06911757fldr1639295020','thermaflex'),(268,'Teplocom',NULL,NULL,'61b5a82c1db760.67221043fldr1639295020','teplocom'),(269,'Tecofi',NULL,NULL,'61b5a82c2029d6.71656975fldr1639295020','tecofi'),(270,'TECH',NULL,NULL,'61b5a82c219af6.31240275fldr1639295020','tech'),(271,'SYR',NULL,NULL,'61b5a82c230542.75922251fldr1639295020','syr'),(272,'SUPER-EGO',NULL,NULL,'61b5a82c2465a3.21121712fldr1639295020','super-ego'),(273,'STP',NULL,NULL,'61b5a82c25d2a8.77207104fldr1639295020','stp'),(274,'STOUT',NULL,NULL,'61b5a82c274309.24037097fldr1639295020','stout'),(275,'Stiebel Eltron',NULL,NULL,'61b5a82c28b023.04814968fldr1639295020','stiebel-eltron'),(276,'Stahlmann',NULL,NULL,'61b5a82c2a13a0.90467760fldr1639295020','stahlmann'),(277,'SPERONI',NULL,NULL,'61b5a82c2b7662.97189171fldr1639295020','speroni'),(278,'Sinikon',NULL,NULL,'61b5a82c2ce7b2.14522309fldr1639295020','sinikon'),(279,'Sanha',NULL,NULL,'61b5a82c2e5884.17622338fldr1639295020','sanha'),(280,'ROMMER',NULL,NULL,'61b5a82c2fbfd2.71380075fldr1639295020','rommer'),(281,'RIFAR',NULL,NULL,'61b5a82c312b19.37867265fldr1639295020','rifar'),(282,'REHAU',NULL,NULL,'61b5a82c32a2f9.80958526fldr1639295020','rehau'),(283,'Reflex',NULL,NULL,'61b5a82c3412d1.17553443fldr1639295020','reflex'),(284,'RACCORDERIE METALLICHE',NULL,NULL,'61b5a82c359da2.71472853fldr1639295020','raccorderie-metalliche'),(285,'PROTHERM',NULL,NULL,'61b5a82c370423.19096432fldr1639295020','protherm'),(286,'Prandelli',NULL,NULL,'61b5a82c387d87.39651218fldr1639295020','prandelli'),(287,'PRACTIC',NULL,NULL,'61b5a82c39f4c7.06657541fldr1639295020','practic'),(288,'PipeLife',NULL,NULL,'61b5a82c3b6995.63033078fldr1639295020','pipelife'),(289,'PEXcase',NULL,NULL,'61b5a82c3cde93.58872944fldr1639295020','pexcase'),(290,'OVENTROP',NULL,NULL,'61b5a82c3e5795.58063057fldr1639295020','oventrop'),(291,'Ostendorf',NULL,NULL,'61b5a82c3fc186.19372195fldr1639295020','ostendorf'),(292,'Novopress',NULL,NULL,'61b5a82c412676.74435079fldr1639295020','novopress'),(293,'Noirot',NULL,NULL,'61b5a82c428771.05662069fldr1639295020','noirot'),(294,'NOBILI',NULL,NULL,'61b5a82c44b8b3.94363185fldr1639295020','nobili'),(295,'N.T.M.',NULL,NULL,'61b5a82c468750.20325219fldr1639295020','ntm'),(296,'MicroART',NULL,NULL,'61b5a82c47f3b3.34181158fldr1639295020','microart'),(297,'Meibes',NULL,NULL,'61b5a82c495926.19576133fldr1639295020','meibes'),(298,'MANIERO',NULL,NULL,'61b5a82c4ac071.53943294fldr1639295020','maniero'),(299,'LUXOR',NULL,NULL,'61b5a82c4c23e5.23316015fldr1639295020','luxor'),(300,'LOWARA',NULL,NULL,'61b5a82c4d85f3.90920449fldr1639295020','lowara'),(301,'Kiturami',NULL,NULL,'61b5a82c4ee178.01057531fldr1639295020','kiturami'),(302,'Kermi',NULL,NULL,'61b5a82c5044a5.57265550fldr1639295020','kermi'),(303,'Kalde',NULL,NULL,'61b5a82c51b225.06424297fldr1639295020','kalde'),(304,'K-FLEX',NULL,NULL,'61b5a82c531f51.50541420fldr1639295020','k-flex'),(305,'JEREMIAS',NULL,NULL,'61b5a82c5482c6.66318994fldr1639295020','jeremias'),(306,'JAFAR',NULL,NULL,'61b5a82c55e928.98480495fldr1639295020','jafar'),(307,'Jacob Delafon',NULL,NULL,'61b5a82c5757e4.08309520fldr1639295020','jacob-delafon'),(308,'ITELMA',NULL,NULL,'61b5a82c58bff1.01170180fldr1639295020','itelma'),(309,'Itap',NULL,NULL,'61b5a82c5a2f19.30496552fldr1639295020','itap'),(310,'ITALTECNICA',NULL,NULL,'61b5a82c5b8e90.88159600fldr1639295020','italtecnica'),(311,'IMI',NULL,NULL,'61b5a82c5ceeb1.49814621fldr1639295020','imi'),(312,'Hongsen',NULL,NULL,'61b5a82c5e52c7.65750281fldr1639295020','hongsen'),(313,'Honeywell',NULL,NULL,'61b5a82c5fc1f0.77406031fldr1639295020','honeywell'),(314,'HME',NULL,NULL,'61b5a82c612de0.62726307fldr1639295020','hme'),(315,'HERZ',NULL,NULL,'61b5a82c62d948.88050865fldr1639295020','herz'),(316,'HEIMEIER',NULL,NULL,'61b5a82c65b688.52985492fldr1639295020','heimeier'),(317,'Hansgrohe',NULL,NULL,'61b5a82c6715e2.23657468fldr1639295020','hansgrohe'),(318,'Grundfos',NULL,NULL,'61b5a82c687285.45774504fldr1639295020','grundfos'),(319,'Gorenje',NULL,NULL,'61b5a82c69d054.78742256fldr1639295020','gorenje'),(320,'GM Cobra',NULL,NULL,'61b5a82c6b32c9.00385052fldr1639295020','gm-cobra'),(321,'Global',NULL,NULL,'61b5a82c6c9323.96077562fldr1639295020','global'),(322,'Giersch',NULL,NULL,'61b5a82c6dfcd2.06023735fldr1639295020','giersch'),(323,'Giacomini',NULL,NULL,'61b5a82c6f5aa2.01559023fldr1639295020','giacomini'),(324,'FV-PLAST',NULL,NULL,'61b5a82c70b610.45750435fldr1639295020','fv-plast'),(325,'Frisquet',NULL,NULL,'61b5a82c721417.93897187fldr1639295020','frisquet'),(326,'Flamco',NULL,NULL,'61b5a82c7371f8.44798381fldr1639295020','flamco'),(327,'FIRAT',NULL,NULL,'61b5a82c750142.83916209fldr1639295020','firat'),(328,'FAR',NULL,NULL,'61b5a82c76a363.56918201fldr1639295020','far'),(329,'Esbe',NULL,NULL,'61b5a82c78c532.46991228fldr1639295020','esbe'),(330,'Ekoplastik',NULL,NULL,'61b5a82c7abd98.72706125fldr1639295020','ekoplastik'),(331,'Drazice',NULL,NULL,'61b5a82c7c5f05.26406646fldr1639295020','drazice'),(332,'DIXIS',NULL,NULL,'61b5a82c7e4b38.57377532fldr1639295020','dixis'),(333,'DeDietrich',NULL,NULL,'61b5a82c803240.16573505fldr1639295020','dedietrich'),(334,'Decast',NULL,NULL,'61b5a82c823646.66804976fldr1639295020','decast'),(335,'Danfoss',NULL,NULL,'61b5a82c851b44.79552542fldr1639295020','danfoss'),(336,'CYKLON',NULL,NULL,'61b5a82c8809c2.88796565fldr1639295020','cyklon'),(337,'CIMM',NULL,NULL,'61b5a82c8a9e51.07120679fldr1639295020','cimm'),(338,'Cimberio',NULL,NULL,'61b5a82c8c42e1.30412066fldr1639295020','cimberio'),(339,'BUGATTI',NULL,NULL,'61b5a82c9007d2.18236091fldr1639295020','bugatti'),(340,'Buderus',NULL,NULL,'61b5a82c91e6d4.02309946fldr1639295020','buderus'),(341,'BROEN',NULL,NULL,'61b5a82c93c634.45465595fldr1639295020','broen'),(342,'Bosch',NULL,NULL,'61b5a82c963777.21679126fldr1639295020','bosch'),(343,'Blansol',NULL,NULL,'61b5a82c983035.25438136fldr1639295020','blansol'),(344,'BESTFIX',NULL,NULL,'61b5a82c99de25.19311999fldr1639295020','bestfix'),(345,'Baxi',NULL,NULL,'61b5a82c9b39d1.20375499fldr1639295020','baxi'),(346,'BARBERI',NULL,NULL,'61b5a82c9ca6a0.78415320fldr1639295020','barberi'),(347,'Baltur',NULL,NULL,'61b5a82c9e1421.96607122fldr1639295020','baltur'),(348,'Atoll',NULL,NULL,'61b5a82c9f7653.42644703fldr1639295020','atoll'),(349,'Askon',NULL,NULL,'61b5a82ca1bb50.71985140fldr1639295020','askon'),(350,'Ariston',NULL,NULL,'61b5a82ca31449.39657907fldr1639295020','ariston'),(351,'ARCO',NULL,NULL,'61b5a82ca47ed1.44981273fldr1639295020','arco'),(352,'AquaFilter',NULL,NULL,'61b5a82ca84c01.35163207fldr1639295020','aquafilter'),(353,'APE',NULL,NULL,'61b5a82cab1085.36543973fldr1639295020','ape'),(354,'ALSO',NULL,NULL,'61b5a82cac85d3.58149627fldr1639295020','also'),(355,'Alca Plast',NULL,NULL,'61b5a82cadf734.21498399fldr1639295020','alca-plast'),(356,'ACV',NULL,NULL,'61b5a82caf6924.36408588fldr1639295020','acv');
/*!40000 ALTER TABLE `product_manufacturer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_stock_status`
--

DROP TABLE IF EXISTS `product_stock_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_stock_status` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `description` text,
  `delivery_time` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_stock_status`
--

LOCK TABLES `product_stock_status` WRITE;
/*!40000 ALTER TABLE `product_stock_status` DISABLE KEYS */;
INSERT INTO `product_stock_status` VALUES (2,'В наличии на заводе','Товар находится на складе изготовителя',14),(3,'В наличии в магазине','Товар находится на нашем складе',0),(4,'На складе поставщика','Товар в наличии. Сейчас находится на складе у поставщика',7);
/*!40000 ALTER TABLE `product_stock_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_unit`
--

DROP TABLE IF EXISTS `product_unit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_unit` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(4) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `symbol_national` varchar(64) DEFAULT NULL,
  `symbol_international` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `products_units_code_uindex` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_unit`
--

LOCK TABLES `product_unit` WRITE;
/*!40000 ALTER TABLE `product_unit` DISABLE KEYS */;
INSERT INTO `product_unit` VALUES (2,'003','Миллиметр','мм','mm'),(3,'004','Сантиметр','см','cm'),(4,'006','Метр','м','m'),(5,'212','Ватт','Вт','W'),(6,'214','Киловатт','кВт','kW'),(7,'222','Вольт','В','V'),(8,'243','Ватт-час','Вт.ч','W.h'),(9,'245','Киловатт-час','кВт.ч','kW.h'),(10,'260','Ампер','А','A'),(11,'163','Грамм','г','g'),(19,'166','Килограмм','кг','kg'),(20,'053','Квадратный метр','м2','m2'),(21,'113','Кубический метр','м3','m3');
/*!40000 ALTER TABLE `product_unit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promotional_code`
--

DROP TABLE IF EXISTS promo_code;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promotional_code` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date_start` varchar(32) DEFAULT NULL,
  `date_end` varchar(32) DEFAULT NULL,
  `discount` varchar(128) DEFAULT NULL,
  `promocode` varchar(255) DEFAULT NULL,
  `auto_apply` tinyint(1) DEFAULT NULL,
  `conditions` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promotional_code`
--

LOCK TABLES promo_code WRITE;
/*!40000 ALTER TABLE promo_code DISABLE KEYS */;
INSERT INTO promo_code VALUES (5,NULL,NULL,'{\"val\":\"10\",\"unit\":\"percent\"}','123',1,'{\"countProducts\":{\"max\":7,\"min\":5},\"sumOrder\":{\"max\":10000,\"min\":5000}}');
/*!40000 ALTER TABLE promo_code ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title_prefix` varchar(128) DEFAULT NULL,
  `title_postfix` varchar(128) DEFAULT NULL,
  `title_prefix_product` varchar(128) DEFAULT NULL,
  `title_postfix_product` varchar(128) DEFAULT NULL,
  `maintenance_mode` tinyint(1) DEFAULT NULL,
  `image_header` varchar(255) DEFAULT NULL,
  `image_logo` varchar(255) DEFAULT NULL,
  `logo_text` varchar(255) DEFAULT NULL,
  `template_footer` text,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,NULL,'Лучший магазин','Купить','в \"Лучшем магазине\"',0,'',NULL,NULL,'[{\"values\":{\"12\":12,\"16\":16,\"25\":25},\"type\":\"pages\"},{\"values\":\"<span style=\\\"color:red;\\\"><b>График работы:<\\/b><\\/span> \\nПн:    Выходной; <br>\\nВт:  с 9:00 до 19:00; <br>\\nСр:  с 9:00 до 19:00; <br>\\nЧт:  с 9:00 до 19:00; <br>\\nПт:  с 9:00 до 19:00; <br>\\nСб:  с 9:00 до 19:00; <br>\\nВс:  с 9:00 до 17:00;\",\"type\":\"html\"},{\"values\":\"\",\"type\":\"html\"}]');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings_banner`
--

DROP TABLE IF EXISTS `settings_banner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings_banner` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `banner_name` varchar(64) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `href` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings_banner`
--

LOCK TABLES `settings_banner` WRITE;
/*!40000 ALTER TABLE `settings_banner` DISABLE KEYS */;
INSERT INTO `settings_banner` VALUES (11,'test','c3fd666fec090d2f391056419cc81e71.jpg',NULL,NULL);
/*!40000 ALTER TABLE `settings_banner` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings_index_page`
--

DROP TABLE IF EXISTS `settings_index_page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings_index_page` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `image_header` varchar(255) DEFAULT NULL,
  `slider` text,
  `seo` text,
  `layout` text,
  `folder` varchar(255) DEFAULT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings_index_page`
--

LOCK TABLES `settings_index_page` WRITE;
/*!40000 ALTER TABLE `settings_index_page` DISABLE KEYS */;
INSERT INTO `settings_index_page` VALUES (1,'8c087954a68d6f8abe3830b09cd76945.jpg','{\"images\":[9]}','{\"title\":\"Интернет-магазин оригинальных аксессуаров и декора для бань и саун\",\"description\":\"Интернет-магазин оригинальных аксессуаров и декора для бань и саун\"}','[{\"typeRow\":2,\"titleRow\":\"Вешалки\",\"blocks\":[7]},{\"typeRow\":2,\"titleRow\":\"Полки\",\"blocks\":[8]},{\"typeRow\":2,\"titleRow\":\"Абажуры\",\"blocks\":[6]},{\"typeRow\":1,\"titleRow\":\"Категории\",\"blocks\":[92,93,94,95,96,97]}]','index');
/*!40000 ALTER TABLE `settings_index_page` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings_layouts`
--

DROP TABLE IF EXISTS `settings_layouts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings_layouts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `layout_for` varchar(32) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT NULL,
  `for_id` bigint unsigned DEFAULT NULL,
  `blocks` text,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings_layouts`
--

LOCK TABLES `settings_layouts` WRITE;
/*!40000 ALTER TABLE `settings_layouts` DISABLE KEYS */;
INSERT INTO `settings_layouts` VALUES (3,'1',1,0,'{\"topBlocks\":[],\"sidebarBlocks\":[],\"bottomBlocks\":[]}'),(4,'1',0,92,'{\"topBlocks\":[],\"sidebarBlocks\":[],\"bottomBlocks\":[]}');
/*!40000 ALTER TABLE `settings_layouts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings_notifications`
--

DROP TABLE IF EXISTS `settings_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings_notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `smtp` varchar(64) DEFAULT NULL,
  `email_login` varchar(64) DEFAULT NULL,
  `email_pass` varchar(64) DEFAULT NULL,
  `email_to_send` varchar(64) DEFAULT NULL,
  `email_to_receive` varchar(64) DEFAULT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings_notifications`
--

LOCK TABLES `settings_notifications` WRITE;
/*!40000 ALTER TABLE `settings_notifications` DISABLE KEYS */;
INSERT INTO `settings_notifications` VALUES (1,'smtp.yandex.ru','noreply@kamin42.ru','Flame2020pr','noreply@kamin42.ru','kostyalinks@gmail.com');
/*!40000 ALTER TABLE `settings_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings_product_page`
--

DROP TABLE IF EXISTS `settings_product_page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings_product_page` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `offers` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `settings_product_page_id_uindex` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings_product_page`
--

LOCK TABLES `settings_product_page` WRITE;
/*!40000 ALTER TABLE `settings_product_page` DISABLE KEYS */;
INSERT INTO `settings_product_page` VALUES (1,'{\"1\":\"<p>Текст с описанием системы скидок на товары.<\\/p>\",\"2\":\"<p>Способы связаться с нами<\\/p>\",\"3\":\"<div class=\\\"vue-tabpanel\\\" role=\\\"tabpanel\\\" data-v-7c098904=\\\"\\\">\\n<div class=\\\"tab__payment-info\\\" data-v-7c098904=\\\"\\\">Способы оплаты:\\n<ul data-v-7c098904=\\\"\\\">\\n<li data-v-7c098904=\\\"\\\">Оплата наличными или банковской картой в магазине;<\\/li>\\n<li data-v-7c098904=\\\"\\\">Оплата банковским переводом для физических лиц;<\\/li>\\n<li data-v-7c098904=\\\"\\\">Безналичная оплата по счёту для юридических лиц и ИП (без НДС);<\\/li>\\n<li data-v-7c098904=\\\"\\\">Оплата картой он-лайн;<\\/li>\\n<\\/ul>\\n<\\/div>\\n<div class=\\\"tab__delivery-info\\\" data-v-7c098904=\\\"\\\">Способы доставки:\\n<ul data-v-7c098904=\\\"\\\">\\n<li data-v-7c098904=\\\"\\\">Самовывоз из магазина по адресу:<br data-v-7c098904=\\\"\\\" \\/>г.Кемерово, ул.Юрия Двужильного 7, корп. 2, отд. 114;<\\/li>\\n<li data-v-7c098904=\\\"\\\">Доставка наёмным транспортом с нашей погрузкой по Кемерово от 500 до 1500 рублей, в зависимости от района;<\\/li>\\n<li data-v-7c098904=\\\"\\\">Доставка наёмным транспортом с нашей погрузкой по Кемеровской области по тарифам грузоперевозчиков;<\\/li>\\n<li data-v-7c098904=\\\"\\\">Безоплатная доставка до терминала транспортной компании при заказе от 30 000 рублей;<\\/li>\\n<\\/ul>\\n<\\/div>\\n<\\/div>\"}');
/*!40000 ALTER TABLE `settings_product_page` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings_sections`
--

DROP TABLE IF EXISTS `settings_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings_sections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `section` bigint unsigned DEFAULT NULL,
  `seo` text,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `section` (`section`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings_sections`
--

LOCK TABLES `settings_sections` WRITE;
/*!40000 ALTER TABLE `settings_sections` DISABLE KEYS */;
INSERT INTO `settings_sections` VALUES (2,2,'{\"title\":\"Подборки товаров - «Камин42» Каминно Печной Дискаунтер\",\"description\":\"\"}','Подборки товаров',NULL),(4,4,'{\"title\":\"\",\"description\":\"\",\"keywords\":\"\"}','Услуги по проектированию систем отопления и дымоходов, изготовлению, созданию и установке дизайнерских каминов по индивидуальным проектам от «Камин42»',''),(5,5,'{\"title\":\"\",\"description\":\"\",\"keywords\":\"\"}','Наши работы - «Камин42» Каминно Печной Дискаунтер',NULL),(6,6,'{\"title\":\"\",\"description\":\"\",\"keywords\":\"\"}','Подарочные сертификаты',NULL),(7,7,'{\"title\":\"Фотогалерея интернет-магазина Камин42\",\"description\":\"\"}','Фотогалерея',NULL);
/*!40000 ALTER TABLE `settings_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(64) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `access_level` varchar(32) DEFAULT NULL,
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `users_login_uindex` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'zerotul','$2y$10$Px4EbwicUIfn.kru2a0y/uhGVPojR1dW/TnaG3hi7AvSK/19WPRxu','zerotul61e6d98bca4136.54350467','admin'),(2,'manager','$2y$10$70cStgwHNB9IDN8BISYZnurn02HRpqjRWkn/Uv./EXY5sobrD0J2y','manager5e5a0e1ce3ea68.64565966','admin'),(8,'test3','$2y$10$uyFrMdbprcYyRFuyDpI6oueW5.kp.wB/yaqqGgA9q03uJiEqqWuIW','test3604cf5567a5c99.01897738','manager'),(22,'111','$2y$10$CCuLxp8FP35Vt5W7pQbdROH6XvvvY2JpGNg2jwM/bJYUPZhZeQaj.','1604cfb961f45d3.58576314','manager');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-01-20  9:21:26
