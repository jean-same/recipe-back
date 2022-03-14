<?php

namespace App\Controller;

use App\Repository\TypeRepository;
use App\Service\commonMessageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/v1/type', name: 'app_v1_type_')]
class TypeController extends AbstractController
{
    private $em;
    private $validator;
    private $serializer;
    private $typeRepository;
    private $commonMessageService;

    public function __construct( TypeRepository $typeRepository, commonMessageService $commonMessageService, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $em )
    {
        $this->em                               = $em;
        $this->validator                        = $validator;
        $this->serializer                       = $serializer;
        $this->typeRepository                   = $typeRepository;
        $this->commonMessageService             = $commonMessageService;
    }

    #[Route('/', name: 'browse', methods: ['GET'])]
    public function browse(): Response
    {
        $types = $this->typeRepository->findByNames();

        return $this->json($this->commonMessageService->found($types), Response::HTTP_OK, []);
    }

}
