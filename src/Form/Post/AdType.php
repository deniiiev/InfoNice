<?php

namespace App\Form\Post;

use App\Entity\Category;
use App\Entity\Post;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class AdType extends AbstractType
{
    private $categories;

    public function __construct(CategoryRepository $categories)
    {
        $this->categories = $categories;
    }

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
                'label' => 'Категории',
                'class' => Category::class,
                'required' => false,
                'multiple' => true,
                'choice_label' => 'title',
                'choices' => $this->categories->findBy(['ads' => true]),
                'label_attr' => ['class' => 'checkbox-custom'],
                'attr' => [
                    'data-placeholder' => 'Выберите категории',
                    'class' => 'chosen'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Описание',
                'required' => false,
                'attr' => [
                    'class' => 'ckeditor'
                ]
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Цена',
                'required' => false
            ])
            ->add('priceType', ChoiceType::class, [
                'choices' => [
                    '-' => null,
                    'В час' => 'perhour',
                    'В день' => 'perday',
                    'В неделю' => 'perweek',
                    'В месяц' => 'permonth',
                    'За шт.' => 'apiece',
                    'За кг.' => 'perkilogram',
                    'За л.' => 'perliter',
                ]
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
