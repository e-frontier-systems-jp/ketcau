<?php

namespace Ketcau\Form\Type;

use Ketcau\Common\KetcauConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RepeatedPasswordType extends AbstractType
{
    public function __construct(
        protected KetcauConfig $ketcauConfig
    ){}


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'type' => TextType::class,
                'invalid_message' => 'form_error.same_password',
                'required' => true,
                'error_bubbling' => false,
                'options' => [
                    'constraints' => [
                        new Assert\Length([
                            'min' => $this->ketcauConfig['ketcau_password_min_len'],
                            'max' => $this->ketcauConfig['ketcau_password_max_len'],
                        ]),
                        new Assert\Regex([
                            'pattern' => $this->ketcauConfig['ketcau_password_pattern'],
                            'message' => 'form.error.password_pattern_invalid',
                        ]),
                    ],
                ],
                'first_options' => [
                    'attr' => [
                        'placeholder' => trans('common.password_sample', [
                            '%min%' => $this->ketcauConfig['ketcau_password_min_len'],
                            '%max%' => $this->ketcauConfig['ketcau_password_max_len'],
                        ]),
                    ],
                ],
                'second_options' => [
                    'attr' => [
                        'placeholder' => trans('common.repeated_confirm'),
                    ],
                    'constraints' => [
                        new Assert\NotBlank(),
                    ],
                ],
            ]);
    }


    public function getParent()
    {
        return RepeatedType::class;
    }


    public function getBlockPrefix()
    {
        return 'repeated_password';
    }
}