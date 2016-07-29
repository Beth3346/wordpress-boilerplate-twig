<?php

namespace Framework;

class ThemeOptions {
    public function addThemeMenu($options_title, array $subpages)
    {

        add_theme_page(
            $options_title,
            $options_title,
            'administrator',
            'theme_options',
            function() use ($subpages)
            {
                $this->themeDisplay($subpages, 'Theme Settings');
            }
        );

        add_menu_page(
            $options_title,
            $options_title,
            'administrator',
            'theme_menu',
            function() use ($subpages)
            {
                $this->themeDisplay($subpages, 'Theme Settings');
            }
        );

        foreach ($subpages as $subpage)
        {
            add_submenu_page(
                'elr_theme_menu',
                __($subpage['title'], 'elr'),
                __($subpage['title'], 'elr'),
                'administrator',
                'theme_' . $subpage['id']
            );
        }
    }

    public function themeDisplay(array $subpages, $title = 'Theme Settings')
    {
            $active_tab = isset($_GET['tab']) ? $_GET['tab'] : $subpages[0]['id'];
        ?>
            <div class="wrap">
                <h2><?php _e($title, 'elr' ); ?></h2>
                <?php settings_errors(); ?>

                <h2 class="nav-tab-wrapper">
                    <?php foreach ($subpages as $subpage) : ?>
                        <a href="?page=theme_options&tab=<?php echo $subpage['id']; ?>" class="nav-tab <?php echo $active_tab == $subpage['id'] ? 'nav-tab-active' : ''; ?>"><?php _e( $subpage['title'], 'elr' ); ?></a>
                    <?php endforeach; ?>
                </h2>

                <?php foreach ($subpages as $subpage) : ?>
                    <form method="post" action="options.php">
                        <?php if ($active_tab == $subpage['id'])
                        {
                            settings_fields($subpage['id']);
                            do_settings_sections($subpage['id']);
                            submit_button();
                        } ?>
                    </form>
                <?php endforeach; ?>
            </div>
        <?php
    }

    public function defaultOptions(array $fields)
    {
        // push default values to defaults array
        print_r($fields);

        $defaults = [];

        foreach ($fields as $field)
        {
            $defaults[] = $field['default_value'];
        }

        print_r($defaults);

        return apply_filters('default_options', $defaults);
    }

    public function fieldCallback($field, $subpage_id)
    {
        // First, we read the social options collection
        $options = get_option($subpage_id);
        $id = $field['id'];
        $label = (isset($field['label'])) ? $field['label'] :  ucwords(str_replace('_', ' ', $field['id']));
        $instructions = (isset($field['instructions'])) ? $field['instructions'] : '';
        $type = (isset($field['input_type'])) ? $field['input_type'] : 'text';

        if ( !empty( $options[$id] ) )
        {
            $value = $options[$id];
        } else {
            $value = null;
        } ?>

        <?php if ($type == 'textarea') : ?>
            <textarea class="widefat" name="<?php echo $subpage_id . '[' . $id . ']'; ?>" id="<?php echo $id ?>" cols="30" rows="10"><?php echo $value ?></textarea>
        <?php elseif ($type == 'select') : ?>
            <select class="widefat" name="<?php echo $subpage_id . '[' . $id . ']'; ?>" id="<?php echo $id ?>">
                <?php foreach ($field['options'] as $option) : ?>
                    <?php if ($value == $option) : ?>
                        <option selected value="<?php echo $option; ?>"><?php echo $option; ?></option>
                    <?php else : ?>
                        <option value="<?php echo $option; ?>"><?php echo $option; ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        <?php else : ?>
            <input
                type="<?php echo $type; ?>"
                class="widefat"
                id="<?php echo $id; ?>"
                placeholder="<?php echo $label; ?>"
                name="<?php echo $subpage_id . '[' . $id . ']'; ?>"
                value="<?php echo esc_attr( $value ); ?>"
            />
        <?php endif; ?>
        <small><?php echo $instructions; ?></small>
    <?php }

    public function initializeOptions(array $fields, $subpage_id, $subpage_title, $subpage_description = '')
    {
        $section = $subpage_id . '_section';

        if ( false == get_option($subpage_id) )
        {
            add_option($subpage_id, apply_filters([$this, 'defaultOptions'], $this->defaultOptions($fields)));
        }

        add_settings_section(
            $section,
            __($subpage_title, 'elr'),
            function() use($subpage_description)
            {
                echo '<p>' . __($subpage_description, 'elr') . '</p>';
            },
            $subpage_id
        );

        foreach ($fields as $field)
        {
            $id = $field['id'];
            $label = (isset($field['label'])) ? $field['label'] . ':' :  ucwords(str_replace('_', ' ', $field['id'])) . ':';

            add_settings_field(
                $id,
                $label,
                function() use($field, $subpage_id)
                {
                    $this->fieldCallback($field, $subpage_id);
                },
                $subpage_id,
                $section
            );
        }

        register_setting(
            $subpage_id,
            $subpage_id
            //'elr_theme_validate_social_options'
        );
    }
} ?>