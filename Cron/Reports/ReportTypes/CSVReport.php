<?php
/**
 * Created by PhpStorm.
 * User: maste
 * Date: 28/03/2019
 * Time: 8:34
 */

namespace Cron\Reports\ReportTypes;
use \Cron\Reports\Report;

class CSVReport extends Report
{
    /**
     * Recibe el arreglo del resultado que le envio la base de datos y crea el documento.
     * Esto es abstracto porque cada reporte debe tener su propio algoritmo.
     * @param array $resultado
     * @return mixed
     */

    protected function createDocument(array $resultado){
        if(empty($resultado)){
            $this->createFile($this->createEmptyErrorText());
            return true;
        }
        $str="";
        foreach ($resultado as $row){
            foreach ($row as $key => $field){
                if (strrpos($key, 'row-settings') === false){
                    $str.= str_replace(",",';',$field).",";
                }
            }
            $str=substr($str,0,-1);
            $str.="\n";
        }
        $this->ruta=$this->getRepositoryLink();
        $this->createFile($this->ruta,$str);
    }

    protected function createEmptyErrorText(){
        return "El reporte no generó datos\n";
    }
    /**
     * Devuelve la extensión que debe llevar el archivo
     * @return String
     */
    protected function getFileExtension(){
        return ".csv";
    }
}