drop table #__user;
CREATE TABLE #__user (
	userid INT AUTO_INCREMENT,
	username VARCHAR(50) NULL DEFAULT NULL,
	password VARCHAR(50) NULL DEFAULT NULL,
	createddate DATETIME NULL DEFAULT NULL,
	rightsadmin INT NULL DEFAULT 0,
	rightsbusiness INT NULL DEFAULT 0,
	rightsviewer INT NULL DEFAULT 0,
	realname VARCHAR(50) NULL DEFAULT NULL,
	loginlast DATETIME NULL DEFAULT NULL,
	PRIMARY KEY (userid)
);

drop table #__userstamp;
CREATE TABLE #__userstamp (
	stampid INT AUTO_INCREMENT,
	userid INT NOT NULL,
	loginstamp VARCHAR(20) NULL DEFAULT NULL,
	PRIMARY KEY (stampid)
);

---default adminstrator: 12345678
INSERT INTO `#__user` (`userid`, `username`, `password`, `createddate`, `rightsadmin`, `rightsbusiness`, `rightsviewer`) 
VALUES(1, 'admin01', MD5('12345678'), '2015-09-21 00:00:00', 1, 1, 1);

drop table #__task;
CREATE TABLE #__task (
	taskid INT AUTO_INCREMENT,
	todo VARCHAR(512) NULL DEFAULT NULL,
	duedate INT NULL DEFAULT NULL,
	contact VARCHAR(50) NULL DEFAULT NULL,
	status INT NULL DEFAULT 0,
	userid INT NULL DEFAULT 0,
	createddate DATETIME NULL DEFAULT NULL,
	PRIMARY KEY (taskid)
);

drop table #__client;
CREATE TABLE #__client (
	clientid INT AUTO_INCREMENT,
	firstname VARCHAR(30) NULL DEFAULT NULL,
	lastname VARCHAR(30) NULL DEFAULT NULL,
	title VARCHAR(10) NULL DEFAULT NULL,
	phone VARCHAR(20) NULL DEFAULT NULL,
	email VARCHAR(50) NULL DEFAULT NULL,

	address1 VARCHAR(50) NULL DEFAULT NULL,
	address2 VARCHAR(50) NULL DEFAULT NULL,
	address3 VARCHAR(50) NULL DEFAULT NULL,
	addresscity VARCHAR(50) NULL DEFAULT NULL,
	addresscode VARCHAR(50) NULL DEFAULT NULL,
	post1 VARCHAR(50) NULL DEFAULT NULL,
	post2 VARCHAR(50) NULL DEFAULT NULL,
	post3 VARCHAR(50) NULL DEFAULT NULL,
	postcity VARCHAR(50) NULL DEFAULT NULL,
	postcode VARCHAR(50) NULL DEFAULT NULL,

	company VARCHAR(50) NULL DEFAULT NULL,
	remark VARCHAR(250) NULL DEFAULT NULL,
	userid INT NULL DEFAULT 0,
	createddate DATETIME NULL DEFAULT NULL,
	PRIMARY KEY (clientid)
);
