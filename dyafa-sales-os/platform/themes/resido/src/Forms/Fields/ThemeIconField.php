<?php

namespace Theme\Resido\Forms\Fields;

use Botble\Theme\Facades\Theme;
use Kris\LaravelFormBuilder\Fields\FormField;

class ThemeIconField extends FormField
{
    protected function getTemplate(): string
    {
        return Theme::getThemeNamespace() . '::partials.forms.fields.theme-icon-field';
    }
}
