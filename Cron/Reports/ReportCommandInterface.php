<?php
/**
 * Created by PhpStorm.
 * User: maste
 * Date: 28/03/2019
 * Time: 10:34
 */
namespace Cron\Reports;

use Cron as NC;

interface ReportCommandInterface extends NC\CronInterface{
    public function getQuery();
    public function getDestinataries();
    public function send();
}