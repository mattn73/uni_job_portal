<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;

class CompanyUpdateProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cpEmail', EmailType::class, [
                'label' => 'Contact Email address*',
                'constraints' => [
                    new NotBlank(['message' => "This email is invalid"]),
                ]
            ])
            ->add('companyName', TextType::class, [
                'label' => 'Company Name*',
                'constraints' => [
                    new NotBlank(['message' => "This Company Name is invalid"]),
                    new Regex(['pattern' => "/(\<\w*)((\s\/\>)|(.*\<\/\w*\>))/", 'match' => false, 'message' => "This field is invalid"]),
                    new Length(['max' => 100, 'maxMessage' => 'Cannot contain more than 100 Characters'])
                ]
            ])
            ->add('cpName', TextType::class, [
                'label' => 'Contact Person Full name',
                'constraints' => [
                    new Regex(['pattern' => "/(\<\w*)((\s\/\>)|(.*\<\/\w*\>))/", 'match' => false, 'message' => "This field is invalid"]),
                    new Length(['max' => 100, 'maxMessage' => 'Cannot contain more than 100 Characters'])
                ]
            ])
            ->add('pAddress', TextType::class, [
                'label' => 'Postal Address*',
                'constraints' => [
                    new NotBlank(['message' => "This name is invalid"]),
                    new Regex(['pattern' => "/(\<\w*)((\s\/\>)|(.*\<\/\w*\>))/", 'match' => false, 'message' => "This field is invalid"]),
                    new Length(['max' => 100, 'maxMessage' => 'Cannot contain more than 100 Characters'])
                ]
            ])
            ->add('update', SubmitType::class, [
                'attr' => ['value' => 'Update'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
    }
}
