-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Апр 18 2022 г., 02:45
-- Версия сервера: 10.5.11-MariaDB
-- Версия PHP: 8.0.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `testtask`
--

-- --------------------------------------------------------

--
-- Структура таблицы `available_things`
--

CREATE TABLE `available_things` (
  `id` int(11) NOT NULL,
  `name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `available_things`
--

INSERT INTO `available_things` (`id`, `name`) VALUES
(62, 'Мобильный телефон'),
(64, 'Подарочный сертификат'),
(65, 'Кофеварка'),
(67, 'Билет на концерт'),
(68, 'Пицца');

-- --------------------------------------------------------

--
-- Структура таблицы `bonus_prizes`
--

CREATE TABLE `bonus_prizes` (
  `prize_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `admissed` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `money_prizes`
--

CREATE TABLE `money_prizes` (
  `prize_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `converted` tinyint(1) NOT NULL,
  `transferred` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `money_prizes`
--

INSERT INTO `money_prizes` (`prize_id`, `amount`, `converted`, `transferred`) VALUES
(45, 780, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `prizes_received`
--

CREATE TABLE `prizes_received` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `prizes_received`
--

INSERT INTO `prizes_received` (`id`, `user_id`, `type`) VALUES
(45, 2, 'money');

-- --------------------------------------------------------

--
-- Структура таблицы `settings`
--

CREATE TABLE `settings` (
  `param` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `settings`
--

INSERT INTO `settings` (`param`, `value`) VALUES
('available_money', 2205);

-- --------------------------------------------------------

--
-- Структура таблицы `thing_prizes`
--

CREATE TABLE `thing_prizes` (
  `prize_id` int(11) NOT NULL,
  `name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipped` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bonus_account` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `name`, `login`, `password_hash`, `bonus_account`) VALUES
(1, 'Счастливчик', 'lucky', '$2y$10$BTcyXRNwWg29sdXB8Pwpje1uGTQKg7BWKLVFrCKIfP45DLOKIgtlK', 1992),
(2, 'test', '', '', 0);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `available_things`
--
ALTER TABLE `available_things`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `bonus_prizes`
--
ALTER TABLE `bonus_prizes`
  ADD UNIQUE KEY `prize_id` (`prize_id`);

--
-- Индексы таблицы `money_prizes`
--
ALTER TABLE `money_prizes`
  ADD UNIQUE KEY `prize_id` (`prize_id`);

--
-- Индексы таблицы `prizes_received`
--
ALTER TABLE `prizes_received`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`param`);

--
-- Индексы таблицы `thing_prizes`
--
ALTER TABLE `thing_prizes`
  ADD UNIQUE KEY `prize_id` (`prize_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `available_things`
--
ALTER TABLE `available_things`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT для таблицы `prizes_received`
--
ALTER TABLE `prizes_received`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=331;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `bonus_prizes`
--
ALTER TABLE `bonus_prizes`
  ADD CONSTRAINT `bonus_prizes_ibfk_1` FOREIGN KEY (`prize_id`) REFERENCES `prizes_received` (`id`);

--
-- Ограничения внешнего ключа таблицы `money_prizes`
--
ALTER TABLE `money_prizes`
  ADD CONSTRAINT `money_prizes_ibfk_1` FOREIGN KEY (`prize_id`) REFERENCES `prizes_received` (`id`);

--
-- Ограничения внешнего ключа таблицы `prizes_received`
--
ALTER TABLE `prizes_received`
  ADD CONSTRAINT `prizes_received_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `thing_prizes`
--
ALTER TABLE `thing_prizes`
  ADD CONSTRAINT `thing_prizes_ibfk_1` FOREIGN KEY (`prize_id`) REFERENCES `prizes_received` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
