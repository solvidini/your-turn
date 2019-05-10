<?php

namespace App\Service;

//Weather Sentence Generator
class SentenceGenerator {

    public function getSentence($data){
        $sentence = "\r\n"."Today in <b>".$data['name']."</b> ";

        if ($data['weather']['main'] == "Rain"){
            $sentence .= 'you should bring an umbrella, because you may encounter '
                ."<b>".$data['weather']['description']."</b>";
        } else if ($data['weather']['main'] == "Thunderstorm"){
            $sentence .= 'avoid tall trees, because you may encounter '
                ."<b>".$data['weather']['description']."</b>";
        } else if ($data['weather']['main'] == "Drizzle"){
            $sentence .= 'there will be '
                ."<b>".$data['weather']['description']."</b>";
        } else if ($data['weather']['main'] == "Snow"){
            $sentence .= 'be careful on the road, because you may encounter '
                ."<b>".$data['weather']['description']."</b>";
        } else if ($data['weather']['main'] == "Clear"){
            $sentence .= 'you can admire the beauty of the blue sky';
        } else if ($data['weather']['main'] == "Clouds"){
            $sentence .= 'the sky will be covered with '
                ."<b>".$data['weather']['description']."</b>";
        } else if ($data['weather']['main'] == "Tornado"){
            $sentence .= 'you should hide in the basement, because there might be a '
                ."<b>".$data['weather']['description']."</b>";
        } else {
            $sentence .= 'you may encounter '
                ."<b>".$data['weather']['description']."</b>";
        }

        $sentence .= ". <img src=\"http://openweathermap.org/img/w/"
            .$data['weather']['icon'].".png\" alt=\"Weather Icon\">";

        $sentence .= "<br>"."Temperature: <b>" . round($data['main']['temp'])
            ."°C</b> (min:  " . $data['main']['temp_min']
            ."°C / max: " . $data['main']['temp_max']
            ."°C)" . "<br>" . "Wind speed: <b>" . $data['wind'] . " km/h</b>.";

        return $sentence;
    }

}