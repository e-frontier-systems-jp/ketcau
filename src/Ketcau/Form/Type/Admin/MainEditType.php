<?php

namespace Ketcau\Form\Type\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Ketcau\Common\KetcauConfig;
use Ketcau\Entity\Layout;
use Ketcau\Entity\Master\DeviceType;
use Ketcau\Entity\Page;
use Ketcau\Form\Validator\TwigLint;
use Ketcau\Repository\Master\DeviceTypeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class MainEditType extends AbstractType
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected DeviceTypeRepository $deviceTypeRepository,
        protected KetcauConfig $ketcauConfig
    ){}


    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'max' => $this->ketcauConfig['ketcau_stext_len'],
                    ]),
                ],
            ])
            ->add('url', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'max' => $this->ketcauConfig['ketcau_stext_len'],
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^([0-9a-zA-Z_\-]+\/?)+(?<!\/)$/',
                    ]),
                ],
            ])
            ->add('file_name', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'max' => $this->ketcauConfig->get('ketcau_stext_len'),
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^([0-9a-zA-Z_\-]+\/?)+$/'
                    ])
                ],
            ])
            ->add('data', TextareaType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new TwigLint(),
                ],
            ])
            ->add('author', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->ketcauConfig['ketcau_stext_len'],
                    ]),
                ],
            ])
            ->add('description', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->ketcauConfig['ketcau_stext_len'],
                    ]),
                ],
            ])
            ->add('keyword', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->ketcauConfig['ketcau_stext_len'],
                    ]),
                ],
            ])
            ->add('meta_robots', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->ketcauConfig['ketcau_stext_len'],
                    ]),
                ],
            ])
            ->add('meta_tags', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->ketcauConfig['ketcau_ltext_len'],
                    ]),
                ],
            ])
            
            ->add('PcLayout', EntityType::class, [
                'mapped' => false,
                'placeholder' => '---',
                'required' => false,
                'class' => Layout::class,
                'query_builder' => function (EntityRepository $er) {
                    $DeviceType = $this->deviceTypeRepository->find(DeviceType::DEVICE_TYPE_PC);
                    return $er->createQueryBuilder('l')
                        ->where('l.id != :DefaultLayoutPreviewPage')
                        ->andWhere('l.DeviceType = :DeviceType')
                        ->setParameter('DeviceType', $DeviceType)
                        ->setParameter('DefaultLayoutPreviewPage', Layout::DEFAULT_LAYOUT_PREVIEW_PAGE)
                        ->orderBy('l.id', 'DESC');
                },
            ])
            ->add('SpLayout', EntityType::class, [
                'mapped' => false,
                'placeholder' => '---',
                'required' => false,
                'class' => Layout::class,
                'query_builder' => function (EntityRepository $er) {
                    $DeviceType = $this->deviceTypeRepository->find(DeviceType::DEVICE_TYPE_MB);
                    return $er->createQueryBuilder('l')
                        ->where('l.id != :DefaultLayoutPreviewPage')
                        ->andWhere('l.DeviceType = :DeviceType')
                        ->setParameter('DeviceType', $DeviceType)
                        ->setParameter('DefaultLayoutPreviewPage', Layout::DEFAULT_LAYOUT_PREVIEW_PAGE)
                        ->orderBy('l.id', 'DESC');
                },
            ])
            
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                $Page = $event->getData();
                if (is_null($Page->getId())) {
                    return;
                }
                $form = $event->getForm();
                $Layouts = $Page->getLayouts();
                foreach ($Layouts as $Layout) {
                    if ($Layout->getDeviceType()->getId() == DeviceType::DEVICE_TYPE_PC) {
                        $form['PcLayout']->setData($Layout);
                    }
                    if ($Layout->getDeviceType()->getId() == DeviceType::DEVICE_TYPE_MB) {
                        $form['SpLayout']->setData($Layout);
                    }
                }
            })
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();

                /** @var Page $Page */
                $Page = $event->getData();

                $qb = $this->entityManager->createQueryBuilder();
                $qb->select('count(p)')
                    ->from('Ketcau\\Entity\\Page', 'p')
                    ->where('p.url = :url')
                    ->setParameter('url', $Page->getUrl());

                // 更新時は自身のページを重複チェックから除外
                if (!is_null($Page->getId())) {
                    $qb
                        ->andWhere('p.id <> :page_id')
                        ->setParameter('page_id', $Page->getId());
                }
                // 確認ページの編集ページが存在している場合
                if ($Page->getEditType() == Page::EDIT_TYPE_DEFAULT_CONFIRM && $Page->getMasterPage()) {
                    $qb
                        ->andWhere('p.id <> :master_page_id')
                        ->setParameter('master_page_id', $Page->getMasterPage()->getId());
                }

                $count = $qb->getQuery()->getSingleScalarResult();
                if ($count > 0) {
                    $form['url']->addError(new FormError(trans('admin.content.page_url_exists')));
                }

                //
                if (Page::EDIT_TYPE_USER === $Page->getEditType()) {
                    $qb = $this->entityManager->createQueryBuilder();
                    $qb->select('count(p)')
                        ->from('Ketcau\\Entity\\Page', 'p')
                        ->where('p.file_name = :file_name')
                        ->andWhere('p.edit_type >= :edit_type')
                        ->setParameter('file_name', $Page->getFileName())
                        ->setParameter('edit_type', Page::EDIT_TYPE_DEFAULT);

                    if (!is_null($Page->getId())) {
                        $qb
                            ->andWhere('p.id <> :page_id')
                            ->setParameter('page_id', $Page->getId());
                    }

                    $count = $qb->getQuery()->getSingleScalarResult();

                    if ($count > 0) {
                        $form['file_name']->addError(new FormError(trans('admin.content.page_file_name_exists')));
                    }
                }
            });
    }


    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Page::class,
        ]);
    }


    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'main_edit';
    }
}