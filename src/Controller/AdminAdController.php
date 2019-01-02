<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Common\Persistence\ObjectManager;
use App\Service\PaginationService;

class AdminAdController extends AbstractController
{

    /**
     * Permet à un administrateur d'éditer les annonces
     * @Route("/admin/ads/{id}/edit", name="admin_ads_edit")
     * @param Ad $ad
     * @return Response
     */
    public function edit(Ad $ad,Request $request, ObjectManager $manager){

        $form=$this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                'Annonce modifiée avec succés !'
            );
        }

        return $this->render('admin/ad/edit.html.twig', [
            'form' => $form->createView(),
            'ad' => $ad
        ]);
    }

    /**
     * Permt de supprimer une annonce
     * 
     * @Route("/admin/ads/{id}/delete", name="admin_ads_delete")
     *
     * @param Ad $ad
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Ad $ad, ObjectManager $manager){

        if( count($ad->getBookings())> 0 ){
            $this->addFlash(
                'warning',
                "Vous ne pouvez pas supprimer l'annonce <strong>{$ad->getTitle()}</strong> car elle posséde déjà ds réservations !"
            );
        }else{

            $manager->remove($ad);
            $manager->flush();
    
            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimée !"
            );
        }


        return $this->redirectToRoute('admin_ads_index');
    }

    
    /**
     * Au niveau de la route nous définissons la variable page avec ses requirements <> de plus
     * nous expliquons avec le ? et que la valeur par défaut est 1
     * @Route("/admin/ads/{page<\d+>?1}", name="admin_ads_index")
     */
    public function index(AdRepository $repo, $page, PaginationService $pagination)
    {   // Méthode 1, les variables ont été supprimé de la vue pour faire la méthode 2 avec le service
        // $limit variable qui va définir le nobre d'éléments à afficher
        //$limit = 10;

        //$start va définir à partir du quelième élément on va afficher c'est l'offset
        //$start = $page*$limit - $limit ;

        //Calcul du nombre total d'enregistrements
        //$total = count($repo->findAll());

        //Calcul du nombre de pages avec la fonction ceil qui arrondit au chiffre supérieur
        //$pages = ceil($total / $limit);

        // La fonction findBy() avec les 4   paramètres :
        // 1 - qui permet de dire sur quel(s) champs on fait les filtres
        // 2 - c'est l'ordonnancement
        // 3 - le nombre d'éléments que l'on veut afficher
        // 4 - à partir de quel élément l'affichage commemnce

        //Méthode 2

        $pagination->setEntityClass(Ad::class)
                   ->setPage($page);
        
        return $this->render('admin/ad/index.html.twig', [
            "pagination" => $pagination
        ]);
    }


}
