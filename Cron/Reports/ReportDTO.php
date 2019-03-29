<?php


namespace Cron\Reports\ReportTypes;


class ReportDTO
{
    var $id;
    var $query;
    var $created;
    var $last_modified;
    var $nombre;
    var $ruta_repositorio;
    var $area;
    var $integacion_calidad;
    var $f_ini_vigencia;
    var $f_fin_vigencia;
    var $status;
    var $tipo;
    var $Responsable;
    var $id_periodicidad;
    var $Fecha_Solicitud;
    var $fecha_Entrada;
    var $Solicitante;
    var $Area;
    var $Impacto_Operativo;
    var $Impacto_Decisiones;
    var $Reutilizacion_Duplicidad;
    var $Personas_Reporte;
    var $Horas;
    var $Fecha_Inicio;
    var $Horas_Usuario;
    var $Clasificacion;
    var $Horas_Ahorradas;
    var $integracion_calidad;
    var $documento;
    var $estado;
    var $SSRS;
    var $SSRS_URL;
    var $SSRS_IMG;

    private $destinatarios;

    public function __construct($id)
    {
        $this->id=$id;
    }

    public function randomize(){
        $this->tipos=array("ExcelReport","ExecutionReport","CSVReport","TXTReport","EmailHTMLReport");
        $this->tipos=array("Contabilidad","Cuentas Corporativas","Reservaciones","Finanzas");

        $this->query="SELECT * FROM Tabla WHERE id=".$this->id;
        $this->created=$this->rand_date('2014-01-01 00:00:00',date('Y-m-d H:i:s'));
        $this->last_modified=$this->rand_date($this->created,date('Y-m-d H:i:s'));
        $this->nombre = "Reporte ".$id;
        $this->ruta_repositorio="/var/www/vhosts/nationalcar.com.mx/subdomains/larus/httpdocs".
            "/download_folder/reportes/".$this->nombre."/";
        $this->area=$this->areas[rand(0,3)];
        $this->integacion_calidad=$this->rand_date($this->created,date('Y-m-d H:i:s'));
        $this->f_ini_vigencia=$this->rand_date($this->created,$this->integacion_calidad);
        $this->f_fin_vigencia=$this->rand_date($this->f_fin_vigencia, '2014-12-31 00:00:00');
        $this->status=rand(0,1);
        $this->tipo=$this->tipos[rand(0,count($this->tipos)-1)];
    }

    public function hydrate(array $array){
        foreach ($array as $key=>$value){
            if (property_exists(get_class($this),$key)){
                $this->$key=$value;
            } else {
                echo "La propiedad ".$key." No existe <br />";
            }
        }
    }

    function rand_date($min_date, $max_date){
        $min_epoch = strtotime($min_date);
        $max_epoch = strtotime($max_date);
        $rand_epoch = rand($min_epoch, $max_epoch);
        return date('Y-m-d H:i:s', $rand_epoch);
    }

    public function setDestinataries(array $destinatarios){
        $this->destinatarios=$destinatarios;
    }

    public function getDestinataries(){
        return $this->destinatarios;
    }
}