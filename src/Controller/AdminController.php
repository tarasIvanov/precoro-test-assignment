<?php

namespace App\Controller;

use App\Entity\Item;
use App\Form\ItemType;
use App\Repository\ItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/items', name: 'app_admin_items', methods: ['GET'])]
    public function items(ItemRepository $itemRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            'items' => $itemRepository->findAll(),
        ]);
    }

    #[Route('/items/new', name: 'app_admin_items_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $item = new Item();
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($item);
            $entityManager->flush();

            $this->addFlash('success', 'Товар успішно створено');
            return $this->redirectToRoute('app_admin_items');
        }

        return $this->render('admin/_form.html.twig', [
            'form' => $form,
            'title' => 'Створення нового товару'
        ]);
    }

    #[Route('/items/{id}/edit', name: 'app_admin_items_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Item $item, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Товар успішно оновлено');
            return $this->redirectToRoute('app_admin_items');
        }

        return $this->render('admin/_form.html.twig', [
            'form' => $form,
            'title' => 'Редагування товару'
        ]);
    }

    #[Route('/items/{id}/delete', name: 'app_admin_items_delete', methods: ['POST'])]
    public function delete(Request $request, Item $item, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$item->getId(), $request->request->get('_token'))) {
            $entityManager->remove($item);
            $entityManager->flush();
            
            $this->addFlash('success', 'Товар успішно видалено');
        }

        return $this->redirectToRoute('app_admin_items');
    }
} 