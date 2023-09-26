<?php

namespace App\Form;

use App\Entity\Ouvreur;
use App\Entity\Theatre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class OuvreurFormType extends AbstractType
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
            ->add('nom', TextType::class ,['required' => true])
            ->add('prenom', TextType::class ,['required' => true])
            ->add('telephone', TextType::class, [
                'constraints' => [
                    new Regex([
                        'pattern' => '/^(\+\d{1,3}[-\s]?)?\d{10}$/',
                        'message' => 'Le numéro de téléphone n\'est pas valide.',
                    ]),
                ],
            ])
            ->add('theatre', EntityType::class, [
                'class' => Theatre::class,
                'choice_label' => 'nom',
                'label' => 'Theatre associé',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ouvreur::class,
        ]);
    }
}
