<?php

namespace App\Controller;
use App\Entity\Ingredient;
use App\Form\IngredientType;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController
{
   
    /**
     * Controller  pour lister les ingrédients.
     * 
     * @param  EntityManagerInterface $entityManager 
     * @return Response
     */
     

    #[Route('/ingredient', name: 'app_ingredient', methods:['GET'])]
    public function index(IngredientRepository  $repository, PaginatorInterface $paginator, Request $request): Response
    {
    
        $ingredients = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1),
            10
        );


            return $this->render('pages/ingredient/index.html.twig', [
                'ingredient' => $ingredients
        ]);
    }

    /*****************************
     * 
     * Ajouter un ingrédient
     * 
     * ***********************************/
    
    #[Route('/ingredient/nouveau', 'ingredient.new' , methods : [ "GET", "POST" ])]
    public function new( 
        Request $request,
        EntityManagerInterface $manager
    ) : Response {

       $ingredient = new Ingredient();
       $form = $this->createForm(IngredientType::class, $ingredient);

       $form->handleRequest( $request ) ;
        
       if($form->isSubmitted() && $form->isValid()) {
           // Enregistrement de l'objet dans la BDD 
           $ingredient = $form->getData();

            $manager->persist($ingredient) ;
            $manager->flush();

            $this->addFlash(
                'success',
                'votre ingredient a bien été ajouté!'
            );
            return $this->redirectToRoute("app_ingredient");

            // return $this->redirectToRoute('ingredient.index') ;

        }

        return $this->render('pages/ingredient/new.html.twig' , [
            'form' => $form->createView()
        ]);
    }

    /*
     * Afficher le formulaire d'édition pour une recette
     */

    #[Route('/ingredient/editer/{id}', name: 'ingredient.edit' , methods:  ['GET','POST'])]
    public function edit( Ingredient $ingredient ) : Response {
        

         $form = $this->createForm(IngredientType::class, $ingredient);

         return  $this->render('pages\ingredient\edit.html.twig' , [
                 'form' => $form->createView()

              ]);
    }

}
