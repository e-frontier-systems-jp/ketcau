<?php

namespace Ketcau\Form\Type\Admin;

use Doctrine\ORM\EntityRepository;
use Ketcau\Entity\PageLayout;
use Ketcau\Form\Type\Master\DeviceTypeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class LayoutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $layout_id = $options['layout_id'];

        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'constraints' => [
                        new Assert\NotBlank(),
                    ],
                ]
            )
            ->add(
                'DeviceType',
                DeviceTypeType::class,
                [
                    'constraints' => [
                        new Assert\NotBlank(),
                    ],
                    'required' => false,
                ]
            )
            ->add(
                'Page',
                EntityType::class,
                [
                    'mapped' => false,
                    'placeholder' => 'common.select',
                    'required' => false,
                    'choice_label' => 'Page.name',
                    'choice_value' => 'page_id',
                    'class' => PageLayout::class,
                    'query_builder' => function (EntityRepository $er) use ($layout_id) {
                        return $er->createQueryBuilder('pl')
                            ->orderBy('pl.page_id', 'ASC')
                            ->where('pl.layout_id = :layout_id')
                            ->setParameter('layout_id', $layout_id);
                    },
                ]
            );

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Ketcau\Entity\Layout',
            'layout_id' => null,
        ]);
    }


    public function getBlockPrefix()
    {
        return 'admin_layout';
    }
}