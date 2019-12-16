<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 07.12.2019
 * Time: 22:26
 */

namespace App\Form\Admin;


use App\Entity\Admin\LearningMaterial;
use App\Repository\Admin\LearningMaterialRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class LearningMaterialType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
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
            ])
            ->add('is_required', ChoiceType::class, [
                'choices'  => [
                    'Yes' => true,
                    'No' => false,
                ]])
            ->add('save', SubmitType::class, ['label' => 'Add Learning Material'])
            ->setMethod('POST')
            ->getForm();

    }

    public function configureOptions(OptionsResolver $resolver)
    {
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