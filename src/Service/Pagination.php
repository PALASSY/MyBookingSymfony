<?php

namespace App\Service;

use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class Pagination
{
  //Il faut : Entity/Limit/Page actuelle initiale/ Manager
  private $entityClass;
  private $limit = 10;
  private $currentPage = 1;
  private $manager; 
  //Il faut également: Route et Environment twig
  private $route;
  private $twig;
  //Il faut: le chemin du template pour être personnalisé
  private $templatePath;

  //On va initialiser l'ObjectManager et l'environment twig et la requête de la route dans URL
  public function __construct(ObjectManager $manager,Environment $twig,RequestStack $request,$templatePath)
  {
      //On va faire la requête de la route dans URL
      $this->route = $request->getCurrentRequest()->attributes->get('_route');
      //dd($this->route);
      $this->manager = $manager;
      $this->twig = $twig;
      //Initialiser ce chemin du template
      $this->templatePath = $templatePath;
  }


  //On va créer une function qui affiche les environement de twig (la page twig et ses éléments)
  public function display()
  {
      //On va attribué à la variable $templatePath le chemin du template
    $this->twig->display($this->templatePath,[
      'route'=>$this->route,
      'page'=>$this->currentPage,
      'pages'=>$this->getPages()
    ]);
  }



  //On va faire le getter et setter de ces methodes private
  //Entity
  public function getEntityClass()
  {
    return $this->entityClass;        
  }
  public function setEntityClass($entityClass)
  {
    $this->entityClass = $entityClass;
    return $this;
  }
  //Limit
  public function getLimit()
  {
    return $this->limit;
  }
  public function setLimit($limit)
  {
    $this->limit=$limit;
    return $this;
  }
  //Page actuelle initiale 
  public function getPage()
  {
    return $this->currentPage;
  }
  public function setPage($page)
  {
    $this->currentPage = $page;
    return $this;
  }
  //La route 
  public function getRoute()
  {
    return $this->route;
  }
  public function setRoute($route)
  {
    $this->route = $route;
    return $this;
  }
  public function getTemplatePath()
  {
    return $this->templatePath;
  }
  public function setTemplatePath($templatePath)
  {
    $this->templatePath = $templatePath;
    return $this;
  }




  //On va calculer le OFFSET pour afficher une Entity personnalisée
  public function getData()
  {
    //Si l'Entity n'est pas renseigné, alors il y aura une erreur
    if (empty($this->entityClass)) {
      throw new \Exception("Le setEntityClass n'a pas été renseigné dans -C-");
    }
    //Calculer offset
    $offset = $this->currentPage * $this->limit - $this->limit;

    //On va récupérer l'Entity(correspond) avec getRepository de Manager 
    $repo = $this->manager->getRepository($this->entityClass);

    //Définir l'affichage personnalisé 
    $data = $repo->findBy([],[],$this->limit,$offset);

    //retourner affichage personnalisé
    return $data;
  }

  //On va calculer le nombre de page par rapport les données de l'Entity pour que le offset soit dynamique
  public function getPages()
  {
    //On va récupérer l'Entity(correspond) avec getRepository de Manager 
    $repo = $this->manager->getRepository($this->entityClass);

    //On va récupérer toutes les données de l'Entity
    $total = count($repo->findAll());

    //Calculer à partir de $total le nombre des pages
    $pages = ceil($total/$this->limit);

    //retourner le nombre des pages
    return $pages;
  }

}