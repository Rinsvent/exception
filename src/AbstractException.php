<?php

namespace Rinsvent\Exception;

use Exception;
use Throwable;

abstract class AbstractException extends Exception
{
    public static string $idRegisterClass = IdRegister::class;
    public static ?int $rootExceptionPosition = null;

    protected ?string $codeText = null;

    public function __construct($message = "", Throwable $previous = null)
    {
        $this->codeText = $this->codeText ?? $this->grabCodeText();
        parent::__construct($message, self::$idRegisterClass::MAP[static::class] ?? 0, $previous);
    }

    public function getCodeText(): string
    {
        return $this->codeText;
    }

    private function grabCodeText(): string
    {
        $class = static::class;

        $classParts = explode('\\', $class);
        $classParts = array_reverse($classParts);
        $position = self::$rootExceptionPosition ?? array_search('Exception', $classParts, true);
        $classParts = array_reverse($classParts);
        $classParts = array_slice($classParts, $position);

        $result = [];
        foreach ($classParts as $classPart) {
            $result[] = Inflector::tableize($classPart);
        }

        return implode('.', $result);
    }
}
