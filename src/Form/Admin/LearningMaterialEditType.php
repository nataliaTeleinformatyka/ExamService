<?php

namespace App\Form\Admin;

use App\Entity\Admin\LearningMaterial;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class LearningMaterialEditType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('is_required', ChoiceType::class, [
                'choices'  => [
                    'Yes' => true,
                    'No' => false,
                ]])
            ->add('save', SubmitType::class, ['label' => 'Edit Learning Material'])
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
        return 'learning_material_edit';
    }
}