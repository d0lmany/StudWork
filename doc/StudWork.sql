-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: MySQL-8.2
-- Время создания: Дек 13 2024 г., 23:16
-- Версия сервера: 8.2.0
-- Версия PHP: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `StudWork`
--
CREATE DATABASE IF NOT EXISTS `StudWork` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `StudWork`;

-- --------------------------------------------------------

--
-- Структура таблицы `chat`
--

CREATE TABLE `chat` (
  `id` int NOT NULL,
  `sender_id` int NOT NULL,
  `receiver_id` int NOT NULL,
  `message` text NOT NULL,
  `timestamp` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `claims`
--

CREATE TABLE `claims` (
  `id` int NOT NULL,
  `owner_id` int NOT NULL,
  `object_type` varchar(100) NOT NULL,
  `object_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `employer`
--

CREATE TABLE `employer` (
  `id` int NOT NULL,
  `name_organization` varchar(255) NOT NULL,
  `address` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `is_verified` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `employer`
--

INSERT INTO `employer` (`id`, `name_organization`, `address`, `is_verified`) VALUES
(4, 'Doofenshmirtz Evil Inc.', '9297 Polly Parkway', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `favourites_list`
--

CREATE TABLE `favourites_list` (
  `id` int NOT NULL,
  `owner_id` int NOT NULL,
  `list` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `invite_list`
--

CREATE TABLE `invite_list` (
  `id` int NOT NULL,
  `student_id` int NOT NULL,
  `employer_id` int NOT NULL,
  `vacancy_id` int NOT NULL,
  `invite_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `response`
--

CREATE TABLE `response` (
  `id` int NOT NULL,
  `student_id` int NOT NULL,
  `employer_id` int NOT NULL,
  `vacancy_id` int NOT NULL,
  `resume_id` int NOT NULL,
  `response_date` date NOT NULL,
  `status` enum('Отправлен','Отказ','Приглашение') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `resume`
--

CREATE TABLE `resume` (
  `id` int NOT NULL,
  `student_id` int NOT NULL,
  `cover_letter` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `student`
--

CREATE TABLE `student` (
  `id` int NOT NULL,
  `education` json NOT NULL,
  `skills` json DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `student`
--

INSERT INTO `student` (`id`, `education`, `skills`, `is_verified`) VALUES
(2, '{\"ei\": \"ГБПОУ Челябинский энергетический колледж им. С.М. Кирова\", \"yog\": \"2026\", \"level\": \"Среднее\", \"faculty\": \"Информационные системы и программирование\", \"specialization\": \"Разработчик веб и мультимедийных приложений\"}', '{\"skills\": [\"HTML\", \"CSS\", \"JavaScript\", \"PHP\", \"SQL\"]}', 1),
(3, '{\"ei\": \"ГБПОУ Челябинский энергетический колледж им. С.М. Кирова\", \"yog\": \"2026\", \"level\": \"Среднее\", \"faculty\": \"Информационные системы и программирование\", \"specialization\": \"Разработчик веб и мультимедийных приложений\"}', '{\"skills\": [\"HTML\", \"CSS\", \"JavaScript\", \"PHP\", \"SQL\", \"Bootstrap\", \"Tailwind\", \"C#\", \"XAML\", \"VueJS\", \"Git\", \"Linux\", \"Figma\"]}', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `city` varchar(25) NOT NULL,
  `role` enum('student','employer','admin') NOT NULL,
  `birthday` date NOT NULL,
  `about` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `full_name`, `email`, `password`, `city`, `role`, `birthday`, `about`) VALUES
(1, 'GOD', 'god@null.die', '$2y$10$4piuJzc.lQigGuK1tHAlD.WvMIaqvtOODhx4EFd71x0zu7jfDcdZa', 'WORLD', 'admin', '2024-12-13', NULL),
(2, 'Султанов Артём', 'DaNoneNya@test.ru', '$2y$10$kfCMSWwYLdSk1W1oVSVcVuT6A0LYL98oRe7NyoshrFwV2ds9mp39C', 'Челябинск', 'student', '2005-04-14', '🐢👍🏻'),
(3, 'Лакомкин Юсуп', 'd0l.many@test.ru', '$2y$10$lEiyxSllx3R7BZI3RM9ds.IIWuetyhPZ9K03T5e5NZaE43fsyn0sS', 'Челябинск', 'student', '2006-04-14', '🐈'),
(4, 'Фуфелшмерц Пакостин', 'FPC@test.en', '$2y$10$v0EruJ.c32z6JWvw6y.6GOWKCZPyVPpMW4mZpIYCsBsOCCp9NpbNK', 'Триштатье', 'employer', '1987-05-13', 'ТРЕПЕЩИ ПЕРРИ ЧУРКАБЕС, Я ИЗОБРЁЛ ДЕПОРТАТОР');

-- --------------------------------------------------------

--
-- Структура таблицы `vacancy`
--

CREATE TABLE `vacancy` (
  `id` int NOT NULL,
  `employer_id` int NOT NULL,
  `target` varchar(255) NOT NULL,
  `internship_period_start` date NOT NULL,
  `internship_period_end` date NOT NULL,
  `salary` double DEFAULT NULL,
  `requirements` text NOT NULL,
  `responsibilities` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Индексы таблицы `claims`
--
ALTER TABLE `claims`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Индексы таблицы `employer`
--
ALTER TABLE `employer`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name_organization` (`name_organization`),
  ADD KEY `id` (`id`);

--
-- Индексы таблицы `favourites_list`
--
ALTER TABLE `favourites_list`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `owner_id` (`owner_id`);

--
-- Индексы таблицы `invite_list`
--
ALTER TABLE `invite_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `employer_id` (`employer_id`),
  ADD KEY `vacancy_id` (`vacancy_id`);

--
-- Индексы таблицы `response`
--
ALTER TABLE `response`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `employer_id` (`employer_id`),
  ADD KEY `vacancy_id` (`vacancy_id`),
  ADD KEY `resume_id` (`resume_id`);

--
-- Индексы таблицы `resume`
--
ALTER TABLE `resume`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`);

--
-- Индексы таблицы `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Индексы таблицы `vacancy`
--
ALTER TABLE `vacancy`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employer_id` (`employer_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `chat`
--
ALTER TABLE `chat`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `claims`
--
ALTER TABLE `claims`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `favourites_list`
--
ALTER TABLE `favourites_list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `invite_list`
--
ALTER TABLE `invite_list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `response`
--
ALTER TABLE `response`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `resume`
--
ALTER TABLE `resume`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `vacancy`
--
ALTER TABLE `vacancy`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `chat`
--
ALTER TABLE `chat`
  ADD CONSTRAINT `chat_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `user` (`id`);

--
-- Ограничения внешнего ключа таблицы `claims`
--
ALTER TABLE `claims`
  ADD CONSTRAINT `claims_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `employer`
--
ALTER TABLE `employer`
  ADD CONSTRAINT `employer_ibfk_1` FOREIGN KEY (`id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `favourites_list`
--
ALTER TABLE `favourites_list`
  ADD CONSTRAINT `favourites_list_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `invite_list`
--
ALTER TABLE `invite_list`
  ADD CONSTRAINT `invite_list_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invite_list_ibfk_2` FOREIGN KEY (`employer_id`) REFERENCES `employer` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invite_list_ibfk_3` FOREIGN KEY (`vacancy_id`) REFERENCES `vacancy` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `response`
--
ALTER TABLE `response`
  ADD CONSTRAINT `response_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `response_ibfk_2` FOREIGN KEY (`resume_id`) REFERENCES `resume` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `response_ibfk_3` FOREIGN KEY (`employer_id`) REFERENCES `employer` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `response_ibfk_4` FOREIGN KEY (`vacancy_id`) REFERENCES `vacancy` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `resume`
--
ALTER TABLE `resume`
  ADD CONSTRAINT `resume_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`id`) REFERENCES `user` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
