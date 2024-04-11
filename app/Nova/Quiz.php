<?php

namespace App\Nova;

use Laravel\Nova\Fields\Text as FieldsText;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\HasMany;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Textarea;

class Quiz extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Quiz>
     */
    public static $model = \App\Models\Quiz::class;

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

            Text::make('Title')
                ->sortable()
                ->rules('required', 'max:255'),

            BelongsToMany::make('Categories')->searchable(),


            Number::make('Estimated Time', 'estimated_time')
                ->sortable()
                ->min(0)
                ->step(1)
                ->rules('required', 'integer', 'min:0'),

            HasMany::make('Questions'),

            Image::make('Image')
                ->path('quizzes')
                ->rules('required', 'image'),

            BelongsTo::make('Difficulty Level', 'difficultyLevel', DifficultyLevel::class)->sortable(),
            Text::make('Instruction')
                ->sortable()
                ->hideFromIndex(),

            Textarea::make('Entry Question')
                ->sortable()
                ->hideFromIndex(),

            ];

    }


}
