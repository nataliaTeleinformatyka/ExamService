<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 03.12.2019
 * Time: 13:12
 */

namespace App\Controller\Admin;


use App\Entity\Admin\UserExam;
use App\Form\Admin\UserExamType;
use App\Repository\Admin\UserExamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserExamController  extends AbstractController
{
    /**
     * @Route("/userExam", name="userExam")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function new(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(UserExam::class);
        $exam = new UserExam([]);

        $form = $this->createForm(UserExamType::class, $exam);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

        /*    $data[0] = $request->request->get('name');
            $data[1] = $request->request->get('learning_required');
            $data[2] = $request->request->get('min_questions');
            $data[3] = $request->request->get('max_attempts');
            $data[4] = $request->request->get('start_date');
            $data[5] = $request->request->get('end_date');
            $data[6] = $request->request->get('additional_information');*/

            $userExam = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $values = $exam->getAllInformation();
            $repositoryExam = new UserExamRepository();
            $repositoryExam->insert($values);

            // return $this->forward($this->generateUrl('user'));
            //return $this->redirectToRoute('userExamList');
        }

        return $this->render('userExamAdd.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("userExamList", name="userExamList")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function examListCreate()
    {
        $examInformation = new UserExamRepository();
        $id = $examInformation->getQuantity();
        if ($id > 0) {
            for ($i = 0; $i < $id; $i++) {
                $userExam = $examInformation->getUserExam($i);

                $tplArray[$i] = array(
                    'user_id' => $userExam['user_id'],
                    'exam_id' => $userExam['exam_id'],
                    'date_of_resolve_exam' => $userExam['date_of_resolve_exam'],
                    'start_access_time' => $userExam['start_access_time']['date'],
                    'end_access_time' => $userExam['end_access_time']['date']
                );
            }
        } else {
            $tplArray = array(
                'user_id' => 0,
                'exam_id' => 0,
                'date_of_resolve_exam' => 0,
                'start_access_time' => 0,
                'end_access_time' => 0
            );
        }
        return $this->render('userExamList.html.twig', array(
            'data' => $tplArray
        ));
    }

    /**
     * @param Request $request
     * @param Exam $exam
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/edit/{exam}", name="edit")
     */
    public function editExam(Request $request/*, Exam $exam*/)
    {
        $repository = $this->getDoctrine()->getRepository(UserExam::class);
        /* $id = $request->attributes->get('exam');
         print_r($id);
         $examEn= new Exam([]);
         $examrepo = new ExamRepository();
         $efxam = $examrepo->getExam($id);
         print_r($efxam);*/
        $exam = new UserExam([]);

        $form = $this->createForm(UserExamType::class, $exam);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $repository->flush();

            return $this->redirectToRoute('edit', [
                'name' => $exam->getName(),
            ]);
        }
        return $this->render('examEdit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @Route("/deleteUserExam/{userId}/{examId}", name="deleteUserExam")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteExam(Request $request)
    {
        $userId = $request->attributes->get('user_id');
        $examId  = $request->attributes->get('exam_id');
        $repo = new UserExamRepository();
        $repo->delete($userId,$examId);
        //todo: redirect to examList nie usuwa zapytania ktore jest jako 1
        //todo: zapytanie czy chce usunac egzamin gdy sa powiazane question i answers
        //todo: nie mozna usunac egzaminu, gdy jest powiazanie userexam, result

        return $this->redirectToRoute('userExamList');
    }
}
