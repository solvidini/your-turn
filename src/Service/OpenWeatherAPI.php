<?php

namespace App\Service;

//Open Weather Map API https://openweathermap.org
class OpenWeatherAPI {
    private $apiKey = '7311ae3abccd263a4f22f2b0659c1d89';

    public function getData($cityName){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'api.openweathermap.org/data/2.5/'
            .'weather?q='.$cityName
            .'&APPID='.$this->apiKey
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response);

        //print_r($data);

        if ($data->cod == '200')
            return $data;
        else return false;
    }

    public function getArray($cityName){
        if(!$this->getData($cityName)){
            return false;
        }
        $data = $this->getData($cityName);

        //associative array
        $array = array(
            'weather' => array(
                'main' => $data->weather[0]->main,
                'description' => $data->weather[0]->description,
                'icon' => $data->weather[0]->icon
            ),
            'main' => array(
                'temp' => $data->main->temp - 273.15,
                'temp_min' => $data->main->temp_min - 273.15,
                'temp_max' => $data->main->temp_max - 273.15
            ),
            'wind' => $data->wind->speed * 1.6,
            'name' => $data->name
        );

        return $array;
    }

}