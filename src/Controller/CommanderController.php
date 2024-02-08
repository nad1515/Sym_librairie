<?php

namespace App\Controller;
use App\Entity\Commander;
use App\Form\CommanderType; 
use App\Repository\CommanderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class CommanderController extends AbstractController
{
    #[Route('/commander', name: 'app_commander',methods:['GET'])]
    public function index(CommanderRepository $commanderRepository): Response
    {
        return $this->render('commander/index.html.twig', [
            'commandes' => $commanderRepository->findAll(),
        ]);
    }
  // ..............................suprimer une commande..................................

  #[Route('/commander/{id}/delete', name: 'commander_delete')]
  public function delete( int $id, EntityManagerInterface $entityManager,  CommanderRepository $commangerRepository ): Response
  {
      $commande = $commangerRepository->find($id);

      $entityManager->remove($commande);

      $entityManager->flush();
      return $this->redirectToRoute('app_commander');
  }

// ...........................Mettre a jours une commande.........................

  #[Route('/commander{id}/edit', name: 'commander_edit',methods:['GET','POST'])]
  public function commander_edit(int $id, Request $request, CommanderRepository $commangerRepository , EntityManagerInterface $entityManager): Response
  {
    $form= $this-> createForm(CommanderType::class, $commangerRepository->find($id));
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();

    return $this->redirectToRoute('app_commander',[],
    Response::HTTP_SEE_OTHER);
  }
  return $this->render('commander/edit.html.twig', [
    'form'=> $form, 'commande'=> $commangerRepository->findAll(),
  ]);
}
// ............................ajouter une commande..........................

#[Route('/Add', name: 'commander_add',methods:['GET','POST'])]
public function livres_add( Request $request, CommanderRepository $commangerRepository, EntityManagerInterface $entityManager): Response
{
  $commande = new Commander();
  $form= $this-> createForm(CommanderType::class,$commande);
  $form->handleRequest($request);
  if ($form->isSubmitted() && $form->isValid()) {
      $entityManager->persist($commande);
      $entityManager->flush();

  return $this->redirectToRoute('app_commander',[],
  Response::HTTP_SEE_OTHER);
}
return $this->render('commander/add.html.twig', [
  'form'=> $form->createView() ,
]);
}

}

  
