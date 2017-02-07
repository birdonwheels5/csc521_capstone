CREATE  TABLE  Price_Buffer
(
	bitfinex FLOAT
	bitstamp FLOAT
	btce FLOAT
	btcChina FLOAT
	coinbase FLOAT
	huobi FLOAT
	kraken FLOAT
	okcoin FLOAT
)
CREATE  TABLE Price_History
(	
	bitfinex FLOAT
	bitstamp FLOAT
	btce FLOAT
	btcChina FLOAT
	coinbase FLOAT
	huobi FLOAT
	kraken FLOAT
	okcoin FLOAT
	tstamp BIGINT
)
CREATE  TABLE Reddit_Posts
(
	tstamp BIGINT
)
CREATE  TABLE Twitter_Posts
(
	tstamp BIGINT
)
CREATE  TABLE Forum_Posts
(
	tstamp BIGINT
)
CREATE  TABLE Users
(
	user_id_num INT AUTO_INCREMENT PRIMARY KEY
	username VARCHAR(25)
	uuid VARCHAR(256)
	hashed_password VARCHAR(512)
	new_hashed_pasword VARCHAR(512)
	salt VARCHAR(32)
	authority_level INT
	creation_time BIGINT
	last_login BIGINT
	email VARCHAR(255)
	validate TINYINT
	session_id VARCHAR(256)
)