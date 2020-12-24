<?php

namespace App\Form;

use App\Entity\Profile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('avatarFile', VichImageType::class, [
                'label' => 'form.avatar',
                'required' => false,
                'download_uri' => false,
                'image_uri' => false,
                'allow_delete' => false
            ])
            ->add('about', TextareaType::class, [
                'label' => 'form.about.me',
                'required' => false,
                'attr' => [
                    'style' => 'opacity:0;margin-bottom:20px',
                    'class' => 'ckeditor',
                    'rows' => 10
                ],
                'constraints' => [
                    new Length([
                        'max' => 800,
                        'maxMessage' => 'form.max.message'
                    ])
                ]
            ])
            ->add('url', UrlType::class, [
                'label' => 'form.website',
                'required' => false
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'form.gender',
                'expanded' => true,
                'label_attr' => ['class' => 'radio-custom'],
                'choices' => [
                    'form.gender.male' => '0',
                    'form.gender.female' => '1'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'form.gender.required.message'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Profile::class,
        ]);
    }
}
