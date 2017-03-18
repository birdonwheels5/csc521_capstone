DROP TABLE IF EXISTS Price_Buffer, Price_History, Reddit_Posts, Twitter_Posts, Forum_Posts, Users;

CREATE TABLE  Price_Buffer
(
	bitfinex FLOAT,
	bitstamp FLOAT,
	btc-e FLOAT,
	btcchina FLOAT,
	coinbase FLOAT,
	huobi FLOAT,
	kraken FLOAT,
	okcoin FLOAT,
	extra TINYINT NOT NULL PRIMARY KEY
);
CREATE TABLE Price_History
(	
	bitfinex FLOAT,
	bitstamp FLOAT,
	`btc-e` FLOAT,
	btcchina FLOAT,
	coinbase FLOAT,
	huobi FLOAT,
	kraken FLOAT,
	okcoin FLOAT,
	tstamp BIGINT
);

CREATE TABLE Reddit_Posts
(
	tstamp BIGINT,
	rp_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	OP VARCHAR(20),
	post_text VARCHAR(65536)
	
);

CREATE TABLE Twitter_Posts
(
	tstamp BIGINT,
	tp_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(15),
	post_text VARCHAR(140)
);

CREATE TABLE Forum_Posts
(
	tstamp BIGINT,
	fp_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(15),
	post_text VARCHAR(65536),
	forum_name VARCHAR(256)
);

CREATE TABLE users
(
	user_id_num INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(25) NOT NULL,
	uuid VARCHAR(256) NOT NULL,
	hashed_password VARCHAR(512) NOT NULL,
	new_hashed_pasword VARCHAR(512),
	salt VARCHAR(32) NOT NULL,
	authority_level INT NOT NULL,
	creation_time BIGINT NOT NULL,
	last_login BIGINT,
	email VARCHAR(256) NOT NULL,
	validate TINYINT NOT NULL,
	session_id VARCHAR(256)
);

-- Initialize the Price_Buffer table
INSERT INTO Price_Buffer (bitfinex, bitstamp, `btc-e`, btcchina, coinbase, huobi, kraken, okcoin, extra) VALUES ('0', '0', '0', '0', '0', '0', '0', '0', '0');
