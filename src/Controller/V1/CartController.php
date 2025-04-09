<?php

namespace App\Controller\V1;

use App\Entity\Item;
use App\Entity\User;
use App\Exception\UserNotFoundException;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/cart')]
#[IsGranted('ROLE_USER')]
class CartController extends AbstractController
{
    public function __construct(
        private readonly CartService $cartService
    ) {
    }

    #[Route('/', name: 'app_cart_index', methods: ['GET'])]
    public function index(): Response
    {
        try {
            $user = $this->getAuthenticatedUser();

            return $this->render('cart/index.html.twig', 
                $this->cartService->getCartItems($user)
            );
        } catch (UserNotFoundException) {
            $this->addFlash('error', 'Будь ласка, увійдіть в систему.');
            return $this->redirectToRoute('app_login');
        }
    }

    #[Route('/add/{id}', name: 'app_cart_add', methods: ['POST'])]
    public function add(Item $item, Request $request): Response
    {
        try {
            $user = $this->getAuthenticatedUser();

            $this->cartService->addItemToCart($user, $item);
            $this->addFlash('success', 'Товар додано до кошика');

            $referer = $request->headers->get('referer');
            return $referer ? $this->redirect($referer) : $this->redirectToRoute('app_item_index');
        } catch (UserNotFoundException) {
            $this->addFlash('error', 'Будь ласка, увійдіть в систему.');
            return $this->redirectToRoute('app_login');
        }
    }

    #[Route('/remove/{id}', name: 'app_cart_remove', methods: ['POST'])]
    public function remove(Item $item): Response
    {
        try {
            $user = $this->getAuthenticatedUser();

            $this->cartService->removeItemFromCart($user, $item);
            $this->addFlash('success', 'Товар видалено з кошика');

            return $this->redirectToRoute('app_cart_index');
        } catch (UserNotFoundException) {
            $this->addFlash('error', 'Будь ласка, увійдіть в систему.');
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @throws UserNotFoundException
     */
    private function getAuthenticatedUser(): User
    {
        $user = $this->getUser();
        
        if (!$user instanceof User) {
            throw new UserNotFoundException();
        }
        
        return $user;
    }
} 