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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
// use Symfony\Component\Routing\Annotation\Route;




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


// #[Route('/auteur', name: 'livres_auteur', methods:['GET','POST'])]
// public function livres_auteur(Request $request, LivresRepository $livresRepository, EntityManagerInterface $entityManager): Response
// {
//     $livre = new Livres(); // Créer une nouvelle instance de Livre
//     $form = $this->createFormBuilder($livre)
//         ->add('Nom_auteur', LivresType::class, [
//             'class' => Livres::class,
//             'choice_label' => 'Nom_auteur', // Champ de votre entité Livre contenant l'auteur
//             'multiple' => false, // Sélection unique
//             'expanded' => false, // Menu déroulant
//         ])
//         ->getForm();

//     $form->handleRequest($request);

//     // if ($form->isSubmitted() && $form->isValid()) {
//     //     // Traitez le formulaire, enregistrez les modifications, etc.
//     //     $entityManager->flush();

//     //     return $this->redirectToRoute('livres_auteur');
//     // }

//     return $this->render('livres/auteur.html.twig', [
//         'form' => $form->createView(),
//         'livres' => $livresRepository->findAll(),
//     ]);
// }

// #[Route('/auteur', name: 'livres_auteur', methods:['GET','POST'])]
// public function livres_auteur(Request $request, LivresRepository $livresRepository, EntityManagerInterface $entityManager): Response
// {
//     $livres = $livresRepository->findAll(); // Récupérer tous les livres

//     // Récupérer tous les noms d'auteurs distincts
//     $auteurs = array_unique(array_map(function($livre) {
//         return $livre->getNomAuteur();
//     }, $livres));
//     $id = array_unique(array_map(function($livre) {
//       return $livre->getId();
//   }, $livres));

//     // Créer le formulaire
//     $form = $this->createFormBuilder()
//         ->add('Nom_auteur', ChoiceType::class, [
//             'choices' => array_combine($auteurs, $id), // Utiliser les noms d'auteurs comme choix
//             'placeholder' => 'Choisir un auteur', // Placeholder
//             'required' => false, // Rendre le champ facultatif
//         ])
//         ->getForm();

//     $form->handleRequest($request);

//   //    if ($form->isSubmitted() && $form->isValid()) {
//   //     $data = $form->getData();
//   //     $nomAuteurChoisi = $data['Nom_auteur'];

//   //     // Récupérer l'id de l'auteur choisi depuis la base de données
//   //     $idAuteur = $livresRepository->findIdByNomAuteur($nomAuteurChoisi);

//   //     // Récupérer toutes les informations concernant cet auteur
//   //     $auteurInfos = $livresRepository->findBy(['id_auteur' => $idAuteur]);

//   //     return $this->render('livres/auteur_infos.html.twig', [
//   //         'auteurInfos' => $auteurInfos,
//   //     ]);
//   // }


//     return $this->render('livres/auteur.html.twig', [
//         'form' => $form->createView(),
//         'livres' => $livres,
//     ]);
// }


#[Route('/auteur', name: 'livres_auteur', methods:['GET','POST'])]
public function livres_auteur( Request $request, LivresRepository $livresRepository, EntityManagerInterface $entityManager): Response
{
    $livres = $livresRepository->findAll(); // Récupérer tous les livres

    // Créer le formulaire
    $form = $this->createFormBuilder()
        ->add('Nom_auteur', ChoiceType::class, [
            'choices' => $livres, // Utiliser les noms d'auteur comme choix
            'choice_label' => 'Nom_auteur',
            'choice_value' => 'id',
             'placeholder' => 'Choisir un auteur', 
             'required' => false, // Rendre le champ facultatif
             'multiple' => false
        ])
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $auteurchoisis = $form->get('Nom_auteur')->getData();
        $IdAuteur = $auteurchoisis->getId();

        return $this->render('livres/auteurinfos.html.twig', [
          'livres' => $livresRepository->find($IdAuteur),
          
        ]);
    }
   

    return $this->render('livres/auteur.html.twig', [
        'form' => $form->createView(),
      
  
    ]);
}
}

