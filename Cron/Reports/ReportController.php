<?php
namespace Cron\Reports;

use Cron\CommandControllerInterface;
use Cron\DataLink\DataLink;
use Cron\Reports\ReportTypes\ReportDTO;

/**
 * Class ReportController
 *Controlador de la clase reportes, es quien obtiene la lista de reportes y lo pasa al Factory
 * @package Cron\Reports
 */
class ReportController implements CommandControllerInterface
{
    private $reportsCollection=array();
    private $reportsFactory;
    private $timeQuery=null;

    public function __construct()
    {
        $this->reportsFactory=new ReportFactory();
    }

    public function createTimeQuery($periodicidad){
        if (empty($this->timeQuery)){
            $sql="SELECT r.*,p.id_periodicidad FROM reportes_Reporte r ".
                "JOIN reportes_periodicidad p ON p.id_reporte.id ".
                "WHERE r.status>0".
                " AND p.id_periodicidad IN (";
            foreach ($periodicidad as $periodo){
                $sql.="$periodo,";
            }
            $sql=substr($sql,0,-1).")";
        }
        $this->timeQuery=$sql;
        return $this->timeQuery;
    }

    public function getCommands()
    {
        // TODO: Implement getCommands() method.
        if(empty($this->timeQuery)) throw new \Exception("Periodicity not set");
        $dataLink=new DataLink(); $dataLink=$dataLink->getLink();

        foreach ($dataLink->query($this->timeQuery, \PDO::FETCH_ASSOC) as $row){
            $reporte=new ReportDTO($row['id']);
            $reporte->hydrate($row);
            $this->reportsCollection[]=$reporte;
        }
        $this->reportsCollection=$this->reportsFactory->createReportFromBatch($this->reportsCollection);
        return $this->reportsCollection;
    }

    public function establishPeriodicity(array $periodicidad)
    {
        // TODO: Implement establishPeriodicity() method.
        return $this->createTimeQuery($periodicidad);
    }
}