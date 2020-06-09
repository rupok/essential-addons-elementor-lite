<?php

/**
 * Template Name: Layout Default
 * 
 */

echo '<article class="eael-better-docs-category-grid-post" data-id="'.get_the_ID().'">
    <div class="eael-bd-cg-inner">';

        if($settings['show_header'] === 'true') {
            $cat_icon_id = get_term_meta( $term->term_id, 'doc_category_image-id', true);
            if($cat_icon_id){
                $cat_icon = wp_get_attachment_image( $cat_icon_id, 'thumbnail', [ 'alt' => esc_attr(get_post_meta($cat_icon_id, '_wp_attachment_image_alt', true)) ] );
            } else {
                $cat_icon = '<img class="docs-cat-icon" src="'.BETTERDOCS_ADMIN_URL.'assets/img/betterdocs-cat-icon.svg" alt="">';
            }
            echo '<div class="eael-bd-cg-header">
                <div class="eael-bd-cg-header-inner">';
                if($settings['show_icon']) {
                    echo '<div class="eael-docs-cat-icon">'.$cat_icon.'</div>';
                }
                if($settings['show_title']) {
                    echo '<'.$settings['title_tag'].' class="eael-docs-cat-title">'.$term->name.'</'.$settings['title_tag'].'>';
                }
                if($settings['show_count']) {
                    echo '<div class="eael-docs-item-count">'.$this->get_doc_post_count($term->count, $term->term_id).'</div>';
                }
                echo '</div>';
            echo '</div>';
        }
        
        if($settings['show_list'] === 'true') {
            echo '<div class="eael-bd-cg-body">';
                $args = array(
                    'post_type'   => 'docs',
                    'post_status' => 'publish',
                    'posts_per_page'    => $settings['post_per_page'],
                    'orderby'   => $settings['post_orderby'],
                    'order' => $settings['post_order'],
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'doc_category',
                            'field'    => 'slug',
                            'terms'    => $term->slug,
                            'operator'          => 'AND',
                            'include_children'  => false
                        ),
                    ),
                );

                $query = new \WP_Query( $args );
                if ( $query->have_posts() ) {
                    echo '<ul>';
                    while ( $query->have_posts() ) {
                        $query->the_post();
                        $attr = ['href="'.get_the_permalink().'"'];

                        echo '<li>';
                        if(isset($settings['list_icon']['value']['url']) && !empty($settings['list_icon']['value']['url'])) {
                            echo '<img class="eael-bd-cg-post-list-icon" src="' . $settings['list_icon']['value']['url'] . '" />';
                        }else {
                            echo '<i class="' .$settings['list_icon']['value'].' eael-bd-cg-post-list-icon"></i>';
                        }
                        echo '<a '.implode(' ',$attr).'>'.get_the_title().'</a>
                        </li>';
                    }
                    
                    echo '</ul>';
                }
                wp_reset_query();
                
                // Nested category query
                if($settings['nested_subcategory'] === 'true') {

                    $args = array(
                        'child_of' => $term->term_id,
                        'order' => $settings['order'],
                        'orderby' => $settings['orderby'],
                    );

                    $sub_categories = get_terms( 'doc_category', $args);

                    if($sub_categories){
                        
                        foreach($sub_categories as $sub_category) {
                            echo '<span class="eael-bd-grid-sub-cat-title">';

                            if(isset($settings['nested_list_title_closed_icon']['value']['url']) && !empty($settings['nested_list_title_closed_icon']['value']['url'])) {
                                echo '<img class="toggle-arrow arrow-right" src="' . $settings['nested_list_title_closed_icon']['value']['url'] . '" />';
                            }else {
                                echo '<i class="' .$settings['nested_list_title_closed_icon']['value'].' toggle-arrow arrow-right"></i>';
                            }

                            if(isset($settings['nested_list_title_open_icon']['value']['url']) && !empty($settings['nested_list_title_open_icon']['value']['url'])) {
                                echo '<img class="toggle-arrow arrow-down" src="' . $settings['nested_list_title_open_icon']['value']['url'] . '" />';
                            }else {
                                echo '<i class="' .$settings['nested_list_title_open_icon']['value'].' toggle-arrow arrow-down"></i>';
                            }

                            echo '<a href="#">'.$sub_category->name.'</a></span>';
                            echo '<ul class="docs-sub-cat-list">';
                            $sub_args = array(
                                'post_type'   => 'docs',
                                'post_status' => 'publish',
                                'tax_query' => array(
                                    array(
                                        'taxonomy' => 'doc_category',
                                        'field'    => 'slug',
                                        'terms'    => $sub_category->slug,
                                        'operator'          => 'AND',
                                        'include_children'  => false
                                    ),
                                )
                            );
                            
                            $sub_args['posts_per_page'] = -1;
                            $sub_post_query = new \WP_Query( $sub_args );
                            if ( $sub_post_query->have_posts() ) :
                                while ( $sub_post_query->have_posts() ) : $sub_post_query->the_post();
                                    $sub_attr = ['href="'.get_the_permalink().'"'];
                                    echo '<li class="sub-list">';
                                    if(isset($settings['list_icon']['value']['url']) && !empty($settings['list_icon']['value']['url'])) {
                                        echo '<img class="eael-bd-cg-post-list-icon" src="' . $settings['list_icon']['value']['url'] . '" />';
                                    }else {
                                        echo '<i class="' .$settings['list_icon']['value'].' eael-bd-cg-post-list-icon"></i>';
                                    }
                                    echo '<a '.implode(' ',$sub_attr).'>'.get_the_title().'</a></li>';
                                endwhile;
                            endif;
                            wp_reset_query();
                            echo '</ul>';
                        }
                    }

                }
            echo '</div>';
        }

        if($settings['show_button']) {
            echo '<a class="eael-bd-cg-button" href="'.get_term_link( $term->slug, 'doc_category' ).'">';

            if($settings['icon_position'] === 'before') {
                if(isset($settings['button_icon']['value']['url']) && !empty($settings['button_icon']['value']['url'])) {
                    echo '<img class="eael-bd-cg-button-icon" src="' . $settings['button_icon']['value']['url'] . '" />';
                }else {
                    echo '<i class="' .$settings['button_icon']['value'].' eael-bd-cg-button-icon"></i>';
                }
            }

            echo $settings['button_text'];

            if($settings['icon_position'] === 'after') {
                if(isset($settings['button_icon']['value']['url']) && !empty($settings['button_icon']['value']['url'])) {
                    echo '<img class="eael-bd-cg-button-icon" src="' . $settings['button_icon']['value']['url'] . '" />';
                }else {
                    echo '<i class="' .$settings['button_icon']['value'].' eael-bd-cg-button-icon"></i>';
                }
            }

            echo '</a>';
        }
    echo '</div>';
echo '</article>';