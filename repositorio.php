<?php
/**
 * Created by PhpStorm.
 * User: maste
 * Date: 27/03/2019
 * Time: 17:31
 */

ini_set('display_errors',E_ALL);
function escupir($obj,$ttl=null){
    echo (($ttl!=null)?"<h4>".$ttl."</h4>":"")."<pre>".print_r($obj,true)."</pre>";
}

require_once ('autoload.php');
$myLibLoader = new SpLClassLoader('Vendor/');
$myLibLoader->register();

$repository = new Repositorio\Repo();
try {
    $repository->getFile($_GET['key']);
}catch (exception $e){
    escupir($e->getMessage());
}