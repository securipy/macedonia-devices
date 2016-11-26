<?php
namespace App\Validation;

use App\Lib\Response,
    App\Validation\masterValidation;


class devicesValidation {

    public static function Validate($data){
        $response = new Response();
        
      

        $key = 'day_scan';
        if(empty($data[$key])) {
            $response->errors[$key][] = 'Este campo es obligatorio';
        }

        $data[$key] = $data[$key].":00";
        $result = masterValidation::validateDate($data[$key]);
        if(!$result){
          $response->errors[$key][] = 'Formato de la fecha no valida';
        }


        $key = 'ip_domain';
        if(empty($data[$key])) {
            $response->errors[$key][] = 'Este campo es obligatorio';
        }

        $key = 'servers';
        if(empty($data[$key])) {
            $response->errors[$key][] = 'Este campo es obligatorio';
        }
        
        $response->setResponse(count($response->errors) === 0);


        return $response;
    }


}