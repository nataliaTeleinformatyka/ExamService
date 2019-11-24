<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 23.11.2019
 * Time: 17:20
 */

namespace App\Controller;


use App\Entity\LearningMaterialsGroup;
use App\Form\LearningMaterialsGroupType;
use App\Repository\LearningMaterialsGroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LearningMaterialsGroupController extends AbstractController
{
    /**
     * @Route("/learningMaterialsGroup")
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

            // return $this->forward($this->generateUrl('user'));
            // return $this->redirectToRoute('/user');
        }

        return $this->render('learningMaterialsGroupAdd.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/learningMaterialsGroupList")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function examListCreate() {
        $learningMaterialsGroupInformation= new LearningMaterialsGroupRepository();
        $id = $learningMaterialsGroupInformation -> getQuantity();
        if($id>0) {
            for ($i = 0; $i < $id; $i++) {
                $learningMaterialsGroup = $learningMaterialsGroupInformation->getExam($i);

                $tplArray[$i] = array(
                    'id' => $i,
                    'name_of_group' => $learningMaterialsGroup['name_of_group'],
                );
            }
        } else {
            $tplArray = array(
                'id' => "",
                'name_of_group' => "",

            );
        }
        return $this->render( 'LearningMaterialsGroupList.html.twig', array (
            'data' => $tplArray
        ) );
    }
}
