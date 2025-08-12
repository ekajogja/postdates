<?php
/*
Plugin Name: PostDates
Plugin URI: https://github.com/ekajogja/postdates
Description: A simple WordPress plugin that displays the publication and last update date on posts, pages, and custom post types. Now with date format and position options.
Version: 2.0
Author: ekajogja
Author URI: https://github.com/ekajogja
License: GPL2
Text Domain: postdates
Domain Path: /languages
*/

if (!defined('ABSPATH')) {
    exit;
}

class PostDates
{
    private $options;

    public function __construct()
    {
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('the_content', array($this, 'display_dates'));
    }

    public function load_textdomain()
    {
        load_plugin_textdomain('postdates', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    public function add_plugin_page()
    {
        add_options_page(
            __('PostDates Settings', 'postdates'),
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
            <h1><?php _e('PostDates Settings', 'postdates'); ?></h1>
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

        $checkbox_settings = array(
            'show_both_on_posts' => __('Show both published and last update on Posts', 'postdates'),
            'show_both_on_pages' => __('Show both published and last update on Pages', 'postdates'),
            'show_both_on_custom_posts' => __('Show both published and last update on Custom Posts', 'postdates'),
        );

        foreach ($checkbox_settings as $id => $title) {
            add_settings_field(
                $id,
                $title,
                array($this, 'checkbox_callback'),
                'postdates',
                'postdates_options_section',
                array(
                    'id' => $id,
                    'label_for' => $id,
                )
            );
        }

        add_settings_field(
            'date_position',
            __('Position of Dates', 'postdates'),
            array($this, 'date_position_callback'),
            'postdates',
            'postdates_options_section'
        );

        add_settings_field(
            'date_format',
            __('Date Format', 'postdates'),
            array($this, 'date_format_callback'),
            'postdates',
            'postdates_options_section'
        );
    }

    public function sanitize($input)
    {
        $new_input = array();

        if (isset($input['show_both_on_posts'])) {
            $new_input['show_both_on_posts'] = '1';
        }
        if (isset($input['show_both_on_pages'])) {
            $new_input['show_both_on_pages'] = '1';
        }
        if (isset($input['show_both_on_custom_posts'])) {
            $new_input['show_both_on_custom_posts'] = '1';
        }

        if (isset($input['date_position']) && in_array($input['date_position'], array('above', 'below'), true)) {
            $new_input['date_position'] = $input['date_position'];
        }

        if (isset($input['date_format'])) {
            $new_input['date_format'] = sanitize_text_field($input['date_format']);
        }

        return $new_input;
    }

    public function checkbox_callback($args)
    {
        $id = $args['id'];
        printf(
            '<input type="checkbox" id="%s" name="postdates_options[%s]" value="1" %s/>',
            esc_attr($id),
            esc_attr($id),
            checked(isset($this->options[$id]), true, false)
        );
    }

    public function date_position_callback()
    {
        $position = isset($this->options['date_position']) ? $this->options['date_position'] : 'above';
        ?>
        <select name="postdates_options[date_position]">
            <option value="above" <?php selected($position, 'above'); ?>><?php esc_html_e('Above Content', 'postdates'); ?></option>
            <option value="below" <?php selected($position, 'below'); ?>><?php esc_html_e('Below Content', 'postdates'); ?></option>
        </select>
        <?php
    }

    public function date_format_callback()
    {
        $date_format = isset($this->options['date_format']) ? $this->options['date_format'] : get_option('date_format');
        printf(
            '<input type="text" id="date_format" name="postdates_options[date_format]" value="%s" />',
            esc_attr($date_format)
        );
        echo '<p class="description">' . sprintf(
            /* translators: %s: link to WordPress date formatting documentation */
            __('Enter a valid PHP date format. See the <a href="%s" target="_blank">WordPress documentation</a> for more information.', 'postdates'),
            'https://wordpress.org/support/article/formatting-date-and-time/'
        ) . '</p>';
    }

    public function display_dates($content)
    {
        if (!is_singular()) {
            return $content;
        }

        $this->options = get_option('postdates_options');
        $post_type = get_post_type();

        $option_name = '';
        if ($post_type === 'post') {
            $option_name = 'show_both_on_posts';
        } elseif ($post_type === 'page') {
            $option_name = 'show_both_on_pages';
        } else {
            $option_name = 'show_both_on_custom_posts';
        }

        if (isset($this->options[$option_name])) {
            $date_format = isset($this->options['date_format']) ? $this->options['date_format'] : get_option('date_format');
            $published_date = get_the_date($date_format);
            $updated_date = get_the_modified_date($date_format);

            $display_text = sprintf(
                /* translators: 1: Published date, 2: Updated date */
                esc_html__('Published: %1$s | Updated: %2$s', 'postdates'),
                esc_html($published_date),
                esc_html($updated_date)
            );

            $position = isset($this->options['date_position']) ? $this->options['date_position'] : 'above';
            $date_html = '<p>' . $display_text . '</p>';

            if ($position === 'above') {
                $content = $date_html . $content;
            } else {
                $content .= $date_html;
            }
        }

        return $content;
    }
}

$postdates = new PostDates();