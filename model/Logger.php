<?php
/**
 * Gestore dei log ghost Customer
 * @author Geosystems S.r.l. - LM
 * @version 1.0.0
 */

//namespace ghostCustomer;

require_once "../vendor/autoload.php";

use Monolog\Logger as MonoLogger;
use Monolog\Handler\RotatingFileHandler as StreamHandler;
use Monolog\Handler\RotatingFileHandler as RotatingFileHandler;
use Monolog\Formatter\LineFormatter as LineFormatter;

class Logger
{
    private $logPath = '';
    private $logFile = '';
    private $maxSize = 0;
    private $maxFile = 0;
    private $logLevel = 5;
    private $logger;

    public function __construct($name = 'PHP-LAKE-NAVIGATION')
    {
        $configuration = parse_ini_file(__DIR__ . "/../lnConfig.ini", true);
        $this->logPath = $configuration['logger']['logPath'];
        $this->logFile = $configuration['logger']['logFile'];
        $this->maxSize = intval($configuration['logger']['logMaxSize']);
        $this->maxFile = intval($configuration['logger']['logMaxFile']);
        $this->logLevel = intval($configuration['logger']['logLevel']);
        $this->logger = new MonoLogger($name);
        $file_handler = new RotatingFileHandler($this->logPath . $this->logFile . ".log", $this->maxFile, $this->logLevel);
        $lineFormatter = new LineFormatter(null, null, true, true);
        $file_handler->setFormatter($lineFormatter);
        $this->logger->pushHandler($file_handler);
    }

    public function warning($message){
        try {
            $this->logger->warning($message);
        }catch (\Exception $e){ }
    }

    public function info($message){
        try {
            $this->logger->info($message);
        }catch (\Exception $e){ }

    }

    public function debug($message){
        try {
            $this->logger->debug($message);
        }catch (\Exception $e){ }
    }

    public function error($message){
        try {
            $this->logger->error($message);
        }catch (\Exception $e){ }
    }

}
