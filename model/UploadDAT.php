<?php
require_once('../model/Logger.php');
require_once('Errors.php');
class UploadDAT
{

    private $logger;
    private $errors;
    private $iniConf;
    private $queryList;

    public function __construct(){
        $this->iniConf = parse_ini_file('../lnConfig.ini', true);
        $this->queryList = parse_ini_file('../GestioneNLQuery.ini', true)['query'];
        $this->logger = new Logger('UploadDAT');
        $this->errors = new Errors();
    }

    public function UploadDAT() {
        $response = ["DATA" => "", "ESITO" => 0, "MESSAGE" => ""];
        try{
            if($_FILES["allegato"]["name"]) {
                $filename = $_FILES["allegato"]["name"];
                $source = file ($_FILES["allegato"]["tmp_name"]);

                $data = $this->unpackDat($source);
                $response['MESSAGE'] = $this->controllaCasi($data);
                $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Telematico></Telematico>');
                $xml = $this->impostaValori($data, $xml);
                $response['DATA'] = $xml->asXML();
            }else{
                $response['ESITO'] = $this->errors->get_error('ERR_FILE_NOT_FOUND');
                $this->logger->error("Error reading configuration file");
            }
        } catch (Exception $e){
           // $response['ESITO'] = $this->errors->get_error('ERR_UPLOAD_FILE');
            $response['MESSAGE'] = $e->getMessage();
           // $this->logger->error($e->getMessage());
        }
        return $response;
    }

    private function unpackDat($source){
        $newArray = [];
        for($i = 0;$i < count($source); $i++){
            //$tipo = unpack("A8Tipo", $source[$i]);
            $tipo = substr($source[$i], 7, 1);
            $tipo .= substr($source[$i], 16, 1);
            switch ($tipo){//($newArray["Tipo"]){
                case 'A1': //testata
                    $newArray[] = $this->unpackTestata($source[$i]);
                    break;
                case 'B2': //intestato
                    $newArray[] = $this->unpackIntestato($source[$i]);
                    break;
                case 'C3': //unità immobiliari
                    $newArray[] = $this->unpackImm($source[$i]);
                    break;
                case 'C4': //unità immobiliari
                    $newArray[] = $this->unpackImmGraffati($source[$i]);
                    break;
                case 'L9': //annotazioni
                    $newArray[] = $this->unpackAnnotazioni($source[$i]);
                    break;
                case 'PA': //particelle
                    $newArray[] = $this->unpackParticelle($source[$i]);
                    break;
            }
            $newArray[$i]["tipo"] = $tipo;
        }
        return $newArray;
    }

    private function unpackTestata($source){
        return unpack("A2tipo documento/A2progressivo voltura/A3progressivo nota/Aid record/A3progressivo ambito/A5progressivo trascinato/Atipo record/A2data voltura giorno/A2data voltura mese/A4data voltura anno/A17FILLER/A6protocollo/Atipo voltura".
            "/A3causale atto/A40descrizione atto/A2data eff atto giorno/A2data eff atto mese/A4data eff atto anno/A7numero repertorio/A18rogante/A4cod com rogante/A12FILLER/A2tipo ufficio/A4cod ufficio/A2FILLER/A7numero atto".
            "/A7volume atto/A2data atto giorno/A2data atto mese/A4data atto anno/A3numero uiu/A3numero particelle/Ainfo ditta/Aflag catasto/Atipo codifica titoli/A3num intestati/A3num intestati favore/A3sez".
            "/A5particella/A9numero/A4subalterno/A6FILLER/A30descrizione/A6FILLER/Atipo cat/A5cod rif/Aflag preallineamento/Aflag voltura esente/A6numero particolare/A9FILLER/A4chiave record doc/A26FILLER".
            "/A8protocollo generale/A24FILLER/A4cod com/A9FILLER/A2versione",$source);
    }
    private function unpackIntestato($source){
        return unpack("A2tipo documento/A2progressivo voltura/A3progressivo doc/Aid record/A3progressivo ambito/A5progressivo trascinato/Atipo record/A7FILLER/A50cognome/A50nome".
            "/A4cod com nascita/A23FILLER/A2data nascita giorno/A2data nascita mese/A4data nascita anno/Asesso/A16codice fiscale/A2codice titolo/Acodice regime/A3numero ordine intestato".
            "/A50specifica del diritto/A9quota numeratore/A6FILLER/A2nuovi titoli/A2codice titolo1/Acodice titolo2/Aflag titoli/A9quota denominatore/A10FILLER/A4chiave record doc/A58FILLER/A4cod com/A10riservato/A2versione",$source);
    }

    private function unpackImm($source){
        return unpack("A2tipo documento/A2progressivo voltura/A3progressivo nota/Aid record/A3progressivo ambito/A5progressivo trascinato/Atipo record/A8FILLER/A3sezione/A4foglio/A5numero/A4denominatore/A4subalterno".
            "/A227FILLER/A4chiave record/A56riserva/Ainserimento riserva/Acancellazione riserva/A4cod com/A10riservato2/A2versione", $source);
    }

    private function unpackImmGraffati($source){
        return unpack("A2tipo documento/A2progressivo voltura/A3progressivo nota/Aid record/A3progressivo ambito/A5progressivo trascinato/Atipo record".
            "/A3g1_sez/A4g1_foglio/A5g1_numero/A4g1_denom/A4g1_sub".
            "/A3g2_sez/A4g2_foglio/A5g2_numero/A4g2_denom/A4g2_sub".
            "/A3g3_sez/A4g3_foglio/A5g3_numero/A4g3_denom/A4g3_sub".
            "/A3g4_sez/A4g4_foglio/A5g4_numero/A4g4_denom/A4g4_sub".
            "/A3g5_sez/A4g5_foglio/A5g5_numero/A4g5_denom/A4g5_sub".
            "/A3g6_sez/A4g6_foglio/A5g6_numero/A4g6_denom/A4g6_sub".
            "/A3g7_sez/A4g7_foglio/A5g7_numero/A4g7_denom/A4g7_sub".
            "/A3g8_sez/A4g8_foglio/A5g8_numero/A4g8_denom/A4g8_sub".
            "/A3g9_sez/A4g9_foglio/A5g9_numero/A4g9_denom/A4g9_sub".
            "/A3g10_sez/A4g10_foglio/A5g10_numero/A4g10_denom/A4g10_sub".
            "/A55FILLER/A4chiave record/A58riservato/A5cod com/A9riservato2/A2versione", $source);
    }

    private function unpackAnnotazioni($source){
        return unpack("A2tipo documento/A2progressivo voltura/A3progressivo doc/Aid record/A3progressivo ambito/A5progressivo trascinato/Atipo record/A24cognome/A20nome/A4cod com residenza/A35indirizzo/A5civico/A5cap".
            "/A16codice fiscale/A70nella qualita di/A76FILLER/A4chiave record/A58FILLER/A4cod com/A10riservato/A2versione",$source);
    }

    private function unpackParticelle($source){
        return unpack("A2tipo documento/A2progressivo voltura/A3progressivo doc/Aid record/A3progressivo ambito/A5progressivo trascinato/Atipo record/A9FILLER/A5foglio/A5numero/A4denominatore/A4subalterno/A114FILLER".
            "/A3FILLER/AFILLER/A2FILLER/A5FILLER/A10FILLER/A90FILLER/Arichiesta/Ainserimento riserva/Acancellazione riserva/A4chiave record/A58riservato/A5cod com cat/A9riservato2/A2versione",$source);
    }

    private function controllaCasi($dati){
        $msg = [];
        for ($i = 0; $i < count($dati); $i++){
            if(array_key_exists("progressivo voltura", $dati[$i]) && $dati[$i]["progressivo voltura"] != "01"){
                $msg[] = array("msg"=>"Documento non convertibile", "causa"=>"file contenente più volture");
            }
            if(array_key_exists("flag catasto", $dati[$i]) && ($dati[$i]["flag catasto"] == "F" || $dati[$i]["flag catasto"] == "T")){
                $msg[] = array("msg"=>"Documento non convertibile", "causa"=>"flag catasto");
            }
        }
        return $msg;
    }

    private function impostaValori($dati, &$xml){
        //todo: aggiungere codice riscontro da volturautils
        for ($i = 0; $i < count($dati); $i++){
            if($dati[$i]["tipo"] == 'A1'){
                $xml->addChild('IstanzaVoltura', $this->createChiaveIstanzaVoltura($dati[$i]));
            }
        }
        return $xml;
    }

    private function impostaDatiTestata($dati){
        /*$flagPreAllineamento = $dati["flag preallineamento"];
        switch ($flagPreAllineamento){
            case '0':
                $dati["flag preallineamento"] = "VOLTURA ATTUALE";
                break;
            case '1':
                $dati["flag preallineamento"] = "PREALLINEAMENTO";
                break;
            case '2':
                $dati["flag preallineamento"] = "RECUPERO VOLTURA AUTOMATICA";
                break;
        }*/
    }

    private function createChiaveIstanzaVoltura($data){
        $ChiaveIstanzaVoltura = new SimpleXMLElement('<ChiaveIstanzaVoltura/>');
        //todo: aggiungere codice riscontro da volturautils
        $ChiaveIstanzaVoltura->addAttribute('codiceRiscontro', '');
        if ($data['causale atto'] == 'IST') $ChiaveIstanzaVoltura->addChild('VolturaInfoAttoNotarile');
        else if ($data['causale atto'] == 'DEN') $ChiaveIstanzaVoltura->addChild('VolturaInfoSuccessione');
        else $ChiaveIstanzaVoltura->addChild('VolturaInfoAtto');

        return $ChiaveIstanzaVoltura;
    }

}
