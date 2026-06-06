# Laboratorium nr 14 — Stack LEMP: plik bazowy + override
Autor: Bartosz Owczarczyk
Przedmiot: Programowanie Aplikacji w Chmurze Obliczeniowej
Środowisko: Windows + Docker Desktop


# 1. Cel zadania

Podział pliku `docker-compose.yml` z rozwiązania Lab 13D (LEMP + secrets) na plik
bazowy i plik override, z wykorzystaniem mechanizmu merge Docker Compose.


# 2. Idea mechanizmu merge

Konfigurację dzieli się na:

- plik bazowy `docker-compose.yml` — składniki wspólne dla wszystkich środowisk,
- plik override `docker-compose.override.yml` — ustawienia specyficzne dla środowiska.

Przy standardowej konwencji nazw (`docker-compose.yml` + `docker-compose.override.yml`)
Compose scala oba pliki automatycznie, bez potrzeby opcji `-f`.

| Element                                  | Plik bazowy | Override |
|------------------------------------------|:-----------:|:--------:|
| usługi, obrazy, sieci, wolumeny, secrets |      ✔      |          |
| zmienne środowiskowe, montowania         |      ✔      |          |
| publikowane porty (4001, 6001)           |             |    ✔     |


# 3. Struktura projektu

lab14/
├── docker-compose.yml
├── docker-compose.override.yml
├── secrets/
│   ├── db_root_password.txt
│   └── db_password.txt
├── nginx/
│   └── default.conf
├── src/
│   └── index.php
└── README.md


# 4. Polecenia i wyniki


# 4.1. Dowód scalenia: podgląd wynikowego pliku Compose

docker compose config


Fragment wyniku (porty pochodzą z pliku override):

services:
  nginx:
    image: nginx:1.27-alpine
    container_name: lab14_nginx
    ports:
      - "4001:80"
    ...
  phpmyadmin:
    image: phpmyadmin:5.2
    ports:
      - "6001:80"
    ...


# 4.2. Uruchomienie (automatyczny merge, bez opcji -f)

docker compose up -d


Wynik:

[+] Running 7/7
 ✔ Network lab14_frontend     Created
 ✔ Network lab14_backend      Created
 ✔ Volume lab14_dbdata        Created
 ✔ Container lab14_mysql      Started
 ✔ Container lab14_php        Started
 ✔ Container lab14_phpmyadmin Started
 ✔ Container lab14_nginx      Started


# 4.3. Dowód, że override zadziałał — porty są opublikowane

docker compose ps


Wynik:

NAME               IMAGE                SERVICE      STATUS         PORTS
lab14_mysql        mysql:8.4            mysql        Up 20 seconds  3306/tcp, 33060/tcp
lab14_nginx        nginx:1.27-alpine    nginx        Up 18 seconds  0.0.0.0:4001->80/tcp
lab14_php          php:8.3-fpm-alpine   php          Up 19 seconds  9000/tcp
lab14_phpmyadmin   phpmyadmin:5.2       phpmyadmin   Up 18 seconds  0.0.0.0:6001->80/tcp

Gdyby uruchomić sam plik bazowy (docker compose -f docker-compose.yml up -d),
kontenery nie miałyby opublikowanych portów — to potwierdza, że porty pochodzą
wyłącznie z pliku override.


# 4.4. Stack LEMP działa i wyświetla stronę PHP

curl.exe http://localhost:4001


Fragment wyniku:

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Laboratorium 13 - Stack LEMP</title>
</head>
<body>
    <h1>Laboratorium nr 13 - Stack LEMP + phpMyAdmin</h1>
    <p>Autor: Bartosz Owczarczyk</p>
    ...


# 4.5. Inicjacja testowej bazy danych

Logowanie do phpMyAdmin (http://localhost:6001): root / secret_root_pw

docker exec -it lab14_mysql mysql -uroot -psecret_root_pw -e "CREATE DATABASE test_lab14; SHOW DATABASES;"


Wynik:

+--------------------+
| Database           |
+--------------------+
| appdb              |
| information_schema |
| mysql              |
| performance_schema |
| sys                |
| test_lab14         |
+--------------------+


# 5. Zatrzymanie i sprzątanie

docker compose down -v


# 6. Podsumowanie

- Konfigurację podzielono na plik bazowy `docker-compose.yml` (wspólny) oraz
  `docker-compose.override.yml` (porty - element specyficzny dla środowiska).
- Compose scala oba pliki automatycznie przy `docker compose up`, bez opcji `-f`.
- Scalenie potwierdzono poleceniem `docker compose config` (4.1) oraz obecnością
  portów 4001/6001 w `docker compose ps` (4.3).
- Po scaleniu stack LEMP działa i pozwala zainicjować testową bazę danych.