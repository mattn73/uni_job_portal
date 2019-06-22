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

class RegistrationSeekerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('lastName', TextType::class,[
                'label' => 'Lastname*',
                'constraints' => [
                    new NotBlank(),
                    new Regex(array('pattern' => "/(\<\w*)((\s\/\>)|(.*\<\/\w*\>))/", 'match' => false, 'message' => "This field is invalid")),
                    new Length(array('max'=>100,'maxMessage'=>'Cannot contain more than 100 Caractere'))
                ]
            ])
            ->add('firstname', TextType::class,[
                'label' => 'Firstname*',
                'constraints' => [
                    new NotBlank(),
                    new Regex(array('pattern' => "/(\<\w*)((\s\/\>)|(.*\<\/\w*\>))/", 'match' => false, 'message' => "This field is invalid")),
                    new Length(array('max'=>100,'maxMessage'=>'Cannot contain more than 100 Caractere'))
                ]
            ])
            ->add('email', EmailType::class,[
                'label' => 'Email*',
                'constraints' => [
                    new NotBlank(),
                 ]
            ])
            ->add('address', TextType::class,[
                'label' => 'Address*',
                'constraints' => [
                    new NotBlank(),
                    new Regex(array('pattern' => "/(\<\w*)((\s\/\>)|(.*\<\/\w*\>))/", 'match' => false, 'message' => "This field is invalid")),
                    new Length(array('max'=>100,'maxMessage'=>'Cannot contain more than 100 Caractere'))
                ]
            ])
            ->add('contact', TextType::class,[
                'label' => 'Contact*',
                'constraints' => [
                    new NotBlank(),
                    new Regex(array('pattern' => "/(\<\w*)((\s\/\>)|(.*\<\/\w*\>))/", 'match' => false, 'message' => "This field is invalid")),
                    new Length(array('max'=>100,'maxMessage'=>'Cannot contain more than 100 Caractere'))
                ]
            ])
            ->add('dob', DateType::class,[
                'label' => 'Date of Birth*',
                'constraints' => [
                    new NotBlank(),
                    new Regex(array('pattern' => "/(\<\w*)((\s\/\>)|(.*\<\/\w*\>))/", 'match' => false, 'message' => "This field is invalid")),
                    new Length(array('max'=>100,'maxMessage'=>'Cannot contain more than 100 Caractere'))
                ]
            ])
            ->add('hqa', TextType::class,[
                'label' => 'Highest Qualification acheived*',
                'constraints' => [
                    new NotBlank(),
                    new Regex(array('pattern' => "/(\<\w*)((\s\/\>)|(.*\<\/\w*\>))/", 'match' => false, 'message' => "This field is invalid")),
                    new Length(array('max'=>100,'maxMessage'=>'Cannot contain more than 100 Caractere'))
                ]
            ])
            ->add('cv', TextType::class,[
                'label' => 'CV*',
                'constraints' => [
                    new NotBlank(),
                    new Regex(array('pattern' => "/(\<\w*)((\s\/\>)|(.*\<\/\w*\>))/", 'match' => false, 'message' => "This field is invalid")),
                    new Length(array('max'=>100,'maxMessage'=>'Cannot contain more than 100 Caractere'))
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Seeker::class,
        ]);
    }
}
