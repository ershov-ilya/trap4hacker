<?php
header('Content-Type: text/html; charset=utf8');
//header('HTTP/1.0 404 Not Found');
setlocale(LC_ALL, "ru_RU.UTF-8");
session_start();
if(!isset($_SESSION['trap4hacker.attempt_num'])) $_SESSION['trap4hacker.attempt_num']=1;
else $_SESSION['trap4hacker.attempt_num']++;

define('DEBUG', false);

require "getactualcache.php";

function getIPinfo($ip)
{
    $res=$json = file_get_contents('http://api.sypexgeo.net/json/'.$ip);
    return json_decode($res);
}

// Функции
function logWrite($str, $fh)
{
    if(DEBUG) print "$str\n";
    if(!DEBUG) fwrite($fh, "$str\n");
}

// Конфиг
$path	=	'';
$filename = 'access.log';
$template_cache_filename='template.cache';
$template_source_path='http://ershov.pw/ajax/traptemplate';

// Инициализация
$startTime=microtime(true);
if(DEBUG) print "<pre>\n";
if(DEBUG) print "DEBUG mode\n";

$info = getIPinfo($_SERVER['REMOTE_ADDR']);
if(DEBUG) { print_r($info); print "\n";}


// Открытие текстовых файлов
$fhBuf = fopen($path.$filename, "a");

$locked=null;
$attempts = 100;

while(!$locked && $attempts>0)
{
    $locked = flock($fhBuf, LOCK_EX | LOCK_NB);
    if($locked) break;
    usleep(30000);
    $attempts--;
}

if(!$locked) {
    if(DEBUG) echo "Не удалось получить блокировку\n";
    exit(-1);
}
else
{
    if(DEBUG) echo "Блокировка на файл получена\n";
    $output = "-------------------------------------------\n";
    $output .= "REMOTE_ADDR		" . $_SERVER['REMOTE_ADDR'] . "\n";
    $output .= "-------------------------------------------\n";
    $output .= 'Попытка ' . $_SESSION['trap4hacker.attempt_num'] . "		от " . date('j-m-Y H:i:s') . "\n";
    if ($_SERVER['HTTP_X_REAL_IP'] != $_SERVER['REMOTE_ADDR'] && !empty($_SERVER['HTTP_X_REAL_IP']))
        $output .= "HTTP_X_REAL_IP	" . $_SERVER['HTTP_X_REAL_IP'] . "\n";

    $domain = gethostbyaddr($_SERVER['REMOTE_ADDR']);
    if ($domain != $_SERVER['REMOTE_ADDR'])
        $output .= "HOST DNS NAME		" . $domain . "\n";

    $output .= "HTTP_USER_AGENT\t\t" . $_SERVER['HTTP_USER_AGENT'] . "\n";
    $output .= "REQUEST_URI		" . $_SERVER['REQUEST_URI'] . "\n";
    if (!empty($_SERVER['QUERY_STRING']))
        $output .= "QUERY_STRING		" . $_SERVER['QUERY_STRING'] . "\n";
//$output.="REQUEST_TIME		".$_SERVER['REQUEST_TIME']."\n";

    if (!empty($info->city->name_ru)) $output .= "Город\t\t\t" . $info->city->name_ru;
    if (!empty($info->region->name_ru)) $output .= " - регион: " . $info->region->name_ru;
    if (!empty($info->country->name_ru)) $output .= " - " . $info->country->name_ru;
    $output .= "\n";

    if (!empty($info->request)) $output .= "Запрос к  API Sypex\t" . $info->request . "\n";

    logWrite($output, $fhBuf);

//logWrite(microtime(true) - $startTime, $fhBuf);
// Закрываем файл
    fflush($fhBuf) or die($php_errormsg);
    flock($fhBuf, LOCK_UN) or die($php_errormsg);
    fclose($fhBuf) or die($php_errormsg);
}

unset($fhBuf);
// Файл освобождён

if(DEBUG) print "</pre>";

// Template caching system
if(!DEBUG) print getActualCache($template_cache_filename, 86400, $template_source_path);
