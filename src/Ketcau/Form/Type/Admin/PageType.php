<?php

namespace Ketcau\Form\Type\Admin;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('layout', EntityType::class, [
                'label' => false,
                'class' => 'Ketcau\Entity\Page',
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er) {
                    return $er
                        ->createQueryBuilder('l')
                        ->where('l.id <> 0')
                        ->orderBy('l.id', 'ASC');
                },
            ]);
    }


    public function getBlockPrefix(): string
    {
        return 'admin_page';
    }
}