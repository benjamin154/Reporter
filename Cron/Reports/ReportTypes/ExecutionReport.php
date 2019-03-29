<?php
/**
 * Created by PhpStorm.
 * User: maste
 * Date: 28/03/2019
 * Time: 10:05
 */

namespace Cron\Reports\ReportTypes;
use \Cron\Reports\Report;

class ExecutionReport extends Report
{
    public function send(){
        return true;
    }

    /**
     * Recibe el arreglo del resultado que le envio la base de datos y crea el documento.
     * Esto es abstracto porque cada reporte debe tener su propio algoritmo.
     * @param array $resultado
     * @return mixed
     */

    protected function createDocument(array $resultado){
        escupir($resultado, "Resultado del Reporte Execution");
        // TODO: implement createDocument() method

    }

    /**
     * Devuelve la extensión que debe llevar el archivo
     * @return String
     */

    protected function getFileExtension(){
        return null;
    }

    protected function createEmptyErrorText(){
        return "Reporte si datos";
    }
}