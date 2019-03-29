<?php
/**
 * Created by PhpStorm.
 * User: maste
 * Date: 28/03/2019
 * Time: 10:12
 */

namespace Cron\Reports\ReportTypes;
use \Cron\Reports\Report;

class TXTReport extends Report
{
    /**
     * Recibe el arreglo del resultado que le envio la base de datos y crea el documento.
     * Esto es abstracto porque cada reporte debe tener su propio algoritmo.
     * @param array $resultado
     * @return mixed
     */

    protected function createDocument(array $resultado){
        if (empty($resultado)){
            $this->createFile($this->createEmptyErrorText());
            return true;
        }
        $str="";
        foreach ($resultado as $row){
            foreach ($row as $key => $field){
                if (strpos($key, 'report-settings') === false){
                    $str.=$field."|";
                }
            }
            $str=substr($str,0,-1);
            $str.="\n";
        }
        $this->createFile($str);
    }

    protected function getFileExtension(){
        return ".txt";
    }
}