<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\BookingRepository;

use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=BookingRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Booking
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booker;

    /**
     * @ORM\ManyToOne(targetEntity=Ad::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ad;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type(type="\DateTimeInterface", message="Le format doit être une date")
     * @Assert\NotNull
     * @Assert\GreaterThan("now", message="Aucune chambre n'est disponible aujourd'hui")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type(type="\DateTimeInterface", message="Le format doit être une date")
     * @Assert\GreaterThan(propertyPath="startDate", message="La date du départ doît être ultérieur que la date de votre arrivée")
     */
    private $endDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;


    /**
     * On va initialiser la date de création de la réservation
     * On va initialiser le montant de la réservation 
     * On va initialiser la durée de la reservation 
     * @ORM\PrePersist
     */
    public function prePersist(){
        //Si le date de la réservation est vide alors on crée  une nouvelle date et elle est de ce jour
        if(empty($this->created)){
            $this->created = new \DateTime();
        }

        //Si le montant est vide alors on multiplie le prix de l'annonce à la durée de la reservation
        //Remarque: ici la durée n'existe pas encore alors on va la créer apres cette function(HasLifeCycleCallbacks) 
        if(empty($this->amount)){
            $this->amount = $this->ad->getPrice() * $this->getDuration();
        }
    }

    //Récupérer de la durée du séjour(nombre): on soustrait la date de la fin de la reservation à la date du début de la réservation(pas l'inverse sinon Bug car on obtient un nombre négatif)
    public function getDuration(){
        $duree = ($this->endDate)->diff($this->startDate);
        //On le retourne mais en date 
        return $duree->days;
    }

    //Vérifié la date de la réservation si elle est encore disponible
    public function isBookabledays(){
        //Récupérer les dates réservées
        $notAvailableDays = $this->ad->getNotAvailableDays();
        //Récupérer mes date de la réservation souhaitées
        $bookingDays = $this->getDays();

        //Convertir ces 2 tableaux en chaîne de caractère pour faciliter la comparaison
        $notAvailableDays = array_map(function($day){
            return $day->format('Y-m-d');
        },$notAvailableDays);

        $days = array_map(function($day){
            return $day->format('Y-m-d');
        },$bookingDays);

        //Si les 2 chaînes de caractères sont identique alors:
            //c'est FALSE(Indisponible)
        //Si ces 2 chaînes de caractères sont différentes alors:
            //c'est TRUE(Dispo)
        //Vérifier dans le tableau de $days(date de réservation souhaitée), on va faire une boucle
        foreach($days as $day){
            if(array_search($day,$notAvailableDays) !== false)return false;
        }
        return true;
    }


    //Récupérer la durée du séjour(tableau)
    public function getDays(){

        //Mettre dans un tableau l'interval de la date du début de la réservation et la date de fin de la réservation en timestamp
        $resultat = range(
            $this->getStartDate()->getTimestamp(),
            $this->getEndDate()->getTimestamp(),
            24*60*60
        );
        //Convertir ce timestamp en date mais dans un tableau
        $days = array_map(function($day){
            return new \DateTime(date('Y-m-d',$day));
        },$resultat);

        //Retourner ce tableau 
        return $days;
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

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

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

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
