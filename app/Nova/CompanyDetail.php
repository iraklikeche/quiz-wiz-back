<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class CompanyDetail extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\CompanyDetail>
     */
    public static $model = \App\Models\CompanyDetail::class;

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

            Text::make('Email')
                ->sortable()
                ->rules('required', 'email', 'max:255'),

            Text::make('Number')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Facebook Link')
                ->sortable()
                ->rules('required', 'url', 'max:255'),

            Text::make('LinkedIn')
                ->sortable()
                ->rules('required', 'url', 'max:255'),

        ];
    }

}
