<?php


namespace Yiisoft\Yii\Web\Tests\Middleware;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Http\Method;
use Yiisoft\Http\Status;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Validator;
use Yiisoft\Yii\Web\Middleware\RequestValidator;
use Yiisoft\Yii\Web\Tests\Middleware\Mock\MockRequestHandler;

class ValidatorTest extends TestCase
{
    public function testValidRequest(): void
    {
        $middleware = new RequestValidator($this->createValidator(), new Psr17Factory());
        $request = $this->createServerRequest(
            Method::POST,
            [
                'page' => 10,
                'per_page' => 20
            ]
        );

        $response = $middleware->process($request, new MockRequestHandler());
        $this->assertEquals(Status::OK, $response->getStatusCode());
    }

    public function testInvalidRequest(): void
    {
        $middleware = new RequestValidator($this->createValidator(), new Psr17Factory());
        $request = $this->createServerRequest(
            Method::POST,
            [
                'page' => 'XXX',
                'per_page' => 20
            ]
        );

        $response = $middleware->process($request, new MockRequestHandler());
        $this->assertEquals(Status::BAD_REQUEST, $response->getStatusCode());
    }

    private function createServerRequest(
        string $method = Method::POST,
        array $bodyParams = [],
        array $queryParams = []
    ): ServerRequestInterface {
        return (new ServerRequest($method, '/blog/index'))
            ->withParsedBody($bodyParams)
            ->withQueryParams($queryParams);
    }

    private function createValidator(): Validator
    {
        return new Validator(
            [
                'page' => [
                    (new Number())->integer(),
                    (new Required())
                ],
                'per_page' => [
                    (new Number())->integer(),
                ],
            ]
        );
    }
}
