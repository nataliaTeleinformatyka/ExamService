<?php

namespace App\Form\Admin;

use App\Entity\Admin\LearningMaterial;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class LearningMaterialType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', TextType::class)
            ->add('is_required', ChoiceType::class, [
                'choices'  => [
                    'Yes' => true,
                    'No' => false,
                ]])
            ->setMethod('POST')
            ->getForm();

            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $learningMaterial = $event->getData();
            $form = $event->getForm();

            if(!$learningMaterial || null === $learningMaterial->getId()) {
                $form
                    ->add('attachment', FileType::class, [
                        'mapped' => false,
                        'required' => true,
                        'constraints' => [
                            new File([
                                'maxSize' => '1024k',
                                'mimeTypes' => [
                                    'application/pdf',
                                    'application/x-pdf',
                                    'image/jpeg',
                                    'image/jpg',
                                    'image/png',
                                    "video/wmv",
                                    "video/avi",
                                    "audio/mpeg3"
                                ],
                                'mimeTypesMessage' => 'Please upload a valid PDF document,image : jpeg, jpg, png, video: wmv, avi or audio: mp3',
                            ])
                        ],
                    ]);
                $label = "Add learning material";
            } else {
                $label = "Edit learning material";
            }
            $form ->add('save', SubmitType::class, ['label' => $label]);
        });
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => LearningMaterial::class,
        ]);
    }

    /**
     * @return string
     */
    public function getName() {
        return 'learning_material_add';
    }
}