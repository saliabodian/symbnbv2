<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Faker\Provider\tr_TR\DateTime;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Booking
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booker;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ad", inversedBy="yes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ad;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="la date d'arrivée doit être au bon format !")
     * @Assert\GreaterThan("today", message = "La date d'arrivée doit être ultérieure à la date d'aujourdh'hui!", groups={"front"})
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="la date de départ doit être au bon format !")
     * @Assert\GreaterThan(propertyPath = "startDate", message = "La date de départ doit être plus éloignée que la date d'arrivée !")
     */
    private $endDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="text")
     */
    private $comment;
    /**
     * Callback function qu'il faut appeler à chaque qu'on insére une réservation
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * @return void
     */
    public function prePersist(){
        if(empty($this->createdAt)){
            $this->createdAt = new \DateTime();
        }
        // Calcul du montant
        if(empty($this->amount)){
            $this->amount = $this->ad->getPrice() * $this->getDuration();
        }
    
    }

    public function isBookableDates(){
        // 1- Il faut conna^tre les dates impossibles pour les réservations
        $notAvailablesDays = $this->ad->getNotAvailableDays();
        
        // 2- Il faut comparer les dates choisies avec les dates impossibles 
        $bookingDays = $this->getDays();
        
        // 3- Transformation des tableaux $notAvailablesDays et $bookingDays en tableau de chaîne de caractéres pour faire la comparaison
        
        // 3' refactorisation du code
        $formatDay = function($day){
            return $day->format('Y-m-d');
        };

        $days = array_map($formatDay, $bookingDays);
        
        $notAvailable = array_map($formatDay, $notAvailablesDays);
        
        // 4- On fait la comparaion proprement dite
        foreach($days as $day){
            if(array_search($day, $notAvailable) !== false) return false ;
        }

        return true;
    }

    /**
     * Permet de récupérer les jours de réservation choisie
     *
     * @return array Un tableau d'objets Datetime représentant les jours de la réservation 
     */
    public function getDays(){
        $resultat = range(
            $this->getStartDate()->getTimestamp(),
            $this->getEndDate()->getTimestamp(),
            24 * 60 * 60
        );

        $days = array_map(function($dayTimestamp){
            return new \DateTime(date('Y-m-d',$dayTimestamp));
        }, $resultat);

        return $days;
    }

    public function getDuration(){
        // Calcul de la diiféence entre la date de dbut et la date de fin $diff est de type date_intervalle et
        // et posséde de fonctions telles que days
        $diff = $this->endDate->diff($this->startDate);
        return $diff->days;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): self
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    public function setAd(?Ad $ad): self
    {
        $this->ad = $ad;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
