<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email',TextType::class, [
                'attr' => [
                    'placeholder' => 'example@ynov.com',
                    'class' => 'form-control'
                ],
                'label' => false
            ])
            ->add('pseudo', TextType::class, [
                'attr' => [
                    'placeholder' => 'Entrez votre pseudo',
                    'class' => 'form-control'
                ],
                'label' => false
            ])
            ->add('date_of_birth', TextType::class, [
                'attr' => [
                    'placeholder' => 'Date de naissance au format JJ/MM/AAAA',
                    'class' => 'form-control'
                ],
                'label' => false
            ])
            ->add('password', PasswordType::class, [
                'attr' => [
                    'placeholder' => 'Mot de passe (5 caractères minimum)',
                    'class' => 'form-control'
                ],
                'label' => false
            ])
            ->add('confirm_password', PasswordType::class, [
                'attr' => [
                    'placeholder' => 'Confirmation du mot de passe',
                    'class' => 'form-control'
                ],
                'label' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
