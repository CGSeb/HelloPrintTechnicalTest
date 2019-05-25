<?php
namespace API;

use API\Entity\User;
use API\Tools\DbConnector;

class LoginController {

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @ User
     */
    private $user;

    /**
     * @var DbConnector
     */
    private $dbConnector;

    public function __construct($request)
    {
        $this->username = $request['username'];
        $this->password = $request['password'];
        $this->dbConnector = new DbConnector('helloprint-db', 'root', 'root', 3306);
        $this->assertUserExist();
    }

    private function assertUserExist()
    {
        if (!$this->username) {
            echo json_encode([
                'success' => false,
                'message' => "Username must be set!",
            ]);
            exit();
        }

        if (!$this->password) {
            echo json_encode([
                'success' => false,
                'message' => "Password must be set!",
            ]);
            exit();
        }

        $this->user = $this->dbConnector->getUserByUsername($this->username);

        if (!($this->user instanceof User)) {
            echo json_encode([
                'success' => false,
                'message' => "This username doesn't exist!",
            ]);
            exit();
        }

        if ($this->user->getPassword() !== $this->password) {
            echo json_encode([
                'success' => false,
                'message' => "Wrong credentials!",
            ]);
            exit();
        }
    }

    public function login()
    {
        echo json_encode([
            'success' => true,
            'username' => $this->user->getUsername(),
            'email' => $this->user->getEmail(),
        ]);
        exit();
    }
}