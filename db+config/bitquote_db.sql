DROP TABLE IF EXISTS Price_Buffer, Price_History, Reddit_Posts, Twitter_Posts, Forum_Posts, users;

CREATE TABLE  Price_Buffer
(
	bitfinex FLOAT,
	bitstamp FLOAT,
	`btc-e` FLOAT,
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
	tstamp BIGINT NOT NULL PRIMARY KEY
);

CREATE TABLE Reddit_Posts
(
	tstamp BIGINT NOT NULL,
	rp_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	post_url VARCHAR(2085) NOT NULL, -- 2085 characters because IE can only only handle URLS with max 2083 characters
	OP VARCHAR(20) NOT NULL,
	post_text VARCHAR(65536) NOT NULL
);

CREATE TABLE Twitter_Posts
(
	tstamp BIGINT NOT NULL,
	tp_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(22) NOT NULL, -- 22 characters instead of the 15 that twitter allows, because we store 7 characters of html formatting
	post_text VARCHAR(280) NOT NULL -- Doubled because we're storing html safe tweets
);

CREATE TABLE Forum_Posts
(
	tstamp BIGINT NOT NULL,
	fp_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	post_url VARCHAR(2085) NOT NULL, -- 2085 characters because IE can only only handle URLS with max 2083 characters
	username VARCHAR(2) NOT NULL,
	post_text VARCHAR(65536) NOT NULL,
	forum_name VARCHAR(256) NOT NULL
);

CREATE TABLE users
(
	user_id_num INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(25) NOT NULL,
	uuid VARCHAR(256) NOT NULL,
	hashed_password VARCHAR(512) NOT NULL,
	new_hashed_pasword VARCHAR(512),
	salt BLOB NOT NULL,
	authority_level INT NOT NULL,
	creation_time BIGINT NOT NULL,
	last_login BIGINT,
	email VARCHAR(256) NOT NULL,
	validate TINYINT NOT NULL,
	session_id VARCHAR(256)
);

-- ALTER TABLE Twitter_Posts ADD UNIQUE(post_text);

-- Initialize the Price_Buffer table
INSERT INTO Price_Buffer (bitfinex, bitstamp, `btc-e`, btcchina, coinbase, huobi, kraken, okcoin, extra) VALUES ('0', '0', '0', '0', '0', '0', '0', '0', '0');
