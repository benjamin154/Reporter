<?php


namespace Cron;


class Config
{
    //DataLink
    public static $database_dsn='mysql:dbname=reportes;host=0ee175e80ef4f5483c785dc86526d7928aae35e2.rackspaceclouddb.com;charset=utf8';
    public static $database_username='global';
    public static $database_password='8Eks3UlKU62yxRR';

    //Repository
    public static $downloads_root="/var/www/vhosts/nationalcar.com.mx/subdomains/larus/httpdocs/reporteador/public/reportes/"; //url relativa
    public static $downloads_repository="https://larus.nationalcar.com.mx/reporteador/public/reportes/ReporteExecution/"; //url absoluta


}

