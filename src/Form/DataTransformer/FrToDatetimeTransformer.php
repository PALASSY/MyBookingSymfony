<?php


namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class FrToDatetimeTransformer implements DataTransformerInterface{

    //Données initiales à transformer 
     public function transform($date)
     {
       //Si aucune date à transformer, on return vide 
       if($date === null){
         return "";
       }
       //Sinon on retur la date en format français
       return $date->format('d/m/Y');
     }


    //L'inverse (date en format français en date initiale)
     public function reverseTransform($dateFr)
     {
       //Si aucune date à transformer, on demande à l'utilisateur de fournir une date
       if($dateFr === null){
         throw new TransformationFailedException('Veuillez fournir une date');
       }
       //Sinon on va formater la date avec PHP
       $date = \DateTime::createFromFormat('d/m/Y', $dateFr);

       //Si cette date formatée est faux on lance une erreur
       if($date === false){
         //exception
       }

       //Sinon return la date formaté
       return $date;
     }
}