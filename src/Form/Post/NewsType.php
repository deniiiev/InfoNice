<?php

namespace App\Form\Post;

use App\Entity\Category;
use App\Entity\Post;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class NewsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imageFile', VichImageType::class, [
                'label' => 'Изображение',
                'required' => false,
                'download_uri' => false,
                'image_uri' => false,
                'allow_delete' => false
            ])
            ->add('title', TextType::class, [
                'label' => 'Заголовок'
            ])
            ->add('categories', EntityType::class, [
                'label' => 'Раздел',
                'class' => Category::class,
                'required' => false,
                'multiple' => true,
                'choice_label' => 'title',
                'label_attr' => ['class' => 'checkbox-custom'],
                'attr' => [
                    'data-placeholder' => 'Выберите раздел',
                    'class' => 'chosen'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Описание',
                'required' => false
            ])
            ->add('anonymous', CheckboxType::class, [
                'label' => 'Анонимный пост',
                'required' => false,
                'label_attr' => ['class' => 'switch-custom']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
