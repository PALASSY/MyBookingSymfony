<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;



/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"email"},message="Ce mail existe déjà")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Email(message="Veuillez renseignez un email valide")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url()
     */
    private $avatar;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mdphashed;

    /**
     * Confiramtion de Mot de Passe
     * @Assert\EqualTo(propertyPath="mdphashed", message="Les 2 mots de passe ne sont pas identiques")
     */
    public $passwordConfirm;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=10,minMessage="Votre introduction doit faire au moins 10 caractères")
     */
    private $introduction;

    /**
     * @ORM\Column(type="text")
     *  @Assert\Length(min=15,minMessage="Votre introduction doit faire au moins 15 caractères")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=Ad::class, mappedBy="author", orphanRemoval=true)
     */
    private $ads;


    //Concater le nom et prénom 
    public function getFullname(){
        return "{$this->firstname} {$this->lastname}";
    }

    public function __construct()
    {
        $this->ads = new ArrayCollection();
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
           $this->slug = $slugify->slugify($this->firstname.' '.$this->lastname);
       }
       
    }





    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getMdphashed(): ?string
    {
        return $this->mdphashed;
    }

    public function setMdphashed(string $mdphashed): self
    {
        $this->mdphashed = $mdphashed;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    /**
     * @return Collection|Ad[]
     */
    public function getAds(): Collection
    {
        return $this->ads;
    }

    public function addAd(Ad $ad): self
    {
        if (!$this->ads->contains($ad)) {
            $this->ads[] = $ad;
            $ad->setAuthor($this);
        }

        return $this;
    }

    public function removeAd(Ad $ad): self
    {
        if ($this->ads->removeElement($ad)) {
            // set the owning side to null (unless already changed)
            if ($ad->getAuthor() === $this) {
                $ad->setAuthor(null);
            }
        }

        return $this;
    }

    
    
    
    
    /**
     * @see UserInterface
     */
    public function getRoles(){
        return ['ROLE_USER'];
    }

    /**
     * This method is deprecated since Symfony 5.3, implement it from {@link PasswordAuthenticatedUserInterface} instead.
     * @return string|null The hashed password if any
     */
    public function getPassword(): ?string{
        return $this->mdphashed;
    }

    /**
     *@see UserInterface
     * This method is deprecated since Symfony 5.3, implement it from {@link LegacyPasswordAuthenticatedUserInterface} instead.
     * @return string|null The salt
     */
    public function getSalt(){
        //Notre algorythme utilise déjà le Salt
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(){
        //
    }

    /**
     * @return string
     * @deprecated since Symfony 5.3, use getUserIdentifier() instead
     */
    public function getUsername(){
        return $this->email;
    }
   
   


    
    
}
