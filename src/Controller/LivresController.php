<?php

// namespace App\Controller;
 
// use App\Entity\Livres;
// use App\Form\LivresType;
// use App\Repository\LivresRepository;
// use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\Routing\Annotation\Route;
// //  use Symfony\Component\BrowserKit\Request;
// use Symfony\Component\HttpFoundation\Request;
// use Doctrine\ORM\EntityManagerInterface;
// use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\LivresRepository;
use App\Form\LivresType;
use App\Entity\Livres;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


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

// ...............choix du livre par auteur..............................

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
        ->add('auteur', ChoiceType::class, [
            'choices' => $livres, // Utiliser les noms d'auteur comme choix
            'choice_label' => 'Nomauteur',
            'choice_value' => 'Nomauteur',
             'placeholder' => 'Choisir un auteur', 
             'required' => false, // Rendre le champ facultatif
             'multiple' => false
        ])
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      
        $auteurchoisis = $form->get('auteur')->getData();
        $NomAuteur = $auteurchoisis->getNomAuteur();
        return $this->render('livres/auteurinfos.html.twig', [
        //  'livres' => $livresRepository->findBy(['Nom_auteur' => $NomAuteur]),
          
      ]);
     }
    //  else {
    //   dump($form->getErrors());                   
    //  }
      return $this->render('livres/auteur.html.twig', [
        'form' => $form->createView(),
      
  
    ]);
}
#[Route('/titre', name: 'livres_titre', methods: ['GET', 'POST'])]
        public function livres_titre(Request $request, LivresRepository $livresRepository, EntityManagerInterface $entityManager): Response
        {
            $livres = $livresRepository->findAll();
            $form = $this->createFormBuilder()
                    ->add('Titre', ChoiceType::class, [
                        'choices' => $livres, 
                        'choice_label' => 'titreLivre',
                        'choice_value' => 'id',
                        'placeholder' => 'Choisir un titre', 
                        'required' => false, 
                    ])
                    ->getForm();
            $form->handleRequest($request);
            if ($form->isSubmitted()) {
              if ($form->isValid()) {
                  // Le formulaire est soumis et valide
                  // Effectuez ici les actions nécessaires
          
                  // Par exemple, récupérez l'ID du livre sélectionné
                  $livreId = $form->get('Titre')->getData();
                  
                  // Utilisez l'ID pour récupérer les informations du livre depuis le repository
                  $livreSelectionne = $livresRepository->find($livreId);
                  
                  // Affichez les informations du livre dans un autre template Twig
                  return $this->render('livres/titreresult.html.twig', [
                      'livre' => $livreSelectionne,
                  ]);
              } else {
                  // Le formulaire est soumis mais non valide
                  // Traitez les erreurs de validation ici, si nécessaire
          
                  // Par exemple, récupérez les erreurs de validation
                  // $errors = $form->getErrors(true, false);
                  dump($form->getErrors());  
                  // Traitez les erreurs, par exemple en les affichant ou en les enregistrant dans un journal
              }
          } else {
              // Le formulaire n'a pas encore été soumis
              // Vous pouvez ignorer cette condition si vous ne devez pas traiter le formulaire non soumis différemment
          
              // Créez simplement le formulaire et affichez-le dans la vue Twig comme d'habitude
              // Cela peut être fait en dehors de la condition if
          }
          
          // Si vous avez besoin d'afficher le formulaire dans tous les cas, y compris quand il n'est pas encore soumis
          // Vous pouvez placer cette partie du code à l'extérieur de la condition if
          return $this->render('livres/titre.html.twig', [
              'form' => $form->createView(),
          ]);
                    
          //   if ($form->isSubmitted() && $form->isValid()) {
          //       $livreId = $form->get('Titre')->getData();
          //       // $livreId = $livreSelectionne->getId();
          //       $livreSelectionne= $livresRepository->find($livreId);
          //       return $this->render('livres/titreinfos.html.twig', [
          //           'livre' => $livreSelectionne,
          //       ]);
          //   }
          //   return $this->render('livres/titre.html.twig', [
          //       'form' => $form->createView(),
          //   ]);
          // }

}

}