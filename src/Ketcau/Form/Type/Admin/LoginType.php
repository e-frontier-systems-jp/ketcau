<?php

namespace Ketcau\Form\Type\Admin;

use Ketcau\Common\KetcauConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class LoginType extends AbstractType
{
    public function __construct(
        protected KetcauConfig $ketcauConfig,
        protected SessionInterface $session
    ){}


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('login_id', TextType::class, [
                'attr' => [
                    'maxlength' => $this->ketcauConfig['ketcau_id_max_len'],
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
                'data' => $this->session->get('_security.last_username'),
            ]);

        $builder
            ->add('password', PasswordType::class, [
                'attr' => [
                    'maxlength' => $this->ketcauConfig['ketcau_password_max_len'],
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }


    public function getBlockPrefix(): string
    {
        return 'admin_login';
    }
}