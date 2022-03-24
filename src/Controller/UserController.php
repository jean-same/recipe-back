<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\commonMessageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/v1/user', name: 'app_v1_user')]
class UserController extends AbstractController
{
    private $em;
    private $validator;
    private $serializer;
    private $userRepository;
    private $commonMessageService;

    public function __construct( UserRepository $userRepository , commonMessageService $commonMessageService, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $em )
    {
        $this->em                               = $em;
        $this->validator                        = $validator;
        $this->serializer                       = $serializer;
        $this->userRepository                   = $userRepository;
        $this->commonMessageService             = $commonMessageService;
    }

    #[Route('/{userId<\d+>}', name: 'read', methods: ['GET'])]
    public function read($userId): Response
    {
        $user = $this->userRepository->find($userId);

        //dd($user);
        return $this->json($this->commonMessageService->found($user), Response::HTTP_OK, [], ['groups' => "app_v1_user_browse"]);
    }

    #[Route('/{userId<\d+>}', name: 'edit', methods: ['PATCH'])]
    public function edit(int $userId ,  Request $request) : Response {

        $user = $this->userRepository->find($userId);

        if (is_null($user)) {
            return $this->commonMessageService->getNotFoundResponse();
        }

        $jsonContent = $request->getContent();

        $this->serializer->deserialize($jsonContent, User::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $user]);

        $errors = $this->validator->validate($user);

        $this->commonMessageService->errorsCheck($errors);

        $this->em->flush();

        $responseAsArray = [
            'message' => 'Utilisateur mis à jour',
            'title' => $user->getPseudo()
        ];

        return $this->json($responseAsArray, Response::HTTP_OK);

    }

    #[Route('', name: 'add', methods: ['POST'])]
    public function add(Request $request):Response
    {

        $jsonContent = $request->getContent();

        $user = $this->serializer->deserialize($jsonContent, User::class, 'json');

        $errors = $this->validator->validate($user);

        $this->commonMessageService->errorsCheck($errors);

        $this->em->persist($user);
        $this->em->flush();

        $responseAsArray = [
            'message' => 'Utilisateur ajouté',
            'title' => $user->getPseudo()
        ];

        return $this->json($responseAsArray, Response::HTTP_CREATED);
    }


    #[Route('/{userId<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $userId): Response
    {
        $user = $this->userRepository->find($userId);

        if (is_null($user)) {
            return $this->commonMessageService->getNotFoundResponse();
        }

        $userPseudo = $user->getPseudo();

        $this->em->remove($user);
        $this->em->flush();

        $responseAsArray = [
            'message' => 'Utilisateur supprimée',
            'title' => $userPseudo
        ];
        return $this->json($responseAsArray);
    }
}
