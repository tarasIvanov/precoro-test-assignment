<?php

namespace App\Controller\V1;

use App\Entity\User;
use App\Exception\EmptyCartException;
use App\Exception\UserNotFoundException;
use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class OrderController extends AbstractController
{
    public function __construct(
        private readonly OrderService $orderService
    ) {
    }

    #[Route('/order', name: 'app_order')]
    public function index(): Response
    {
        try {
            $user = $this->getAuthenticatedUser();

            return $this->render('order/index.html.twig', [
                'orders' => $this->orderService->getUserOrders($user),
            ]);
        } catch (UserNotFoundException) {
            $this->addFlash('error', 'Будь ласка, увійдіть в систему.');
            return $this->redirectToRoute('app_login');
        }
    }

    #[Route('/order/create', name: 'app_order_create', methods: ['POST'])]
    public function create(): Response
    {
        try {
            $user = $this->getAuthenticatedUser();

            $this->orderService->createOrderFromCart($user);
            $this->addFlash('success', 'Замовлення успішно створено');
            
            return $this->redirectToRoute('app_order');
        } catch (UserNotFoundException) {
            $this->addFlash('error', 'Будь ласка, увійдіть в систему.');
            return $this->redirectToRoute('app_login');
        } catch (EmptyCartException $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('app_cart_index');
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
