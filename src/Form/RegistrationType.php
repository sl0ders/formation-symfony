<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, $this->getConfig("Prenom", "Votre prenom ..."))
            ->add('lastname', TextType::class, $this->getConfig("Nom", "Votre nom de famille ..."))
            ->add('email', EmailType::class, $this->getConfig("email", "Votre Adresse email ..."))
            ->add('picture', UrlType::class, $this->getConfig("photo de profil", "URL de votre avatar ..."))
            ->add('hash',PasswordType::class, $this->getConfig("Mot de passe", "Choisissez un mot de passe ..."))
            ->add('passwordConfirm',PasswordType::class, $this->getConfig("Confirmation Mot de passe", "veuillez confirmer votre mot de passe ..."))
            ->add('introduction', TextType::class,$this->getConfig("Introduction", "Presentez vous en quelque mots ..."))
            ->add('description', TextareaType::class, $this->getConfig("Description détaillée", "c'est le moment de vous présenter en details"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
