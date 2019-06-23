<?php

namespace App\Form;

use App\Entity\Seeker;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RegistrationSeekerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class,[
                'label' => 'title',
                'constraints' => [
                    new NotBlank(array('message' => "This lastname is invalid")),
                    new Length(array('max'=>100,'maxMessage'=>'Cannot contain more than 100 Caractere'))
                ]
            ])
            ->add('lastName', TextType::class,[
                'label' => 'Lastname*',
                'constraints' => [
                    new NotBlank(array('message' => "This lastname is invalid")),
                    new Regex(array('pattern' => "/(\<\w*)((\s\/\>)|(.*\<\/\w*\>))/", 'match' => false, 'message' => "This field is invalid")),
                    new Length(array('max'=>100,'maxMessage'=>'Cannot contain more than 100 Caractere'))
                ]
            ])
            ->add('firstname', TextType::class,[
                'label' => 'Firstname*',
                'constraints' => [
                    new NotBlank(array('message' => "This fname is invalid")),
                    new Regex(array('pattern' => "/(\<\w*)((\s\/\>)|(.*\<\/\w*\>))/", 'match' => false, 'message' => "This field is invalid")),
                    new Length(array('max'=>100,'maxMessage'=>'Cannot contain more than 100 Caractere'))
                ]
            ])
            ->add('email', EmailType::class,[
                'label' => 'Email*',
                'constraints' => [
                    new NotBlank(array('message' => "This email is invalid")),
                ]
            ])
            ->add('address', TextType::class,[
                'label' => 'Address*',
                'constraints' => [
                    new NotBlank(array('message' => "This email is invalid")),
                    new Regex(array('pattern' => "/(\<\w*)((\s\/\>)|(.*\<\/\w*\>))/", 'match' => false, 'message' => "This field is invalid")),
                    new Length(array('max'=>100,'maxMessage'=>'Cannot contain more than 100 Caractere'))
                ]
            ])
            ->add('contact', TextType::class,[
                'label' => 'Contact*',
                'constraints' => [
                    new NotBlank(array('message' => "This email is invalid")),
                    new Length(array('max'=>100,'maxMessage'=>'Cannot contain more than 100 Caractere'))
                ]
            ])
            ->add('dob', DateType::class,[
                'label' => 'Date of Birth*',
                'constraints' => [
                    new NotBlank(array('message' => "This email is invalid")),
                ]
            ])
            ->add('hqa', TextType::class,[
                'label' => 'Highest Qualification acheived*',
                'constraints' => [
                    new NotBlank(array('message' => "This email is invalid")),
                    new Regex(array('pattern' => "/(\<\w*)((\s\/\>)|(.*\<\/\w*\>))/", 'match' => false, 'message' => "This field is invalid")),
                    new Length(array('max'=>100,'maxMessage'=>'Cannot contain more than 100 Caractere'))
                ]
            ])
            ->add('cv', TextType::class,[
                'label' => 'CV*',
                'constraints' => [
                    new NotBlank(array('message' => "This email is invalid")),
                    new Regex(array('pattern' => "/(\<\w*)((\s\/\>)|(.*\<\/\w*\>))/", 'match' => false, 'message' => "This field is invalid")),
                    new Length(array('max'=>100,'maxMessage'=>'Cannot contain more than 100 Caractere'))
                ]
            ])
            ->add('password', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => [
                    'label' => 'Password',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            'max' => 4096,
                        ]),
                    ],
                ],
                'second_options' => ['label' => 'Repeat Password'],
            ])
            ->add('submit', SubmitType::class, array(
                'label' => 'register',
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'allow_extra_fields' => true
            )
        );
    }
}
