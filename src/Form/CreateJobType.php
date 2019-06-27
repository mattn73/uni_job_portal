<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use App\Validator\Constraints\checkDate;

class CreateJobType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('jTitle', TextType::class, [
                'label' => 'Job Title*',
                'constraints' => [
                    new NotBlank(['message' => "This Job Title is invalid"]),
                    new Regex(['pattern' => "/(\<\w*)((\s\/\>)|(.*\<\/\w*\>))/", 'match' => false, 'message' => "This field is invalid"]),
                    new Length(['max' => 100, 'maxMessage' => 'Cannot contain more than 100 Characters'])
                ]
            ])
            ->add('jReference', TextType::class, [
                'label' => 'Job Reference*',
                'constraints' => [
                    new NotBlank(['message' => "This Job Reference is invalid"]),
                    new Regex(['pattern' => "/(\<\w*)((\s\/\>)|(.*\<\/\w*\>))/", 'match' => false, 'message' => "This field is invalid"]),
                    new Length(['max' => 100, 'maxMessage' => 'Cannot contain more than 100 Characters'])
                ]
            ])
            ->add('cDate', DateType::class, [
                'label' => 'Closing Date*',
                'widget' => 'choice',
                'format' => 'yyyy-MM-dd',
                'data' => new \DateTime(),
                'constraints' => [
                    new checkDate(),
                ]
            ])
            ->add('jDesc', TextareaType::class, [
                'label' => 'Job Description*',
                'constraints' => [
                    new NotBlank(['message' => "This field is invalid"]),
                    new Regex(['pattern' => "/(\<\w*)((\s\/\>)|(.*\<\/\w*\>))/", 'match' => false, 'message' => "This field is invalid"]),
                    new Length(['max' => 100, 'maxMessage' => 'Cannot contain more than 100 Characters'])
                ]
            ])
            ->add('create', SubmitType::class, [
                'attr' => ['value' => 'Create Job'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
    }
}
