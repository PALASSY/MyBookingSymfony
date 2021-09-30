<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;

class ApplicationTYpe extends AbstractType
{
    //Pour éviter la répétition de la personnalisation d'un label et de placeholder de chaque champ, on va créer une function privée   puis on rajoute un 3èm   param pour indiquer que un champ peut ne pas required(en l'occurence ici le Slug) puis documenter que le param est un array et il s'appelle $options
  /**
   * Configuration de base de chaque champ
   * @param string $label
   * @param string $placeholder
   * @param array $options
   * @return array
   */
  protected function getConfiguration($label, $placeholder,$options=[])
  {
      #Comme il y a 2 tabeaux à returner alors on utilise la methode array_merge()
      return array_merge([
                          'label' => $label,
                          'attr' => ['placeholder' => $placeholder]
                          ],
                           $options);
  }
}