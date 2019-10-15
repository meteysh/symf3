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