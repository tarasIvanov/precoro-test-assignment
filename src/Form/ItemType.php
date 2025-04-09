<?php

namespace App\Form;

use App\Entity\Item;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class ItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Назва товару не може бути порожньою']),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Назва товару повинна містити щонайменше {{ limit }} символи',
                        'maxMessage' => 'Назва товару не може бути довшою за {{ limit }} символів',
                    ]),
                ],
            ])
            ->add('price', MoneyType::class, [
                'currency' => 'UAH',
                'constraints' => [
                    new NotBlank(['message' => 'Ціна товару не може бути порожньою']),
                    new Positive(['message' => 'Ціна товару повинна бути додатнім числом']),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Item::class,
        ]);
    }
}
