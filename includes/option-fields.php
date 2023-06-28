<?php

use Carbon_Fields\Carbon_Fields;
use Carbon_Fields\Container;

use Carbon_Fields\Field;

add_action("after_setup_theme", "load_carbon_fields");

function load_carbon_fields()
{
    Carbon_Fields::boot();

    Container::make('theme_options', __('Contact form'))
        ->add_fields(array(
            Field::make('text', 'contact_name', __('contact name'))
            ->set_attribute("placeholder","set your contact name"),
            Field::make('textarea', 'description', __('description'))->set_help_text("This is the description of your contact")
        ));
}
