<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 23.11.2019
 * Time: 17:20
 */

namespace App\Form\Admin;


use App\Entity\Admin\Exam;
use App\Entity\Admin\LearningMaterialsGroup;
use App\Repository\Admin\ExamRepository;
use App\Repository\Admin\LearningMaterialsGroupRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LearningMaterialsGroupType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $learningMaterialsGroups = new LearningMaterialsGroupRepository();

        for ($i = 0; $i < $learningMaterialsGroups->getQuantity(); $i++) {
            $values = $learningMaterialsGroups->getLearningMaterialsGroup($i);
            $names[$i] = $values['name'];
            $ids[$i]=$values['exam_id'];
        }
            $builder
                ->add('name_of_group', TextType::class)
                ->add('exam_id', ChoiceType::class, [
                    'choices' => $ids //todo: ."-".$names wyswietlanie nazwy egzaminu
                ])
                ->add('save', SubmitType::class, ['label' => 'Add Learning Materials Group'])
                ->setMethod('POST')
                ->getForm();

    }

    public function configureOptions(OptionsResolver $resolver)
    {
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