<?php 

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('query', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Hledat...',
                    'class' => 'search-input',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Hledat',
                'attr' => ['class' => 'search-button'],
            ]);
    }
}
