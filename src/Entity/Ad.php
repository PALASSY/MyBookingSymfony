<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use App\Repository\AdRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=AdRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(
 * fields={"title"},
 * message="Ce titre existe déjà, choisissez-en un autre"
 * )
 */
class Ad
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=10,max=255,minMessage="Le titre doit faire de plus de 10 caractères",maxMessage=" votre titre est trop long pas plus de 255 caractères")
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=10,minMessage="Merci de mettre au moins 10 caractères")
     */
    private $introduction;

    /**
     * @ORM\Column(type="text")
     * * @Assert\Length(min=10,minMessage="Merci de mettre au moins 10 caractères")
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $coverImage;

    /**
     * @ORM\Column(type="integer")
     */
    private $rooms;

    /**
     * @ORM\OneToMany(targetEntity=Image::class, mappedBy="ad",orphanRemoval=true)
     */
    private $yes;
    //Une annonce peut avoir plusieurs images(secondaires)


    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="ads")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity=Booking::class, mappedBy="ad")
     */
    private $bookings;
    //Plusieurs annonces peuvent appartenir qu'à un seul utilisateur 

    public function __construct()
    {
        $this->yes = new ArrayCollection();
        $this->bookings = new ArrayCollection();
    }



    /**
     * Fonction qui permet d'initialiser le slug (avant persistance et avant la MAJ)
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * 
     */
    public function initializeSlug(){

        //Vérifié si le slug est vide, on le charge avec le nouvel Objet Slugify en passant par le titre 
        if(empty($this->slug)){
            //Création de nouvel Objet Slugify
            $slugify = new Slugify();

            //Avoir le slug(de cet Objet) avec Slugify à partir du titre
            $this->slug = $slugify->slugify($this->title);
        }
        
    }


    //récupérér toutes les dates réservées dans un tableau
    public function getNotAvailableDays(){ 
        //Les mettre dans tableau
        $notAvailableDays = [];

        //On va utiliser la boucle
        foreach ($this->bookings as $booking) {

            //Récupérer dans un tableau l'interval des dates de début d'une réservation et la date de fin de réservation 
            $resultat = range(
               $booking->getStartDate()->getTimestamp(),
               $booking->getEndDate()->getTimestamp(),
               24*60*60
            );

        //Convertir ce timestamp en date toujours dans un tableau pour faciliter la fusion 
        $days = array_map(function($day){
            return new \DateTime(date('Y-m-d',$day));
        },$resultat);
        
        //Fusionner ces 2 tableaux
        $notAvailableDays = array_merge($notAvailableDays,$days);
        }
        //On returne ce tableau 
        return $notAvailableDays;

    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(string $introduction): self
    {
        $this->introduction = $introduction;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(string $coverImage): self
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    public function getRooms(): ?int
    {
        return $this->rooms;
    }

    public function setRooms(int $rooms): self
    {
        $this->rooms = $rooms;

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getYes(): Collection
    {
        return $this->yes;
    }

    public function addYe(Image $ye): self
    {
        if (!$this->yes->contains($ye)) {
            $this->yes[] = $ye;
            $ye->setAd($this);
        }

        return $this;
    }

    public function removeYe(Image $ye): self
    {
        if ($this->yes->removeElement($ye)) {
            // set the owning side to null (unless already changed)
            if ($ye->getAd() === $this) {
                $ye->setAd(null);
            }
        }

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection|Booking[]
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setAd($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getAd() === $this) {
                $booking->setAd(null);
            }
        }

        return $this;
    }
}
