<?php

namespace App\Form;

use App\Entity\Jeux;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class JeuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre',  TextType::class, [
                'attr' => [
                    'placeholder' => 'Titre du jeu',
                    'class' => 'form-control'
                ],
                'label' => false
            ])
            ->add('image', TextType::class, [
                'attr' => [
                    'placeholder' => 'URL',
                    'class' => 'form-control'
                ],
                'label' => false
            ])
            ->add('prix', TextType::class, [
                'attr' => [
                    'placeholder' => 'Prix du jeu',
                    'class' => 'form-control'
                ],
                'label' => false
            ])
            ->add('details', TextType::class, [
                'attr' => [
                    'placeholder' => 'Détails du jeu...',
                    'class' => 'form-control'
                ],
                'label' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Jeux::class,
        ]);
    }
}
