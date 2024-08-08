<?php
/*
Plugin Name: PostDates
Plugin URI: https://github.com/ekajogja/postdates
Description: A simple WordPress plugin that displays the publication and last update date on posts, pages, and custom post types.
Version: 1.0
Author: ekajogja
Author URI: https://github.com/ekajogja
License: GPL2
Text Domain: postdates
*/

if (!defined('ABSPATH')) {
    exit;
}

class PostDates
{
    private $options;

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('the_content', array($this, 'display_dates'));
        add_action('plugins_loaded', array($this, 'load_textdomain'));
    }

    public function load_textdomain()
    {
        load_plugin_textdomain('postdates', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    public function add_plugin_page()
    {
        add_options_page(
            __('PostDates', 'postdates'),
            __('PostDates', 'postdates'),
            'manage_options',
            'postdates',
            array($this, 'create_admin_page')
        );
    }

    public function create_admin_page()
    {
        $this->options = get_option('postdates_options');
        ?>
        <div class="wrap">
            <h1><?php _e('PostDates', 'postdates'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('postdates_options_group');
                do_settings_sections('postdates');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function register_settings()
    {
        register_setting(
            'postdates_options_group',
            'postdates_options',
            array($this, 'sanitize')
        );

        add_settings_section(
            'postdates_options_section',
            '',
            null,
            'postdates'
        );

        $this->add_settings_field_with_translation('show_last_update_on_posts', 'Show last update on Posts');
        $this->add_settings_field_with_translation('show_both_on_posts', 'Show both published and last update on Posts');
        $this->add_settings_field_with_translation('show_last_update_on_pages', 'Show last update on Pages');
        $this->add_settings_field_with_translation('show_both_on_pages', 'Show both published and last update on Pages');
        $this->add_settings_field_with_translation('show_last_update_on_custom_posts', 'Show last update on Custom Posts');
        $this->add_settings_field_with_translation('show_both_on_custom_posts', 'Show both published and last update on Custom Posts');
        $this->add_settings_field_with_translation('date_position', 'Position of Dates');
    }

    private function add_settings_field_with_translation($id, $title)
    {
        add_settings_field(
            $id,
            __($title, 'postdates'),
            array($this, $id . '_callback'),
            'postdates',
            'postdates_options_section'
        );
    }

    public function sanitize($input)
    {
        $sanitary_values = array();
        foreach ($input as $key => $val) {
            $sanitary_values[$key] = sanitize_text_field($val);
        }
        return $sanitary_values;
    }

    public function show_last_update_on_posts_callback()
    {
        printf(
            '<input type="checkbox" name="postdates_options[show_last_update_on_posts]" %s>',
            isset($this->options['show_last_update_on_posts']) ? 'checked' : ''
        );
    }

    public function show_both_on_posts_callback()
    {
        printf(
            '<input type="checkbox" name="postdates_options[show_both_on_posts]" %s>',
            isset($this->options['show_both_on_posts']) ? 'checked' : ''
        );
    }

    public function show_last_update_on_pages_callback()
    {
        printf(
            '<input type="checkbox" name="postdates_options[show_last_update_on_pages]" %s>',
            isset($this->options['show_last_update_on_pages']) ? 'checked' : ''
        );
    }

    public function show_both_on_pages_callback()
    {
        printf(
            '<input type="checkbox" name="postdates_options[show_both_on_pages]" %s>',
            isset($this->options['show_both_on_pages']) ? 'checked' : ''
        );
    }

    public function show_last_update_on_custom_posts_callback()
    {
        printf(
            '<input type="checkbox" name="postdates_options[show_last_update_on_custom_posts]" %s>',
            isset($this->options['show_last_update_on_custom_posts']) ? 'checked' : ''
        );
    }

    public function show_both_on_custom_posts_callback()
    {
        printf(
            '<input type="checkbox" name="postdates_options[show_both_on_custom_posts]" %s>',
            isset($this->options['show_both_on_custom_posts']) ? 'checked' : ''
        );
    }

    public function date_position_callback()
    {
        $position = isset($this->options['date_position']) ? $this->options['date_position'] : 'above';
        ?>
        <select name="postdates_options[date_position]">
            <option value="above" <?php selected($position, 'above'); ?>><?php _e('Above Content', 'postdates'); ?></option>
            <option value="below" <?php selected($position, 'below'); ?>><?php _e('Below Content', 'postdates'); ?></option>
        </select>
        <?php
    }

    public function display_dates($content)
    {
        if (is_singular()) {
            $this->options = get_option('postdates_options');
            $post_type = get_post_type();
            $published_date = get_the_date();
            $updated_date = get_the_modified_date();

            $display_text = '';

            if ($post_type == 'post') {
                if (isset($this->options['show_last_update_on_posts']) || isset($this->options['show_both_on_posts'])) {
                    $display_text .= sprintf(
                        __('Published: %1$s | Updated: %2$s', 'postdates'),
                        esc_html($published_date),
                        esc_html($updated_date)
                    );
                }
            } elseif ($post_type == 'page') {
                if (isset($this->options['show_last_update_on_pages']) || isset($this->options['show_both_on_pages'])) {
                    $display_text .= sprintf(
                        __('Published: %1$s | Updated: %2$s', 'postdates'),
                        esc_html($published_date),
                        esc_html($updated_date)
                    );
                }
            } else {
                if (isset($this->options['show_last_update_on_custom_posts']) || isset($this->options['show_both_on_custom_posts'])) {
                    $display_text .= sprintf(
                        __('Published: %1$s | Updated: %2$s', 'postdates'),
                        esc_html($published_date),
                        esc_html($updated_date)
                    );
                }
            }

            if (!empty($display_text)) {
                $position = isset($this->options['date_position']) ? $this->options['date_position'] : 'above';
                if ($position == 'above') {
                    $content = '<p>' . $display_text . '</p>' . $content;
                } else {
                    $content .= '<p>' . $display_text . '</p>';
                }
            }
        }
        return $content;
    }
}

$postdates = new PostDates();