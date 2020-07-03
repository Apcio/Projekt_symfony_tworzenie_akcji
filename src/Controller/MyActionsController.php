<?php

namespace App\Controller;

use App\Entity\MyActions;
use App\Repository\MyActionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form;
use Symfony\Component\Form\Form as FormForm;

class MyActionsController extends AbstractController {
    public function index() {
        $maRepo = $this->getMaRepo();
        $data = $maRepo->getShortList();

        return $this->render('myactions/index.html.twig', ['data' => $data]);
    }

    public function show(int $id) {
        $maRepo = $this->getMaRepo();
        $data = $maRepo->find($id);

        if(!isset($data)) {
            return $this->redirectToRoute("myActionHomePage");
        }

        return $this->render('myactions/show.html.twig', ['data' => $data]);
    }

    public function remove() {
        $maRepo = $this->getMaRepo();
        $em = $this->getDoctrine()->getManager();

        $request = Request::createFromGlobals();
        $id = $request->query->getInt('id', -1);
        
        $response = new Response;
        $response->setContent("");
        $response->headers->remove('Content-Type');

        $entity = null;
        if($id >= 0) {
            $entity = $maRepo->find($id);
        }

        if(!isset($entity)) {
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            $response->send();
            return;
        }

        $em->remove($entity);
        $em->flush();
        
        $response->setStatusCode(Response::HTTP_OK);
        $response->send();
    }

    public function create(Request $request) {
        $data = new MyActions();
        $form = $this->getForm($data);
        
        $form->handleRequest($request);
        if($this->saveEntity($data, $form) === true) {
            $this->addFlash('success', 'Wprowadzono nową akcję');
            return $this->redirectToRoute("myActionHomePage");
        }

        return $this->render('myactions/form.html.twig', ['data' => $form->createView()]);
    }

    public function update(Request $request, int $id) {
        $maRepo = $this->getMaRepo();
        $data = $maRepo->find($id);

        if(!isset($data)) {
            $this->addFlash('danger', 'Nie znaleziono akcji');
            return $this->redirectToRoute("myActionHomePage");
        }

        $form = $this->getForm($data);
        
        $form->handleRequest($request);
        if($this->saveEntity($data, $form, true) === true) {
            $this->addFlash('success', 'Edytowano istniejącą akcję');
            return $this->redirectToRoute("myActionHomePage");
        }

        return $this->render('myactions/form.html.twig', ['data' => $form->createView()]);
    }

    private function saveEntity(MyActions $data, FormForm $form, bool $update = false): bool {
        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();

            if(!$update) {
                $data->beforeInsert();
                $em->persist($data);
            } else {
                $data->beforeUpdate();
            }

            $em->flush();

            return true;
        }
        return false;
    }

    private function getForm(MyActions $data) {
        return $this->createFormBuilder($data)
                ->add('title', TextType::class, ['attr' => ['maxlength' => 255]])
                ->add('description', TextareaType::class, ['attr' => ['rows' => 3, 'cols' => 50, 'maxlength' => 2048], 'required' => false])
                ->add('save', SubmitType::class, ['label' => 'Wprowadź'])
                ->getForm();
    }

    private function getMaRepo(): MyActionsRepository {
        return $this->getDoctrine()
                ->getManager()
                ->getRepository(MyActions::class);
    }
}

?>