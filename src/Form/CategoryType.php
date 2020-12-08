<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class,[
                'label' => 'Заголовок'
            ])
            ->add('news',CheckboxType::class,[
                'label'=>'news',
                'translation_domain' => 'forms',
                'required' => false,
                'label_attr' => ['class' => 'checkbox-custom']
            ])
            ->add('ads',CheckboxType::class,[
                'label'=>'ads',
                'translation_domain' => 'forms',
                'required' => false,
                'label_attr' => ['class' => 'checkbox-custom']
            ])
            ->add('questions',CheckboxType::class,[
                'label'=>'questions',
                'translation_domain' => 'forms',
                'required' => false,
                'label_attr' => ['class' => 'checkbox-custom']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
