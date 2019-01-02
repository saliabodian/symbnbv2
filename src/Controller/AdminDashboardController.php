<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ObjectManager;
use App\Service\StatsService;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(ObjectManager $manager, StatsService $statsService)
    {
        // Requête DQL poue compter le nombre d'enregistrements dans nos différentes tables
        // Les requêtes ci dessous vont être traitées dans le service StatsService
        // $users = $stats->getUsersCount();

        // $ads = $stats->getAdsCount();

        // $bookings = $stats->getBookingsCount();

        // $comments = $stats->getCommentsCount();
        
        // Gestion des meilleures annonces

        $stats = $statsService->getStats();

        // dump($bestAds);

        // Gestion des meilleures et pires annonces xternaliser dans le service

        // $worstAds = $manager->createQuery('
        //                         SELECT AVG(c.rating) as note, a.title, a.id, u.firstName, u.lastName, u.picture
        //                         FROM App\Entity\Comment c
        //                         JOIN c.ad a
        //                         JOIN a.author u
        //                         GROUP BY a
        //                         ORDER BY note ASC
        //                     ')
        //                     ->setMaxResults(5)
        //                     ->getResult();

        $bestAds = $statsService->getAdsStats('DESC');

        $worstAds = $statsService->getAdsStats('ASC');

        return $this->render('admin/dashboard/index.html.twig', [
            // Méthode 1 passage des valeurs dans un tableau stats
            //'stats' => [
            //    'users' => $users,
            //    'ads' => $ads,
            //    'bookings' => $bookings,
            //    'comments' => $comments
            //]
            // Méthode 2 passage de ces mêmes valeurs dans un tableau stats 
            // grâce à la fonction php compact qui à partir des clés créent les variables correspondantes.

            'stats' => $stats,
            'bestAds' => $bestAds,
            'worstAds' => $worstAds
        ]);
    }
}
