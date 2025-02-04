<?php

namespace Statikbe\FilamentFlexibleContentBlocks\Filament\Actions;

use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\ContentBlocksField;
use Statikbe\FilamentFlexibleContentBlocks\FilamentFlexibleContentBlocks;

/**
 * Re-usable implementation for Filament actions to copy the content blocks to other locales.
 */
class CopyContentBlocksToLocalesActionHandler
{
    public function handle(Model $record, Component $livewire, ?array $contentBlocks): void
    {
        if ($contentBlocks) {
            //check if the LocaleSwitch action is implemented:
            if (! method_exists($livewire, 'getActiveFormsLocale')) {
                Notification::make()
                    ->title(trans('filament-flexible-content-blocks::filament-flexible-content-blocks.form_component.copy_content_blocks_to_other_locales.error_resource_not_translatable'))
                    ->danger()
                    ->send();

                return;
            }

            if (! method_exists($record, 'setTranslation')) {
                Notification::make()
                    ->title(trans('filament-flexible-content-blocks::filament-flexible-content-blocks.form_component.copy_content_blocks_to_other_locales.error_model_not_translatable'))
                    ->danger()
                    ->send();

                return;
            }

            try {
                //get other locales than the current one.
                $currentLocale = $livewire->getActiveFormsLocale();
                $otherLocales = collect(FilamentFlexibleContentBlocks::getLocales())->diff([$currentLocale]);

                //copy content blocks
                foreach ($otherLocales as $otherLocale) {
                    $record->setTranslation(ContentBlocksField::FIELD, $otherLocale, $contentBlocks);
                    //update form data:
                    $livewire->otherLocaleData[$otherLocale][ContentBlocksField::FIELD] = $contentBlocks;
                }

                if ($otherLocales->isNotEmpty()) {
                    $record->save();
                }

                Notification::make()
                    ->title(trans('filament-flexible-content-blocks::filament-flexible-content-blocks.form_component.copy_content_blocks_to_other_locales.success'))
                    ->success()
                    ->send();

                //reload page to see the changes:
                redirect(request()->header('Referer'));
            } catch (\Exception $exception) {
                Log::error($exception);

                Notification::make()
                    ->title(trans('filament-flexible-content-blocks::filament-flexible-content-blocks.form_component.copy_content_blocks_to_other_locales.error', ['error' => $exception->getMessage()]))
                    ->danger()
                    ->send();
            }
        }
    }
}
