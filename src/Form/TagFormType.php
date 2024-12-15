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

class TagFormType extends AbstractType
{
// src/Form/TagFormType.php
public function buildForm(FormBuilderInterface $builder, array $options)
{
    $builder
        ->add('name', TextType::class)
        ->add('description', TextType::class, [
            'required' => false,
        ])
        ->add('imagePath', FileType::class, [
            'label' => 'Obrázek',
            'mapped' => false,
            'required' => false,
        ])
        ->add('parentTag', EntityType::class, [
            'class' => Tag::class,
            'choice_label' => 'name',
            'required' => false,
            'placeholder' => 'None',
            'choices' => $options['tags']
        ]);
}


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tag::class,
            'tags' => [], // Doplnění seznamu tagů do formuláře
        ]);
    }
}
