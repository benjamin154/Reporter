<?php
/**
 * Created by PhpStorm.
 * User: maste
 * Date: 27/03/2019
 * Time: 17:24
 */
ini_set('display_errors',E_ALL);
date_default_timezone_set('America/Mazatlan');

function escupir($obj,$ttl=false){
    echo ($ttl?"<h4>".$ttl."</h4>":"")."<pre>".print_r($obj,true)."</pre>";
}

require_once ('autoload.php');
require_once ("class.phpmailer.php");

$myLibloader = new SplClassLoader('Neofrek','vendor/');
$myLibloader->register();

//Create the cron Controller
$cron = new Neofrek\Cron\CronController();

//Pass a new program to execute
$cron->addCommandController(new \Neofrek\Cron\Reports\ReportController());

//Run the program
$cron->execute();


