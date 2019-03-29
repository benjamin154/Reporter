<?php


namespace Cron;

use Cron\DataLink\DataLink;

class CronExecutionLog
{
    var $idExecution;
    var $message;
    var $executionStart;
    var $executionDuration;
    var $id_periodicidad;

    public function __construct($message,\DateTime $executionStart, $executionDuration, $periodicidad)
    {
        $this->message=$message;
        $this->executionStart=$executionStart;
        $this->executionDuration=$executionDuration*10000000000000000;
        $this->id_periodicidad=$periodicidad;
    }

    public function save(){
        $dl=new DataLink();
        $dl=$dl->getLink();
        $sql="INSERT INTO cron_Ejecuciones (message,executionStart,executionDuration,id_periodicidad)";
        $sql.="values('".$this->message."','".
            $this->executionStart->format('Y-m-d H:i:s')."','".
            floor($this->executionDuration)."','".
            $this->id_periodicidad.
            ")";
        try{
            if(!$dl->query($sql)) throw new \Exception("No se pudo gurdar el log");
        }catch (\Exception $e){
            escupir($e->getMessage(),"Error al guardar el log");
        }
    }
}