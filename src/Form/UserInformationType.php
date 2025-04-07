<?php

namespace App\Form;

use App\Entity\OrderSummary;
use App\Entity\UserInformation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserInformationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Křestní jméno',
                'constraints' => [
                    new NotBlank()
                ],
                'attr' => [
                    'class' => ''
                ]
            ])
            ->add('lastName', TextType::class, ['label' => 'Křestní jméno'])
            ->add('mail', EmailType::class)
            ->add('phoneNumber', null , [

            ])
            ->add('town')
            ->add('street')
            ->add('houseNumber')
            ->add('postcode')
    
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserInformation::class,
        ]);
    }
}
