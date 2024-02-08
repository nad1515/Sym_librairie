<?php

namespace App\Controller;
 
use App\Entity\Livres;
use App\Form\LivresType;
use App\Repository\LivresRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//  use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/livres')]
class LivresController extends AbstractController
{
    #[Route('/', name: 'app_livres',methods:['GET'])]
    public function index(LivresRepository $livresRepository): Response
    {
        return $this->render('livres/index.html.twig', [
            'livres' => $livresRepository->findAll(),
        ]);
    }

    // ..............................suprimer un livre..................................

    #[Route('/livres/{id}/delete', name: 'livres_delete')]
    public function delete( int $id, EntityManagerInterface $entityManager,  LivresRepository $livresRepository ): Response
    {
        $livre = $livresRepository->find($id);
        var_dump($livre);
        $entityManager->remove($livre);

        $entityManager->flush();
        return $this->redirectToRoute('app_livres');
    }

// ...........................Mettre a jours un livre.........................

    #[Route('/livres{id}/edit', name: 'livres_edit',methods:['GET','POST'])]
    public function livres_edit(int $id, Request $request, LivresRepository $livresRepository, EntityManagerInterface $entityManager): Response
    {
      $form= $this-> createForm(LivresType::class, $livresRepository->find($id));
      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid()) {
          $entityManager->flush();

      return $this->redirectToRoute('app_livres',[],
      Response::HTTP_SEE_OTHER);
    }
    return $this->render('livres/edit.html.twig', [
      'form'=> $form, 'livre'=> $livresRepository->findAll(),
    ]);
  }
// ............................ajouter un livre..........................

  #[Route('/Add', name: 'livres_add',methods:['GET','POST'])]
  public function livres_add( Request $request, LivresRepository $livresRepository, EntityManagerInterface $entityManager): Response
  {
    $livre = new Livres();
    $form= $this-> createForm(LivresType::class,$livre);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($livre);
        $entityManager->flush();

    return $this->redirectToRoute('app_livres',[],
    Response::HTTP_SEE_OTHER);
  }
  return $this->render('livres/add.html.twig', [
    'form'=> $form->createView() ,
  ]);
}

    

}

