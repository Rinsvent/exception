<?php

declare(strict_types=1);

namespace Rinsvent\Exception;

use Exception;
use Throwable;

abstract class AbstractException extends Exception
{
    public static string $exceptionEnum = '';

    private const ERROR_MESSAGE = 'Use AbstractException::$exceptionEnum = ExceptionEnum::class; You can set any instance of CodeInterface';

    protected ?string $codeText = null;

    public function __construct($message = "", Throwable $previous = null)
    {
        if (!enum_exists(self::$exceptionEnum)) {
            throw new \Exception(self::ERROR_MESSAGE);
        }
        if (!is_subclass_of(self::$exceptionEnum, CodeInterface::class)) {
            throw new \Exception(self::ERROR_MESSAGE);
        }
        /** @var CodeInterface $exceptionEnum */
        $exceptionEnum = self::$exceptionEnum::tryFrom(static::class);
        parent::__construct($message, $exceptionEnum?->code() ?? 0, $previous);
    }

    public function getCodeText(): string
    {
        return $this->codeText ?? $this->grabCodeText();
    }

    public function getSummary(): string
    {
        if ($message = $this->getMessage()) {
            return $message;
        }

        $rc = new \ReflectionClass(static::class);
        $shortName = $rc->getShortName();
        $shortName = Inflector::tableize($shortName);
        $shortName = str_replace('_', ' ', $shortName);
        return ucfirst($shortName);
    }

    private function grabCodeText(): string
    {
        $class = static::class;

        $classParts = explode('\\', $class);
        $position = array_search('Exception', $classParts, true);
        $classParts = array_slice($classParts, $position);

        $result = array_map(
            static fn (string $classPart) => Inflector::tableize($classPart),
            $classParts
        );
        return implode('.', $result);
    }
}
