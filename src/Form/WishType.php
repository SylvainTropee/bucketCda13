<?php

namespace App\Form;

use App\Entity\Wish;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class WishType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('author')
            ->add('wishImage', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'constraints' => [
                    new Image(
                        maxSize: '1M',
                        maxSizeMessage: "L'image ne doit pas dépasser 1 Mo",
                        extensions: ['png', 'jpg'],
                        extensionsMessage: "Les types autorisés sont .png et .jpg"
                    )
                ]
            ])
            ->add("isPublished", CheckboxType::class, [
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Wish::class,
        ]);
    }
}
