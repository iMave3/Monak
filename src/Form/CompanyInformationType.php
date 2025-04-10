<?php

namespace App\Form;

use App\Entity\CompanyInformation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyInformationType extends AbstractType
{
    // Metoda pro vytvoření formuláře
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Přidání pole pro název společnosti (companyName)
        $builder
            ->add('companyName', TextType::class, [
                'label' => 'Název společnosti', // Label zobrazený u pole
                'required' => false, // Pole není povinné
            ])
            // Přidání pole pro IČO (ico), které musí být číslo
            ->add('ico', NumberType::class, [
                'label' => 'IČO', // Label pro pole
                'required' => false, // Pole není povinné
            ])
            // Přidání pole pro DIČ (dic), textové pole pro zadání DIČ
            ->add('dic', TextType::class, [
                'label' => 'DIČ', // Label pro pole
                'required' => false, // Pole není povinné
            ]);
    }

    // Metoda pro nastavení výchozích hodnot formuláře
    public function configureOptions(OptionsResolver $resolver): void
    {
        // Nastavení výchozího data_class pro tento formulář
        $resolver->setDefaults([
            'data_class' => CompanyInformation::class, // Třída, kterou tento formulář obsluhuje (CompanyInformation)
        ]);
    }
}
