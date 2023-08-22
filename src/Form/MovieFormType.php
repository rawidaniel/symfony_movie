<?php

namespace App\Form;

use App\Entity\Movie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MovieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => array(
                    'class' => 'bg-white block border-b-2 w-full h-12 text-xl outline-none',
                    'placeholder'=> 'Enter Title...'
                ),
                'label' => false,
                'required' => false,
            ])
            ->add('releaseYear',IntegerType::class, [
                'attr' => array(
                    'class' => 'bg-white block border-b-2 mt-5 w-full h-12 text-xl outline-none',
                    'placeholder'=> 'Enter Release Year...'
                ),
                'label' => false,
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'attr' => array(
                    'class' => 'bg-white block mt-5 border-b-2 w-full h-20 text-xl outline-none',
                    'placeholder'=> 'Enter Description...'
                ),
                'label' => false,
                'required' => false,
            ])
            ->add('imagePath', FileType::class, [
                'required' => false,
                'mapped' => false
            ])
            // ->add('actors')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}
