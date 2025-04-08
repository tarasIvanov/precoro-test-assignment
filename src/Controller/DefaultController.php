<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        // Якщо користувач вже авторизований, можливо, перенаправити його кудись інде?
        // Наприклад, на сторінку 'app_item_index'. Це опціонально.
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('app_item_index');
        // }

        return $this->render('default/index.html.twig');
    }
}