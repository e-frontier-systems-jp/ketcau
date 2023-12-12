<?php

namespace Ketcau\Form\Type\Master;

use Ketcau\Entity\Master\DeviceType;
use Ketcau\Form\Type\MasterType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeviceTypeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => DeviceType::class,
            'label' => 'device.label.type',
            'placeholder' => false,
        ]);
    }


    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return MasterType::class;
    }


    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'device_type';
    }
}