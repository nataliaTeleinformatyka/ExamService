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
        $userExams = new UserRepository();
        for ($i = 0; $i < $userExams->getQuantity(); $i++) {
            $values = $userExams->getUser($i);
            $firstName = $values['first_name'];
            $lastName = $values['last_name']; //todo sprawpdzac i wyswietlac tylko uzytkownikow z rola student
            $id=$i;
            $userInfo[$i] = $id." - ".$firstName." ".$lastName;
            print_r($userInfo);
        }
        $exams= new ExamRepository();
        for($j=0;$j<$exams->getQuantity();$i++) {
            $valuesExam = $exams->getExam($i);
            $name = $valuesExam['name'];
            $id=$valuesExam['exam_id'];
            $examInfo[$i] = $id." - ".$name;
        }

        $builder
            ->add('user_id', ChoiceType::class,
                [
                    'choices'  => $userInfo ]) //todo wyswietlac zawartosc komorki a nie numer komorki tablicy
            ->add('exam_id', ChoiceType::class,
                [
                    'choices'  => $examInfo]) //todo wyswietlac zawartosc komorki a nie numer komorki tablicy
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