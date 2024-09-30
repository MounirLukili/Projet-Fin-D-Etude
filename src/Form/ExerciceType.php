<?php

namespace App\Form;



use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ExerciceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $step = $options['step'];
        $type = $options['type'];

        if ($step == 1) {
            $builder
                ->add('sujet')
                ->add('niveau', ChoiceType::class, [
                    'choices' => [
                        '1' => '1',
                        '2' => '2',
                        '3' => '3',
                    ],
                ])
                ->add('type', ChoiceType::class, [
                    'choices' => [
                        'QCM' => 'QCM',
                        'FTB' => 'FTB',
                        'OQ' => 'OQ',
                    ],
                ]);
        } else {
            switch ($type) {
                case 'QCM':
                    $builder
                    ->add('contenu', TextareaType::class, ['required' => true])
                    ->add('solution', TextareaType::class, ['required' => true])
                    ->add('qcm_enoncé', TextareaType::class, [
                        'mapped' => false,
                        'required' => false,
                    ])
                    ->add('qcm_solution1', TextareaType::class, [
                        'mapped' => false,
                        'required' => false,
                    ])
                    ->add('qcm_solution2', TextareaType::class, [
                        'mapped' => false,
                        'required' => false,
                    ])
                    ->add('qcm_solution3', TextareaType::class, [
                        'mapped' => false,
                        'required' => false,
                    ])
                    ->add('qcm_solution1', TextareaType::class, [
                        'mapped' => false,
                        'required' => false,
                    ])
                    ->add('qcm_reponse', TextareaType::class, [
                        'mapped' => false,
                        'required' => false,
                    ]);
                    break;
                case 'FTB':
                    $builder
                        ->add('contenu', TextareaType::class, ['required' => true])
                        ->add('solution', TextareaType::class, ['required' => true])
                        ->add('ftb_contenu', TextareaType::class, [
                            'mapped' => false,
                            'required' => false,
                        ])
                        ->add('ftb_solution1', TextType::class, [
                            'mapped' => false,
                            'required' => false,
                        ])
                        ->add('ftb_solution2', TextType::class, [
                            'mapped' => false,
                            'required' => false,
                        ]);
                    break;
                case 'OQ':
                    $builder
                        ->add('contenu', TextareaType::class, ['required' => true])
                        ->add('solution', TextareaType::class, ['required' => true])
                        ->add('oq_contenu', TextareaType::class, [
                            'mapped' => false,
                            'required' => false,
                        ])
                        ->add('oq_expected_output', TextType::class, [
                            'mapped' => false,
                            'required' => false,
                        ])
                        ->add('oq_correction', TextType::class, [
                            'mapped' => false,
                            'required' => false,
                        ]);
                    break;
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Exercice', // Ensure the correct entity class
            'step' => 1,
            'type' => null,
        ]);
    }
}
