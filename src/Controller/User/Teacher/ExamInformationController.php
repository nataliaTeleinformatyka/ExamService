<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 08.12.2019
 * Time: 14:40
 */

namespace App\Controller\User\Teacher;



use App\Entity\Admin\Exam;
use App\Form\Admin\ExamType;
use App\Repository\Admin\AnswerRepository;
use App\Repository\Admin\ExamRepository;
use App\Repository\Admin\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ExamInformationController extends AbstractController
{
    /**
     * @Route("teacherExamList", name="teacherExamList")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function examListCreate()
    {
        $examInformation = new ExamRepository();
        $id = $examInformation->getQuantity();
        if ($id > 0) {
            for ($i = 0; $i < $id; $i++) {
                $exams = $examInformation->getExam($i);
                if ($exams['created_by'] == 0){ //todo: Pobieranie id uzytkownika z sesji
                    if ($exams['learning_required'] == 1) {
                        $is_required = true;
                    } else {
                        $is_required = false;
                    }
                    print_r($exams);
                    $tplArray[$i] = array(
                        'id' => $i,
                        'name' => $exams['name'],
                        'learning_required' => $is_required,
                        'min_questions' => $exams['min_questions'],
                        'max_attempts' => $exams['max_attempts'],
                        'duration_of_exam' => $exams['duration_of_exam']['date'], //todo: only time!!
                        'start_date' => $exams['start_date']['date'],
                        'end_date' => $exams['end_date']['date'],
                        'additional_information' => $exams['additional_information']
                    );
                }
            }
        } else {
                $tplArray = array(
                    'id' => "",
                    'name' => "",
                    'learning_required' => "",
                    'min_questions' => "",
                    'max_attempts' => "",
                    'duration_of_exam' => "",
                    'start_date' => "",
                    'end_date' => "",
                    'additional_information' => ""
                );
            }
        return $this->render('teacherExamList.html.twig', array(
            'data' => $tplArray
        ));

    }

    /**
     * @Route("teacherExamInfo/{exam}", name="teacherExamInfo")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function examInfoCreate(Request $request)
    {
        $examInformation = new ExamRepository();
        $examId = $request->attributes->get('exam');
        $exams = $examInformation->getExam($examId);
        if ($exams['learning_required'] == 1) {
            $is_required = true;
        } else {
            $is_required = false;
        }
        $examInfoArray[0] = array(
            'id' => $examId,
            'name' => $exams['name'],
            'learning_required' => $is_required,
            'min_questions' => $exams['min_questions'],
            'max_attempts' => $exams['max_attempts'],
            'duration_of_exam' => $exams['duration_of_exam']['date'], //todo: only time!!
            'start_date' => $exams['start_date']['date'],
            'end_date' => $exams['end_date']['date'],
            'additional_information' => $exams['additional_information']
        );

        $questionInformation= new QuestionRepository();
        $idQuestion = $questionInformation -> getQuantity($examId);
        if($idQuestion>0) {
            for ($i = 0; $i < $idQuestion; $i++) {
                $questions = $questionInformation->getQuestion($examId,$i);
                $tplArray[$i] = array(
                    'id' => $i,
                    'exam_id' => $questions['exam_id'],
                    'content' => $questions['content'],
                );
            }
        } else {
            $tplArray = array(
                'id' => 0,
                'exam_id' => 0,
                'content' => 0,
            );
        }

        return $this->render('teacherExamInfo.html.twig', array(
            'data' => $examInfoArray,
            'question_data' => $tplArray
        ));

    }



    /**
     * @param Request $request
     * @Route("/delete/{exam}", name="deleteByTeacher")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteExam(Request $request)
    {
        $id = $request->attributes->get('exam');
        $repo = new ExamRepository();
        $repo->delete($id);
        //todo: redirect to examList nie usuwa zapytania ktore jest jako 1
        //todo: zapytanie czy chce usunac egzamin gdy sa powiazane question i answers
        //todo: nie mozna usunac egzaminu, gdy jest powiazanie userexam, result

        return $this->redirectToRoute('teacherExamList');
    }
}