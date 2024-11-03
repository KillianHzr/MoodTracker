<?php

namespace App\Form;

use App\Entity\Advice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

class AdviceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('moodLevel', IntegerType::class, [
                'label' => 'Mood Level',
                'constraints' => [
                    new Range(['min' => 1, 'max' => 5]),
                ],
                'attr' => [
                    'min' => 1,
                    'max' => 5,
                ],
            ])
            ->add('emailTemplate', ChoiceType::class, [
                'label' => 'Email Template',
                'choices' => $options['email_templates'],
                'placeholder' => 'Select an email template',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Advice::class,
            'email_templates' => [],
        ]);
    }
}
