<?php
namespace Cron\Reports;

use Cron\Reports\ReportTypes;

class ReportFactory
{
    /**
     * @var Report $createdReports
     * ®Ultimo reporte creado.
     */
    var $createdReports = [];

    /**
     * Crea un reporte en base al DTO Obtenido de la base de datos y lo agrega a la colección.
     * @param \Cron\Reports\ReportDTO $report
     * @throws \Exception
     */
    public function createReport(ReportDTO $report){
        $clase ="Cron\\Report\\ReportTypes\\".$report->tipo;
        if(class_exists($clase)){
            $this->createdReports[]=new $clase($report);
        }
        else
            throw new \Exception("Este tipo de reporte no existe,".
                " favor de crear la clase <b><b>".$clase."</b></i><br />");
    }

    /**
     * Crea una colección de reportes a partir de un arreglo de \Cron]Reports\ReportDTO
     * @param \Cron\Reports\array $array
     * @return \Cron\Reports\Report
     */
    public function createReportFromBatch(array $array){
        foreach ($array as $registro){
            try{
                $this->createReport($registro);
            }catch (\Exception $e){
                echo "<b>Error al importar reporte: </b>";
                echo $e->getMessage();
            }
        }
        return $this->getReports();
    }

    /**
     * Regresa una lista de reportes
     * @return \Cron\Reports\Report
     */
    public function getReports(){
        return $this->createdReports;
    }

}