<?php

namespace App\Form;

use App\Entity\Need;
use App\Entity\UserNeed;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManagerInterface;

class UserNeedFormType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('priority', IntegerType::class, [
                'attr' => [
                    'type' => 'range',
                    'min' => 5,
                    'max' => 10,
                    'class' => 'form-range',
                    'style' => 'cursor:pointer;',
                ],
                'label' => 'Comment évaluer ce besoin ?',
            ])
            ->add('need', HiddenType::class);

        $builder->get('need')
            ->addModelTransformer(new CallbackTransformer(
                function ($need) {
                    return $need ? $need->getId() : '';
                },
                function ($needId) {
                    if (!$needId) {
                        return null;
                    }
                    return $this->entityManager->getRepository(Need::class)->find($needId);
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserNeed::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'user_need_form',
        ]);
    }
}
