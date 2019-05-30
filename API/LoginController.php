<?php
namespace API;

use API\Tools\LoginProducer;
use Ramsey\Uuid\Uuid;

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
     * @var LoginProducer
     */
    private $loginProducer;

    public function __construct()
    {
        $this->loginProducer = new LoginProducer();
    }

    /**
     * @param array $request
     */
    public function sendLoginRequest($request)
    {
        $this->username = $request['username'];
        $this->password = $request['password'];
        $this->assertValidRequest();

        $loginUuid = Uuid::uuid4()->toString();
        $this->loginProducer->publish($this->username, $this->password, $loginUuid);
        $this->loginProducer->close();
    }

    private function assertValidRequest()
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
    }
}