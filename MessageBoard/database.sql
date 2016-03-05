-- USERS Table
CREATE TABLE IF NOT EXISTS `users` (
  `UserID` int(10) unsigned NOT NULL auto_increment,
  `Username` varchar(50) character set utf8 NOT NULL,
  `PasswordHash` varchar(50) character set utf8 NOT NULL,
  `PasswordSalt` varchar(50) character set utf8 NOT NULL,
  `DateCreated` datetime default NULL,
  `LastLogin` datetime default NULL,
  PRIMARY KEY  (`UserID`),
  UNIQUE KEY `Username` (`Username`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

