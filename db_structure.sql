--
-- Table structure for table `shop`
--

DROP TABLE IF EXISTS `shop`;
CREATE TABLE `shop` (
  `shop_name` varchar(191) NOT NULL,
  `city` varchar(191) NOT NULL,
  `address` varchar(191) NOT NULL,
  `id` int(10) unsigned AUTO_INCREMENT PRIMARY KEY,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `firstname` varchar(191) NOT NULL,
  `secondname` varchar(191) NOT NULL,
  `username` varchar(191) NOT NULL UNIQUE,
  `password` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `birthdate` date NOT NULL,
  `isAdmin` tinyint(1) DEFAULT NULL,
  `confirmed` tinyint(1) DEFAULT NULL,
  `id` int(10) unsigned AUTO_INCREMENT PRIMARY KEY,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
