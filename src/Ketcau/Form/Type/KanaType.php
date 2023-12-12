<?php

namespace Ketcau\Form\Type;

use Ketcau\Common\KetcauConfig;
use Ketcau\Form\EventListener\ConvertKanaListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class KanaType extends AbstractType
{
    protected $ketcauConfig;


    public function __construct(KetcauConfig $ketcauConfig)
    {
        $this->ketcauConfig = $ketcauConfig;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new ConvertKanaListener('CV'));
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'lastname_options' => [
                'attr' => [
                    'placeholder' => 'common.last_name_kana',
                ],
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^[ァ-ヶｦ-ﾟー]+$/u',
                        'message' => 'form_error.kana_only',
                    ]),
                    new Assert\Length([
                        'max' => $this->ketcauConfig['ketcau_kana_len'],
                    ]),
                ]
            ],
            'firstname_options' => [
                'attr' => [
                    'placeholder' => 'common.firstname_kana',
                ],
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^[ァ-ヶｦ-ﾟー]+$/u',
                        'message' => 'form_error.kana_only',
                    ]),
                    new Assert\Length([
                        'max' => $this->ketcauConfig['ketcau_kana_len'],
                    ]),
                ]
            ],
        ]);
    }


    public function getParent()
    {
        return NameType::class;
    }


    public function getBlockPrefix()
    {
        return 'kana';
    }
}