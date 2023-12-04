-- phpMyAdmin SQL Dump
-- version 4.6.6deb5ubuntu0.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 04, 2023 at 02:30 PM
-- Server version: 5.7.35-0ubuntu0.18.04.1
-- PHP Version: 7.2.24-0ubuntu0.18.04.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `TinklaiIP`
--

-- --------------------------------------------------------

--
-- Table structure for table `Krepselio_dalis`
--

CREATE TABLE `Krepselio_dalis` (
  `Kiekis` int(11) NOT NULL,
  `id_Krepselio_dalis` int(11) NOT NULL,
  `fk_Lektuvo_dalisid_Lektuvo_dalis` int(11) NOT NULL,
  `fk_Uzsakymasid_Uzsakymas` int(11) NOT NULL,
  `Busena` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Dumping data for table `Krepselio_dalis`
--

INSERT INTO `Krepselio_dalis` (`Kiekis`, `id_Krepselio_dalis`, `fk_Lektuvo_dalisid_Lektuvo_dalis`, `fk_Uzsakymasid_Uzsakymas`, `Busena`) VALUES
(2, 45, 16, 12, 1),
(3, 55, 13, 11, 3),
(2, 56, 11, 11, 1),
(2, 57, 17, 11, 2),
(7, 59, 17, 14, 3),
(1, 60, 17, 12, 2),
(3, 62, 15, 16, 3),
(2, 63, 16, 16, 3);

-- --------------------------------------------------------

--
-- Table structure for table `Lektuvo_dalis`
--

CREATE TABLE `Lektuvo_dalis` (
  `Pavadinimas` varchar(100) COLLATE utf8_lithuanian_ci NOT NULL,
  `Gamintojas` varchar(100) COLLATE utf8_lithuanian_ci NOT NULL,
  `Modelis` varchar(100) COLLATE utf8_lithuanian_ci NOT NULL,
  `Kaina` decimal(8,2) NOT NULL,
  `Pristatymo_laikas` int(11) NOT NULL,
  `Kiekis` int(11) NOT NULL,
  `id_Lektuvo_dalis` int(11) NOT NULL,
  `fk_Tiekejo_imoneid_Tiekejo_imone` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Dumping data for table `Lektuvo_dalis`
--

INSERT INTO `Lektuvo_dalis` (`Pavadinimas`, `Gamintojas`, `Modelis`, `Kaina`, `Pristatymo_laikas`, `Kiekis`, `id_Lektuvo_dalis`, `fk_Tiekejo_imoneid_Tiekejo_imone`) VALUES
('Posukis', 'HELLA', 'hfd56161', '10.00', 5, 13, 7, 3),
('Apsauga', 'Signeda', 'ghc13465', '3.00', 7, 13, 8, 3),
('Tarpine', 'Variklio dalys', 'Rgff4654', '4.00', 9, 13, 11, 2),
('Lempute', 'Jovaitas', 'T-40', '3.00', 6, 9, 12, 2),
('Jungiklis', 'Inter', 'RHd46514', '7.00', 6, 3, 13, 2),
('Guolis', 'Signeda', 'GLL1657', '8.00', 9, 9, 14, 2),
('Sparnas', 'Lektuvo_Sparnai', 'bxgas4676', '16.00', 12, 1, 15, 4),
('Ratas', 'Ratai-gamykla', 'ra6574', '20.00', 4, 42, 16, 4),
('Å½ibintas', 'Å½ibintÅ³_gamykla', 'zhc132465', '14.00', 5, 51, 17, 5),
('Laikiklis', 'Klarksonas', 'LLK1657', '12.13', 6, 15, 18, 5),
('Laikiklis', 'Inter', 'tyc134', '9.23', 3, 20, 19, 2),
('Å½ibintas', 'Klarksonas', 'KGH164', '125.00', 3, 16, 20, 4);

-- --------------------------------------------------------

--
-- Table structure for table `Naudotojas`
--

CREATE TABLE `Naudotojas` (
  `Vardas` varchar(60) COLLATE utf8_lithuanian_ci NOT NULL,
  `Pavarde` varchar(60) COLLATE utf8_lithuanian_ci NOT NULL,
  `El_pastas` varchar(150) COLLATE utf8_lithuanian_ci NOT NULL,
  `Telefono_numeris` varchar(40) COLLATE utf8_lithuanian_ci DEFAULT NULL,
  `Slaptazodis` varchar(255) COLLATE utf8_lithuanian_ci NOT NULL,
  `Registravimo_data` date NOT NULL,
  `Role` int(11) NOT NULL,
  `id_Naudotojas` int(11) NOT NULL,
  `fk_Tiekejo_imoneid_Tiekejo_imone` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Dumping data for table `Naudotojas`
--

INSERT INTO `Naudotojas` (`Vardas`, `Pavarde`, `El_pastas`, `Telefono_numeris`, `Slaptazodis`, `Registravimo_data`, `Role`, `id_Naudotojas`, `fk_Tiekejo_imoneid_Tiekejo_imone`) VALUES
('testas1', 'testas1', 'testas1@gmail.com', '123456', '098f6bcd4621d373cade4e832627b4f6', '2023-11-03', 2, 5, NULL),
('testas2', 'testas2', 'testas2@gmail.com', '123456', 'c55c7c55eea0d6368e8cbb96315e9942', '2023-11-03', 1, 6, NULL),
('admin', 'admin', 'admin@admin.com', '123456', '21232f297a57a5a743894a0e4a801fc3', '2023-11-03', 3, 7, NULL),
('tiekejas', 'tiekejas', 'tiekejas@tiekejas.com', '123456', 'a14018564c67c42940badcbe8e2a0be2', '2023-11-04', 2, 8, 3),
('tiekejas2', 'tiekejas2', 'tiekejas2@tiekejas2.com', '123456', '5999fe73ffdc5225b920f98e0c95c238', '2023-11-04', 2, 9, 2),
('Paulius', 'Preiksa', 'preiksap@gmail.com', '37061495147', '092e859d508456d35d09b32971b2a50f', '2023-11-05', 2, 10, 4),
('vart1', 'vart1', 'vart1@gmail.com', '5467984123', 'e8e6676d4a3a1d59203f37fa0e8dd7a4', '2023-11-06', 1, 11, NULL),
('tiekejas3', 'tiekejas3', 'tiekejas3@tiekejas3.com', '156789467', '31a6bfd34ee66b4951d6dce561f8e5c1', '2023-11-07', 2, 12, 5),
('Jonas', 'Jonaitis', 'jonaitis@gmail.com', '3706146875', 'd90d5f8de6ebf47bd86f628ad86e648d', '2023-11-07', 2, 15, 2);

-- --------------------------------------------------------

--
-- Table structure for table `Naudotojo_role`
--

CREATE TABLE `Naudotojo_role` (
  `id_Naudotojo_role` int(11) NOT NULL,
  `name` char(12) COLLATE utf8_lithuanian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Dumping data for table `Naudotojo_role`
--

INSERT INTO `Naudotojo_role` (`id_Naudotojo_role`, `name`) VALUES
(1, 'Vadybininkas'),
(2, 'Tiekėjas'),
(3, 'Direktorius');

-- --------------------------------------------------------

--
-- Table structure for table `Tiekejo_imone`
--

CREATE TABLE `Tiekejo_imone` (
  `Pavadinimas` varchar(100) COLLATE utf8_lithuanian_ci NOT NULL,
  `Adresas` varchar(255) COLLATE utf8_lithuanian_ci NOT NULL,
  `Imones_kodas` varchar(50) COLLATE utf8_lithuanian_ci NOT NULL,
  `id_Tiekejo_imone` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Dumping data for table `Tiekejo_imone`
--

INSERT INTO `Tiekejo_imone` (`Pavadinimas`, `Adresas`, `Imones_kodas`, `id_Tiekejo_imone`) VALUES
('Tiekejas1', 'Kaunas', '23498765', 2),
('Tiekejas2', 'Vilnius', '1364865', 3),
('Autogata', 'Prienai', '3004697891', 4),
('Lektuvu dalys', 'Kaunas', '300465737', 5);

-- --------------------------------------------------------

--
-- Table structure for table `Uzsakymas`
--

CREATE TABLE `Uzsakymas` (
  `Saskaitos_numeris` varchar(100) COLLATE utf8_lithuanian_ci NOT NULL,
  `Data` date NOT NULL,
  `Busena` int(11) NOT NULL,
  `id_Uzsakymas` int(11) NOT NULL,
  `fk_Naudotojasid_Naudotojas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Dumping data for table `Uzsakymas`
--

INSERT INTO `Uzsakymas` (`Saskaitos_numeris`, `Data`, `Busena`, `id_Uzsakymas`, `fk_Naudotojasid_Naudotojas`) VALUES
('QRfhNZ1wBS', '2023-11-05', 2, 11, 6),
('2a8Jix0Bcr', '2023-11-06', 1, 12, 6),
('6NXE0HsHed', '2023-11-07', 3, 14, 6),
('Rt2Ufm9zYM', '2023-11-28', 3, 16, 6);

-- --------------------------------------------------------

--
-- Table structure for table `Uzsakymo_busena`
--

CREATE TABLE `Uzsakymo_busena` (
  `id_Uzsakymo_busena` int(11) NOT NULL,
  `name` char(9) COLLATE utf8_lithuanian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Dumping data for table `Uzsakymo_busena`
--

INSERT INTO `Uzsakymo_busena` (`id_Uzsakymo_busena`, `name`) VALUES
(1, 'Pateiktas'),
(2, 'Priimtas'),
(3, 'Įvykdytas');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Krepselio_dalis`
--
ALTER TABLE `Krepselio_dalis`
  ADD PRIMARY KEY (`id_Krepselio_dalis`),
  ADD KEY `Pasirinkta` (`fk_Lektuvo_dalisid_Lektuvo_dalis`),
  ADD KEY `Sudarytas` (`fk_Uzsakymasid_Uzsakymas`),
  ADD KEY `Busena` (`Busena`);

--
-- Indexes for table `Lektuvo_dalis`
--
ALTER TABLE `Lektuvo_dalis`
  ADD PRIMARY KEY (`id_Lektuvo_dalis`),
  ADD KEY `Teikia` (`fk_Tiekejo_imoneid_Tiekejo_imone`);

--
-- Indexes for table `Naudotojas`
--
ALTER TABLE `Naudotojas`
  ADD PRIMARY KEY (`id_Naudotojas`),
  ADD KEY `Role` (`Role`),
  ADD KEY `Priklauso` (`fk_Tiekejo_imoneid_Tiekejo_imone`);

--
-- Indexes for table `Naudotojo_role`
--
ALTER TABLE `Naudotojo_role`
  ADD PRIMARY KEY (`id_Naudotojo_role`);

--
-- Indexes for table `Tiekejo_imone`
--
ALTER TABLE `Tiekejo_imone`
  ADD PRIMARY KEY (`id_Tiekejo_imone`);

--
-- Indexes for table `Uzsakymas`
--
ALTER TABLE `Uzsakymas`
  ADD PRIMARY KEY (`id_Uzsakymas`),
  ADD KEY `Busena` (`Busena`),
  ADD KEY `Atlieka` (`fk_Naudotojasid_Naudotojas`);

--
-- Indexes for table `Uzsakymo_busena`
--
ALTER TABLE `Uzsakymo_busena`
  ADD PRIMARY KEY (`id_Uzsakymo_busena`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Krepselio_dalis`
--
ALTER TABLE `Krepselio_dalis`
  MODIFY `id_Krepselio_dalis` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;
--
-- AUTO_INCREMENT for table `Lektuvo_dalis`
--
ALTER TABLE `Lektuvo_dalis`
  MODIFY `id_Lektuvo_dalis` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `Naudotojas`
--
ALTER TABLE `Naudotojas`
  MODIFY `id_Naudotojas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `Tiekejo_imone`
--
ALTER TABLE `Tiekejo_imone`
  MODIFY `id_Tiekejo_imone` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `Uzsakymas`
--
ALTER TABLE `Uzsakymas`
  MODIFY `id_Uzsakymas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `Krepselio_dalis`
--
ALTER TABLE `Krepselio_dalis`
  ADD CONSTRAINT `Krepselio_dalis_ibfk_1` FOREIGN KEY (`Busena`) REFERENCES `Uzsakymo_busena` (`id_Uzsakymo_busena`),
  ADD CONSTRAINT `Pasirinkta` FOREIGN KEY (`fk_Lektuvo_dalisid_Lektuvo_dalis`) REFERENCES `Lektuvo_dalis` (`id_Lektuvo_dalis`),
  ADD CONSTRAINT `Sudarytas` FOREIGN KEY (`fk_Uzsakymasid_Uzsakymas`) REFERENCES `Uzsakymas` (`id_Uzsakymas`);

--
-- Constraints for table `Lektuvo_dalis`
--
ALTER TABLE `Lektuvo_dalis`
  ADD CONSTRAINT `Teikia` FOREIGN KEY (`fk_Tiekejo_imoneid_Tiekejo_imone`) REFERENCES `Tiekejo_imone` (`id_Tiekejo_imone`);

--
-- Constraints for table `Naudotojas`
--
ALTER TABLE `Naudotojas`
  ADD CONSTRAINT `Naudotojas_ibfk_1` FOREIGN KEY (`Role`) REFERENCES `Naudotojo_role` (`id_Naudotojo_role`),
  ADD CONSTRAINT `Priklauso` FOREIGN KEY (`fk_Tiekejo_imoneid_Tiekejo_imone`) REFERENCES `Tiekejo_imone` (`id_Tiekejo_imone`);

--
-- Constraints for table `Uzsakymas`
--
ALTER TABLE `Uzsakymas`
  ADD CONSTRAINT `Atlieka` FOREIGN KEY (`fk_Naudotojasid_Naudotojas`) REFERENCES `Naudotojas` (`id_Naudotojas`),
  ADD CONSTRAINT `Uzsakymas_ibfk_1` FOREIGN KEY (`Busena`) REFERENCES `Uzsakymo_busena` (`id_Uzsakymo_busena`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
