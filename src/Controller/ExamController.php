<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 20.11.2019
 * Time: 12:34
 */

namespace App\Controller;


use App\Entity\Exam;
use App\Form\ExamType;
use App\Repository\ExamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ExamController extends AbstractController
{
    /**
     * @Route("/exam")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function new(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Exam::class);
        $exam = new Exam([]);

        $form = $this->createForm(ExamType::class, $exam);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data[0] = $request->request->get('name');
            $data[1] = $request->request->get('learning_required');
            $data[2] = $request->request->get('min_questions');
            $data[3] = $request->request->get('max_attempts');
            $data[4] = $request->request->get('start_date');
            $data[5] = $request->request->get('end_date');
            $data[6] = $request->request->get('additional_information');

            $exam = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $values = $exam->getAllInformation();
            $repositoryExam = new ExamRepository();
            $repositoryExam->insert($values);

            // return $this->forward($this->generateUrl('user'));
            // return $this->redirectToRoute('/user');
        }

        return $this->render('examAdd.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/examList")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function examListCreate() {
        $examInformation= new ExamRepository();
        $id = $examInformation -> getQuantity();
        if($id>0) {
            for ($i = 0; $i < $id; $i++) {
                $exams = $examInformation->getExam($i);
                if ($exams['learning_required'] == 1) {
                    $is_required = true;
                } else {
                    $is_required = false;
                }
                $tplArray[$i] = array(
                    'id' => $i,
                    'name' => $exams['name'],
                    'learning_required' => $is_required,
                    'min_questions' => $exams['min_questions'],
                    'max_attempts' => $exams['max_attempts'],
                    'start_date' => $exams['start_date']['date'],
                    'end_date' => $exams['end_date']['date'],
                    'additional_information' => $exams['additional_information']
                );
            }
        } else {
            $tplArray = array(
                'id' => "",
                'name' => "",
                'learning_required' => "",
                'min_questions' => "",
                'max_attempts' => "",
                'start_date' => "",
                'end_date' => "",
                'additional_information' => ""
            );
        }
        return $this->render( 'examList.html.twig', array (
            'data' => $tplArray
        ) );
    }

}
