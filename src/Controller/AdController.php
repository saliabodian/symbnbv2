<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Entity\Image;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {
        /**
         * Je recupére le repository de la classe Ad et je fais un findAll sur le repository
         */

        //$repo = $this->getDoctrine()->getRepository(Ad::class);

        $ads = $repo->findAll();

        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }

    
    /**
     * 
     * permet de créer une annonce
     * 
     * 
     * @Route("ads/new", name="ads_create")
     * 
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function create(Request $request, ObjectManager $manager){
        
        $ad = new Ad();
        
        //Fabriquer un formulaire
        
        $form = $this->createForm(AdType::class, $ad);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            
            foreach($ad->getImages() as $image){
                $image->setAd($ad);
                $manager->persist($image);
            }

            $ad->setAuthor($this->getUser());

            $manager->persist($ad);
            
            $manager->flush();
            
            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a été enregisté avec succés !"
            );

             return $this-> redirectToRoute('ads_show', [
                 'slug' => $ad->getSlug()
             ]);
         }

        return $this->render('ad/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'éditer une annonce
     * 
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * 
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()", message ="Cette annonce ne vous appartient pas. Vous ne pouvez pas la modifier !")
     * 
     * @return Response
     */

     public function edit(Ad $ad, Request $request, ObjectManager $manager){

        $form = $this->createForm(AdType::class, $ad);
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            foreach($ad->getImages() as $image){
                $image->setAd($ad);
                $manager->persist($image);
            }

            $manager->persist($ad);
            
            $manager->flush();
            
            $this->addFlash(
                'success',
                "Les modifications de l'annonce <strong>{$ad->getTitle()}</strong> ont bien été enregistrées !"
            );

             return $this-> redirectToRoute('ads_show', [
                 'slug' => $ad->getSlug()
             ]);
         }

        return $this->render('ad/edit.html.twig', [
            'form' => $form->createView(),
            'ad' => $ad
        ]);
     }

    /**
     * pour afficher une annonce à partir du slug
     * 
     * @Route("/ads/{slug}", name="ads_show")
     * 
     * @return Response
     */
    public function show(Ad $ad)
    {
        // je récupére l'annonce qui correspond au slug grâce à l'injection de dépendance
        //En utilisant la propriété ParamConverter nous n'avons plus besoin de la ligne ci-dessous car nous passons
        //directement un Ad en paramétre dans la fonction show en lieu et place du AdRepository
        //$ad = $repo->findOneBySlug($slug);
        
        return $this->render('ad/show.html.twig', [
            'ad' => $ad
        ]);
    }

    /**
     * Permet de supprimer une annonce
     * 
     * @Route("/ads/{slug}/delete", name="ads_delete")
     * @Security("is_granted('ROLE_USER') and user == ad.getAuthor()", message ="Vous n'êtes pas autorisé à supprimer cette annonce !")
     * 
     * @param Ad $ad
     * @param ObjectManager $manager
     * 
     * @return Response
     * 
     */
    public function delete(Ad $ad, ObjectManager $manager){
        $manager->remove($ad);
        $manager->flush();

        $this->addFlash(
            'success',
            "L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimée !" 
        );

        return $this->redirectToRoute('ads_index');
    }

}
