<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RegistrationType extends ApplicationType
{

    //Pour éviter la répétition de la personnalisation d'un label et de placeholder de chaque champ, on va créer une function privée   puis on rajoute un param pour indiquer que un champ peu ne pas required(en l'occurence ici le Slug) puis documenter que le param est un array et il s'appelle $options
    /**
     * @see src/Form/ApplicationType.php
     */


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname',TextType::class, $this->getConfiguration('Nom', 'Mettez votre nom'))
            ->add('lastname',TextType::class, $this->getConfiguration('Prenom', 'Mettez votre prénom'))
            ->add('email',EmailType::class, $this->getConfiguration('Email', 'Mettez un email valide'))
            ->add('avatar', UrlType::class, $this->getConfiguration('Avatar', 'URL de votre avatar'))
            ->add('mdphashed', PasswordType::class, $this->getConfiguration('Mot de passe', 'Mettez votre mot de passe'))
            ->add('passwordConfirm', PasswordType::class, $this->getConfiguration('Confiramtion de MDP', 'Veuillez confirmer votre mot de passe!!'))
            ->add('introduction',TextType::class, $this->getConfiguration('Introduction', 'Mettez une introduction'))
            ->add('description',TextareaType::class, $this->getConfiguration('Description', 'Decrivez ce que vous voulez'))
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
