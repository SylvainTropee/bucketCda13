<?php

namespace App\Controller;

use App\Repository\WishRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/wish', name: 'wish_')]
final class WishController extends AbstractController
{
    #[Route('', name: 'list')]
    public function list(WishRepository $wishRepository): Response
    {
        $wishes = $wishRepository->findBy(["isPublished" => true], ["dateCreated" => "DESC"]);
        return $this->render("wish/list.html.twig", [
            "wishes" => $wishes
        ]);
    }

    #[Route('/detail/{id}', name: 'detail', requirements: ['id' => '\d+'])]
    public function detail(int $id, WishRepository $wishRepository): Response
    {
        $wish = $wishRepository->find($id);

        if(!$wish){
            throw $this->createNotFoundException("Oooops ! Wish not found !");
        }

        return $this->render("wish/detail.html.twig", [
            "wish" => $wish
        ]);
    }
}
