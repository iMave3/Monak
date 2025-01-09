<?php

// src/Form/TagFormType.php

namespace App\Form;

use App\Entity\Tag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType; // Přidejte FileType pro soubor
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class TagFormType extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options)
{
    $required = $options['imageRequired'];
    $constraints = [];
    if ($required) {
        $constraints = [
            new Image([
                'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG, WEBP).',
                'maxSize' => '5M'
            ]),
        ];
    }

    $builder
        ->add('name', TextType::class)
        ->add('description', TextType::class, [
            'required' => false,
        ])
        ->add('image', FileType::class, [
            'label' => 'Obrázek',
            'required' => $required,
            'mapped' => false,
            'constraints' => $constraints
        ]);
}


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('imageRequired');
        $resolver->setDefaults([
            
        ]);
    }
}
