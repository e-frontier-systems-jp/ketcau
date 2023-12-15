<?php

namespace Ketcau\Form\Type\Master;

use Ketcau\Form\Type\MasterType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrefType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => 'Ketcau\Entity\Master\Pref',
            'placeholder' => 'common.select_pref',
        ]);
    }


    public function getBlockPrefix()
    {
        return 'pref';
    }


    public function getParent()
    {
        return MasterType::class;
    }
}