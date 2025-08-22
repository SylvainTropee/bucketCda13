<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishType;
use App\Repository\WishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/wish', name: 'wish_')]
final class WishController extends AbstractController
{
    #[Route('/list', name: 'list')]
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

        if (!$wish) {
            throw $this->createNotFoundException("Oooops ! Wish not found !");
        }

        return $this->render("wish/detail.html.twig", [
            "wish" => $wish
        ]);
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $wish = new Wish();
        $wishForm = $this->createForm(WishType::class, $wish);

        $wishForm->handleRequest($request);

        if ($wishForm->isSubmitted() && $wishForm->isValid()) {
            //extraire le fichier de type UploadedFile
            $image = $wishForm->get('wishImage')->getData();

            /**
             * @var UploadedFile $image
             */
            $newFileName = uniqid() . '.' . $image->guessExtension();
            $image->move($this->getParameter('wish_image_dir'), $newFileName);

            $wish->setWishImage($newFileName);
            $entityManager->persist($wish);
            $entityManager->flush();

            $this->addFlash("success", "Idea successfully added !");
            return $this->redirectToRoute('wish_detail', ['id' => $wish->getId()]);
        }

        return $this->render("wish/add.html.twig", [
            "wishForm" => $wishForm
        ]);
    }

    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '\d+'])]
    public function delete(
        int                    $id,
        WishRepository         $wishRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $wish = $wishRepository->find($id);

        if (!$wish) {
            throw $this->createNotFoundException("Ooops ! Wish not found !");
        }

        $entityManager->remove($wish);
        $entityManager->flush();

        $this->addFlash('success', 'Idea successfully deleted !');
        return $this->redirectToRoute('wish_list');
    }


    #[Route('/update/{id}', name: 'update')]
    public function update(
        Request $request,
        EntityManagerInterface $entityManager,
        WishRepository $wishRepository,
        int $id
    ): Response
    {
        $wish = $wishRepository->find($id);

        if(!$wish){
            throw $this->createNotFoundException('Ooops ! Wish not found !');
        }

        $wishForm = $this->createForm(WishType::class, $wish);
        $wishForm->handleRequest($request);

        if ($wishForm->isSubmitted() && $wishForm->isValid()) {


            $entityManager->persist($wish);
            $entityManager->flush();

            $this->addFlash("success", "Idea successfully updated !");
            return $this->redirectToRoute('wish_detail', ['id' => $wish->getId()]);
        }

        return $this->render("wish/update.html.twig", [
            "wishFormUpdate" => $wishForm
        ]);
    }


}
