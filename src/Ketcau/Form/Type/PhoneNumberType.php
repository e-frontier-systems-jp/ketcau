<?php

namespace Ketcau\Form\Type;

use Ketcau\Common\KetcauConfig;
use Ketcau\Form\EventListener\ConvertKanaListener;
use Ketcau\Form\EventListener\TruncateHyphenListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class PhoneNumberType extends AbstractType
{
    public function __construct(
        protected KetcauConfig $ketcauConfig
    ){}


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new ConvertKanaListener());
        $builder->addEventSubscriber(new TruncateHyphenListener());
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setNormalizer('constraints',function ($options, $value) {
            $constraints = [];

            if (isset($options['required']) && true === $options['required']) {
                $constraints[] = new Assert\NotBlank();
            }

            $constraints[] = new Assert\Length([
                'max' => $this->ketcauConfig['ketcau_tel_len_max'],
            ]);

            $constraints[] = new Assert\Type([
                'type' => 'digit',
                'message' => 'form_error.numeric_only',
            ]);

            return array_merge($constraints, $value);
        });

        $resolver->setDefaults([
            'attr' => [
                'placeholder' => 'common.phone_number_sample'
            ],
            'trim' => true,
        ]);
    }


    public function getParent()
    {
        return TelType::class;
    }


    public function getBlockPrefix()
    {
        return 'phone_number';
    }
}