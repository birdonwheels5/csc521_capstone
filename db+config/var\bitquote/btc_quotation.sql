-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 14, 2015 at 11:59 AM
-- Server version: 5.5.44-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `btc_quotation`
--

-- --------------------------------------------------------

--
-- Table structure for table `bitfinex`
--

CREATE TABLE IF NOT EXISTS `bitfinex` (
  `btc_price` float NOT NULL,
  `extra` int(11) NOT NULL,
  UNIQUE KEY `extra` (`extra`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bitfinex`
--

INSERT INTO `bitfinex` (`btc_price`, `extra`) VALUES
(334.02, 0);

-- --------------------------------------------------------

--
-- Table structure for table `bitstamp`
--

CREATE TABLE IF NOT EXISTS `bitstamp` (
  `btc_price` float NOT NULL,
  `extra` int(11) NOT NULL,
  UNIQUE KEY `extra` (`extra`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bitstamp`
--

INSERT INTO `bitstamp` (`btc_price`, `extra`) VALUES
(333.25, 0);

-- --------------------------------------------------------

--
-- Table structure for table `btc-e`
--

CREATE TABLE IF NOT EXISTS `btc-e` (
  `btc_price` float NOT NULL,
  `extra` int(11) NOT NULL,
  UNIQUE KEY `extra` (`extra`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `btc-e`
--

INSERT INTO `btc-e` (`btc_price`, `extra`) VALUES
(329.28, 0);

-- --------------------------------------------------------

--
-- Table structure for table `btcchina`
--

CREATE TABLE IF NOT EXISTS `btcchina` (
  `btc_price` float NOT NULL,
  `extra` int(11) NOT NULL,
  UNIQUE KEY `extra` (`extra`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `btcchina`
--

INSERT INTO `btcchina` (`btc_price`, `extra`) VALUES
(334.66, 0);

-- --------------------------------------------------------

--
-- Table structure for table `coinbase`
--

CREATE TABLE IF NOT EXISTS `coinbase` (
  `btc_price` float NOT NULL,
  `extra` int(11) NOT NULL,
  UNIQUE KEY `extra` (`extra`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `coinbase`
--

INSERT INTO `coinbase` (`btc_price`, `extra`) VALUES
(334, 0);

-- --------------------------------------------------------

--
-- Table structure for table `cryptsy`
--

CREATE TABLE IF NOT EXISTS `cryptsy` (
  `btc_price` float NOT NULL,
  `extra` int(11) NOT NULL,
  UNIQUE KEY `extra` (`extra`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cryptsy`
--

INSERT INTO `cryptsy` (`btc_price`, `extra`) VALUES
(366.63, 0);

-- --------------------------------------------------------

--
-- Table structure for table `huobi`
--

CREATE TABLE IF NOT EXISTS `huobi` (
  `btc_price` float NOT NULL,
  `extra` int(11) NOT NULL,
  UNIQUE KEY `extra` (`extra`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `huobi`
--

INSERT INTO `huobi` (`btc_price`, `extra`) VALUES
(335.3, 0);

-- --------------------------------------------------------

--
-- Table structure for table `kraken`
--

CREATE TABLE IF NOT EXISTS `kraken` (
  `btc_price` float NOT NULL,
  `extra` int(11) NOT NULL,
  UNIQUE KEY `extra` (`extra`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `kraken`
--

INSERT INTO `kraken` (`btc_price`, `extra`) VALUES
(333.33, 0);

-- --------------------------------------------------------

--
-- Table structure for table `okcoin`
--

CREATE TABLE IF NOT EXISTS `okcoin` (
  `btc_price` float NOT NULL,
  `extra` int(11) NOT NULL,
  UNIQUE KEY `extra` (`extra`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `okcoin`
--

INSERT INTO `okcoin` (`btc_price`, `extra`) VALUES
(334.1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tweets`
--

CREATE TABLE IF NOT EXISTS `tweets` (
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
  `extra` int(11) NOT NULL,
  UNIQUE KEY `extra` (`extra`),
  UNIQUE KEY `extra_2` (`extra`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tweets`
--

INSERT INTO `tweets` (`tweet0`, `tweet1`, `tweet2`, `tweet3`, `tweet4`, `tweet5`, `tweet6`, `tweet7`, `tweet8`, `tweet9`, `extra`) VALUES
('1447260358 <b>bitcoin</b>: RT @coindesk: The latest Bitcoin Price Index is 309.30 USD https://t.co/lzUu2wyPQN https://t.co/Kt5Irp7nLB', '1447260958 <b>actualcryptos</b>: RT coindesk: The latest Bitcoin Price Index is 309.30 USD https://t.co/bbgZeLsuEQ https://t.co/9iSFZXXCNK', '1447260563 <b>btcforum</b>: Press â€¢ [2015-11-11] CNBC: Banks could use bitcoin technology by next year: study https://t.co/E34MI6CDHL', '1447463316 <b>cryptocoinsnews</b>: Scam Alert â€“ Coinspace https://t.co/KfQPySRmGN #Bitcoin https://t.co/d10hHJ8aEt', '1447506252 <b>bitcoin</b>: ðŸ™‡ Bigwigs praising blockchains https://t.co/vamcUcChDJ', '1447506708 <b>actualcryptos</b>: ðŸ™‡ Bigwigs praising blockchains https://t.co/QS6LqCKYAG', '1447512576 <b>cryptocoinsnews</b>: Bitcoin Price Takes a Fall to Hit a Low of $300 https://t.co/7V6XWb1NO3 #cryptocurrency https://t.co/e3rpeuUrBf', '1447516716 <b>btcforum</b>: Bitcoin Discussion â€¢ This is an Epic Scale, &quot;miner&quot; Bitcoin https://t.co/3QMTWLN6OH', '1447517896 <b>bitcoin</b>: RT @coindesk: The latest Bitcoin Price Index is 332.63 USD https://t.co/lzUu2wyPQN https://t.co/YMZHinGvHr', '1447518530 <b>actualcryptos</b>: RT coindesk: The latest Bitcoin Price Index is 332.63 USD https://t.co/bbgZeLsuEQ https://t.co/j8oJDAEGPI', 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
