<?php

// src/Form/ProductFormType.php

namespace App\Form;

use App\Entity\Tag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class ProductFormType extends AbstractType
{
    // Metoda pro sestavení formuláře
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Získání hodnoty, zda je obrázek povinný
        $required = $options['imageRequired'];

        // Definování validace pro obrázek, pokud je povinný
        $constraints = [];
        if ($required) {
            $constraints = [
                new Image([  // Validace pro obrázek (JPEG, PNG, WEBP, max velikost 5MB)
                    'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                    'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG, WEBP).',
                    'maxSize' => '5M'
                ]),
            ];
        }

        // Sestavení formuláře
        $builder
            // Přidání pole pro název produktu
            ->add('name', TextType::class, [
                'label' => 'Název produktu'
            ])
            // Přidání pole pro informaci, zda je produkt skladem
            ->add('available', CheckboxType::class, [
                'label' => 'Skladem', 
                'required' => false // Pole není povinné
            ])
            // Přidání pole pro obrázek, pokud je povinný, použije se validace pro obrázek
            ->add('image', FileType::class, [
                'label' => 'Obrázek',
                'required' => $required,  // Určí, zda je obrázek povinný
                'mapped' => false,        // Tento formulářový prvek není propojen s entitou (nebude mapován na vlastnost)
                'constraints' => $constraints  // Při povinném obrázku přidáváme validaci
            ])
            // Přidání pole pro cenu produktu
            ->add("price", NumberType::class, [
                "label" => "Cena"  // Nastavení labelu pro cenu
            ]);
    }

    // Nastavení výchozích hodnot pro formulář
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('imageRequired');  // Toto pole je povinné pro volbu, zda je obrázek povinný
        $resolver->setDefaults([  // V tomto případě nevyplňujeme žádné výchozí hodnoty
        ]);
    }
}
