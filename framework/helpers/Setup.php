<?php

namespace Framework\Helpers;

class Setup
{

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    public function themeSlugSetup()
    {
        add_theme_support('title-tag');
    }

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    public function registerMenus(array $menus)
    {
        foreach ($menus as $menu) {
            $name = $menu;
            $title = str_replace('-', ' ', ucwords($menu));

            register_nav_menus([
                $name => __($title, 'elr')
            ]);
        }
    }

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    public function registerSidebars(array $sidebars, $title_tag = 'h3')
    {
        foreach ($sidebars as $sidebar) {
            $name = $sidebar;
            $title = str_replace('-', ' ', ucwords($sidebar));
            $args = [
                'name' => $title,
                'id' => $name,
                'before_widget' => '<section id="'. $name .'" class="widget sidebar-widget ' . $name . '">',
                'after_widget' => '</section>',
                'before_title' => '<' . $title_tag . ' class="widget-title">',
                'after_title' => '</' . $title_tag . '>',
            ];

            register_sidebar($args);
        }
    }
}