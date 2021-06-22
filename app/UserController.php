<?php

include('UserRepository.php');
include('MessageBroker.php');

class UserController {

    private $requestMethod;

    private $userRepository;

    public function __construct($db, $requestMethod, $userId)
    {
        $this->requestMethod = $requestMethod;
        $this->userRepository = new UserRepository($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'POST':
                $response = $this->createUserFromRequest();
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function createUserFromRequest()
    {
        $input = $_POST;
        if (!$this->validateUser($input)) {
            return $this->unprocessableEntityResponse();
        }
        $idUser = $this->userRepository->insert($input);
        if (!$idUser) {
            // TODO: handle duplicate entries error (http 409)
            return $this->unprocessableEntityResponse();
        }

        $messageBroker = new MessageBroker();
        $message = json_encode([
            'user_id' => $idUser,
            'image_base64' => $input['image'],
        ]);
        $messageBroker->sendMessage($message);

        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }


    private function validateUser($input)
    {
        if (!isset($input['username']) || empty(str_replace(' ', '', $input['username']))) {
            return false;
        }
        if (!isset($input['password']) || empty(str_replace(' ', '', $input['password']))) {
            return false;
        }
        if (!isset($input['email']) || empty(str_replace(' ', '', $input['email']))) {
            return false;
        }
        if (!isset($input['image']) || empty(str_replace(' ', '', $input['image']))) {
            return false;
        }
        return true;
    }

    
    private function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 405 Invalid input';
        $response['body'] = json_encode([
            'error' => 'Invalid input'
        ]);
        return $response;
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}