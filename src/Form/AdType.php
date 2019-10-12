<?php

namespace App\Form;

use App\Entity\Ad;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options = [])
    {
        $builder
            ->add(
                'title',
                TextType::class,
                $this->getConfig(
                    "Titre",
                    "Titre de l'annonce..."
                ))
            ->add(
                'slug',
                TextType::class,
                $this->getConfig(
                    "Adresse web",
                    "URL de l'adresse web (autotmatique)",
                    ['required' => false]
                ))
            ->add(
                'coverImage',
                UrlType::class,
                $this->getConfig(
                    "Url de l'image",
                    "Url de l'image..."
                ))
            ->add(
                'introduction',
                TextType::class,
                $this->getConfig(
                    "Introduction",
                    "Introduction de l'annonce..."
                ))
            ->add(
                'content',
                TextareaType::class,
                $this->getConfig("Contenu de l\'aticle",
                    "Contenu de l'annonce..."
                ))
            ->add(
                'rooms',
                IntegerType::class,
                $this->getConfig(
                    "Nombre de chambre",
                    "Precisez le nombre de chambre dans le biens"
                ))
            ->add(
                'price',
                MoneyType::class,
                $this->getConfig(
                    "Prix par nuit",
                    "Indiquez le prix d'une nuit"
                ))
            ->add(
                'images',
                CollectionType::class,
                [
                    'entry_type' => ImageType::class,
                    'allow_add' => true,
                    'allow_delete' => true
                ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
