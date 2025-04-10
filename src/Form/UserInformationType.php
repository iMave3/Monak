<?php

namespace App\Form;

use App\Entity\OrderSummary;
use App\Entity\UserInformation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserInformationType extends AbstractType
{
    // Metoda pro sestavení formuláře
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Přidání pole pro jméno uživatele, povinné pole
            ->add('firstName', TextType::class, [
                'label' => 'Jméno *',  // Popis pro toto pole
                'constraints' => [new NotBlank()]  // Přidání validace, aby pole nebylo prázdné
            ])
            // Přidání pole pro příjmení uživatele, povinné pole
            ->add('lastName', TextType::class, [
                'label' => 'Příjmení *',
                'constraints' => [new NotBlank()]  // Přidání validace pro povinné pole
            ])
            // Přidání pole pro e-mail uživatele, povinné pole
            ->add('mail', EmailType::class, [
                'label' => 'E-mail *',  // Popis pro e-mailové pole
                'constraints' => [new NotBlank()]  // E-mail musí být vyplněn
            ])
            // Přidání pole pro telefonní číslo, povinné pole
            ->add('phoneNumber', NumberType::class, [
                'label' => 'Telefonní číslo *',
                'constraints' => [new NotBlank()]  // Validace pro povinné telefonní číslo
            ])
            // Přidání pole pro město, povinné pole
            ->add('town', TextType::class, [
                'label' => 'Město *',
                'constraints' => [new NotBlank()]  // Validace pro povinné město
            ])
            // Přidání pole pro ulici, povinné pole
            ->add('street', TextType::class, [
                'label' => 'Ulice *',
                'constraints' => [new NotBlank()]  // Validace pro povinnou ulici
            ])
            // Přidání pole pro číslo popisné, povinné pole
            ->add('houseNumber', NumberType::class, [
                'label' => 'Číslo popisné *',
                'constraints' => [new NotBlank()]  // Validace pro povinné číslo popisné
            ])
            // Přidání pole pro PSČ, povinné pole
            ->add('postcode', NumberType::class, [
                'label' => 'PSČ *',  // Popis pro PSČ
                'constraints' => [new NotBlank()]  // PSČ musí být vyplněno
            ]);
    }

    // Nastavení výchozích hodnot pro formulář
    public function configureOptions(OptionsResolver $resolver): void
    {
        // Tato možnost říká, že data formuláře budou mapována na entitu UserInformation
        $resolver->setDefaults([
            'data_class' => UserInformation::class,
        ]);
    }
}
