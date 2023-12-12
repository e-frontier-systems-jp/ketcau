<?php

namespace Ketcau\Form\Type\Front;

use Ketcau\Common\KetcauConfig;
use Ketcau\Form\Type\KanaType;
use Ketcau\Form\Type\NameType;
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