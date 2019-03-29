<?php
/**
 * Created by PhpStorm.
 * User: maste
 * Date: 28/03/2019
 * Time: 10:23
 */

namespace Cron\Reports;

use Cron\Config;
use Cron\CronExecutionLog;
use Cron\DataLink\DataLink;

use Repositorio\Repo;

abstract class Report implements ReportCommandInterface {
    protected  $DTO;
    protected $resultados;
    protected $executionStart;
    protected $executionDuration;
    protected $idPeriodicidad;
    protected $ruta;
    protected $repo;
    protected $myfile;

    /**
     * Construye el reporte en base al DTO que se obtuvo de la base de datos.
     * @param \Neofrek\Cron\Reports\ReportDTO $report
     */

    public function __construct(ReportDTO $report)
    {
        $this->DTO=$report;
        $this->repo = new Repo();
    }

    /**
     * Ejecuta el reporte desde el patron comando
     */
    public function execute(){
        $this->executionStart=microtime(true);
        try{
            $this->createDocument($this->getResult());
            $this->send(); //gman
            $message="Programa Ejecutado con éxito";
        }catch (\Exception $e){
            $message="";
            $message.=$e->getMessage();
        }
        $this->executionDuration=microtime(true)-$this->executionStart;
        $dt=new \DateTime(date("Y-m-d H:i:s",$this->executionStart));
        $this->idPeriodicidad=$this->DTO->id_periodicidad;
        $executionLog=new  CronExecutionLog(
            $message
            ,$dt
            ,$this->executionDuration
            ,$this->idPeriodicidad
        );
        $this->saveExecutionLog($executionLog);
    }

    /**
     * Guarda el log de ejecución de acuerdo a los requisitos de la interfaz
     * de la librería del cron
     * @param \Neofrek\Cron\CronExecutionLog $el
     */
    public function saveExecutionLog(CronExecutionLog $el){
        $el->save();
    }

    /**
     * Obtiene la query del DTO
     * @return mixed
     */
    public function getQuery()
    {
        // TODO: Implement getQuery() method.
        return $this->DTO->query;
    }

    /**
     * Obtiene los resultados de ejecutar el query, asumimos que se va a regresar un resultado por
     * default.
     * @return array
     */
    private function getResult(){
        try{
            //TODO Qué pasa cuando no hay resultados?
            $link=new DataLink();
            $link=$link->getLink();
            foreach ($link->query($this->getQuery(), \PDO::FETCH_ASSOC) as $resultado){
                $this->resultados[]=$resultado;

            }
            if (!empty($this->resultados)){

                return $this->resultados;
            } else {
                $resultado=array();
                return $resultado;
            }

        }catch (\Exception $e){
            die($e->getMessage());

        }
    }

    /**
     * Se ejecuta en vez de create document cuando el arreglo de datos viene vacío.
     * @return string
     */
    protected abstract function createEmptyErrorText();

    /**
     * Obtiene la lista de destinatarios a la que hay que enviar el reporte;
     */
    public function getDestinataries()
    {
        // TODO: Implement getDestinataries() method.
        $DataLink=new DataLink();
        $DataLink=$DataLink->getLink();
        $sql="SELECT * FROM reportes_Destinatarios WHERE id_reporte =".$this->DTO->id;
        $resultado=[];
        foreach ($DataLink->query($sql, \PDO::FETCH_ASSOC) as $row){
            $resultado[]=$row;
        }
        $this->DTO->setDestinataries($resultado);
        return $resultado;
    }

    /**
     * Recibe el arreglo del resultado que le envio la base de datos y crea el documento.
     * Esto es abstracto porque cada reporte debe tener su propio algoritmo.
     * @param array $resultado
     * @return mixed
     */
    abstract protected function createDocument(array $resultado);

    /**
     * Devuelve la extensión que debe llevar el archivo
     * @return String
     */
    abstract protected function getFileExtension();

    /**
     * Crea el archivo físico que se va a descargar desde el sistema.
     * @param $name
     * @param $contents
     */
    protected function createFile($contents){
        try{
            $url_absoluta=$this->getRepositoryLink(); //url absoluta
            $this->myfile = fopen($url_absoluta, "w") or die("Unable to open file!");
            if ($this->myfile===false) throw new Exception("El archivo fisico no se pudo abrir");
            fwrite($this->myfile, $contents);
            fclose($this->myfile);
            $this->repo->add_repositorio_file($url_absoluta);
        } catch (Exception $e){
            $this->notificacion("Se ha generado un error",$e->getMessage());
        }
    }

    /**
     * Crea la ruta del repositorio en base a la estructura de la base de datos.
     * A este nombre solamente hay que agregarle la extensión del archivo.
     * @return string
     */
    protected function getRepositoryLink(){
        if($this->ruta==null) {
            $this->ruta = (substr($this->DTO->ruta_repositorio, 0, 1) == "/") ? $this->DTO->ruta_repositorio : Config::$downloads_root . $this->DTO->ruta_repositorio; //url relativa

            $this->ruta = str_replace(' ', '_', $this->ruta);
            if (!file_exists($this->ruta) AND !mkdir($this->ruta, 0777)) {
                throw new \Exception("No se pudo crear la estructura para guardar el archivo");
            }
            $this->ruta .= str_replace(' ', '_', $this->DTO->nombre) . "_" . date('Y-m-d H:i:s') . $this->getFileExtension();
        }
        return $this->ruta;
    }

    /**
     * Envia el reporte si es que hay que enviar algo, este metodo debe poder sobreescribirse en
     * los tipos de reporte.
     */
    public function send()
    {
        // TODO: Implement send() method.
        foreach ($this->getDestinataries() as $recipient){
            $this->repo->add_repositorio_link($this->repo->getFileHash($this->getRepositoryLink()),$recipient['email']);
            $this->envio($recipient['nombre'],$recipient['email']);
            echo "ENVIANDO a ".$recipient['nombre']." &lt;".$recipient['email']."&gt; .... !!".'<br />';
        }
    }

    /**
     * Crea el mensaje del correo que se debe crear para cada destinatario en base al doc que se creo
     *
     * @return mixed
     */
    protected function createMessage($recipient){
        return "<html>
                    <head>
                        <style>
                            
                        </style>
                    </head>
                    <body>
                        <center>
                            <table width='800px' border='0' cellpadding='0' style='border: 1px solid #007431'>
                                <tr align='center'>
                                    <td style='background-color: #008542; padding: 5px;' colspan='2'>
                                        <h4 style='font-size: 14pt; text-align: left; color: white; padding: 10px 10px 0 10px'>Estimado ".$recipient['nombre'].", le informamos que el sistema automatizado de reportes ha generado el reporte: ".$this->DTO->nombre."</h4>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan='2'>
                                        <p align='justify' style='padding: 10px;'>
                                            Favor de descargarlo en la siguiente liga:
                                             <a href='https://larus.nationalcar.com.mx/reporteador/repositorio.php?key=".$this->repo->getDownloadKeyFromURL($this->getRepositoryLink(),$recipient['email']).
                                             "'>https://larus.nationalcar.com.mx/reporteador/repositorio.php?key=".$this->repo->getDownloadKeyFromURL($this->getRepositoryLink(),$recipient['email']).
                                             "</a>,igual si necesita comunicarse al departamento de sistemas estamos a sus ordenes en la <strong>extensión
                                             5000</strong>, al telefóno <strong>01 800 1482689</strong>
                                             o al correo electrónico<strong>
                                             <a href='mailto:sistemas@grupoantyr.com.mx?subject=Sistema de reportes automatizado'>sistemas@grupoantyr.com.mx</a>
                                            </strong>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </center>
                    </body>
                </html>";
    }

    /** envia por correo la notificacion del nuevo reporte generado -opcional aun, pendiente de elimar esta funcion si no se necesita mas o anexarla a la clase repositorio-
     * @param $nombre
     * @param $correo
     */
    public function envio($nombre, $correo){
        $mail = new \PHPMailer(true);
        $mail->IsSMTP();
        $msgstr=$this->createMessage(array("nombre"=>$nombre,"email"=>$correo));
        try{
            $mail->CharSet = 'UTF-8';
            $mail->Host      = "smtp.emailsrvr.com"; // SMTP Server
            $mail->SMTPAuth  = true;                 // enable SMTP authentication
            $mail->Port      = 25;                   // set the SMTP port for the GMAIL server
            $mail->Username  = "sistemas@grupoantyr.com.mx"; // SMTP account username
            $mail->Password  = "AEmail2017#";       // SMTP account password
            $mail->SetFrom("sistemas@grupoantyr.com.mx", "Grupo Antyr");
            $mail->Subject = "Notificación: ".$this->DTO->nombre." Generado";
            $mail->AltBody = "Para ver el correo por favor use un administrador de correos compatible con HTML."; // optional - MsgHTML will create an alternate automatically
            $mail->MsgHTML($msgstr);
            $mail->AddAddress($correo, 'Departamento Sistemas');


            $mail->Send();
        } catch (\phpmailerException $e) {
            $data['error']= $e->errorMessage();
        } catch (\Exception $e) {
            $data['message']= $e->getMessage();
        }
    }

    public function notificacion($error){
        $mail = new \PHPMailer(true);
        $mail->IsSMTP();
        $msgstr="<html>
                    <head>
                        <style>
                        
                        </style>
                    </head>
                    <body>
                        <center>
                            <table width='800px' border='0' cellpadding='0' style='border: 1px solid #007431'>
                                <tr align='center'>
                                    <td style='background-color: #008542;padding: 5px;' colspan='2'>
                                        <h4 style='font-size: 14pt; text-align: left; color: white; padding: 10px 10px 0 10px;'>El sistema automatizado de reportes ha generado un error</h4>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan='2' style='padding: 5px; font-weight: bold'>
                                        Mensaje de error
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p align='justify' style='padding: 10px;'>".$error."</p>
                                    </td>
                                </tr>
                            </table>
                        </center>
                    </body>
                 </html>";
        try {
            $mail->CharSet = 'UTF-8';
            $mail->Host       = "smtp.emailsrvr.com"; // SMTP server
            $mail->SMTPAuth  = true;                 // enable SMTP authentication
            $mail->Port      = 25;                   // set the SMTP port for the GMAIL server
            $mail->Username  = "sistemas@grupoantyr.com.mx"; // SMTP account username
            $mail->Password  = "AEmail2017#";       // SMTP account password
            $mail->SetFrom("sistemas@grupoantyr.com.mx", "Grupo Antyr");
            $mail->Subject = "Notificación: ".$this->DTO->nombre." Generado";
            $mail->AltBody = "Para ver el correo por favor use un administrador de correos compatible con HTML."; // optional - MsgHTML will create an alternate automatically
            $mail->MsgHTML($msgstr);

            $mail->AddAddress('gvillegas@grupoantyr.com.mx', 'Departamento Sistemas');

            $mail->Send();

        } catch (\phpmailerException $e) {
            $data['error']= $e->errorMessage();
        } catch (\Exception $e) {
            $data['message'] =$e->getMessage();
        }
    }
}
