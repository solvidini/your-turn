<?php

namespace App\Controller;

use App\Service\OpenWeatherAPI;
use App\Service\SentenceGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;


class Forecast extends AbstractController
{
    /**
     * @Route("/forecast", name="forecast")
     * @param Request $request
     * @param UserInterface $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function index(Request $request, UserInterface $user)
    {

        // if requested to change
        if($request->query->has('city_name') && !empty($request->query->get('city_name'))){
            return $this->redirectToRoute('show_forecast', [
                'city_name'=>$request->get('city_name')
            ]);
        } else if ($request->query->has('city_name')
            && empty($request->query->get('city_name'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $user->setCity(null);

            $entityManager->persist($user);
            $entityManager->flush();
        }


        // if user has city
        if (!empty($user->getCity())) {
            return $this->redirectToRoute('show_forecast', [
                'city_name'=>$user->getCity()
            ]);
        }

        //if user has not city
        return $this->render('forecast/forecast_form.html.twig');
    }

    // alt+enter
    /**
     * @Route("/city/{city_name}", name="show_forecast")
     * @param Request $request
     * @param $city_name
     * @param OpenWeatherAPI $openWeatherAPI
     * @param SentenceGenerator $sentenceGenerator
     * @param UserInterface $user
     * @return Response
     */
    public function citySearch(Request $request, $city_name, OpenWeatherAPI $openWeatherAPI, SentenceGenerator $sentenceGenerator, UserInterface $user)
    {
        if ($openWeatherAPI->getData($city_name)) {
            $data = $openWeatherAPI->getArray($city_name);
            $sentence = $sentenceGenerator->getSentence($data);

            $entityManager = $this->getDoctrine()->getManager();

            $user->setCity($city_name);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->render('forecast/forecast_generated.html.twig', [
                'success' => true,
                'sentence' => $sentence
            ]);

        }

        return $this->render('forecast/forecast_generated.html.twig', [
            'success' => false
        ]);
    }

    /**
     * @Route("/remove/city", name="remove")
     * @param UserInterface $user
     * @return Response
     */
    public function remove(UserInterface $user)
    {

            $entityManager = $this->getDoctrine()->getManager();

            $user->setCity(null);

            $entityManager->persist($user);
            $entityManager->flush();

        return $this->redirectToRoute('forecast');
    }
}
