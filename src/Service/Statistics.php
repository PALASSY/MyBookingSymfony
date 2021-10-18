<?php
namespace App\Service;

use Doctrine\Persistence\ObjectManager;

class Statistics  
{
  private $manager;

  public function __construct(ObjectManager $manager) {
    $this->manager = $manager;
  }

  //On va réunir tous ces résultats ci-dessous avec compact
  public function getStatistics()
  {
    $users = $this->getUsersCount();
    $ads = $this->getAdsCount();
    $comments = $this->getCommentsCount();
    $bookings = $this->getBookingsCount();
    return compact('users','ads','comments','bookings');
  }

  //On va récupérer le nombres des utilisateurs
  public function getUsersCount()
  {
    return $this->manager->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
  }

  //On va récupérer le nombres des annonces
  public function getAdsCount()
  {
    return $this->manager->createQuery('SELECT COUNT(a) FROM App\Entity\Ad a')->getSingleScalarResult();
  }

  //On va récupérer le nombres des commentaires
  public function getCommentsCount()
  {
    return $this->manager->createQuery('SELECT COUNT(c) FROM App\Entity\Comment c')->getSingleScalarResult();
  }

  //On va récupérer le nombres des réservation
  public function getBookingsCount()
  {
    return $this->manager->createQuery('SELECT COUNT(b) FROM App\Entity\Booking b')->getSingleScalarResult();
  }

  //On va récupérer également les meilleures et pires note des commentaires dans l'annonce
  //Remarque: ici la seule diffférence c'est (DESC/ASC) alors on va le passer en argument(param)
  //Remarque il faut mettre un espace entre ORDER BY note et (') sinon c'est considèré concaténation de note et $direction note.$direction 
  public function getAdsStats($direction)
  {
    return $this->manager->createQuery(
      'SELECT AVG(c.rating) as note,
      a.title,
      u.firstname,
      u.lastname,
      u.avatar
      FROM App\Entity\Comment c
      JOIN c.ad a
      JOIN a.author u
      GROUP BY a
      ORDER BY note ' .$direction)
      ->setMaxResults(5)
      ->getResult();

  }

}
