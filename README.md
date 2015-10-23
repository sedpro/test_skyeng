# Тестовое задание для php-разработчика

## Инструкция по развертыванию

```
# Скачать с гитхаба
git clone git@github.com:sedpro/test_skyeng.git

# Перейти в созданную папку
cd test_skyeng

# Установить нужные библиотеки:
php composer.phar install

# Записать в конфиг параметры базы данных(имя пользователя, пароль, имя базы данных)
nano config/autoload/global.php

# Создать нужные таблицы и заполнить их рандомными данными 
# Внимание! Данные генерятся больше двух минут.
php public/index.php install

# Запустить встроенный в php сервер
php -S 127.0.0.1:8080 -t public/

# В браузере открыть страницу 
http://127.0.0.1:8080
```

## Задание
Представьте, что вы работаете компании, которая занимается изучением китайского языка. Клиентов накопилось достаточно, и компания начала разработку системы учёта учеников и учителей.

Вам поручили подготовить небольшой экспериментальный проект, чтобы убедиться, что MySQL способна хранить информацию о сотне тысяч учеников, а с помощью PHP эти данные можно быстро обрабатывать.

В демонстрационном проекте взаимодействуют два объекта: учитель и ученик.

Свойства «Учителя»:
- имя
- пол
- номер телефона

Свойства «Ученика»:
- имя
- email
- день рождения
- текущий уровень знания языка (в соответствии с https://clck.ru/9aWMu)

Вам надо наполнить базу тестовыми данными: 100 000 учеников и 10 000 преподавателей. 

Вам нужно реализовать следующие экраны:
- Добавление нового учителя
- Добавление нового ученика
- Назначение учителю ученика. У учителя/ученика может быть любое количество учеников/учителей.
- Список всех учителей с количеством занимающихся у них учеников.
- Список учителей, с которыми занимаются только ученики, родившиеся в апреле.
- Имена любых двух учителей, у которых максимальное количество общих учеников, и список этих общих учеников.

- Ваш проект должен работать на php 5.4+, mysql 5.6+.
- Можете использовать любой фреймворк, но это не обязательно
- Итоговое приложение должно запускаться на встроенном в php сервере
- Приложите инструкцию для разворачивания приложения и первичного наполнения таблиц данными.
