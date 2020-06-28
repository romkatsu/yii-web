<?php

namespace Yiisoft\Yii\Web\Data;

use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Validator\DataSetInterface;

class DataRequestSet implements DataSetInterface
{
    private ServerRequestInterface $request;

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    public function getAttributeValue(string $attribute)
    {
        if (!$this->hasAttribute($attribute)) {
            return null;
        }

        return $this->getParams()[$attribute];
    }

    public function hasAttribute(string $attribute): bool
    {
        return isset($this->getParams()[$attribute]);
    }

    private function getParams(): array
    {
        return array_merge($this->request->getQueryParams(), $this->request->getParsedBody());
    }
}
