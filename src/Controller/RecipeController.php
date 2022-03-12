<?php

namespace App\Controller;

use App\Entity\Recipe;
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
    private $checkUserAuthorizationService;

    public function __construct( RecipeRepository $recipeRepository,  SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $em , CheckUserAuthorizationService $checkUserAuthorizationService)
    {
        $this->em                               = $em;
        $this->validator                        = $validator;
        $this->serializer                       = $serializer;
        $this->recipeRepository                 = $recipeRepository;
        $this->checkUserAuthorizationService    = $checkUserAuthorizationService;
    }

    #[Route('/', name: 'browse' , methods: ['GET'])]
    public function browse(): Response
    {
        $recipes = $this->recipeRepository->findAll();

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

        $this->checkUserAuthorizationService->isAllow($recipe);

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

    #[Route('/{recipeId<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $recipeId): Response
    {
        $recipe = $this->recipeRepository->find($recipeId);

        if (is_null($recipe)) {
            return $this->getNotFoundResponse();
        }

        $this->checkUserAuthorizationService->isAllow($recipe);

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
