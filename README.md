Trap for hacker
========

A simple script to record all attempts to enter the website admin panel. Records time, the number of attempts, the address where the knocking, IP address, HTTP_USER_AGENT string and an attempt is made to obtain the DNS host name by IP, if it is possible - that is also recorded. Also recorded the parameter string GET request, so we can write all kinds of attempts to injection.

With .htaccess you can emulate the way to the various CMS.

Sample .htaccess file is given in ht.access. This piece of code should be included at the beginning of .htaccess, immediately after RewriteEngine On RewriteBase /

Setting

Trap4hacker throw folder in the root of the site (although if you feel like it and you can put a little deeper, but then you have to observe all the way) include a piece of code from ht.access in your .htaccess, front part of the Friendly URLs - EVERYTHING.

Journal of intrusion attempts will be conducted in the file log.txt.

По-русски
------
Автор: Илья Ершов http://ershov.pw

Простой скрипт для записи всех попыток входа в админ панель сайта.
Записывается время, количество попыток, адрес куда стучались, IP адрес, строка HTTP_USER_AGENT и выполняется попытка получить DNS имя хоста по IP, если удаётся - то тоже записывается.
Также записывается строка параметров GET запроса, таким образом можно записать всевозможные попытки инъекций.

####С помощью .htaccess можно эмулировать пути к различным CMS.
Образец .htaccess приведён в файле ht.access. Этот кусок кода нужно включать в начале .htaccess, сразу после
RewriteEngine On
RewriteBase /

#### Установка
Папку trap4hacker кидаете в корень сайта (хотя если не лень можно положить и поглубже, но тогда надо соблюсти все пути)
Включаете кусок кода из ht.access в свой .htaccess, перед частью Friendly URLs - ВСЁ.

Журнал попыток проникновения будет вестись в файле log.txt.
