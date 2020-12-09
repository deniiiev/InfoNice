<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('message', TextareaType::class,[
                'attr' => [
                    'class' => 'md-autosizer',
                    'placeholder' => 'Написать комментарий',
                    'rows' => 2
                ],
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('anonymous', CheckboxType::class, [
                'label' => 'Анонимно',
                'required' => false,
                'label_attr' => ['class' => 'switch-custom']
            ])
            ->add('replyTo', HiddenType::class, [
                'mapped' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
