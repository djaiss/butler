<?php

namespace App\Settings\ManageLifeEventCategories\Web\ViewHelpers;

use App\Models\Account;
use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\LifeEventCategory;
use App\Models\LifeEventType;

class PersonalizeLifeEventCategoriesViewHelper
{
    public static function data(Account $account): array
    {
        $lifeEventCategories = $account->lifeEventCategories()
            ->with('lifeEventTypes')
            ->orderBy('label', 'asc')
            ->get();

        $collection = collect();
        foreach ($lifeEventCategories as $type) {
            $collection->push(self::dtoLifeEventCategory($type));
        }

        return [
            'life_event_categories' => $collection,
            'url' => [
                'settings' => route('settings.index'),
                'personalize' => route('settings.personalize.index'),
                'store' => route('settings.personalize.life_event_categories.store'),
            ],
        ];
    }

    public static function dtoLifeEventCategory(LifeEventCategory $category): array
    {
        return [
            'id' => $category->id,
            'label' => $category->label,
            'life_event_types' => $category->lifeEventTypes->map(function ($type) use ($category) {
                return self::dtoType($category, $type);
            }),
            'url' => [
                'store' => route('settings.personalize.life_event_categories.store'),
                'update' => route('settings.personalize.life_event_categories.update', [
                    'lifeEventCategory' => $category->id,
                ]),
                'destroy' => route('settings.personalize.life_event_categories.destroy', [
                    'lifeEventCategory' => $category->id,
                ]),
            ],
        ];
    }

    public static function dtoType(LifeEventCategory $category, LifeEventType $type): array
    {
        return [
            'id' => $type->id,
            'label' => $type->label,
            'url' => [
                'update' => route('settings.personalize.life_event_types.update', [
                    'lifeEventCategory' => $category->id,
                    'lifeEventType' => $type->id,
                ]),
                'destroy' => route('settings.personalize.life_event_types.destroy', [
                    'lifeEventCategory' => $category->id,
                    'lifeEventType' => $type->id,
                ]),
            ],
        ];
    }
}
