<?php

namespace App\Service;

//Weather Sentence Generator
class SentenceGenerator {

    public function getSentence($data){
        $sentence = "\r\n"."Today in ".$data['name'].' ';

        if ($data['weather']['main'] == "Rain"){
            $sentence .= 'you should bring an umbrella, because you may encounter '
                .$data['weather']['description'];
        } else if ($data['weather']['main'] == "Thunderstorm"){
            $sentence .= 'avoid tall trees, because you may encounter '
                .$data['weather']['description'];
        } else if ($data['weather']['main'] == "Drizzle"){
            $sentence .= 'there will be '
                .$data['weather']['description'];
        } else if ($data['weather']['main'] == "Snow"){
            $sentence .= 'be careful on the road, because you may encounter '
                .$data['weather']['description'];
        } else if ($data['weather']['main'] == "Clear"){
            $sentence .= 'you can admire the beauty of the blue sky';
        } else if ($data['weather']['main'] == "Clouds"){
            $sentence .= 'the sky will be covered with '
                .$data['weather']['description'];
        } else if ($data['weather']['main'] == "Tornado"){
            $sentence .= 'you should hide in the basement, because there might be a '
                .$data['weather']['description'];
        } else {
            $sentence .= 'you may encounter '
                .$data['weather']['description'];
        }

        $sentence .= ". <img src=\"http://openweathermap.org/img/w/"
            .$data['weather']['icon'].".png\" alt=\"Weather Icon\">";

        $sentence .= "<br>"."Average temperature is around " . round($data['main']['temp'])
            ."°C (min:  " . $data['main']['temp_min']
            ."°C / max: " . $data['main']['temp_max']
            ."°C), also the wind speed is around " . $data['wind'] . " km/h.";

        return $sentence;
    }

}