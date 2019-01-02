<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Form\BookingType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\CommentType;

class BookingController extends AbstractController
{
    /**
     * @Route("/ads/{slug}/book", name="booking_create")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function book(Ad $ad, Request $request, ObjectManager  $manager)
    {
        $booking = new Booking();

        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // on récupére le user connecté et lui associe la réservation
            $user = $this->getUser();
            $booking->setBooker($user); 
            
            //On associe l'annonce à la réservation
            $booking->setAd($ad);

            // Si les dates ne sont pas disponibles alores message d'erreur
            if(!($booking->isBookableDates())){
                $this->addFlash(
                    'warning',
                    "Les dates que vous avez choisi ne sont pas disponibles: elles sont déjà réservées !"
                );
            }else{
                // Sinon enregistrement
                $manager->persist($booking);
                $manager->flush();
    
                return $this->redirectToRoute('booking_show', [
                    'id' => $booking->getId(),
                    'withAlert' => true
                    ]) ;
            }

        }

        return $this->render('booking/book.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de visualier la réservaion créée
     * 
     * @Route("/booking/{id}", name ="booking_show")
     * 
     * @param Booking $booking
     * @param Request $request
     * @param ObjectManager $manager
     * 
     * @return Response
     */
    public function show(Booking $booking, Request $request, ObjectManager $manager){

        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $comment->setAd($booking->getAd())
                    ->setAuthor($this->getUser());

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre commantaire a été bien enregistré"
            );

        }


        return $this->render('booking/show.html.twig', [
            'booking' => $booking,
            'form'    => $form->createView()
        ]);

    }
}
