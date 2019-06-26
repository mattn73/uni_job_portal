<?php

namespace App\Form;

use App\Entity\Seeker;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Form\FormInterface;


class SeekerProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', ChoiceType::class, array(
                'choices' => array('Mme' => 'Mme',
                    'Mr' => 'Mr'),
                'expanded' => true,
                'multiple' => false,
                'data' => 'Mme',
                'label' => 'Civilité :',
                'attr' => array('class' => 'form-check-inline'),
                'constraints' => new NotBlank(),
            ))
            ->add('lastName', TextType::class,[
                'label' => 'Lastname*',
                'constraints' => [
                    new NotBlank(array('message' => "This lastname is invalid")),
                    new Length(array('max'=>100,'maxMessage'=>'Cannot contain more than 100 Caractere'))
                ]
            ])
            ->add('firstname', TextType::class,[
                'label' => 'Firstname*',
                'constraints' => [
                    new NotBlank(array('message' => "This fname is invalid")),
                    new Length(array('max'=>100,'maxMessage'=>'Cannot contain more than 100 Caractere'))
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
                    new Length(array('max'=>10,'maxMessage'=>'Cannot contain more than 100 Caractere'))
                ]
            ])
            ->add('dob', BirthdayType::class,[
                'label' => 'Date of Birth*',
                'constraints' => [
                    new NotBlank(array('message' => "This email is invalid")),
                ]
            ])
            ->add('hqa', TextType::class,[
                'label' => 'Highest Education Achieved*',
                'constraints' => [
                    new NotBlank(array('message' => "This fname is invalid")),
                    new Regex(array('pattern' => "/(\<\w*)((\s\/\>)|(.*\<\/\w*\>))/", 'match' => false, 'message' => "This field is invalid")),
                    new Length(array('max'=>100,'maxMessage'=>'Cannot contain more than 100 Caractere'))
                ]
            ])

            ->add('cv', FileType::class, array(
                'label' => false,
                'multiple' => false,
                "mapped" => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Veuillez saisir une image valide',
                        'maxSizeMessage' => 'L\'image est tro lourd',
                    ]),
                    new Image([
                        'minWidth'  => 45,
                        'maxWidth'  => 800,
                        'minHeight' => 45,
                        'maxHeight' => 800,
                    ])
                ],
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Submit',
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Seeker::class,
        ]);
    }
}
