<?php


namespace Cron;


interface CronInterface
{
    public function execute();
    public function saveExecutionLog(CronExecutionLog $log);
}