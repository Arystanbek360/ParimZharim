<?php declare(strict_types=1);

namespace Modules\Shared\Core\Adapters\Api;

use Modules\Shared\Core\Application\BaseDTO;
use Modules\Shared\Core\Domain\BaseCollection;
use Modules\Shared\Core\Domain\BaseModel;
use Modules\Shared\Core\Domain\BaseValueObject;

/**
 * Class BaseTransformer
 */
abstract class BaseTransformer
{
    /**
     * Применить трансформацию.
     */
    abstract public function transform(BaseModel|BaseValueObject|BaseDTO|array $data);

    /**
     * Трансформировать коллекцию.
     */
    public function collection(BaseCollection $data): array
    {
        return $data->map([$this, 'transform'])->toArray();
    }

    /**
     * Трансформировать элемент.
     */
    public function item(BaseModel|BaseValueObject|BaseDTO|array $data): array
    {
        return $this->transform($data);
    }
}
