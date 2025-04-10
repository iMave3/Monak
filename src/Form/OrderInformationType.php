<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderInformationType extends AbstractType
{
    // Metoda pro sestavení formuláře
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Přidání formuláře pro uživatelské informace
            ->add('userInformation', UserInformationType::class, [
                'label' => false, // Skrytí labelu pro tento podformulář
            ])
            // Přidání formuláře pro firemní informace (volitelné)
            ->add('companyInformation', CompanyInformationType::class, [
                'label' => false, // Skrytí labelu pro tento podformulář
                'required' => false // Označení tohoto pole jako volitelného
            ])
            // Přidání tlačítka pro odeslání formuláře
            ->add('submit', SubmitType::class, [
                'label' => 'Pokračovat', // Text na tlačítku
            ])
        ;
    }

    // Metoda pro nastavení výchozích voleb pro formulář
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Zde nejsou přidány žádné speciální výchozí volby
        ]);
    }
}
