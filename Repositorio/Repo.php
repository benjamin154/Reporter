<?php

namespace Repositorio;

use Cron\DataLink\DataLink;

class Repo
{
    var $last_hash;
    private $result=[];

    /**
     * Método para agregar a la tabla repositorio_file el reporte generado y posteriormente enviar al
     * mail que se introduzca, para que la conteste el destinatario.
     */
    public function add_repositorio_file($url_absoluta){
        $hash=$this->getFileHash($url_absoluta);
        $dl=new DataLink();
        $dl=$dl->getLink();
        $sql="INSERT INTO repositorio_file (hash,ruta) ";
        $sql.="values('".$hash."','".$url_absoluta."')";
        try{
            if (!$dl->query($sql)) throw new \Exception("no se pudo guardar el reporte");
        }catch (\Exception $e){
            escupir($e->getMessage(),"Error al guardar el reporte");
        }
        $this->last_hash=$hash;
        return $hash;
    }

    public function getLastHash(){
        return $this->last_hash;
    }

    public function getFileHash($url_absoluta){
        if (!file_exists($url_absoluta)){
            throw new \Exception("El archivo a hashear no existe");
        }
        $hash=hash('ripemd160',$url_absoluta);
        return $hash;
    }

    //TODO USAR DESPUES
    public function getDownloadKeyFromHash($hashArchivo,$email){
        $hash=md5($hashArchivo.$email);
        return $hash;
    }

    public function getDownloadKeyFromURL($url_absoluta,$email){
        $hash=md5($this->getFileHash($url_absoluta).$email);
        return $hash;
    }

    /**
     * Método para agregar a la tabla de repositorio_link la info del reporte generado
     */
    public function add_repositorio_link($hash, $correo_dest){
        $insert=new  DataLink();
        $insert=$insert->getLink();
        $download_key=$this->getDownloadKeyFromHash($hash,$correo_dest);
        $sql="INSERT INTO repositorio_link (hash,correo_destinatario,download_key) ";
        $sql.="values('".$hash."','".$correo_dest."','".$download_key."')";
        try{
            if ($insert->query($sql)) throw new \Exception("No se pudo guardar el link del reporte");
            return $download_key;
        }catch (\Exception $e){
            escupir($e->getMessage(),"Error al guardar el link del reporte");
        }
    }

    /**
     * Método que agrega el tiempo de descarga del reporte generado
     */
    public function add_time_repository_download($download_key){
        date_default_timezone_set('America/Mazatlan');
        $tiempo=date("Y-m-d H:i:s");
        $insert=new DataLink();
        $insert=$insert->getLink();
        $sql="UPDATE repositorio_link SET fecha_descarga='".$tiempo."' WHERE download_key='".$download_key."' AND fecha_descarga IS NULL";
        try{
            if (!$insert->query($sql)) throw new \Exception("Este reporte ya ha sido descargado");
        }catch (\Exception $e){
            escupir($e->getMessage(),"Error al guardar la fecha de descarga del reporte");
        }
    }

    /**
     * Metodo para descargar el reporte y agregar a la tabla repositorio_link el hash del archivo, correo destinatario, downloadkey y la fecha de la descarga
     */
    public function getFile($key){
        if (!empty($this->result)) return $this->result;

        $sql="SELECT r.ruta FROM `reportes`.repositorio_file r LEFT JOIN `reportes`.repositorio_link l ON r.hash WHERE l.donwload_key = '".$key."' ";

        $this->result=new DataLink();
        $this->result=$this->result->returnSimpleArrayFromQuery($sql);

        if (empty($this->result)) throw new \Exception("No hay reporte generado con esa clave");
        //inserta un espacio en blanco en el reporte
        error_reporting(0);
        array_splice($this->result[0],0,1);
        ini_set('display_errors',E_ALL);

        $name = $this->result[0];

        //genera el nombre corto del archivo
        $reultado = substr($name,89);

        header("Content-Disposition: inline; filename=".$reultado."");
        header("Content-type: application/octet-stream");
        readfile($name);

        $this->add_time_repository_download($key);
    }
}