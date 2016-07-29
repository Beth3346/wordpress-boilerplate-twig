<?php

require_once(get_template_directory() . '/vendor/autoload.php');

use Framework\Helpers\Admin;
use Framework\Helpers\Setup;
use Framework\Helpers\Security;
use Framework\Helpers\Utility;

$timber = new \Timber\Timber();

// Define Constants

define('THEMEROOT', get_stylesheet_directory_uri());
define('IMAGES', THEMEROOT . '/assets/images');
define('SCRIPTS', THEMEROOT . '/assets/js');
define('STYLES', THEMEROOT . '/assets/css');

// Set Up Content Width Value

if (! isset($content_width)) {
    $content_width = 1200;
}

// Make theme available for translation
$lang_dir = THEMEROOT . '/languages';
load_theme_textdomain('elr', $lang_dir);

update_option('uploads_use_yearmonth_folders', 0);

if (! class_exists('Timber')) {
    add_action('admin_notices', function() {
            echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url(admin_url('plugins.php#timber')) . '">' . esc_url(admin_url('plugins.php')) . '</a></p></div>';
        });
    return;
}

Timber::$dirname = ['views'];

class WpBoilerplate extends TimberSite {

    public function __construct()
    {
        $admin = new Admin;
        $setup = new Setup;
        $security = new Security;

        $setup->registerMenus(['main-nav', 'footer-nav', 'social-nav']);
        $setup->registerSidebars(['sidebar']);

        add_theme_support('post-thumbnails');
        add_theme_support('automatic-feed-links');
        add_theme_support('menus');
        add_filter('timber_context', [$this, 'add_to_context']);
        add_filter('get_twig', [$this, 'add_to_twig']);
        add_filter('manage_posts_columns', [$admin, 'thumbnailColumn'], 5);
        add_filter('user_can_richedit' , [$this, 'disableVisualEditor'], 50);
        add_filter('the_generator', [$security, 'removeWpVersion']);
        add_action('wp_enqueue_scripts', [$this, 'loadScripts']);
        add_action('wp_print_scripts', [$this, 'themeQueueJs']);
        add_action('after_setup_theme', [$setup, 'themeSlugSetup']);
        add_action('manage_posts_custom_column', [$admin, 'thumbnailCustomColumn'], 5, 2);
        add_action('dashboard_glance_items' , [$admin, 'dashboardCpts']);
        parent::__construct();
    }

    public function add_to_context($context)
    {
        $context['main_nav'] = new TimberMenu('main-nav');
        $context['social_nav'] = new TimberMenu('social-nav');
        $context['footer_nav'] = new TimberMenu('footer-nav');
        $context['site'] = $this;
        return $context;
    }

    public function add_to_twig($twig)
    {
        /* this is where you can add your own fuctions to twig */
        $twig->addExtension(new Twig_Extension_StringLoader());

        return $twig;
    }

    public function loadScripts()
    {
        wp_register_script('main', SCRIPTS . '/main.0.0.0.min.js', ['jquery'], null, true);
        wp_register_script('font-awesome', 'https://use.fontawesome.com/185c4dbad0.js', [], null);
        wp_register_style('style', STYLES . '/custom.css', [], null, 'screen');
        wp_register_style('fonts', 'https://fonts.googleapis.com/css?family=Roboto:700,500,400,300, 200|Raleway:300italic,400,300|Roboto+Slab:300,400,500', [], null, 'screen');

        wp_enqueue_script('main');
        wp_enqueue_script('font-awesome');
        wp_enqueue_style('fonts');
        wp_enqueue_style('style');
    }

    public function themeQueueJs()
    {
        if ((!is_admin()) && is_single() && comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }
    }

    public function disableVisualEditor()
    {
        # add logic here if you want to permit it selectively
        return false;
    }

    public function breadcrumbs()
    {
        if (function_exists('yoast_breadcrumb')) {
            yoast_breadcrumb('<p id="breadcrumbs" class="breadcrumbs">','</p>');
        }

        return;
    }

    public function email($email)
    {
        if ($email) {
            $html = '<a href="mailto:';
            $html .= antispambot($email);
            $html .= '">';
            $html .= antispambot($email);
            $html .= '</a>';

            return $html;
        }

        return;
    }

    public function setNumberOfCpts($query, $num = -1, $post_types = [], $taxonomies = [])
    {
        if ($query->is_main_query()) {
            foreach ($post_types as $post_type) {
                if (is_post_type_archive($post_type, $num)) {
                    $query->set('posts_per_page', $num);
                }
            }

            foreach ($taxonomies as $tax) {
                if (is_tax($tax)) {
                    $query->set('posts_per_page', $num);
                }
            }

            return $query;
        }
    }
}

new WpBoilerplate();