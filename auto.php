<?php
/**
 * Created by PhpStorm.
 * User: maste
 * Date: 27/03/2019
 * Time: 17:42
 */

function autoload($className)
{
    $className = Ltrim($className, '\\');
    $fileName  = '';
    $namespace = '';
    if ($lastNsPos = strrpos($className, '\\')){
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace('_', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName  = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    require "vendor/Neofrek/Cron/src/".$fileName;
}
spl_autoload_register('autoload');
