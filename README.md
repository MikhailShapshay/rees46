<h1>Парсер курса валют</h1>

Запустите docker-compose:<br>
<b>docker-compose build<br>
docker-compose up -d</b>

Войдите в приложение:<br>
<b>docker exec -it rees46_app bash</b>

Установите зависимости:<br>
<b>composer install</b>

Откройте:
<br>
<b>http://localhost/</b>

Файл настройки доступа к БД:<br>
<b>config/db.xml</b>

Для первоначального запуска:<br>
<b>php month_parser.php start</b><br>
Эта операция удалит таблицу курсов если она есть и создаст снова. Так же она запустит воркеры на заполнение данных за прошедший месяц.

<b>ВНИМАНИЕ!!!</b><br>
Docker-compose уже содержит все необходимые настройки и базу !

Для ежедневного обновления пропишите в крон запуск файла <b>day_parser.php</b>