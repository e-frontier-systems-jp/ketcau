<?php

namespace Ketcau\Form\Type;

use Ketcau\Common\KetcauConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RepeatedEmailType extends AbstractType
{
    public function __construct(
        protected KetcauConfig $ketcauConfig
    ){}



    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'entry_type' => EmailType::class,
            'required' => true,
            'invalid_message' => 'form_error.same_email',
            'options' => [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(null, null, 'strict'),
                    new Assert\Length([
                        'max' => $this->ketcauConfig['ketcau_email_len'],
                    ]),
                ]
            ],
            'first_options' => [
                'attr' => [
                    'placeholder' => 'common.email_address_sample',
                ],
            ],
            'second_options' => [
                'attr' => [
                    'placeholder' => 'common.repeated_confirm',
                ],
            ],
            'error_bubbling' => false,
            'trim' => true,
            'error_mapping' => function (Options $options) {
                return ['.' => $options['second_name']];
            },
        ]);
    }


    public function getParent()
    {
        return RepeatedType::class;
    }


    public function getBlockPrefix()
    {
        return 'repeated_email';
    }
}