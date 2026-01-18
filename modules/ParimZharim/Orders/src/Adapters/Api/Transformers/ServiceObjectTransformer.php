<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Api\Transformers;

use Illuminate\Support\Collection;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\OrderableServiceObject;
use Modules\Shared\Core\Adapters\Api\BaseTransformer;
use Modules\Shared\Core\Application\BaseDTO;
use Modules\Shared\Core\Domain\BaseModel;
use Modules\Shared\Core\Domain\BaseValueObject;

class ServiceObjectTransformer extends BaseTransformer
{

    public function transform(OrderableServiceObject|BaseDTO|BaseValueObject|BaseModel|array $data): array
    {

        // First, extract the service object and additional data from the array
        $serviceObject = $data['serviceObject'];

        $photos = array_merge(
            [$serviceObject->getFirstMediaUrl('main')],
            $serviceObject->getMedia('gallery')->map(function ($media) {
                return $media->getUrl();
            })->toArray()
        );

        $prices = $data['price'];
        $pricesHtml = $this->transformPricesToHtml($prices);


        return [
            'id' => $serviceObject->id,
            'capacity' => $serviceObject->capacity,
            'name' => $serviceObject->name,
            'description' => $serviceObject->description,
            'type' => $serviceObject->category->id,
            'price' => $pricesHtml,
            'kitchen_deposit' => (int) $data['kitchen_deposit'] ?? null,
            'guest_limits' => $data['guest_limits'],
            'photos' => $photos,
            'min_hours' => (int) $data['min_hours'],
            'confirmation_waiting_duration' => (int) $data['confirmation_waiting_duration'],
            'additional_services' => $data['services'],
            'tags' => $data['tags']
        ];
    }


    private function transformPricesToHtml(Collection $prices): array
    {
        $htmlContent = [];

        // Convert prices to an array and filter out null values
        $pricesArray = $prices->filter()->toArray();

        // Check if pricesArray is actually an array and not empty
        if (!is_array($pricesArray) || empty($pricesArray)) {
            return $htmlContent;
        }

        // Loop through each item in the array
        foreach ($pricesArray as $priceGroup) {
            if (!is_array($priceGroup)) { // Make sure each priceGroup is an array
                continue;
            }
            foreach ($priceGroup as $price) {
                if (!isset($price['name']) || !isset($price['price'])) {
                    // Skip the current iteration if required data is missing
                    continue;
                }

                $html = '<div class="card">';
                $html .= '<div class="title">' . htmlspecialchars($price['name']) . '</div>';
                $html .= '<div class="description">' . htmlspecialchars($price['price']) . '</div>';
                $html .= '</div>';

                // Wrap the HTML content within an associative array under 'htmlContent'
                $htmlContent[] = ['htmlContent' => $html];
            }
        }

        return $htmlContent;
    }


}
