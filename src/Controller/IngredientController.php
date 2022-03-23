<?php

namespace App\Controller;

use App\Service\commonMessageService;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/api/v1/ingredient', name: 'app_v1_ingredient')]
class IngredientController extends AbstractController
{
    private $em;
    private $validator;
    private $serializer;
    private $ingredientRepository;
    private $commonMessageService;

    public function __construct( IngredientRepository $ingredientRepository , commonMessageService $commonMessageService, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $em )
    {
        $this->em                               = $em;
        $this->validator                        = $validator;
        $this->serializer                       = $serializer;
        $this->ingredientRepository             = $ingredientRepository;
        $this->commonMessageService             = $commonMessageService;
    }

    #[Route('/', name: 'browse', methods: ['GET'])]
    public function browse(): Response
    {
        $ingredients = $this->ingredientRepository->findForList();
        return $this->json($this->commonMessageService->found($ingredients), Response::HTTP_OK, []);
    }
}
