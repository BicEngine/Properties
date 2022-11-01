<?php

declare(strict_types=1);

namespace Bic\Properties\Exception;

/**
 * @psalm-consistent-constructor
 */
class PropertyException extends \Exception
{
    public const CODE_NON_READABLE = 0x01;
    public const CODE_NON_WRITABLE = 0x02;
    public const CODE_INVALID_DEFINITION = 0x03;

    /**
     * @param non-empty-string $message
     */
    public function __construct(string $message, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @param non-empty-string $message
     */
    public static function fromInvalidDefinition(string $message): static
    {
        return new static($message, self::CODE_INVALID_DEFINITION);
    }

    /**
     * @param class-string $class
     * @param non-empty-string $property
     */
    public static function fromNonReadable(string $class, string $property): static
    {
        $message = \sprintf('Cannot read writeonly property %s::$%s', $class, $property);

        return new static($message, self::CODE_NON_READABLE);
    }

    /**
     * @param class-string $class
     * @param non-empty-string $property
     */
    public static function fromNonWritable(string $class, string $property): static
    {
        $message = \sprintf('Cannot modify readonly property %s::$%s', $class, $property);

        return new static($message, self::CODE_NON_WRITABLE);
    }

    public static function fromThrowable(\Throwable $e): static
    {
        $trace = \debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS);

        $e = $e instanceof self
            ? new ($e::class)($e->getMessage(), $e->getCode())
            : new static($e->getMessage(), $e->getCode());

        if (isset($trace[1]['file'], $trace[1]['line'])) {
            (fn (): array => ['file' => $this->file, 'line' => $this->line] = $trace[1])
                ->call($e);
        }

        return $e;
    }
}
