<?php

namespace App\Service;

use App\Entity\Item;
use App\Form\ItemType;
use App\Repository\ItemRepository;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

readonly class ItemService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ItemRepository $itemRepository,
        private FormFactoryInterface $formFactory
    ) {
    }

    /**
     * @return Item[]
     */
    public function getAllItems(): array
    {
        return $this->itemRepository->findAll();
    }

    public function createItem(Request $request): FormInterface
    {
        $item = new Item();
        $form = $this->formFactory->create(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($item);
            $this->entityManager->flush();
        }

        return $form;
    }

    public function updateItem(Request $request, Item $item): FormInterface
    {
        $form = $this->formFactory->create(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
        }

        return $form;
    }

    public function deleteItem(Item $item): void
    {
        $this->entityManager->remove($item);
        $this->entityManager->flush();
    }
} 