SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `app`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

USE `app`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255),
  `date` date,
  `city` varchar(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);
  
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

CREATE TABLE IF NOT EXISTS `companies` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `cnpj` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cnpj` (`cnpj`);
  
ALTER TABLE `companies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

CREATE TABLE IF NOT EXISTS `users_companies` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `company_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `users_companies`
  ADD PRIMARY KEY (`id`),

  ADD CONSTRAINT `fk_user_id`
  FOREIGN KEY (`user_id`)
  REFERENCES `users`(`id`),

  ADD CONSTRAINT `fk_company_id`
  FOREIGN KEY (`company_id`)
  REFERENCES `companies`(`id`);

ALTER TABLE `users_companies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

INSERT INTO `users` (`name`, `email`, `phone`, `date`, `city`) VALUES
('joao', 'joao@email.com', '(38) 98855-7755', '2022-05-05', 'Montes Claros');

SET @user_id = LAST_INSERT_ID();

INSERT INTO `companies` (`name`, `cnpj`, `address`) VALUES
('empresa a', '315116', 'Rua A, N 40');

SET @company_id = LAST_INSERT_ID();

INSERT INTO `users_companies` (`user_id`, `company_id`) VALUES
(@user_id, @company_id);

INSERT INTO `users` (`name`, `email`, `phone`, `date`, `city`) VALUES
('maria', 'maria@email.com', '(38) 98855-7755', '2022-05-05', 'Sao Paulo');

SET @user_id = LAST_INSERT_ID();

INSERT INTO `companies` (`name`, `cnpj`, `address`) VALUES
('empresa b', '315117', 'Rua B, N 10');

SET @company_id = LAST_INSERT_ID();

INSERT INTO `users_companies` (`user_id`, `company_id`) VALUES
(@user_id, @company_id);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
