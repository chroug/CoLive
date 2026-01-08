<?php

namespace App\Form;

use App\Entity\Announce;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Image;

class AnnounceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre de l\'announce'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description détaillée',
                'attr' => ['rows' => 5]
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de bien',
                'choices'  => [
                    'Chambre' => 'Chambre',
                    'Collocation' => 'Collocation',
                    'Studio' => 'Studio',
                ],
            ])
            ->add('nb_pieces', IntegerType::class, [
                'label' => 'Nombre de pièces'
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix par nuit',
                'currency' => 'EUR'
            ])
            ->add('latitude', NumberType::class, ['scale' => 6,'required' => false])
            ->add('longitude', NumberType::class, ['scale' => 6,'required' => false])
            ->add('equipements', TextType::class, [
                'help' => 'Ex: Wifi, Parking, Piscine...'
            ])
            ->add('regle', TextareaType::class, [
                'required' => false
            ])
//            ->add('dateCreation')
            ->add('disponibilite_debut', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Disponible du'
            ])
            ->add('disponibilite_fin', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Au'
            ])
            ->add('adresse')
            ->add('ville')
            ->add('code_postal', TextType::class, [
                'label' => 'Code postal'
            ])
            ->add('surface', NumberType::class, [
                'label' => 'Surface'
            ])
            ->add('images', FileType::class, [
                'label' => 'Photos du logement',
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new All([
                        new Image([
                            'maxSize' => '5M',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                            ],
                            'mimeTypesMessage' => 'Veuillez uploader une image valide (jpg, png, webp)',
                        ])
                    ])
                ],
                'attr' => [
                    'accept' => 'image/*',
                    'class' => 'd-none',
                ]
            ])
//            ->add('utilisateur', EntityType::class, [
//                'class' => User::class,
//                'choice_label' => 'id',
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Announce::class,
        ]);
    }
}
