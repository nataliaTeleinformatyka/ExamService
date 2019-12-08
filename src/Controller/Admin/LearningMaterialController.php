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
           // $file = $form['attachment']->getData();


            $values = $material->getAllInformation();
            $repositoryMaterial = new LearningMaterialRepository();
            print_r($values);
            $repositoryMaterial->insert($learningMaterialsGroupId,$values);

            return $this->redirectToRoute('learningMaterialList',[
                "learningMaterialsGroupId" => $learningMaterialsGroupId
            ]);
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

        $id = $learningMaterialInformation -> getQuantity($learningMaterialsGroupId);


        if($id>0) {
            for ($i = 0; $i < $id; $i++) {
                $learningMaterial = $learningMaterialInformation->getLearningMaterial($learningMaterialsGroupId,$i);
                if($learningMaterial['is_required'] == true) {
                    $is_required = "true";
                } else {
                    $is_required="false";
                }
                $tplArray[$i] = array(
                    'id' => $i,
                    'learning_materials_group_id' => $learningMaterial['learning_materials_group_id'],
                    'name' => $learningMaterial['name'],
                    'name_of_content' => $learningMaterial['name_of_content'],
                    'is_required' => $is_required
                );
            }
        } else {
            $tplArray = array(
                'id' => 0,
                'learning_materials_group_id' => 0,
                'name' => 0,
                'name_of_content' => 0,
                'is_required' => 0

            );
        }
        return $this->render( 'learningMaterialList.html.twig', array (
            'data' => $tplArray,
            'learningMaterialsGroupId' => $learningMaterialsGroupId

        ) );
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
        $repo->delete($learningMaterialsGroupId,$id);
        //todo: nie usuwac gdy sa powiazania
        //todo: wyswietlanie, gdy brak

        return $this->redirectToRoute('learningMaterialList', [
            'learningMaterialsGroupId' => $learningMaterialsGroupId,
        ]);
    }
}