<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Form\TodoType;
use App\Repository\TodoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class TodoController extends AbstractController
{
    
    /**
     * @Route("/", name="app_todo_index", methods={"GET"})
     */
    public function index(TodoRepository $todoRepository): Response
    {
        return $this->render('todo/index.html.twig', [
            'todos' => $todoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_todo_new", methods={"GET", "POST"})
     */
    public function new(Request $request, TodoRepository $todoRepository): Response
    {
        $todo = new Todo();
        $form = $this->createForm(TodoType::class, $todo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $todo->setState(false);
            $todoRepository->add($todo);
            
            return $this->redirectToRoute('app_todo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('todo/new.html.twig', [
            'todo' => $todo,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_todo_show", methods={"GET"})
     */
    /**public function show(Todo $todo): Response
    {
        return $this->render('todo/show.html.twig', [
            'todo' => $todo,
        ]);
    }*/

    /**
     * @Route("/{id}/edit", name="app_todo_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Todo $todo, TodoRepository $todoRepository): Response
    {
        $form = $this->createForm(TodoType::class, $todo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $todoRepository->add($todo);
            return $this->redirectToRoute('app_todo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('todo/edit.html.twig', [
            'todo' => $todo,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_todo_delete", methods={"POST"})
     */
    public function delete(Request $request, Todo $todo, TodoRepository $todoRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$todo->getId(), $request->request->get('_token'))) {
            $todoRepository->remove($todo);
        }

        return $this->redirectToRoute('app_todo_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/state/{id}", name="app_todo_state", methods={"GET", "POST"})
     */
    public function state(TodoRepository $todoRepository, Todo $todo): Response
    {
        $state = $todo->getState();

        if ($state == false) {
            $todo->setState(true);
        } else {
            $todo->setState(false);
        }
        
        $todoRepository->add($todo);

        return $this->redirectToRoute('app_todo_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/deletetodo", name="app_todo_delete_tododone")
     */
    public function deleteTodoDone(Request $request, TodoRepository $todoRepository): Response
    {
        $todos = $todoRepository->findBy(['state' => true]);
        
        foreach($todos as $todo) {
            $todoRepository->remove($todo);
        }

        if ($this->isCsrfTokenValid('delete', $request->request->get('_token'))) {
            foreach($todos as $todo) {
                $todoRepository->remove($todo);
            }
        }

        return $this->redirectToRoute('app_todo_index', [], Response::HTTP_SEE_OTHER);
    }
}
