<?php


namespace Cron;


interface CommandControllerInterface
{
    public function establishPeriodicity(array $periodicidad);
    public function getCommands();
}