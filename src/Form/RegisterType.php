<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'label'=> 'Votre prénom',
                'attr' =>[
                    'placeholder'=>'Prénom'
                ]
            ])
            ->add('lastname', TextType::class, [
                'label'=> 'Votre nom',
                'attr' =>[
                    'placeholder'=>'Nom'
                ]
            ])
            ->add('email', EmailType::class, [
                'label'=> 'Votre mail',
                'attr' =>[
                    'placeholder'=>'E-mail'
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type'=>PasswordType::class,
                'constraints'=>new Length([
                    'min'=>6
                ]),
                'invalid_message'=>'Le mot de passe et le la confirmation doivent être identique',
                'required'=>true,
                'first_options'=> [
                    'label'=>'Votre mot de passe',
                        'attr' =>[
                            'placeholder'=>'Mot de passe'
                        ]
                    ],
                'second_options'=> ['label'=>'Confirmez votre mot de passe',
                    'attr' =>[
                        'placeholder'=>'Mot de passe'
                    ]
                ]
            ])
            ->add('submit', SubmitType::class,[
                'label'=>'S\'inscrire'
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
