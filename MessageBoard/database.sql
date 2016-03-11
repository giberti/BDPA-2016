CREATE TABLE IF NOT EXISTS `users` (
  `UserID` int(10) unsigned NOT NULL auto_increment,
  `Username` varchar(50) NOT NULL,
  `PasswordHash` varchar(50)  NOT NULL,
  `PasswordSalt` varchar(50) NOT NULL,
  `DateCreated` datetime default NULL,
  `LastLogin` datetime default NULL,
  PRIMARY KEY  (`UserID`),
  UNIQUE KEY `Username` (`Username`),
  INDEX `LastLogin` (`LastLogin`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `messages` (
  `MessageID` int(10) unsigned NOT NULL auto_increment,
  `ReplyTo` int(10) unsigned DEFAULT 0,
  `UserID` int(10) unsigned NOT NULL,
  `DatePosted` datetime NOT NULL,
  `Message` text NOT NULL,
  PRIMARY KEY  (`MessageID`),
  INDEX `ReplyTo` (`ReplyTo`),
  INDEX `DatePosted` (`DatePosted`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
