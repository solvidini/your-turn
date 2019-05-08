<?php

namespace App\Controller;

use App\Service\OpenWeatherAPI;
use App\Service\SentenceGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class CitySearchController extends AbstractController
{
    /**
    * @Route("/citySearch", name="city_search")
     */
    public function index(Request $request)
    {
        if($request->query->has('city_name')){
            return $this->redirectToRoute('show_city', ['city_name'=>$request->get('city_name')]);
        }
        return $this->render('city_search/index.html.twig');
    }

    /**
     * @Route("/city/{city_name}", name="show_city")
     * @param Request $request
     * @param $city_name
     * @param OpenWeatherAPI $openWeatherAPI
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function citySearch(Request $request, $city_name, OpenWeatherAPI $openWeatherAPI, SentenceGenerator $sentenceGenerator)
    {   // alt+enter
        if ($openWeatherAPI->getData($city_name)) {
            $data = $openWeatherAPI->getArray($city_name);
            $sentence = $sentenceGenerator->getSentence($data);

            return $this->render('city_search/generated.html.twig', [
                'success' => true,
                'sentence' => $sentence
            ]);

        }

        return $this->render('city_search/generated.html.twig', [
            'success' => false
        ]);
    }
}
