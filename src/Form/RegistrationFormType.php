<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
    
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Email()
                ],
                'label' => false,
                'attr' => [
                    'placeholder' => 'Zadejte Váš email...',
                    'autocomplete' => 'off'
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('firstName', TextType::class, [
                'constraints' => [
                    new NotBlank()
                ],
                'label' => false,
                'attr' => [
                    'placeholder' => 'Zadejte Vaše jméno...',
                    'autocomplete' => 'off'
                ]
            ])
            ->add('lastName', TextType::class, [
                'constraints' => [
                    new NotBlank()
                ],
                'label' => false,
                'attr' => [
                    'placeholder' => 'Zadejte Vaše příjmení...',
                    'autocomplete' => 'off'
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'label' => false
                ],
                'first_options' => [
                    'attr' => [
                        'placeholder' => 'Zadejte heslo...',
                        'autocomplete' => 'new-password'
                    ]
                ],
                'second_options' => [
                    'attr' => [
                        'placeholder' => 'Zadejte heslo znovu...',
                        'autocomplete' => 'new-password'
                    ]
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 8,
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            
        ]);
    }
}
