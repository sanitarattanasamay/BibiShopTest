<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class,[
                'disabled'=>true,
                'label' => 'Mon mail'
            ])
            ->add('firstname', TextType::class,[
                'disabled'=>true,
                'label' => 'Mon prénom'
            ])
            ->add('lastname', TextType::class,[
                'disabled'=>true,
                'label' => 'Mon nom'
            ])
            ->add('old_password', PasswordType::class,[
                'label' => 'Mot de passe actuel',
                'mapped'=> false,
                'attr' => [
                    'placeholder'=> 'Mot de passe'
                ]
            ])
            ->add('new_password', RepeatedType::class, [
                'type'=>PasswordType::class,
                'mapped'=> false,
                'constraints'=>new Length([
                    'min'=>6
                ]),
                'invalid_message'=>'Le mot de passe et le la confirmation doivent être identique',
                'required'=>true,
                'first_options'=> [
                    'label'=>'Votre nouveau mot de passe',
                        'attr' =>[
                            'placeholder'=>'Nouveau mot de passe'
                        ]
                    ],
                'second_options'=> [
                    'label'=>'Confirmez votre nouveau mot de passe',
                    'attr' =>[
                        'placeholder'=>'Nouveau mot de passe'
                    ]
                ]
            ])
            ->add('submit', SubmitType::class,[
                'label'=>'Mettre à jour'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
