<?php

namespace EssentialAddonsElementor\Traits;

if (!defined('ABSPATH')) {
    exit();
} // Exit if accessed directly

trait Elements
{
    public function elements_path($file)
    {
        $file = ltrim($file, '/');
        $file = $this->plugin_path . 'elements/' . $file . '/' . $file . '.php';
        if (file_exists($file)) {
            return $file;
        }
        return false;
    }

    /**
     * Acivate or Deactivate Modules
     *
     * @since v1.0.0
     */
    public function add_eael_elements($widgets_manager)
    {

        $elements = [
            ['name' => 'post-grid'],
            ['name' => 'post-timeline'],
            ['name' => 'fancy-text'],
            ['name' => 'creative-btn'],
            ['name' => 'count-down'],
            ['name' => 'team-members'],
            ['name' => 'testimonials'],
            ['name' => 'info-box'],
            ['name' => 'flip-box'],
            ['name' => 'call-to-action'],
            ['name' => 'dual-header'],
            ['name' => 'price-table'],
            ['name' => 'twitter-feed'],
            ['name' => 'data-table'],
            ['name' => 'filter-gallery'],
            ['name' => 'image-accordion'],
            ['name' => 'content-ticker'],
            ['name' => 'tooltip'],
            ['name' => 'adv-accordion'],
            ['name' => 'adv-tabs'],
            ['name' => 'progress-bar'],
            ['name' => 'feature-list'],
            [
                'name' => 'product-grid',
                'condition' => [
                    'function_exists',
                    'WC',
                ],
            ],
            [
                'name' => 'contact-form-7',
                'condition' => [
                    'function_exists',
                    'wpcf7',
                ],
            ],
            [
                'name' => 'weforms',
                'condition' => [
                    'function_exists',
                    'WeForms',
                ],
            ],
            [
                'name' => 'ninja-form',
                'condition' => [
                    'function_exists',
                    'Ninja_Forms',
                ],
            ],
            [
                'name' => 'gravity-form',
                'condition' => [
                    'class_exists',
                    'GFForms',
                ],
            ],
            [
                'name' => 'caldera-form',
                'condition' => [
                    'class_exists',
                    'Caldera_Forms',
                ],
            ],
            [
                'name' => 'wpforms',
                'condition' => [
                    'class_exists',
                    '\WPForms\WPForms',
                ],
            ],
        ];

        $is_component_active = $this->get_settings();
        $ea_elements = apply_filters('add_eae_element', $elements);

        foreach ($ea_elements as $element) {
            if (isset($element['condition'])) {
                if (($element['condition'][0]($element['condition'][1])) && $is_component_active[$element['name']]) {
                    require_once $this->elements_path($element['name']);
                }
            } else {
                if ($is_component_active[$element['name']]) {
                    require_once $this->elements_path($element['name']);
                }
            }
        }
    }

}