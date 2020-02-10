<?php

namespace App\Form\Admin;

use App\Entity\Admin\LearningMaterialsGroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LearningMaterialsGroupType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
            $builder
                ->add('name_of_group', TextType::class)
                ->add('save', SubmitType::class, ['label' => 'Add Learning Materials Group'])
                ->setMethod('POST')
                ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => LearningMaterialsGroup::class,
        ]);
    }

    /**
     * @return string
     */
    public function getName() {
        return 'learning_materials_group_add';
    }
}