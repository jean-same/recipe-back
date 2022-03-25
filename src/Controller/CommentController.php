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
}
