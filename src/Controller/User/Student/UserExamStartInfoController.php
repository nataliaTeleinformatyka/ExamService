<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 13.12.2019
 * Time: 11:02
 */

namespace App\Controller\User\Student;


use App\Repository\Admin\ExamRepository;
use App\Repository\Admin\LearningMaterialRepository;
use App\Repository\Admin\LearningMaterialsGroupExamRepository;
use App\Repository\Admin\UserExamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserExamStartInfoController extends AbstractController
{
    /**
     * @Route("studentExamStartInfo/{userExamId}", name="studentExamStartInfo")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function studentExamStartInfoCreate(Request $request) {
        $examInformation = new UserExamRepository();
        $exam = new ExamRepository();
        $learningMaterialRepository = new LearningMaterialRepository();
        $learningMaterialGroupExamRepository = new LearningMaterialsGroupExamRepository();
        $userExamId = $request->attributes->get('userExamId');

        $userExam = $examInformation->getUserExam($userExamId);

        $examInfo = $exam->getExam($userExam['exam_id']);
        $examName = $examInfo['name'];
        $durationOfExam = $examInfo['duration_of_exam'];

        if (date("Y", strtotime($userExam['date_of_resolve_exam']['date'])) < "2020") {
            $resolveDate = " ";
        } else {
            $resolveDate = $userExam['date_of_resolve_exam']['date'];
        }

      $learningMaterialGroups = $learningMaterialGroupExamRepository->findByExamId($userExam['exam_id']);
        $groupsAmount = count($learningMaterialGroups);
        $requiredAmount=0;
        $additionalAmount=0;
        $additionalMaterialsArray[] = array(
            'id' => "",
            'name' => "",
            'name_of_content' => "",
        );
        $requiredMaterialsArray[$requiredAmount] = array(
            'id' => "",
            'name' => "",
            'name_of_content' => "",
        );
        for($i=0;$i<$groupsAmount;$i++){
            $learningMaterialsId = $learningMaterialRepository->getIdLearningMaterials($learningMaterialGroups[$i]);
            $learningMaterialsAmount = count($learningMaterialsId);

            if($learningMaterialsAmount>0) {
                for ($j = 0; $j < $learningMaterialsAmount; $j++) {
                    $learningInfo = $learningMaterialRepository->getLearningMaterial($learningMaterialGroups[$i], $learningMaterialsId[$j]);

                    if($learningInfo['is_required']==true) {
                        $requiredMaterialsArray[$requiredAmount] = array(
                            'id' => $learningInfo['id'],
                            'name' => $learningInfo['name'],
                            'name_of_content' => $learningInfo['name_of_content'],
                        );
                        $requiredAmount++;
                    } else {
                        $additionalMaterialsArray[$additionalAmount] = array(
                            'id' => $learningInfo['id'],
                            'name' => $learningInfo['name'],
                            'name_of_content' => $learningInfo['name_of_content'],
                        );
                        $additionalAmount++;
                    }
                }
            } else {
                $requiredMaterialsArray = "";
                $additionalMaterialsArray="";
            }
        }



        $tplArray= array(
            'user_id' => $userExam['user_id'],
            'exam_id' => $userExam['exam_id'],
            'date_of_resolve_exam' => $resolveDate,

          //  'start_date' => $startDate,
            //'end_date' => $endDate
        );

        return $this->render('studentExamStartInfo.html.twig', array(
            'data' => $tplArray,
            'exam_name' => $examName,
            'time' => $durationOfExam,
            'user_exam_id' => $userExamId,
            'required_materials' => $requiredMaterialsArray,
            'additional_materials' => $additionalMaterialsArray,
        ));
    }
}