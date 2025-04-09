<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order')]
    public function index(OrderRepository $orderRepository): Response
    {
        $user = $this->getUser();
        $orders = $orderRepository->findBy(['user' => $user], ['createdAt' => 'DESC']);

        return $this->render('order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/order/create', name: 'app_order_create', methods: ['POST'])]
    public function create(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $cart = $user->getCart();

        if (!$cart || $cart->getCartItems()->isEmpty()) {
            $this->addFlash('error', 'Ваш кошик порожній');
            return $this->redirectToRoute('app_cart_index');
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
            $entityManager->persist($orderItem);
        }

        $order->setTotalPrice($totalPrice);
        $entityManager->persist($order);

        foreach ($cart->getCartItems() as $cartItem) {
            $entityManager->remove($cartItem);
        }

        $entityManager->flush();

        $this->addFlash('success', 'Замовлення успішно створено');
        return $this->redirectToRoute('app_order');
    }
}
