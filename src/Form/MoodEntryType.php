<?php

namespace App\Form;

use App\Entity\MoodEntry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MoodEntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('mood', IntegerType::class, [
                'required' => true,
                'label' => 'Votre humeur (1-5)',
                'attr' => [
                    'min' => 1,
                    'max' => 5,
                ],
            ])
            ->add('note', TextareaType::class, [
                'required' => true,
                'label' => 'Ajouter une note',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MoodEntry::class,
        ]);
    }
}
