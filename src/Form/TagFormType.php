<?php

// src/Form/TagFormType.php

namespace App\Form;

use App\Entity\Tag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType; // Přidání typu pro nahrání souboru
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class TagFormType extends AbstractType
{
    // Metoda pro sestavení formuláře
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Získání hodnoty 'imageRequired' z možností předaných při vytváření formuláře
        $required = $options['imageRequired'];

        // Inicializace pole pro validace
        $constraints = [];
        if ($required) {
            // Pokud je obrázek povinný, přidáme validace pro obrázky
            $constraints = [
                new Image([  // Validace pro obrázek (JPEG, PNG, WEBP, max velikost 5MB)
                    'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                    'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG, WEBP).',
                    'maxSize' => '5M'  // Maximální velikost obrázku je 5MB
                ]),
            ];
        }

        // Sestavení formuláře s jednotlivými poli
        $builder
            // Přidání pole pro název kategorie
            ->add('name', TextType::class, [
                "label" => "Název kategorie"
            ])
            // Přidání pole pro popis kategorie, není povinné
            ->add('description', TextType::class, [
                "label" => "Popisek",
                'required' => false,  // Pole není povinné
            ])
            // Přidání pole pro nahrání obrázku, pokud je povinný, přidáváme validaci
            ->add('image', FileType::class, [
                'label' => 'Obrázek',  // Popis pro obrázek
                'required' => $required,  // Určuje, zda je obrázek povinný
                'mapped' => false,        // Toto pole není propojeno s entitou (nebude mapováno)
                'constraints' => $constraints  // Pokud je obrázek povinný, přidáváme validace
            ]);
    }

    // Nastavení výchozích hodnot pro formulář
    public function configureOptions(OptionsResolver $resolver)
    {
        // Nastavení, že při vytváření formuláře je nutné specifikovat, zda je obrázek povinný
        $resolver->setRequired('imageRequired');
        $resolver->setDefaults([  // V tomto formuláři nejsou nastavovány žádné výchozí hodnoty
        ]);
    }
}
