<?php
/**
 * Created by PhpStorm.
 * User: maste
 * Date: 28/03/2019
 * Time: 8:50
 */

namespace Cron\Reports\ReportTypes;
use \Cron\Reports\Report;

class EmailHTMLReport extends Report
{
    var $msg;

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
            if (strpos($key, 'row-settings') === false){
                $str.="\n\t\t<td><strong>".ucfirst($key)."</strong></td>";
            }
        }
        $str.="\n\t</tr>";
        foreach ($resultado as $row){
            $str.="\n\t<tr stylr=\"".$row['row-settings_style']."\">";
            foreach ($row as $key => $field){
                if (strpos($key, 'row-settings') === false){
                    $str.="\n\t\t<td>".$field."</td>";
                }
            }
            $str.="\n\t</tr>";
        }
        $str.="\n</table>";
        $this->msg=$str;
    }

    protected function createEmptyErrorText(){
        return "<table><tr><td>El Reporte no generó datos.</td></tr>";
    }

    protected function createMessage($recipient){
        return "<html>
                    <head>
                        <style></style>
                    </head>
                    <body>
                        <center>
                            <table width='800px' border='0' cellpadding='0' style='border:1px solid #007431'>
                                <tr align='center'>
                                    <td style='background-color:#008542; padding: 5px;' colspan='2'>
                                        <h4 style='font-size: 14pt; text-align: left; color: white; padding: 10px 10px 0 10px'> Estimado ".$recipient['nombre'].", le informamos que el sistema automatizado de reportes ha generado el reporte:".$this->DTO->nombre."</h4>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan='2'>".$this->msg."</td>
                                </tr>
                                <tr>
                                    <td colspan='2'>
                                        <p align='justify' style='padding: 10px;'>
									    Si necesita comunicarse al departamento de sistemas estamos a sus ordenes en la <strong>extensi&oacute;n
                                        5000</strong>, a los teléfonos de red <strong>22980,22983 y 22993</strong>
                                        o al correo electrónico <strong>
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
    /**
     * Devuelve la extensión que debe llevar el archivo
     * @return String
     */
    protected function getFileExtension(){
        return false;
    }

    public function send(){
        foreach ($this->getDestinataries() as $recipient){
            $this->envio($recipient['nombre'],$recipient['email']);
            echo "ENVIANDO a ".$recipient['nombre']." &lt;".$recipient['email']."&gt; .... !!".'<br />';
        }
    }
}