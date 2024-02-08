<?php

namespace App\Controller;

use App\Form\FournisseursType;
use App\Repository\FournisseursRepository;
use App\Entity\Fournisseurs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;


// .......................afficher tous les fournisseurs.....................
#[Route('/fournisseurs')]
class FournisseursController extends AbstractController
{
    #[Route('/', name: 'app_fournisseurs',methods:['GET'])]
    public function index(FournisseursRepository $fournisseursRepository): Response
    {
        return $this->render('fournisseurs/index.html.twig', [
            'fournisseurs' => $fournisseursRepository->findAll()
        ]);
    }

    
    // ..............................suprimer un fournisseur..................................

    #[Route('/fournisseurs/{id}/delete', name: 'fournisseurs_delete')]
    public function delete( int $id, EntityManagerInterface $entityManager,  FournisseursRepository $fournisseursRepository ): Response
    {
    $fournisseur = $fournisseursRepository->find($id);
        var_dump($fournisseur);
        $entityManager->remove($fournisseur);

        $entityManager->flush();
        return $this->redirectToRoute('app_fournisseurs');
    }

// ...........................Mettre a jours un fournisseur.........................

    #[Route('/fournisseurs{id}/edit', name: 'fournisseurs_edit',methods:['GET','POST'])]
    public function fournisseurs_edit(int $id, Request $request, FournisseursRepository $fournisseursRepository, EntityManagerInterface $entityManager): Response
    {
      $form= $this-> createForm(FournisseursType::class, $fournisseursRepository->find($id));
      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid()) {
          $entityManager->flush();

      return $this->redirectToRoute('app_fournisseurs',[],
      Response::HTTP_SEE_OTHER);
    }
    return $this->render('fournisseurs/edit.html.twig', [
      'form'=> $form, 'fournisseur'=> $fournisseursRepository->findAll(),
    ]);
  }
// .........................ajouter un fournisseur.......................
  
  #[Route('/Add', name: 'fournisseurs_add',methods:['GET','POST'])]
  public function fournisseurs_add( Request $request, FournisseursRepository $fournisseursRepository, EntityManagerInterface $entityManager): Response
  {
    $fournisseur = new Fournisseurs();
    $form= $this-> createForm(fournisseursType::class,$fournisseur);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($fournisseur);
        $entityManager->flush();

    return $this->redirectToRoute('app_fournisseurs',[],
    Response::HTTP_SEE_OTHER);
  }
  return $this->render('fournisseurs/add.html.twig', [
    'form'=> $form->createView() ,
  ]);
}

    

}
