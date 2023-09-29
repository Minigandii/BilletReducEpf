<?php

namespace App\Form;

use App\Entity\Theatre;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class TheatreFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

    

        $builder
            ->add('email', EmailType::class,['required' => true])
            ->add('password', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('BRId', ChoiceType::class, [
                'choices' => (($options['theatres'])),
                'choice_label' => function ($choice) {
                    $parts = explode('*', $choice); 
                    return $parts[0].', '.$parts[1].', '.$parts[2].', '.$parts[3]; //on affiche tout sauf l'id
                },             
                'label' => 'Choisissez le théâtre à inscrire',
                'required' => true,
            ])
            ->add('qrcode', TextType::class, ['required' => true]);
          

    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'theatres' => [],
        ]);
    }

}
