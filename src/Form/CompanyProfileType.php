<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Repository\SeekerRepository;

class CompanyProfileType extends AbstractType
{
    private $seekerRepository;

    public function __construct(SeekerRepository $seekerRepository)
    {
        $this->seekerRepository = $seekerRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('companyEmail', EmailType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your Email address'
                    ]),
                ],
            ])
            ->add('companyName', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your Company Name'
                    ]),
                ]
            ])
            ->add('name', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your Name'
                    ]),
                ]
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your Email'
                    ]),
                ]
            ])
            ->add('address', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your Address'
                    ]),
                ]
            ])
            ->add('user', EntityType::class, [
                'label' => 'Choose',
                'required' => false,
                'class' => User::class,
                'choices' => $this->seekerRepository->findContactPerson(),
                'placeholder' => 'contact person',
                'constraints' => [
                    new Count([
                        'min' => 1,
                        'minMessage' => 'Please choose a user contact',
                    ])
                ]
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['value' => 'save'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
    }
}
