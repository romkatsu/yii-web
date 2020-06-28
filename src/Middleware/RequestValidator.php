<?php

namespace Yiisoft\Yii\Web\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Http\Status;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\Web\Data\DataRequestSet;

final class RequestValidator implements MiddlewareInterface
{
    private ValidatorInterface $validator;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(ValidatorInterface $validator, ResponseFactoryInterface $responseFactory)
    {
        $this->validator = $validator;
        $this->responseFactory = $responseFactory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->validate($request)) {
            $response = $this->responseFactory->createResponse(Status::BAD_REQUEST);
            $response->getBody()->write(Status::TEXTS[Status::BAD_REQUEST]);
            return $response;
        }

        return $handler->handle($request);
    }

    private function validate(ServerRequestInterface $request): bool
    {
        /**
         * @var Result $result
         */
        foreach ($this->validator->validate($this->getDataSet($request)) as $result) {
            if (!$result->isValid()) {
                return false;
            }
        }

        return true;
    }

    private function getDataSet(ServerRequestInterface $request): DataSetInterface
    {
        return new DataRequestSet($request);
    }
}
