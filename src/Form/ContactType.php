<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    // Metoda pro vytvoření formuláře
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Přidání pole pro email uživatele
        $builder
            ->add('email', EmailType::class, [
                'label' => "Napište nám!", // Zobrazí se jako label nad polem
                'attr' => [
                    'placeholder' => 'Váš email', // Placeholder pro email
                ],
            ])
            // Přidání pole pro předmět zprávy
            ->add('subject', TextType::class, [
                'label' => false, // Není zobrazen label
                'attr' => [
                    'placeholder' => 'Předmět zprávy', // Placeholder pro předmět
                ],
            ])
            // Přidání pole pro zprávu, které je textovým polem
            ->add('message', TextareaType::class, [
                'label' => false, // Není zobrazen label
                'attr' => [
                    'placeholder' => 'Zadejte Vaši zprávu', // Placeholder pro zprávu
                ],
                'constraints' => [
                    new NotBlank() // Zajišťuje, že zpráva nebude prázdná
                ]
            ]);
    }

    // Metoda pro konfiguraci výchozích možností pro tento formulář
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Zde můžete přidat další volby pro formulář, pokud je potřeba
        ]);
    }
}
