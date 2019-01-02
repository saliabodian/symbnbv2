<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Repository\AdRepository;
use App\Repository\UserRepository;


class HomeController extends Controller {


    /**
     * Undocumented function
     *@route("/hello/{prenom}/age/{age}", name ="hello")
     *@route("/hello", name ="hello_base")
     *@route("/hello/{prenom}", name ="hello_prenom")
     * @return void
     */
    public function hello($prenom = "visiteur", $age = 0){
        return $this->render('hello.html.twig',
        [
            'prenom' => $prenom,
            'age' => $age
        ]);
    }
    

    /**
     * Undocumented function
     *@Route("/", name="homepage")
     * @return void
     */
    public function Home(AdRepository $adRepo, UserRepository $userRepo){

        $prenoms = ["Salia" => 37, "Landing" => 39, "Pape" => 42];
        return $this->render('home.html.twig', [
                'ads' => $adRepo->findBestAds(),
                'users' => $userRepo->findBestUsers()
            ]
        );
    }
}


?>