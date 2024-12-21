-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Час створення: Гру 21 2024 р., 13:27
-- Версія сервера: 10.4.32-MariaDB
-- Версія PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База даних: `ink_link`
--

-- --------------------------------------------------------

--
-- Структура таблиці `books`
--

CREATE TABLE `books` (
  `book_code` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `genre` varchar(100) NOT NULL,
  `limit_age` int(3) NOT NULL,
  `availibility` tinyint(1) NOT NULL,
  `sub_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп даних таблиці `books`
--

INSERT INTO `books` (`book_code`, `name`, `author`, `genre`, `limit_age`, `availibility`, `sub_id`) VALUES
(1, 'Кобзар', 'Тарас Шевченко', 'Поезія', 12, 1, 8),
(2, 'Вій', 'Микола Гоголь', 'Фантастика', 16, 1, 8),
(3, 'Тіні забутих предків', 'Іван Франко', 'Проза', 14, 1, 12),
(4, 'Сад Гетсиманський', 'Григорій Сковорода', 'Філософія', 18, 0, 15),
(5, 'Лісова пісня', 'Леся Українка', 'Драма', 16, 1, 15),
(6, 'Голодомор', 'Сергій Джердж', 'Історія', 12, 1, 8),
(7, 'Записки українського самашедшого', 'Ліна Костенко', 'Проза', 16, 1, 12),
(8, 'Захар Беркут', 'Іван Франко', 'Історична проза', 14, 1, 15),
(9, 'Чорна рада', 'Пантелеймон Куліш', 'Історична проза', 16, 1, 16),
(10, '1984', 'Джордж Оруелл', 'Антиутопія', 18, 0, 16),
(11, 'Убити пересмішника', 'Харпер Лі', 'Драма', 14, 1, 15),
(12, 'Гаррі Поттер і філософський камінь', 'Джоан Роулінг', 'Фентезі', 10, 1, 8),
(13, 'Мобі Дік', 'Герман Мелвілл', 'Пригоди', 16, 1, 12),
(14, 'Великий Гетсбі', 'Френсіс Скотт Фіцджеральд', 'Роман', 16, 1, 15),
(15, 'Старий і море', 'Ернест Хемінгуей', 'Пригоди', 16, 1, 16);

-- --------------------------------------------------------

--
-- Структура таблиці `issued_books`
--

CREATE TABLE `issued_books` (
  `issue_id` bigint(20) UNSIGNED NOT NULL,
  `librarian_id` bigint(20) UNSIGNED NOT NULL,
  `reader_id` bigint(20) UNSIGNED NOT NULL,
  `book_code` bigint(20) UNSIGNED NOT NULL,
  `date_issue` date NOT NULL,
  `date_return` date NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп даних таблиці `issued_books`
--

INSERT INTO `issued_books` (`issue_id`, `librarian_id`, `reader_id`, `book_code`, `date_issue`, `date_return`, `status`) VALUES
(1, 1, 1, 1, '2024-11-30', '2024-12-30', 0),
(2, 1, 1, 6, '2024-11-01', '2024-11-30', 1),
(3, 2, 4, 8, '2024-07-03', '2024-12-31', 1),
(4, 1, 4, 11, '2024-09-01', '2024-12-31', 1),
(5, 1, 15, 4, '2024-07-04', '2024-12-31', 1),
(6, 2, 8, 10, '2024-01-05', '2024-06-30', 1),
(7, 2, 8, 9, '2024-11-01', '2024-12-31', 1),
(8, 1, 12, 1, '2024-11-01', '2024-11-30', 1),
(9, 1, 15, 1, '2024-11-20', '2024-11-30', 1);

-- --------------------------------------------------------

--
-- Структура таблиці `librarians`
--

CREATE TABLE `librarians` (
  `librarian_id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(264) NOT NULL,
  `libr_name` varchar(100) NOT NULL,
  `password` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп даних таблиці `librarians`
--

INSERT INTO `librarians` (`librarian_id`, `email`, `libr_name`, `password`) VALUES
(1, 'olena.kovalenko@inklink.com', 'Олена Коваленко', 'olenainklinksecureKoval054'),
(2, 'oleks.levchenko@inklink.com', 'Олександр Левченко', 'levchenkoo84Alex');

-- --------------------------------------------------------

--
-- Структура таблиці `readers`
--

CREATE TABLE `readers` (
  `reader_id` bigint(20) UNSIGNED NOT NULL,
  `name_reader` varchar(100) NOT NULL,
  `phone_num` varchar(15) NOT NULL,
  `birth_date` date NOT NULL,
  `otp_code` varchar(6) NOT NULL,
  `sub_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп даних таблиці `readers`
--

INSERT INTO `readers` (`reader_id`, `name_reader`, `phone_num`, `birth_date`, `otp_code`, `sub_id`) VALUES
(1, 'Ілля Ковальчук', '+380501122334', '2012-04-12', '345123', 12),
(2, 'Марина Ткач', '+380672345678', '2013-08-09', '789456', 16),
(3, 'Артем Бондаренко', '+380631234980', '2014-11-23', '963852', 12),
(4, 'Дмитро Лисенко', '+380931234670', '2009-07-14', '852369', 16),
(5, 'Анна Мельник', '+380503456789', '2011-03-05', '159357', 16),
(6, 'Василь Іванчук', '+380501234572', '2015-06-14', '236798', 15),
(7, 'Олена Сидоренко', '+380671234573', '2000-01-05', '543210', 15),
(8, 'Микола Ткаченко', '+380931234574', '1992-04-22', '678543', 15),
(9, 'Наталія Гринь', '+380991234575', '2007-09-09', '789123', 6),
(10, 'Олександр Литвин', '+380631234576', '1982-12-11', '321654', 7),
(11, 'Галина Громова', '+380991111111', '1995-08-13', '111222', 11),
(12, 'Тарас Кравченко', '+380502222222', '1979-05-30', '222333', 8),
(14, 'Максим Савченко', '+380934444444', '2004-12-04', '444555', 15),
(15, 'Людмила Жовтюк', '+380635555556', '1965-03-03', '555666', 16);

-- --------------------------------------------------------

--
-- Структура таблиці `return_books`
--

CREATE TABLE `return_books` (
  `return_id` bigint(20) UNSIGNED NOT NULL,
  `librarian_id` bigint(20) UNSIGNED NOT NULL,
  `reader_id` bigint(20) UNSIGNED NOT NULL,
  `book_code` bigint(20) UNSIGNED NOT NULL,
  `actual_return` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп даних таблиці `return_books`
--

INSERT INTO `return_books` (`return_id`, `librarian_id`, `reader_id`, `book_code`, `actual_return`) VALUES
(1, 2, 8, 10, '2024-06-29'),
(2, 2, 4, 11, '2024-10-20'),
(3, 1, 4, 8, '2024-11-01'),
(4, 2, 8, 10, '2024-06-28');

-- --------------------------------------------------------

--
-- Структура таблиці `subscriptions`
--

CREATE TABLE `subscriptions` (
  `sub_id` bigint(20) UNSIGNED NOT NULL,
  `sub_type` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` tinyint(1) NOT NULL,
  `sub_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп даних таблиці `subscriptions`
--

INSERT INTO `subscriptions` (`sub_id`, `sub_type`, `start_date`, `end_date`, `status`, `sub_price`) VALUES
(1, 'Місячна', '2024-04-01', '2024-04-30', 0, 300.00),
(2, 'Місячна', '2024-05-01', '2024-05-31', 0, 300.00),
(3, 'Місячна', '2024-06-01', '2024-06-30', 0, 300.00),
(4, 'Місячна', '2024-07-01', '2024-07-31', 0, 300.00),
(5, 'Місячна', '2024-08-01', '2024-08-31', 0, 300.00),
(6, 'Місячна', '2024-09-01', '2024-09-30', 0, 300.00),
(7, 'Місячна', '2024-10-01', '2024-10-31', 0, 300.00),
(8, 'Місячна', '2024-11-01', '2024-11-30', 1, 300.00),
(9, 'Тримісячна', '2024-01-01', '2024-03-31', 0, 800.00),
(10, 'Тримісячна', '2024-04-01', '2024-06-30', 0, 800.00),
(11, 'Тримісячна', '2024-07-01', '2024-09-30', 0, 800.00),
(12, 'Тримісячна', '2024-10-01', '2024-12-31', 1, 800.00),
(14, 'Піврічна', '2024-07-07', '2025-01-06', 0, 1500.00),
(15, 'Піврічна', '2024-07-01', '2024-12-31', 1, 1500.00),
(16, 'Річна', '2024-01-01', '2024-12-31', 1, 2900.00);

--
-- Індекси збережених таблиць
--

--
-- Індекси таблиці `books`
--
ALTER TABLE `books`
  ADD UNIQUE KEY `book_code` (`book_code`),
  ADD KEY `sub_id` (`sub_id`);

--
-- Індекси таблиці `issued_books`
--
ALTER TABLE `issued_books`
  ADD UNIQUE KEY `issue_id` (`issue_id`),
  ADD KEY `librarian_id` (`librarian_id`,`reader_id`,`book_code`),
  ADD KEY `reader_id` (`reader_id`),
  ADD KEY `book_code` (`book_code`);

--
-- Індекси таблиці `librarians`
--
ALTER TABLE `librarians`
  ADD UNIQUE KEY `librarian_id` (`librarian_id`);

--
-- Індекси таблиці `readers`
--
ALTER TABLE `readers`
  ADD UNIQUE KEY `reader_id` (`reader_id`),
  ADD KEY `sub_id` (`sub_id`);

--
-- Індекси таблиці `return_books`
--
ALTER TABLE `return_books`
  ADD UNIQUE KEY `return_id` (`return_id`),
  ADD KEY `reader_id` (`reader_id`),
  ADD KEY `librarian_id` (`librarian_id`,`reader_id`,`book_code`),
  ADD KEY `book_code` (`book_code`);

--
-- Індекси таблиці `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD UNIQUE KEY `sub_id` (`sub_id`);

--
-- AUTO_INCREMENT для збережених таблиць
--

--
-- AUTO_INCREMENT для таблиці `books`
--
ALTER TABLE `books`
  MODIFY `book_code` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13436;

--
-- AUTO_INCREMENT для таблиці `issued_books`
--
ALTER TABLE `issued_books`
  MODIFY `issue_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT для таблиці `librarians`
--
ALTER TABLE `librarians`
  MODIFY `librarian_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблиці `readers`
--
ALTER TABLE `readers`
  MODIFY `reader_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT для таблиці `return_books`
--
ALTER TABLE `return_books`
  MODIFY `return_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблиці `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `sub_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Обмеження зовнішнього ключа збережених таблиць
--

--
-- Обмеження зовнішнього ключа таблиці `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`sub_id`) REFERENCES `subscriptions` (`sub_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Обмеження зовнішнього ключа таблиці `issued_books`
--
ALTER TABLE `issued_books`
  ADD CONSTRAINT `issued_books_ibfk_1` FOREIGN KEY (`reader_id`) REFERENCES `readers` (`reader_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `issued_books_ibfk_2` FOREIGN KEY (`book_code`) REFERENCES `books` (`book_code`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `issued_books_ibfk_3` FOREIGN KEY (`librarian_id`) REFERENCES `librarians` (`librarian_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Обмеження зовнішнього ключа таблиці `readers`
--
ALTER TABLE `readers`
  ADD CONSTRAINT `readers_ibfk_1` FOREIGN KEY (`sub_id`) REFERENCES `subscriptions` (`sub_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Обмеження зовнішнього ключа таблиці `return_books`
--
ALTER TABLE `return_books`
  ADD CONSTRAINT `return_books_ibfk_1` FOREIGN KEY (`reader_id`) REFERENCES `readers` (`reader_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `return_books_ibfk_2` FOREIGN KEY (`librarian_id`) REFERENCES `librarians` (`librarian_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `return_books_ibfk_3` FOREIGN KEY (`book_code`) REFERENCES `books` (`book_code`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
