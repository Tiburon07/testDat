<?php
/**
 * Enumeratore errori
 * @author GeoSystems S.r.l. - LM
 * @version 1.0.0
 * @copyright Geosystems S.r.l.
 */


class Errors
{
    private static $NO_ERROR=array('number'=>0,'description'=>'');
    private static $ERR_UNKNOW=array('number' => -1, 'description' => 'Errore sconosciuto');

    public function get_error($error){
        try{
            return self::$$error;
        }catch (\Exception $e){
            return self::$ERR_UNKNOW;
        }
    }
}
