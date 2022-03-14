<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class commonMessageService {

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

        return new JsonResponse($responseArray, Response::HTTP_GONE);
    }

    private function errorsCheck($errors) {

        if (count($errors) > 0) {
            $responseAsArray = [
                'error' => true,
                'message' => $errors
            ];
            return new JsonResponse($responseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}