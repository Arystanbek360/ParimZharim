<?php declare(strict_types=1);

namespace Modules\Shared\Core\Domain\Enums;

/**
 * Трейт `BaseEnumTrait` (Общие методы Enum)
 *
 * @version 1.0.0
 * @since 2024-11-08
 */
trait BaseEnumTrait {
    /**
     * Получить все отображаемые наименования для типов складских предметов.
     *
     * @return array
     */
    public static function labels(): array
    {
        $labels = [];
        foreach (self::cases() as $case) {
            $labels[$case->value] = $case->label();
        }
        return $labels;
    }

    public static function fromLabel(string $label): ?string
    {
        foreach (self::cases() as $case) {
            if ($case->label() === $label) {
                return $case->value;
            }
        }
        return null;
    }

    public static function fromLabelOrValue(string $value): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->label() === $value) {
                return $case;
            }
        }

        // Если не смогли определить из Label - пробуем из value
        return self::from($value);
    }

}

