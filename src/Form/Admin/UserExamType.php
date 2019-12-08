<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 03.12.2019
 * Time: 13:16
 */

namespace App\Form\Admin;


use App\Entity\Admin\UserExam;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserExamType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user_id', ChoiceType::class,
                [
                    'choices'  => [
                        'Yes' => true,
                        'No' => false,
                    ]])
            ->add('exam_id', ChoiceType::class,
                [
                    'choices'  => [
                        'Yes' => true,
                        'No' => false,
                    ]])
            ->add('date_of_resolve_exam', DateType::class)
            ->add('start_access_time', DateType::class)
            ->add('end_access_time', DateType::class)
            ->add('save', SubmitType::class, ['label' => 'Add Exam'])
            ->setMethod('POST')
            ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserExam::class,
        ]);
    }

    /**
     * @return string
     */
    public function getName() {
        return 'user_exam_add';
    }
}