<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Item;
use App\Repository\CartItemRepository;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/cart')]
#[IsGranted('ROLE_USER')]
class CartController extends AbstractController
{
    #[Route('/', name: 'app_cart_index', methods: ['GET'])]
    public function index(CartItemRepository $cartItemRepository): Response
    {
        $user = $this->getUser();
        $cart = $user->getCart();
        
        if (!$cart) {
            return $this->render('cart/index.html.twig', [
                'cart_items' => [],
                'total_price' => 0,
            ]);
        }

        $cartItems = $cartItemRepository->findCartItemsWithDetails($cart);
        $totalPrice = $cartItemRepository->calculateCartTotal($cart);

        return $this->render('cart/index.html.twig', [
            'cart_items' => $cartItems,
            'total_price' => $totalPrice,
        ]);
    }

    #[Route('/add/{id}', name: 'app_cart_add', methods: ['POST'])]
    public function add(
        Item $item, 
        EntityManagerInterface $entityManager, 
        CartRepository $cartRepository, 
        CartItemRepository $cartItemRepository,
        Request $request
    ): Response {
        $user = $this->getUser();
        $cart = $user->getCart();

        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $cart->setPrice(0);
            $entityManager->persist($cart);
            $entityManager->flush();
        }

        $cartItem = $cartItemRepository->findOneByCartAndItem($cart, $item);

        if ($cartItem) {
            $cartItem->setCount($cartItem->getCount() + 1);
        } else {
            $cartItem = new CartItem();
            $cartItem->setCart($cart);
            $cartItem->setItem($item);
            $cartItem->setCount(1);
            $entityManager->persist($cartItem);
        }

        $entityManager->flush();

        $this->addFlash('success', 'Товар додано до кошика');

        $referer = $request->headers->get('referer');
        return $referer ? $this->redirect($referer) : $this->redirectToRoute('app_item_index');
    }

    #[Route('/remove/{id}', name: 'app_cart_remove', methods: ['POST'])]
    public function remove(Item $item, EntityManagerInterface $entityManager, CartItemRepository $cartItemRepository): Response
    {
        $user = $this->getUser();
        $cart = $user->getCart();

        if (!$cart) {
            return $this->redirectToRoute('app_cart_index');
        }

        $cartItem = $cartItemRepository->findOneByCartAndItem($cart, $item);

        if ($cartItem) {
            if ($cartItem->getCount() > 1) {
                $cartItem->setCount($cartItem->getCount() - 1);
            } else {
                $entityManager->remove($cartItem);
            }
            $entityManager->flush();
        }

        $this->addFlash('success', 'Товар видалено з кошика');

        return $this->redirectToRoute('app_cart_index');
    }
} 