<?php

namespace Statikbe\FilamentFlexibleContentBlocks\Models\Traits;

    use Spatie\Sluggable\HasSlug;
    use Spatie\Sluggable\SlugOptions;

    /**
     * @property string|null $slug
     */
    trait HasSlugAttribute
    {
        use HasSlug;

        /**
         * Get the options for generating the slug.
         */
        public function getSlugOptions(): SlugOptions
        {
            return SlugOptions::create()
                ->generateSlugsFrom('title')
                ->saveSlugsTo('slug')
                ->doNotGenerateSlugsOnUpdate();
        }

        /**
         * Get the route key for the model.
         *
         * @return string
         */
        public function getRouteKeyName()
        {
            return 'slug';
        }
    }
