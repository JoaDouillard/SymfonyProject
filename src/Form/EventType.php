<?php

namespace App\Form;

use App\Entity\Artist;
use App\Entity\Event;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir un nom d\'événement']),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères',
                        'max' => 100,
                    ]),
                ],
                'label' => 'Nom',
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir une description']),
                ],
                'label' => 'Description',
                'attr' => ['rows' => 5],
            ])
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir une date']),
                ],
                'label' => 'Date et heure',
            ])
            ->add('location', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir un lieu']),
                    new Length([
                        'min' => 3,
                        'max' => 100,
                    ]),
                ],
                'label' => 'Lieu',
            ])
            ->add('artist', EntityType::class, [
                'class' => Artist::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisir un artiste',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner un artiste']),
                ],
                'label' => 'Artiste',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
