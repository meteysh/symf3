Тестовое задание Mobifitness

Необходимо реализовать проект на symfony 3.* (php 7,2), включающий в себя следующее:

Тестовая сущность - “Тренер”.
У тренера следующие поля - ФИО, телефон, почта, уровень мастерства, дата трудоустройства, дата увольнения,
ставка, настройки графика.
Уровень мастерства может быть “стажер”, “опытный” и “профессионал”.
Ставка может иметь 2 значения - “пол-ставки” и “полная”.
Обязательность полей определить самостоятельно, кроме ставки и уровня - они обязательные.
Условие - у тренера должно быть минимум 1 средство связи.
Настройки графика - json поле с набором параметров -
работает ли тренер по выходным
ведет ли тренер персональные программы
оплата тренера за час (от 500 до 1000 с интервалом 100)
ведет ли тренер групповые программы
за сколько часов следует предупредить тренера о тренировке (от 1 до 24)

Консольный контроллер, который автоматически создаст 3 миллиона записей тестовой сущности (желательно за 1 запуск с
индикацией процесса и эстимейтом оставшегося времени).  10% тренеров вероятностно сделать уволенными. ФИО можно
выбирать рандомно из 10 значений, остальные поля - произвольно в пределах разумного.

Реализовать (только архитектурно) модуль “Задачи тренера”. Это работы, которые может проводить тренер.
Запускаться должен из консоли, например так console:task:run 1 lesson, где 1 - идентификатор тренера, lesson - тип задачи.
Кол-во задач должно быть легко масштабируемо. В качестве результата работы пусть задача делает просто echo в консоль со
своим именем. Т.е. в примере выше мы должны просто увидеть “lesson” и все. Данное задание направлено на знание паттернов
проектирования и ООП.

Составить sql запрос для выборки из БД всех работающих тренеров (не уволенных), которые имеют ставку от 700 рублей и
больше и могут вести персональные тренировки по выходным. Измерить и проанализировать этот запрос. Объяснить почему он
медленно выполняется. Рассказать как его оптимизировать

Готовым будет считаться проект, где выполнены 3 первых пункта.
Для тестирования я ориентируюсь на запуск всего 4-х команд
composer install
php ./bin/console doctrine:migrations:migrate
php ./bin/console console:seed
php ./bin/console console:task:run 1 lesson
выполняю запрос из п.4 (после оптимизации)

Оцениваться будет все (чистота кода, структура проекта, размышления, логика, знание языка), даже если задание
 выполнено не до конца, или выполнено частично.

Просьба измерить время, которое вы потратили на выполнение задания. Можно через toggl (на каждый этап) и затем
предоставить красивый отчет.
Время выполнения практически никак не повлияет на принятие конечного решения.

Если что-то в задании непонятно - вы всегда можете меня спросить об этом лично через
telegram (в рабочее время с ПН по ПТ с 9.00 до 21.00).

У вас есть квота из 3-х подсказок для консультации по выполнению задания.

папка DataFixtures

по пунктам 1 и 2 :
php bin/console make:migration     выполнить миграцию, чтобы у нас появилась таблица в нашей базе!
php bin/console doctrine:migrations:migrate    чтобы запустить миграцию
php bin/console doctrine:fixtures:load   запустить наши фикстуры

папка TaskTrainer

по пункту 3: вместо Lesson может быть Training ( или все что угодно в дальнейшем): php bin/console console:task:run 5 lesson

пункт 4:

Добавляем вирт столбцы:
ALTER TABLE `trainer` ADD COLUMN `salary_virtual` INT GENERATED ALWAYS AS (`schedule` ->> '$.salary') NOT NULL AFTER `schedule`;
ALTER TABLE `trainer` ADD COLUMN `personal_virtual` INT GENERATED ALWAYS AS (IF(`schedule` ->> '$.personal' = 'true', 1 , 0)) NOT NULL AFTER `salary_virtual`; //с условием
ALTER TABLE `trainer` ADD COLUMN `weekend_virtual` INT GENERATED ALWAYS AS (IF(`schedule` ->> '$.weekend' = 'true', 1 , 0)) NOT NULL AFTER `personal_virtual`; //с условием

Добавляем индексы:
CREATE INDEX `salary_idx` ON `trainer`(`salary_virtual`);
CREATE INDEX `personal_idx` ON `trainer`(`personal_virtual`); --индекс можно добавить только числовому полю
CREATE INDEX `weekend_idx` ON `trainer`(`weekend_virtual`);  --индекс можно добавить только числовому полю
CREATE INDEX `date_end_idx` ON `trainer`(`date_end`);

SELECT * FROM `trainer` WHERE AND JSON_CONTAINS(`schedule`, '{\"salary\": >=700}')  AND JSON_CONTAINS(`schedule`, '{\"weekend\":true}') AND JSON_CONTAINS(`schedule`, '{\"personal\":true}') limit 10;


Итоговый запрос: EXPLAIN SELECT * FROM `trainer` WHERE date_end is null
                                                 AND `schedule`->'$.salary[0]' >= 700
                                                 AND `schedule`->'$.personal[0]'=true
                                                 AND `schedule`->'$.weekend[0]'=true ;
450136 rows in set (5.12 sec)

+----+-------------+---------+------------+------+---------------+------+---------+------+---------+----------+-------------+
| id | select_type | table   | partitions | type | possible_keys | key  | key_len | ref  | rows    | filtered | Extra       |
+----+-------------+---------+------------+------+---------------+------+---------+------+---------+----------+-------------+
|  1 | SIMPLE      | trainer | NULL       | ALL  | NULL          | NULL | NULL    | NULL | 2872907 |    10.00 | Using where |
+----+-------------+---------+------------+------+---------------+------+---------+------+---------+----------+-------------+


Новый запрос: EXPLAIN SELECT * FROM `trainer` WHERE date_end is null AND salary_virtual >= 700 AND personal_virtual = 'true'  AND weekend_virtual = 'true';
1799949 rows in set, 2 warnings (8.80 sec)
+----+-------------+---------+------------+------+---------------+------+---------+------+---------+----------+-------------+
| id | select_type | table   | partitions | type | possible_keys | key  | key_len | ref  | rows    | filtered | Extra       |
+----+-------------+---------+------------+------+---------------+------+---------+------+---------+----------+-------------+
|  1 | SIMPLE      | trainer | NULL       | ALL  | NULL          | NULL | NULL    | NULL | 2872907 |     0.03 | Using where |
+----+-------------+---------+------------+------+---------------+------+---------+------+---------+----------+-------------+

Запрос с числовым полем и индексами по виртуальноым столбцам:
EXPLAIN SELECT * FROM `trainer` WHERE date_end is null
                                AND salary_virtual >= 700
                                AND personal_virtual = true
                                AND weekend_virtual = true;
450136 rows in set (36.86 sec)
450136 rows in set (27.97 sec)   ->> с индексами!
+----+-------------+---------+------------+------+--------------------------------------------------+--------------+---------+-------+---------+----------+------------------------------------+
| id | select_type | table   | partitions | type | possible_keys                                    | key          | key_len | ref   | rows    | filtered | Extra                              |
+----+-------------+---------+------------+------+--------------------------------------------------+--------------+---------+-------+---------+----------+------------------------------------+
|  1 | SIMPLE      | trainer | NULL       | ref  | date_end_idx,salary_idx,personal_idx,weekend_idx | date_end_idx | 4       | const | 1435320 |    12.50 | Using index condition; Using where |
+----+-------------+---------+------------+------+--------------------------------------------------+--------------+---------+-------+---------+----------+------------------------------------+


SELECT *
FROM trainer
WHERE date_end BETWEEN '2019-01-01' AND '2019-09-01'
AND salary_virtual >= 700
AND personal_virtual = 1
AND weekend_virtual = 1 LIMIT 1000;
без инд 16,8 sec после ИНД 5,1 sec

SELECT *
FROM trainer
WHERE date_end BETWEEN '2019-01-01' AND '2019-09-01'
AND schedule->>'$.salary' >= 700
AND schedule->>'$.personal' = 'true'
AND schedule->>'$.weekend' = 'true' LIMIT 1000;
без инд 16.77 sec после ИНД  0.17 sec


SELECT *
FROM trainer
WHERE date_end is null
AND schedule->>'$.salary' >= 700
AND schedule->>'$.personal' = 'true'
AND schedule->>'$.weekend' = 'true' ;
ПОЛНЫЙ после ИНД  44.7 sec

SELECT *
FROM trainer
WHERE date_end is null
AND schedule->>'$.salary' >= 700
AND schedule->>'$.personal' = 'true'
AND schedule->>'$.weekend' = 'true' ;
ПОЛНЫЙ после ИНД  55,7 sec

после индексов напрямую быстрее но указаны два индекса в использовании
а по вирт столбцам указаны все 4 индекса но запрос чуть медленне.
Вывод: скорость обработки запроса значительно увеличивается при добавлении индексов, но при LIMIT
