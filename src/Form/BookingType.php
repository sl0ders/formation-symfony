<?php

namespace App\Form;

use App\Entity\Booking;
use App\Form\DataTransformer\FrenchToDateTimeTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends ApplicationType
{
    private $transformer;

    public function __construct(FrenchToDateTimeTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startTime',
                textType::class,
                $this->getConfig(
                    "Date d'arrivé",
                    "La date a l'aquelle vous comptez arriver"))
            ->add('endDate',
                textType::class,
                $this->getConfig(
                    "Date de depart",
                    "La date a l'aquelle vous quittez les lieux"))
            ->add('comment',
                TextareaType::class,
                $this->getConfig(
                    false,
                    "Si vous avez un commentaire n'hésité pas a en faire part !",
                    ["required" => false]
                ));
        $builder->get('startTime')->addModelTransformer($this->transformer);
        $builder->get('endDate')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
