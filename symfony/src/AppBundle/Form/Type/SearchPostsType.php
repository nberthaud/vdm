<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchPostsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('GET')
            ->add('from', DateTimeType::class, [
                'required' =>  false,
                'widget' => 'single_text',
                'model_timezone' => 'UTC',
            ])
            ->add('to', DateTimeType::class, [
                'required' =>  false,
                'widget' => 'single_text',
                'model_timezone' => 'UTC',
            ])
            ->add('author', TextType::class, ['required' =>  false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }
}