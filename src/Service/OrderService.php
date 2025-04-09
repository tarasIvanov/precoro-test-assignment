<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\User;
use App\Exception\EmptyCartException;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class OrderService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private OrderRepository $orderRepository
    ) {
    }

    /**
     * @return Order[]
     */
    public function getUserOrders(User $user): array
    {
        return $this->orderRepository->findBy(['user' => $user], ['createdAt' => 'DESC']);
    }

    /**
     * @throws EmptyCartException
     */
    public function createOrderFromCart(User $user): void
    {
        $cart = $user->getCart();

        if (!$cart || $cart->getCartItems()->isEmpty()) {
            throw new EmptyCartException();
        }

        $order = new Order();
        $order->setUser($user);
        $totalPrice = 0;

        foreach ($cart->getCartItems() as $cartItem) {
            $orderItem = new OrderItem();
            $orderItem->setOrder($order);
            $orderItem->setItem($cartItem->getItem());
            $orderItem->setItemPrice($cartItem->getItem()->getPrice());
            $orderItem->setQuantity($cartItem->getCount());
            $orderItem->calculateTotalPrice();
            
            $totalPrice += $orderItem->getTotalPrice();
            $this->entityManager->persist($orderItem);
        }

        $order->setTotalPrice($totalPrice);
        $this->entityManager->persist($order);

        foreach ($cart->getCartItems() as $cartItem) {
            $this->entityManager->remove($cartItem);
        }

        $this->entityManager->flush();
    }
} 