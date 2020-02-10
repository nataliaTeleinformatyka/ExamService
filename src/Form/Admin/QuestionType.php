<?php

namespace App\Form\Admin;

use App\Entity\Admin\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('content', TextareaType::class)
            ->add('max_answers', IntegerType::class)
            ->add('save', SubmitType::class, ['label' => 'Add Question'])
            ->setMethod('POST')
            ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }

    /**
     * @return string
     */
    public function getName() {
        return 'question_add';
    }
}