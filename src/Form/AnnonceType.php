<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class AnnonceType extends AbstractType
{
    //Pour éviter la répétition de la personnalisation d'un label et de placeholder de chaque champ, on va créer une function privée   puis on rajoute un 3èm param pour indiquer que un champ peu ne pas required(en l'occurence ici le Slug) puis documenter que le param est un array et il s'appelle $options
    /**
     * Configuration de base de chaque champ
     * @param string $label
     * @param string $placeholder
     * @param array $options
     * @return array
     */
    private function getConfiguration($label, $placeholder,$options=[])
    {
        #Comme il y a 2 tabeaux à returner alors on utilise la methode array_merge()
        return array_merge([
                            'label' => $label,
                            'attr' => ['placeholder' => $placeholder]
                            ],
                             $options);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //Personnalisation de chaque champ d'un label et de placeholder
            ->add('title', TextType::class, $this->getConfiguration('Titre', 'Mettez un titre à votre annonce'))
            ->add('slug', TextType::class, $this->getConfiguration('slug', 'Personnalisez un alias pour générer un URL',['required'=>false]))
            //Le FileType c'est pour télécharger un fichier ou une image
            ->add('coverImage', UrlType::class, $this->getConfiguration('Image de couverture', 'Insérez une image'))
            ->add('introduction', TextType::class, $this->getConfiguration('Résumé', 'Présentez votre bien'))
            ->add('content', TextareaType::class, $this->getConfiguration('Description détaillée', 'Decrivez vos services'))
            ->add('rooms', IntegerType::class, $this->getConfiguration('Nombre de chambre', 'Decrivez le nombre de chambre'))
            ->add('price', MoneyType::class, $this->getConfiguration('Prix', 'Decrivez le prix/nuit'))
            ->add('yes', CollectionType::class, ['entry_type' => ImageType::class, 'allow_add' => true,'allow_delete'=>true])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
