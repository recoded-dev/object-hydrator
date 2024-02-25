<?php

namespace Recoded\ObjectHydrator\Attributes;

use Attribute;
use Closure;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use InvalidArgumentException;
use Recoded\ObjectHydrator\Contracts\Mapping\DataMapper;
use RuntimeException;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_CLASS)]
class ToDateTime implements DataMapper
{
    protected static bool $immutable = false;

    protected static ?Closure $hydrateUsing = null;

    /**
     * Create a new ToDateTime instance.
     *
     * @param string|null $format
     * @param string|\DateTimeZone|null $timezone
     * @return void
     */
    public function __construct(
        public readonly ?string $format = null,
        public readonly string|DateTimeZone|null $timezone = null,
    ) {
        //
    }

    /**
     * Map data.
     *
     * @param mixed $value
     * @param string $name
     * @param array<array-key, mixed>|object $data
     * @return mixed
     * @throws \Exception
     */
    public function map(mixed $value, string $name, array|object $data): mixed
    {
        $timezone = is_string($this->timezone)
            ? new DateTimeZone($this->timezone)
            : $this->timezone;

        if (static::$hydrateUsing !== null) {
            return call_user_func(static::$hydrateUsing, $value, $this->format, $timezone);
        }

        if (!is_string($value) && $value !== null) {
            throw new InvalidArgumentException('Datetime value should either be a string or null');
        }

        return static::hydrate($value, $this->format, $timezone);
    }

    /**
     * Default hydration behaviour.
     *
     * @param string|null $value
     * @param string|null $format
     * @param \DateTimeZone|null $timezone
     * @return \DateTimeInterface|null
     * @throws \Exception
     */
    protected static function hydrate(?string $value, ?string $format, ?DateTimeZone $timezone): ?DateTimeInterface
    {
        if ($value === null) {
            return null;
        }

        if ($format !== null) {
            $created = self::$immutable
                ? DateTimeImmutable::createFromFormat($format, $value, $timezone)
                : DateTime::createFromFormat($format, $value, $timezone);

            if ($created === false) {
                throw new RuntimeException('Failed to create date from format');
            }

            return $created;
        }

        return self::$immutable
            ? new DateTimeImmutable($value, $timezone)
            : new DateTime($value, $timezone);
    }

    /**
     * Indicate whether to use immutable datetime or not.
     *
     * @param bool $immutable
     * @return void
     */
    public static function hydrateUsingDateTimeImmutable(bool $immutable = true): void
    {
        self::$immutable = $immutable;
    }

    /**
     * Indicate whether to use immutable datetime or not.
     *
     * @param (callable(string|null, string|null, \DateTimeZone|null): \DateTimeInterface|null)|null $hydrateUsing
     * @return void
     */
    public static function hydrateUsing(?callable $hydrateUsing): void
    {
        if ($hydrateUsing === null) {
            self::$hydrateUsing = null;

            return;
        }

        self::$hydrateUsing = $hydrateUsing(...);
    }
}
