<?php

namespace Framework;

class CustomFields
{
    public function metaFieldsRegister($fields)
    {

        foreach ($fields as $field)
        {
            $id = $field['id'];
            register_meta('post', $id, [$this, 'metaFieldsSanitize'], '__return_true');
        }
    }

    private function metaFieldsSanitize($meta_value)
    {
        // if meta key has url then sanitize url
        // if meta key has email then sanitize email
        return strip_tags($meta_value, '<a><span><strong><em><br><i><b>');
    }

    public function metaBoxSave($post_id, $fields)
    {

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

        //security check - nonce
        if (isset($_POST['nonce']) && $_POST && !wp_verify_nonce($_POST['nonce'], __FILE__))
        {
            return;
        }

        // if our current user can't edit this post, bail
        // if (!current_user_can('edit_item')) return;

        global $post;

        foreach ($fields as $field)
        {
            $id = $field['id'];
            if (isset($_POST[$id]))
            {
                update_post_meta($post_id, $id, $_POST[$id]);
            }
        }
    }

    public function metaBoxAdd($box_id, $box_title, $post_type, $fields)
    {
        global $post;

        add_meta_box(
            $box_id,
            $box_title,
            [$this, 'metaBoxCb'],
            $post_type,
            'normal',
            'high',
            ['fields' => $fields]
        );
    }

    private function metaFieldInput($field)
    {
        global $post;
        $value = get_post_meta($post->ID, $field['id'], true);

        $label = '<label for="' . $field['id'] . '">' . $field['label'] . ': </label>';
        $input = '<input type="text" id="' . $field['id'] . '" name="' . $field['id'] . '"value="' . esc_attr($value) . '"class="widefat"/>';

        $meta_field = $label . $input;

        return $meta_field;
    }

    private function metaFieldSelect($field)
    {
        global $post;
        $value = get_post_meta($post->ID, $field['id'], true);

        $label = '<label for="' . $field['id'] . '">' . $field['label']. ': </label>';

        $select = '<select class="widefat" name="' . $field['id'] . '" id="' . $field['id'] . '">';

        // Create default value
        $select .= '<option value="">Choose ' . $field['label']. '</option>';

        foreach ($field['options'] as $option)
        {
            if ($value == $option)
            {
                $select .= '<option selected value="' . $option . '">' . $option . '</option>';
            } else {
                $select .= '<option value="' . $option . '">' . $option . '</option>';
            }
        }

        $select .= '</select>';

        $meta_field = $label . $select;

        return $meta_field;
    }

    private function metaFieldTextarea($field)
    {
        global $post;
        $value = get_post_meta($post->ID, $field['id'], true);

        $label = '<label for="' . $field['id'] . '">' . $field['label'] . ': </label>';
        $input = '<textarea class="widefat" id="' . $field['id'] . '" name="' . $field['id'] . '">' . esc_attr($value) . '</textarea>';

        $meta_field = $label . $input;

        return $meta_field;
    }

    public function metaBoxCb($post, $fields)
    {
        wp_nonce_field(__FILE__, 'nonce');

        $inputs = '';

        foreach ($fields['args'] as $field => $values)
        {
            foreach ($values as $value)
            {
                if (array_key_exists('type', $value))
                {
                    $type = $value['type'];
                } else {
                    $type = 'text';
                }

                if ($type == 'textarea')
                {
                    echo $this->metaFieldTextarea($value);
                } elseif ($type == 'select')
                {
                    echo $this->metaFieldSelect($value);
                } else {
                    echo $this->metaFieldInput($value);
                }
            }
        }
    }
}