<?php


namespace Cron;

use Cron\DataLink\DataLink;

class CronController
{
    private $CommandControllerCollection=[];
    private $CommandCollection=[];
    private $periodicidades=[];

    public function addCommandController(CommandControllerInterface $controller){
        $this->CommandControllerCollection[]=$controller;
    }

    public function readActivePeriodicity(){
        if (!empty($this->periodicidades)) return $this->periodicidades;

        $sql="SELECT id_periodicidad FROM cron_Diario WHERE HOUR(hora)=HOUR(CURRENT_TIME())
              AND laborar_natural=IF(DAYOFWEEK(NOW())>0, 'laboral', 'natural')
              
              UNION
              
              SELECT id_periodicidad FROM cron_Semanal WHERE HOUR(hora)=HOUR(CURRENT_TIME())
              AND dia LIKE CONCAT(\"%\",DAYOFWEEEK(NOW()),\"%\")
              
              UNION
              
              SELECT id_peiodicidad FROM cron_Mensual WHERE HOUR(hora)=HOUR(CURRENT_TIME())
              AND ((num_dia_semana=0 AND num_dia=DAYOFMONTH(NOW())) OR (num_dia_semana=1 AND (
              (dia_semana=DAYOFWEEK(NOW()) AND num_dia=FLOOR((DayOfMonth(NOW())-1)/7)+1)
              OR ((num_dia=5) AND (DATE_FORMAT(LAST_DAY(NOW()), '$%D')-DAYOFMONTH(NOW())<7)))))";

        $this->periodicidades=new DataLink();
        $this->periodicidades=$this->periodicidades->returnSimpleArrayFromQuery($sql);
        if (empty($this->periodicidades)) throw new \Exception("No hay nada que hacer");
        return $this->periodicidades;
    }

    private function passPeriodicityToCommandControllers(array $per){
        foreach ($this->CommandControllerCollection as $controller){
            if (!is_a($controller, "Cron\\CommandControllerInterface")){
                throw new \Exception("El objeto no es un Command Controller");
            } else
                $controller->establishPeriodicity($per);
        }
        return $this->CommandControllerCollection;
    }

    public function addCommands(array $commands){
        foreach ($commands as $command){
            if (is_a($command,'Cron\CronInterface')){
                $this->CommandCollection[]=$command;
            } else {
                throw new \Exception("Error: El objeto pasado no es un CronInterface");

            }
        }
    }

    public function addCommand(CronInterface $command){
        $this->CommandCollection[]=$command;
    }

    public function execute(){
        try{
            $cc=$this->passPeriodicityToCommandControllers($this->readActivePeriodicity());
            foreach ($cc as $CommandController){
                $this->addCommands($CommandController->getCommands());
            }
        } catch (\Exception $e){
            die($e->getMessage());
        }

        foreach ($this->CommandCollection as $command){
            try{
                $command->execute();
            }catch (\Exception $e){
                escupir($e->getMessage(),"Error al ejecutar el comando");
                escupir($command,"comando");
                escupir($e,"Error");
            }
        }
    }
}