<?php


namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class EditUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => false,
            ])
            ->add('fullname', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => false,
            ])
            ->add('birthday', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => false,
            ])
            ->add('bio', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => false,
                'required' => false,
            ])
            ->add('socialUrl', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => false,
                'required' => false,
            ])
        ;
    }
}
