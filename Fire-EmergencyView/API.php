<?php

class API {
    function __construct(){
        $this->_BASE_URL_API = 'http://192.168.5.183:8001';
    }

    function getIncident(string $TOKEN, int $skip=0, int $limit=1000){
        $curl = curl_init();
    
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->_BASE_URL_API.'/incidents/?token_api='.$TOKEN.'&skip='.strval($skip).'&limit='.strval($limit),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));
    
        $response = curl_exec($curl);
    
        curl_close($curl);
        return $response;
    
    }

    // 
    function getDetecteur(string $TOKEN, int $skip=0, int $limit=1000){
        $curl = curl_init();
    
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->_BASE_URL_API.'/detecteurs/?token_api='.$TOKEN.'&skip='.strval($skip).'&limit='.strval($limit),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));
    
        $response = curl_exec($curl);
    
        curl_close($curl);
        return $response;
    
    }



    //🏢
    function getCasernes(string $TOKEN, int $skip=0, int $limit=1000){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->_BASE_URL_API.'/casernes/?token_api='.$TOKEN.'&skip='.strval($skip).'&limit='.strval($limit),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));
    
        $response = curl_exec($curl);
    
        curl_close($curl);
        return $response;

    }

    //👨‍🚒 
    //A VERIFIER CELLE LA ... 
    function getPompiersOfCaserne(string $TOKEN, int $id_caserne, int $skip=0, int $limit=1000){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->_BASE_URL_API.'/pompiers/'.$id_caserne.'?token_api='.$TOKEN.'&skip='.strval($skip).'&limit='.strval($limit),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));
    
        $response = curl_exec($curl);
    
        curl_close($curl);
        return $response;

    }
    //🚒
    function getVehiculesOfCaserne(string $TOKEN, int $id_caserne, int $skip=0, int $limit=1000){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->_BASE_URL_API.'/vehicules/'.strval($id_caserne).'?token_api='.$TOKEN.'&skip='.strval($skip).'&limit='.strval($limit),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));
    
        $response = curl_exec($curl);
    
        curl_close($curl);
        return $response;

    }

    function getAddressFromCoords($latitude,$longitude){
        $url = "https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=".strval($latitude)."&lon=".strval($longitude);
        /*$curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'user-agent: ProjetScientifique-CPE/1'
              ),
        ));
    
        $response = curl_exec($curl);
    
        curl_close($curl);
        */
        $response = "Lyon - LONG A CHARGER, très long";
        return $response;
    }
}





