CREATE TABLE IF NOT EXISTS `pre_onecode` (
  `code` varchar(32) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `addtime` datetime DEFAULT NULL,
  `bindtime` datetime DEFAULT NULL,
 PRIMARY KEY (`code`),
 KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
