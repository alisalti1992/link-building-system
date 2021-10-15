<?php


namespace MAM\Plugin\Services\Admin;


use MAM\Plugin\Config;
use MAM\Plugin\Services\ServiceInterface;

class Enqueue implements ServiceInterface
{

    /**
     * @var string plugin base url
     */
    protected $plugin_url;

    /**
     * @inheritDoc
     */
    public function register()
    {
        // set the baseurl
        $this->plugin_url = Config::getInstance()->plugin_url;

        // add action
        add_action('admin_enqueue_scripts', [$this, 'register_css']);
        add_action('admin_enqueue_scripts', [$this, 'register_js']);
    }
    /**
     * Registers the Plugin stylesheet.
     *
     * @wp-hook admin_enqueue_scripts
     */
    public function register_css()
    {
        wp_register_style('link-building-system-admin', $this->plugin_url . 'assets/admin/css/style.css');
        wp_enqueue_style('link-building-system-admin');
    }


    /**
     * Registers the Plugin javascript.
     *
     * @wp-hook admin_enqueue_scripts
     */
    public function register_js()
    {

        wp_register_script('link-building-system-admin', $this->plugin_url . 'assets/admin/js/scripts.js', array('jquery',));
        wp_enqueue_script('link-building-system-admin');
    }

}