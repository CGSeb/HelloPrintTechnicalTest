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

    /**
     * @param array $request
     */
    public function sendLoginRequest($request)
    {
        if ($request['queue'] != '' && $request['request-id'] != '') {
            $this->loginProducer = new LoginProducer($request);
            $this->loginProducer->consume();
        } else {
            $this->loginProducer = new LoginProducer();
            $loginUuid = Uuid::uuid4()->toString();
            $this->username = $request['username'];
            $this->password = $request['password'];
            $this->assertValidRequest();

            $queueName = $this->loginProducer->publish($this->username, $this->password, $loginUuid);
            $this->loginProducer->closeQueue();
            echo json_encode([
                'success' => true,
                'request-id' => $loginUuid,
                'queue' => $queueName,
            ]);
            exit();
        }
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