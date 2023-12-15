<?php

namespace Ketcau\Form\Type\Front;

use Ketcau\Common\KetcauConfig;
use Ketcau\Form\Type\AddressType;
use Ketcau\Form\Type\KanaType;
use Ketcau\Form\Type\NameType;
use Ketcau\Form\Type\PhoneNumberType;
use Ketcau\Form\Type\PostalType;
use Ketcau\Form\Type\RepeatedEmailType;
use Ketcau\Form\Type\RepeatedPasswordType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class SellerEntryType extends AbstractType
{
    protected $ketcauConfig;


    public function __construct(KetcauConfig $ketcauConfig)
    {
        $this->ketcauConfig = $ketcauConfig;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', NameType::class, [
                'required' => true,
            ])
            ->add('kana', KanaType::class, [
                'required' => true,
            ])
            ->add('company_name', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->ketcauConfig['ketcau_stext_len'],
                    ]),
                ],
            ])
            ->add('company_url', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->ketcauConfig['ketcau_stext_len'],
                    ]),
                ],
            ])
            ->add('phone_number', PhoneNumberType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])

            ->add('postal_code', PostalType::class)
            ->add('address', AddressType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])

            ->add('email', RepeatedEmailType::class)
            ->add('plain_password', RepeatedPasswordType::class)
        ;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => '\Customize\Entity\Seller'
        ]);
    }


    public function getBlockPrefix()
    {
        return 'seller_entry';
    }
}