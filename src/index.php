<?php
// Laboratorium 13 - strona startowa PHP obslugiwana przez Nginx + PHP-FPM
// Autor: Bartosz Owczarczyk
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Laboratorium 13 - Stack LEMP</title>
</head>
<body>
    <h1>Laboratorium nr 13 — Stack LEMP + phpMyAdmin</h1>
    <p>Autor: Bartosz Owczarczyk</p>
    <p>Przedmiot: Programowanie Aplikacji w Chmurze Obliczeniowej</p>
    <p>Ta strona jest plikiem <strong>index.php</strong> serwowanym przez Nginx,
       a interpretowanym przez kontener PHP-FPM przez sieć backend.</p>
    <hr>
    <h2>Informacje o środowisku PHP (phpinfo):</h2>
<?php
// Wywolanie phpinfo() dowodzi, ze PHP jest poprawnie wykonywane przez PHP-FPM
phpinfo();
?>
</body>
</html>
