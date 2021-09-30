<?php

namespace App\Form;

use App\Entity\Booking;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\DataTransformer\FrToDatetimeTransformer;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BookingType extends ApplicationType
{

    //On va lancer automatiquement notre DataTransformer(src/Form/DataTransformer/FrToDatetimeTransformer)
    private $transformer;

    public function __construct(FrToDatetimeTransformer $transformer)
    {
        $this->transformer = $transformer;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate',TextType::class, $this->getConfiguration("Date d'arriver","La date de votre arrivée"))
            ->add('endDate',TextType::class, $this->getConfiguration("Date de votre départ","La date de votre départ"))
            ->add('comment',TextareaType::class, $this->getConfiguration(false, "Ajoutez votre commentaire",["required"=>false]))      
             ;

        //On va récupérer les date début/fin de la réservation et injecter notre propre DataTransformer (dans le formulaire de la réservation)
        $builder->get('startDate')->addModelTransformer($this->transformer);
        $builder->get('endDate')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
