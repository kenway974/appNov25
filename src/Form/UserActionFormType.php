<?php

namespace App\Form;

use App\Entity\UserAction;
use App\Entity\Action;
use App\Entity\UserNeed;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserActionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder// Champ booléen isRecurring
            ->add('isRecurring', HiddenType::class, [
                'data' => $options['data']->isRecurring() ? 1 : 0, 
                'mapped' => true, 
            ])

            // Deadline (date, nullable)
            ->add('deadline', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'html5' => true,
            ])

            // StartDate (date, nullable)
            ->add('startDate', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'html5' => true,
            ])

            // Frequency (int, nullable)
            ->add('frequency', IntegerType::class, [
                'required' => false,
                'label' => 'Fréquence (en jours)',
                'attr' => [
                    'min' => 1,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserAction::class,
        ]);
    }
}
