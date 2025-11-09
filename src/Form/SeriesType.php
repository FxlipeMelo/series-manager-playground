<?php

namespace App\Form;

use App\DTO\SeriesCreationInputDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class SeriesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('seriesName', options: ['label' => 'Nome:'])
            ->add('seasonQuantity', NumberType::class, options: ['label' => 'Quantidade de temporadas:'])
            ->add('episodesPerSeason', NumberType::class, options: ['label' => 'Episoddios por temporada:'])
            ->add('save', SubmitType::class, ['label' => $options['is_edit'] ? 'Editar' : 'Adicionar'])
            ->add(
                'coverImage',
                FileType::class,
                options: [
                    'label' => 'Imagen de capa:',
                    'mapped' => false,
                    'required' => false,
                    'constraints' => [
                        new File(
                            maxSize: '5000k',
                            extensions: ['jpg', 'jpeg', 'png'],
                        )
                    ]
                ])
            ->setMethod($options['is_edit'] ? 'PATCH' : 'POST');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SeriesCreationInputDTO::class,
            'is_edit' => false,
        ]);

        $resolver->setAllowedTypes('is_edit', 'bool');
    }
}
