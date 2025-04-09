<?php

namespace App\Controller\V1;

use App\Entity\Item;
use App\Service\ItemService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/item')]
final class ItemController extends AbstractController
{
    public function __construct(
        private readonly ItemService $itemService
    ) {
    }

    #[Route(name: 'app_item_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('item/index.html.twig', [
            'items' => $this->itemService->getAllItems(),
        ]);
    }

    #[Route('/new', name: 'app_item_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $form = $this->itemService->createItem($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('app_item_index');
        }

        return $this->render('item/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_item_show', methods: ['GET'])]
    public function show(Item $item): Response
    {
        return $this->render('item/show.html.twig', [
            'item' => $item,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_item_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Item $item): Response
    {
        $form = $this->itemService->updateItem($request, $item);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('app_item_index');
        }

        return $this->render('item/edit.html.twig', [
            'item' => $item,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_item_delete', methods: ['POST'])]
    public function delete(Item $item): Response
    {
        $this->itemService->deleteItem($item);
        $this->addFlash('success', 'Товар успішно видалено');

        return $this->redirectToRoute('app_item_index');
    }
}
