<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 23.11.2019
 * Time: 17:20
 */

namespace App\Controller\Admin;


use App\Entity\Admin\LearningMaterialsGroup;
use App\Form\Admin\LearningMaterialsGroupType;
use App\Repository\Admin\LearningMaterialsGroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LearningMaterialsGroupController extends AbstractController
{
    /**
     * @Route("/learningMaterialsGroup", name="learningMaterialsGroup")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function new(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(LearningMaterialsGroup::class);
        $learningMaterialsGroup = new LearningMaterialsGroup([]);

        $form = $this->createForm(LearningMaterialsGroupType::class, $learningMaterialsGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data[0] = $request->request->get('name_of_group');

            $group = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $values = $group->getAllInformation();
            $repositoryExam = new LearningMaterialsGroupRepository();

            $repositoryExam->insert($values);

             return $this->redirectToRoute('learningMaterialsGroupList');
        }

        return $this->render('learningMaterialsGroupAdd.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/learningMaterialsGroupList", name="learningMaterialsGroupList")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function learningMaterialsGroupListCreate() {
        $learningMaterialsGroupInformation= new LearningMaterialsGroupRepository();
        $id = $learningMaterialsGroupInformation -> getQuantity();
        if($id>0) {
            for ($i = 0; $i < $id; $i++) {
                $learningMaterialsGroup = $learningMaterialsGroupInformation->getLearningMaterialsGroup($i);

                $tplArray[$i] = array(
                    'id' => $i,
                    'name_of_group' => $learningMaterialsGroup['name_of_group'],
                    'exam_id' => $learningMaterialsGroup['exam_id'],
                );
            }
        } else {
            $tplArray = array(
                'id' => "",
                'name_of_group' => "",
                'exam_id' => "",

            );
        }
        return $this->render( 'LearningMaterialsGroupList.html.twig', array (
            'data' => $tplArray
        ) );
    }
    /**
     * @param Request $request
     * @Route("/deleteGroup/{learningMaterialsGroup}", name="deleteGroup")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteGroup(Request $request)
    {
        $id = $request->attributes->get('learningMaterialsGroup');
        $repo = new LearningMaterialsGroupRepository();
        $repo->delete($id);
        //todo: nie usuwac gdy sa powiazania
        //todo: wyswietlanie, gdy brak grup
        return $this->redirectToRoute('learningMaterialsGroupList');
    }
}
