<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Item;
use App\Entity\User;
use App\Repository\CartItemRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class CartService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CartItemRepository     $cartItemRepository,
    ) {
    }

    public function getCartItems(User $user): array
    {
        $cart = $user->getCart();
        
        if (!$cart) {
            return [
                'cart_items' => [],
                'total_price' => 0,
            ];
        }

        $cartItems = $this->cartItemRepository->findCartItemsWithDetails($cart);
        $totalPrice = $this->cartItemRepository->calculateCartTotal($cart);

        return [
            'cart_items' => $cartItems,
            'total_price' => $totalPrice,
        ];
    }

    public function addItemToCart(User $user, Item $item): void
    {
        $cart = $user->getCart();

        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $cart->setPrice(0);
            $this->entityManager->persist($cart);
            $this->entityManager->flush();
        }

        $cartItem = $this->cartItemRepository->findOneByCartAndItem($cart, $item);

        if ($cartItem) {
            $cartItem->setCount($cartItem->getCount() + 1);
        } else {
            $cartItem = new CartItem();
            $cartItem->setCart($cart);
            $cartItem->setItem($item);
            $cartItem->setCount(1);
            $this->entityManager->persist($cartItem);
        }

        $this->entityManager->flush();
    }

    public function removeItemFromCart(User $user, Item $item): void
    {
        $cart = $user->getCart();

        if (!$cart) {
            return;
        }

        $cartItem = $this->cartItemRepository->findOneByCartAndItem($cart, $item);

        if (!$cartItem) {
            return;
        }

        if ($cartItem->getCount() > 1) {
            $cartItem->setCount($cartItem->getCount() - 1);
        } else {
            $this->entityManager->remove($cartItem);
        }
        
        $this->entityManager->flush();
    }
} 