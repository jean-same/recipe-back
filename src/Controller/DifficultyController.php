<?php

namespace App\Controller;

use App\Service\commonMessageService;
use App\Repository\DifficultyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/v1/difficulty', name: 'app_v1_difficulty')]
class DifficultyController extends AbstractController
{
    private $em;
    private $validator;
    private $serializer;
    private $difficultyRepository;
    private $commonMessageService;

    public function __construct( DifficultyRepository $difficultyRepository , commonMessageService $commonMessageService, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $em )
    {
        $this->em                               = $em;
        $this->validator                        = $validator;
        $this->serializer                       = $serializer;
        $this->difficultyRepository             = $difficultyRepository;
        $this->commonMessageService             = $commonMessageService;
    }

    #[Route('/', name: 'browse', methods: ['GET'])]
    public function browse(): Response
    {
        $difficulties = $this->difficultyRepository->findForList();
        return $this->json($this->commonMessageService->found($difficulties), Response::HTTP_OK, []);
    }
}
