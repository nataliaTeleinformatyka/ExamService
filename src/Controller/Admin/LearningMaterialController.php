<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 07.12.2019
 * Time: 22:23
 */

namespace App\Controller\Admin;


use App\Entity\Admin\LearningMaterial;
use App\Form\Admin\LearningMaterialType;
use App\Repository\Admin\LearningMaterialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LearningMaterialController  extends AbstractController
{
    /**
     * @Route("/learningMaterial/{learningMaterialsGroupId}", name="learningMaterial")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function new(Request $request)
    {
       // $repository = $this->getDoctrine()->getRepository(LearningMaterial::class);
        $learningMaterial = new LearningMaterial([]);

        $learningMaterialsGroupId = $request->attributes->get('learningMaterialsGroupId');


        $form = $this->createForm(LearningMaterialType::class, $learningMaterial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $material = $form->getData();
            $learningMaterialsGroupId = $request->attributes->get('learningMaterialsGroupId');

            $entityManager = $this->getDoctrine()->getManager();

            $values = $material->getAllInformation();
            $repositoryMaterial = new LearningMaterialRepository();
            $file = $form['attachment']->getData();

            $repositoryMaterial->insert($learningMaterialsGroupId,$values,$file);

           /* return $this->redirectToRoute('learningMaterialList',[
                "learningMaterialsGroupId" => $learningMaterialsGroupId
            ]);*/
        }

        return $this->render('learningMaterialAdd.html.twig', [
            'form' => $form->createView(),
            'learningMaterialsGroupId' => $learningMaterialsGroupId
        ]);
    }

    /**
     * @Route("/learningMaterialList/{learningMaterialsGroupId}", name="learningMaterialList")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    //todo: wyswietlanie gdy usuniety zostanie rekord 0 - DLA WSZYSTKICH CONTROLLER
    public function learningMaterialListCreate(Request $request) {
        $learningMaterialInformation= new LearningMaterialRepository();
        $learningMaterialsGroupId = $request->attributes->get('learningMaterialsGroupId');
        $_SESSION['group_id'] = "";
        $id = $learningMaterialInformation -> getQuantity($learningMaterialsGroupId);


        if($id>0) {
            $info = true;
            for ($i = 0; $i < $id; $i++) {
                $learningMaterial = $learningMaterialInformation->getLearningMaterial($learningMaterialsGroupId,$i);
           if($learningMaterial['id']!=$i) {
//todo: pomijac rekord 0 gdy usuniety
                    print_r($learningMaterial);
                }
                if($learningMaterial['is_required'] == true) {
                    $is_required = "true";
                } else {
                    $is_required="false";
                }
                print_r($learningMaterialInformation->get_file($learningMaterial['name_of_content']));
                $tplArray[$i] = array(
                    'id' => $learningMaterial['id'],
                    'learning_materials_group_id' => $learningMaterial['learning_materials_group_id'],
                    'name' => $learningMaterial['name'],
                    'name_of_content' => $learningMaterial['name_of_content'],
                    'is_required' => $is_required
                );
            }
        } else {
            $info=false;
            $tplArray = array(
                'id' => 0,
                'learning_materials_group_id' => 0,
                'name' => 0,
                'name_of_content' => 0,
                'is_required' => 0

            );
        }
      //  $learningMaterialInformation->get_file($learningMaterial['name_of_content']);

        return $this->render( 'learningMaterialList.html.twig', array (
            'data' => $tplArray,
            'learningMaterialsGroupId' => $learningMaterialsGroupId,
            'information' => $info

        ) );
    }
    /**
     * @param Request $request
     * @param LearningMaterial $learningMaterial

     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("editLearningMaterial/{groupId}/{id}", name="editLearningMaterial")
     */
    public function editExam(Request $request, LearningMaterial $learningMaterial)
    {
        $materialInformation = new LearningMaterialRepository();
        $materialId = (int)$request->attributes->get('id');
        $materialGroupId = (int)$request->attributes->get('groupId');
        $_SESSION['group_id'] = $materialGroupId;

        $materials = $materialInformation->getLearningMaterial($materialGroupId, $materialId);

        $examInfoArray = array(
            'id' => $materials['id'],
            'learning_materials_group_id' => $materials['learning_materials_group_id'],
            'name' => $materials['name'],
            'name_of_content' => $materials['name_of_content'],
            'is_required' => $materials['is_required'],

        );

        $form = $this->createForm(LearningMaterialType::class, $learningMaterial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
       /*     $exams = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $examValue = $request->attributes->get('id');
            print_r($examValue);

            $values = $exam->getAllInformation();
            $repositoryExam = new ExamRepository();
            $repositoryExam->update($values,$examId);
            print_r($values);
            // return $this->redirectToRoute('examList');*/
        }
        return $this->render('learningMaterialAdd.html.twig', [
            'form' => $form->createView(),
            'examInformation' =>$examInfoArray,
        ]);
    }


    /**
     * @param Request $request
     * @Route("/deleteMaterial/{learningMaterialsGroupId}/{learningMaterial}", name="deleteMaterial")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteExam(Request $request)
    {
        $id = $request->attributes->get('learningMaterial');
        $learningMaterialsGroupId = $request->attributes->get('learningMaterialsGroupId');
        $repo = new LearningMaterialRepository();
        $info = $repo->getLearningMaterial($learningMaterialsGroupId,$id);
        $filename = $info['name_of_content'];
        $repo->delete($learningMaterialsGroupId,$id,$filename);
        //todo: nie usuwac gdy sa powiazania
        //todo: wyswietlanie, gdy brak

        return $this->redirectToRoute('learningMaterialList', [
            'learningMaterialsGroupId' => $learningMaterialsGroupId,
        ]);
    }
}