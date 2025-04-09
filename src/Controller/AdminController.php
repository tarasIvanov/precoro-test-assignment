<?php

namespace App\Controller;

use App\Entity\Item;
use App\Service\ItemService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    public function __construct(
        private readonly ItemService $itemService
    ) {
    }

    #[Route('/items', name: 'app_admin_items', methods: ['GET'])]
    public function items(): Response
    {
        return $this->render('admin/index.html.twig', [
            'items' => $this->itemService->getAllItems(),
        ]);
    }

    #[Route('/items/new', name: 'app_admin_items_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $form = $this->itemService->createItem($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Товар успішно створено');
            return $this->redirectToRoute('app_admin_items');
        }

        return $this->render('admin/_form.html.twig', [
            'form' => $form,
            'title' => 'Створення нового товару'
        ]);
    }

    #[Route('/items/{id}/edit', name: 'app_admin_items_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Item $item): Response
    {
        $form = $this->itemService->updateItem($request, $item);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Товар успішно оновлено');
            return $this->redirectToRoute('app_admin_items');
        }

        return $this->render('admin/_form.html.twig', [
            'form' => $form,
            'title' => 'Редагування товару'
        ]);
    }

    #[Route('/items/{id}/delete', name: 'app_admin_items_delete', methods: ['POST'])]
    public function delete(Request $request, Item $item): Response
    {
        $this->itemService->deleteItem($item);
        $this->addFlash('success', 'Товар успішно видалено');

        return $this->redirectToRoute('app_admin_items');
    }
} 