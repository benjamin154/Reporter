<?php
/**
 * Created by PhpStorm.
 * User: maste
 * Date: 28/03/2019
 * Time: 11:02
 */

namespace Cron\DataLink;
use Cron\Config;

class DataLink
{
    private $dns;
    private $user;
    private $password;

    public function getLink(){
        try{
            $PDO = new \PDO(config::$database_dns,Config::$database_username, Config::$database_password);
            $PDO->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }catch (\PDOException $e){
            echo 'Connection failed: ' . $e->getMessage();
        }
        return $PDO;
    }

    public function returnSimpleArrayFromQuery($query){
        $dl=$this->getLink();
        $arreglo=[];
        foreach ($dl->query($query) as $row)$arreglo[]=$row[0];
        return $arreglo;
    }
}