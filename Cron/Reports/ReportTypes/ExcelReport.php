<?php
/**
 * Created by PhpStorm.
 * User: maste
 * Date: 28/03/2019
 * Time: 9:44
 */

namespace Cron\Reports\ReportTypes;
use \Cron\Reports\Report;

class ExcelReport extends Report
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

        $str="<table>\n\t<tr>";
        foreach ($resultado[0] as $key=>$value){
            if (strpos($key,'row-settings') === false){
                $str.="\n\t\t<td><strong>".$key."</strong></td>";
            }
        }
        $str.="\n\t</tr>";
        foreach ($resultado as $key => $row){
            $str.="\n\t<tr>";
            foreach ($row as $field){
                if (strpos($key,'row-settings') === false){
                    $str.="\n\t\t<td>".$field."</td>";
                }
            }
            $str.="\n\t</tr>";
        }
        $str.="\n</table>";
        $this->createFile($str);
    }

    protected function getFileExtension(){
        return ".xls";
    }

    protected function createEmptyErrorText(){
        return "<table><tr><td>El reporte no generó datos.</td></tr></table>";
    }
}