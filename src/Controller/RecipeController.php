<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Recipe;
use App\Repository\IngredientRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use App\Service\CheckUserAuthorizationService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/v1/recipe', name: 'app_v1_recipe_')]
class RecipeController extends AbstractController
{
    private $em;
    private $validator;
    private $serializer;
    private $recipeRepository;
    private $ingredientRepository;

    public function __construct( RecipeRepository $recipeRepository, IngredientRepository $ingredientRepository,  SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $em )
    {
        $this->em                               = $em;
        $this->validator                        = $validator;
        $this->serializer                       = $serializer;
        $this->recipeRepository                 = $recipeRepository;
        $this->ingredientRepository             = $ingredientRepository;
    }

    #[Route('/', name: 'browse' , methods: ['GET'])]
    public function browse(): Response
    {
        $recipes = $this->recipeRepository->findBy(["to_validate" => 0 ]) ;

        return $this->json($this->found($recipes), Response::HTTP_OK, [], ['groups' => "app_v1_recipe_browse"]);
    }

    #[Route('/spec', name: 'browse' , methods: ['GET'])]
    public function browseSpecificRecipe(Request $request): Response
    {
        $queryData = $request->query->get("q");
        $recipes = null;

        if($queryData != null) {
            if($queryData == "ml") {
                $recipes = $this->recipeRepository->findRecipeBy("likes");
            } elseif($queryData == "mr" ) {
                $recipes = $this->recipeRepository->findRecipeBy("created_at");
            }
        }

        return $this->json($this->found($recipes), Response::HTTP_OK, [], ['groups' => "app_v1_recipe_browse"]);
    }


    #[Route('/my-recipes', name: 'my_recipes' , methods: ['GET'])]
    public function myRecipes(Security $security): Response
    {
        /** @var User */
        $user = $security->getUser();

        $recipes = $user->getRecipes();

        return $this->json($this->found($recipes), Response::HTTP_OK, [], ['groups' => "app_v1_recipe_browse"]);
    }



    #[Route('/{recipeId<\d+>}', name: 'read', methods: ['GET'])]
    public function read(int $recipeId) : Response
    {
        $recipe = $this->recipeRepository->find($recipeId);

        if (is_null($recipe)) {
            return $this->getNotFoundResponse();
        }

        return $this->json($this->found($recipe), Response::HTTP_OK, [], ['groups' => "app_v1_recipe_browse"]);

    }
    

    #[Route('/{recipeId<\d+>}', name: 'edit', methods: ['PATCH'])]
    public function edit(int $recipeId ,  Request $request) : Response {

        $recipe = $this->recipeRepository->find($recipeId);

        if (is_null($recipe)) {
            return $this->getNotFoundResponse();
        }

        $this->denyAccessUnlessGranted('CURRENT_RECIPE_OWNER', $recipe , "Accès interdit");

        $jsonContent = $request->getContent();

        $this->serializer->deserialize($jsonContent, Recipe::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $recipe]);

        $errors = $this->validator->validate($recipe);

        $this->errorsCheck($errors);

        $this->em->flush();

        $responseAsArray = [
            'message' => 'Recette mise à jour',
            'title' => $recipe->getTitle()
        ];

        return $this->json($responseAsArray, Response::HTTP_OK);

    }

    #[Route('/', name: 'add', methods: ['POST'])]
    public function add(Request $request):Response
    {
        $jsonContent = $request->getContent();

        $recipe = $this->serializer->deserialize($jsonContent, Recipe::class, 'json');

        $errors = $this->validator->validate($recipe);

        $this->errorsCheck($errors);

        $this->em->persist($recipe);
        $this->em->flush();

        $responseAsArray = [
            'message' => 'Recette ajoutée',
            'title' => $recipe->getTitle()
        ];

        return $this->json($responseAsArray, Response::HTTP_CREATED);
    }

    #[Route('/{recipeId<\d+>}/add-ingredients', name: 'add_ingredients', methods: ['PATCH'])]
    public function addIngredients($recipeId, Request $request):Response
    {

        $recipe = $this->recipeRepository->find($recipeId);

        if (is_null($recipe)) {
            return $this->getNotFoundResponse();
        }

        $this->denyAccessUnlessGranted('CURRENT_RECIPE_OWNER', $recipe , "Accès interdit");

        $jsonContent = $request->getContent();
        $jsonDecoded = json_decode($jsonContent);
        $ingredientAdded = 0;
        

        foreach($jsonDecoded as $ingredientId) {
            $ingredient = $this->ingredientRepository->find($ingredientId);

            if(!in_array($ingredient, $recipe->getIngredients()->toArray() ) ) {
                $recipe->addIngredient($ingredient);
                $ingredientAdded++;
            }
           
        }

        $this->em->persist($recipe);
        $this->em->flush();

        $responseAsArray = [
            'ingredient ajouté' => $ingredientAdded,
            'message' => "Ingredient ajouté: $ingredientAdded"
        ];

        return $this->json($responseAsArray, Response::HTTP_OK);
    }

    #[Route('/{recipeId<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $recipeId): Response
    {
        $recipe = $this->recipeRepository->find($recipeId);

        if (is_null($recipe)) {
            return $this->getNotFoundResponse();
        }

        $this->denyAccessUnlessGranted('CURRENT_RECIPE_OWNER', $recipe , "Accès interdit");

        $this->em->remove($recipe);
        $this->em->flush();

        $responseAsArray = [
            'message' => 'Recette supprimée',
            'title' => $recipe->getTitle()
        ];
        return $this->json($responseAsArray);
    }

    public function found($result) {
        return [
            "message" => "success",
            "result" => $result,
        ];
    }

    private function getNotFoundResponse()
    {
        $responseArray = [
            'error' => true,
            'userMessage' => 'Ressource non trouvé',
        ];

        return $this->json($responseArray, Response::HTTP_GONE);
    }

    private function errorsCheck($errors) {

        if (count($errors) > 0) {
            $responseAsArray = [
                'error' => true,
                'message' => $errors
            ];
            return $this->json($responseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
