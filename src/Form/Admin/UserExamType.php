<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 03.12.2019
 * Time: 13:16
 */

namespace App\Form\Admin;


use App\Entity\Admin\User;
use App\Entity\Admin\UserExam;
use App\Repository\Admin\ExamRepository;
use App\Repository\Admin\UserExamRepository;
use App\Repository\Admin\UserRepository;
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
        $user = new UserRepository();
        for ($i = 0; $i < $user->getQuantity(); $i++) {
            $values = $user->getUser($i);
            if (($values['id'] != "" or $values['id'] == 0) and $values['role'] == "ROLE_STUDENT") {

                $firstName = $values['first_name'];
                $lastName = $values['last_name'];
                $userInfo[$i . " - " . $firstName . " " . $lastName] = $values['id'];
            }
        }

        $exams= new ExamRepository();
        for($j=0;$j<$exams->getQuantity();$i++) {
            $valuesExam = $exams->getExam($i);
            $name = $valuesExam['name'];
            $examInfo[$i." - ".$name] = $valuesExam['exam_id'];
        }

        $builder
            ->add('user_id', ChoiceType::class,
                [
                    'choices'  => $userInfo ])
            ->add('exam_id', ChoiceType::class,
                [
                    'choices'  => $examInfo])
           // ->add('date_of_resolve_exam', DateType::class)
            ->add('start_access_time', DateType::class)
            ->add('end_access_time', DateType::class)
            ->add('save', SubmitType::class, ['label' => 'Add userExam'])
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