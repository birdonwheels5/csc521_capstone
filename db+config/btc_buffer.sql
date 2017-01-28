-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 27, 2017 at 06:22 PM
-- Server version: 5.7.13-0ubuntu0.16.04.2
-- PHP Version: 7.0.8-0ubuntu0.16.04.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `btc_buffer`
--

-- --------------------------------------------------------

--
-- Table structure for table `bitfinex`
--

CREATE TABLE `bitfinex` (
  `btc_price` float NOT NULL,
  `extra` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bitfinex`
--

INSERT INTO `bitfinex` (`btc_price`, `extra`) VALUES
(917.53, 0);

-- --------------------------------------------------------

--
-- Table structure for table `bitstamp`
--

CREATE TABLE `bitstamp` (
  `btc_price` float NOT NULL,
  `extra` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bitstamp`
--

INSERT INTO `bitstamp` (`btc_price`, `extra`) VALUES
(919.32, 0);

-- --------------------------------------------------------

--
-- Table structure for table `btc-e`
--

CREATE TABLE `btc-e` (
  `btc_price` float NOT NULL,
  `extra` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `btc-e`
--

INSERT INTO `btc-e` (`btc_price`, `extra`) VALUES
(910, 0);

-- --------------------------------------------------------

--
-- Table structure for table `btcchina`
--

CREATE TABLE `btcchina` (
  `btc_price` float NOT NULL,
  `extra` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `btcchina`
--

INSERT INTO `btcchina` (`btc_price`, `extra`) VALUES
(918.63, 0);

-- --------------------------------------------------------

--
-- Table structure for table `coinbase`
--

CREATE TABLE `coinbase` (
  `btc_price` float NOT NULL,
  `extra` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `coinbase`
--

INSERT INTO `coinbase` (`btc_price`, `extra`) VALUES
(922.6, 0);

-- --------------------------------------------------------

--
-- Table structure for table `huobi`
--

CREATE TABLE `huobi` (
  `btc_price` float NOT NULL,
  `extra` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `huobi`
--

INSERT INTO `huobi` (`btc_price`, `extra`) VALUES
(922.47, 0);

-- --------------------------------------------------------

--
-- Table structure for table `kraken`
--

CREATE TABLE `kraken` (
  `btc_price` float NOT NULL,
  `extra` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `kraken`
--

INSERT INTO `kraken` (`btc_price`, `extra`) VALUES
(922.37, 0);

-- --------------------------------------------------------

--
-- Table structure for table `okcoin`
--

CREATE TABLE `okcoin` (
  `btc_price` float NOT NULL,
  `extra` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `okcoin`
--

INSERT INTO `okcoin` (`btc_price`, `extra`) VALUES
(914.26, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tweets`
--

CREATE TABLE `tweets` (
  `tweet0` text NOT NULL,
  `tweet1` text NOT NULL,
  `tweet2` text NOT NULL,
  `tweet3` text NOT NULL,
  `tweet4` text NOT NULL,
  `tweet5` text NOT NULL,
  `tweet6` text NOT NULL,
  `tweet7` text NOT NULL,
  `tweet8` text NOT NULL,
  `tweet9` text NOT NULL,
  `extra` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tweets`
--

INSERT INTO `tweets` (`tweet0`, `tweet1`, `tweet2`, `tweet3`, `tweet4`, `tweet5`, `tweet6`, `tweet7`, `tweet8`, `tweet9`, `extra`) VALUES
('1485261175 <b>actualcryptos</b>: RT coindesk: The latest Bitcoin Price Index is 906.75 USD https://t.co/FbH2dtnO44 https://t.co/4pKn3UZBmK', '1485260752 <b>cryptocoinsnews</b>: Bitcoin Trading Fees See Volumes Dive in China https://t.co/fQ6bTqwmwq https://t.co/dRHef6nfXN', '1485265063 <b>cryptocoinsnews</b>: Harvard Business Review: Blockchain Is Foundational, Not Disruptive https://t.co/koiHLdvBMs https://t.co/1ghWHKP00J', '1485535494 <b>bitcoin</b>: RT @coindesk: The latest Bitcoin Price Index is 920.82 USD https://t.co/lzUu2wyPQN https://t.co/8RYQYPCeRR', '1485535597 <b>actualcryptos</b>: RT coindesk: The latest Bitcoin Price Index is 920.82 USD https://t.co/FbH2dtnO44 https://t.co/B6zmDNb0FZ', '1485535603 <b>cryptocoinsnews</b>: Italian Politician Links Bitcoin to Mafia Controlled Gambling Industry https://t.co/Vma3sJnyaC https://t.co/T7jcTaEGBJ', '1485540009 <b>coindesk</b>: LendingRobot is Moving Investment Records to a the Public Ethereum Blockchain. https://t.co/hIUj7tLpKm', '1485549965 <b>bitcoin</b>: RT @coindesk: The latest Bitcoin Price Index is 918.78 USD https://t.co/lzUu2wyPQN https://t.co/6Xf7b4t1WX', '1485550271 <b>actualcryptos</b>: RT coindesk: The latest Bitcoin Price Index is 918.78 USD https://t.co/FbH2dtnO44 https://t.co/Go2rgStPXk', '1485547060 <b>cryptocoinsnews</b>: The US Postal Inspection Service is Seeking Bitcoin â€˜Intelligence Gathering Specialistsâ€™ https://t.co/ShQ3JIY16f https://t.co/6xEuypKnVr', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id_num` int(11) NOT NULL,
  `username` text NOT NULL,
  `uuid` text NOT NULL,
  `hashed_password` text NOT NULL,
  `new_hashed_password` text NOT NULL,
  `salt` text NOT NULL,
  `authority_level` int(11) NOT NULL,
  `creation_time` int(11) NOT NULL,
  `last_login` int(11) NOT NULL,
  `email` char(255) NOT NULL,
  `validate` tinyint(1) NOT NULL,
  `session_id` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id_num`, `username`, `uuid`, `hashed_password`, `new_hashed_password`, `salt`, `authority_level`, `creation_time`, `last_login`, `email`, `validate`, `session_id`) VALUES
(1, 'admin', '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', '61e0e218101bc04590b8e45595c8138d02d19f8d8b18be018722fae5394b53ba202e6365d2bef4db88b411bbbe86e48ceda92b159e1d6ffeafd8363660f6402c', '', 'Ø]”_6áVË', 900, 1429838373, 1477618596, 'admin@example.com', 1, ''),
(2, 'user', '04f8996da763b7a969b1028ee3007569eaf3a635486ddab211d512c85b9df8fb', 'b2f638f58b7d0ade2c525754bedaba06d1a23250c4d7aacfebd60000ade1f756f6eeb4011cf39156b2ed14ba8373262dd032c41d8c83e5af803825018025b630', '', 'òŸÜeäm-', 100, 1429838441, 1477617644, 'user@example.com', 1, ''),
(6, 'phpmyadmin', 'c447c6fc675bdcf9229d1a968ffea442a007cbda363e3e81f3783a09c05d9d85', 'e770adeb1d706ae9e68a66eea7221e5ed86f5a823d9e3ded1d3c23be4f02def8b880f58be2e047d935900cc8069dfa708695b04dc943c4ed70782c080799eeb7', '', 'µë3îƒFÒ', 100, 1476895791, 1478042705, 'birdonwheels555@gmail.com', 1, ''),
(7, 'birdonwheels5', '80b83206be19c01cda8c33fc0a026cf0753a08a629d9ae57fceccb4ed32db01d', 'b722ccbcdd2b72df39aa65535b6cdd370d012802822ce47c09edb8d1ec8a6f4595237f6d4b0bbf94d9d535661eeabc4bc0331fb71f1061c58847fca5bc24222b', '', '¥AqÂmho', 100, 1477189932, 1485559165, 'llll@gmail.com', 1, 'f8285fe0e574624f282e879f4e7aae19b6eeeb77506f78e3ea2adb2d278cd8df'),
(8, 'zaa', '621a13096d2107cd950ff4252924fad65bea11a417c68b65fc1c614a9d2acc18', '9afce461e183908d4af1b1d56440ad0ca81203165e8fbccf8d8d5eed3e38125f215ea462c41ffb669b50a6f503013ec9dc220b2dce442f2eee8123dc72787957', '', '\r,ƒ0Üù›', 100, 1480089395, 0, 'billy$#@@@gmail.com', 0, '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bitfinex`
--
ALTER TABLE `bitfinex`
  ADD UNIQUE KEY `extra` (`extra`);

--
-- Indexes for table `bitstamp`
--
ALTER TABLE `bitstamp`
  ADD UNIQUE KEY `extra` (`extra`);

--
-- Indexes for table `btc-e`
--
ALTER TABLE `btc-e`
  ADD UNIQUE KEY `extra` (`extra`);

--
-- Indexes for table `btcchina`
--
ALTER TABLE `btcchina`
  ADD UNIQUE KEY `extra` (`extra`);

--
-- Indexes for table `coinbase`
--
ALTER TABLE `coinbase`
  ADD UNIQUE KEY `extra` (`extra`);

--
-- Indexes for table `huobi`
--
ALTER TABLE `huobi`
  ADD UNIQUE KEY `extra` (`extra`);

--
-- Indexes for table `kraken`
--
ALTER TABLE `kraken`
  ADD UNIQUE KEY `extra` (`extra`);

--
-- Indexes for table `okcoin`
--
ALTER TABLE `okcoin`
  ADD UNIQUE KEY `extra` (`extra`);

--
-- Indexes for table `tweets`
--
ALTER TABLE `tweets`
  ADD UNIQUE KEY `extra` (`extra`),
  ADD UNIQUE KEY `extra_2` (`extra`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id_num`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_2` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id_num` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
