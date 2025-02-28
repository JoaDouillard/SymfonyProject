<?php

namespace App\Form;

use App\Entity\Artist;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ArtistType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir un nom d\'artiste']),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères',
                        'max' => 50,
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
            ]);
        // L'ajout de l'image sera implémenté plus tard avec VichUploaderBundle
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Artist::class,
        ]);
    }
}
