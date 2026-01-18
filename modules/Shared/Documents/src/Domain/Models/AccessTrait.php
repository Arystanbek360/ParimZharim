<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Domain\Models;

/**
 * Трейт `HasAccessTrait` (Доступ)
 * Модель доступа к документам.
 *
 * @property AccessMode $access_mode Режим доступа (для конкретных пользователей или для всех).
 * @property AccessType $default_access_type Тип доступа по умолчанию (чтение или запись).
 *
 * @version 1.0.0
 * @since 2024-08-21
 */
trait AccessTrait {}

