<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Service\commonMessageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/v1/comment', name: 'app_v1_comment')]
class CommentController extends AbstractController
{

    private $em;
    private $validator;
    private $serializer;
    private $commentRepository;
    private $commonMessageService;

    public function __construct( CommentRepository $commentRepository , commonMessageService $commonMessageService, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $em )
    {
        $this->em                               = $em;
        $this->validator                        = $validator;
        $this->serializer                       = $serializer;
        $this->commentRepository                   = $commentRepository;
        $this->commonMessageService             = $commonMessageService;
    }

    #[Route('', name: 'add', methods: ['POST'])]
    public function add(Request $request):Response
    {

        $jsonContent = $request->getContent();

        $comment = $this->serializer->deserialize($jsonContent, Comment::class, 'json');

        $errors = $this->validator->validate($comment);

        $this->commonMessageService->errorsCheck($errors);

        $this->em->persist($comment);
        $this->em->flush();

        $responseAsArray = [
            'message' => 'Commentaire ajouté',
            'title' => $comment->getId()
        ];

        return $this->json($responseAsArray, Response::HTTP_CREATED);
    }

    #[Route('/{commentId<\d+>}', name: 'edit', methods: ['PATCH'])]
    public function edit(int $commentId ,  Request $request) : Response {

        $comment = $this->commentRepository->find($commentId);

        if (is_null($comment)) {
            return $this->commonMessageService->getNotFoundResponse();
        }

        $jsonContent = $request->getContent();

        $this->serializer->deserialize($jsonContent, Comment::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $comment]);

        $errors = $this->validator->validate($comment);

        $this->commonMessageService->errorsCheck($errors);
        

        $this->em->flush();

        $responseAsArray = [
            'message' => 'Recette mise à jour',
            'title' => $comment->getId()
        ];

        return $this->json($responseAsArray, Response::HTTP_OK);

    }

    #[Route('/{commentId<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $commentId): Response
    {
        $comment = $this->commentRepository->find($commentId);

        if (is_null($comment)) {
            return $this->commonMessageService->getNotFoundResponse();
        }

        $this->isGranted('USER_HAS_RIGHT', $comment , "Accès interdit" );

        $this->em->remove($comment);
        $this->em->flush();

        $responseAsArray = [
            'message' => 'Commentaire supprimée'
        ];
        return $this->json($responseAsArray);
    }
}
