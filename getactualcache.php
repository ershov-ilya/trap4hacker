<?php
function getActualCache($cacheFile,$cacheAgeLimit,$source)
{
    $fMakeTemplateCache=false;
    if(is_file($cacheFile))
    {
        $filetime= filemtime($cacheFile);
        $curtime=time();
        $cacheFilenameAge=$curtime-$filetime;
        //print $cacheFilenameAge;
        if($cacheFilenameAge>$cacheAgeLimit) $fMakeTemplateCache=true;
    }
    else $fMakeTemplateCache=true;

    if($fMakeTemplateCache)
    {
        //print "Write new cache";
        // Write new cache
        $cacheContent=file_get_contents($source);
        // Открытие текстовых файлов
        $fhCache = fopen($cacheFile, "w");
        $locked = flock($fhCache, LOCK_EX | LOCK_NB);
        if(!$locked) {
            echo 'Не удалось получить блокировку';
            exit(-1);
        }
    fwrite($fhCache, $cacheContent);
    }
    else
    {
        //print "Read from cache";
        // Read from cache
        $cacheContent=file_get_contents($cacheFile);
    }
    // Output template
    return $cacheContent;
}