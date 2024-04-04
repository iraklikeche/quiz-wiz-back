<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;

class DifficultyLevel extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\DifficultyLevel>
     */
    public static $model = \App\Models\DifficultyLevel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            Text::make('Name')
            ->sortable()
            ->rules('required', 'max:255'),

        Text::make('Text Color')
            ->sortable()
            ->rules('required', 'size:7'),

        Text::make('Background Color')
            ->sortable()
            ->rules('required', 'size:7'),
        ];
    }

}
