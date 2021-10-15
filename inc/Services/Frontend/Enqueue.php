<?php


namespace MAM\Plugin\Services\Frontend;


use MAM\Plugin\Config;
use MAM\Plugin\Services\ServiceInterface;

class Enqueue implements ServiceInterface
{

    /**
     * @var string plugin base url
     */
    private $plugin_url;

    /**
     * @inheritDoc
     */
    public function register()
    {
        // set the baseurl
        $this->plugin_url = Config::getInstance()->plugin_url;

        // add action
        add_action('wp_enqueue_scripts', [$this, 'register_css']);
        add_action('wp_enqueue_scripts', [$this, 'register_js']);
    }

    /**
     * Registers the Plugin stylesheet.
     *
     * @wp-hook wp_enqueue_scripts
     */
    public function register_css()
    {

        wp_register_style('bootstrap', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/css/bootstrap.min.css');
        wp_enqueue_style('bootstrap');

        wp_register_style('bootstrap-select', 'https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css');
        wp_enqueue_style('bootstrap-select');

        wp_register_style('fancybox', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css');
        wp_enqueue_style('fancybox');

        wp_register_style('datatables', 'https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css');
        wp_enqueue_style('datatables');

        wp_register_style('datatables-select', 'https://cdn.datatables.net/select/1.3.3/css/select.dataTables.min.css');
        wp_enqueue_style('datatables-select');

        wp_register_style('bootstrap-select', 'https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css');
        wp_enqueue_style('bootstrap-select');

        wp_register_style('jquery-ui', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css');
        wp_enqueue_style('jquery-ui');

        wp_register_style('jquery-ui-theme', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/base/theme.min.css');
        wp_enqueue_style('jquery-ui-theme');

        wp_register_style('bootstrap-daterangepicker', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.css');
        wp_enqueue_style('bootstrap-daterangepicker');

        wp_register_style('link-building-system-frontend', $this->plugin_url . 'assets/front-end/css/style.css');
        wp_enqueue_style('link-building-system-frontend');
    }


    /**
     * Registers the Plugin javascript.
     *
     * @wp-hook wp_enqueue_scripts
     */
    public function register_js()
    {
        wp_deregister_script('jquery');
        wp_register_script('jquery', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js', false, '2.2.4');
        wp_enqueue_script('jquery');

        wp_register_script('popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js', false);
        wp_enqueue_script('popper');

        wp_register_script('bootstrap', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.min.js', array('jquery', 'popper'));
        wp_enqueue_script('bootstrap');

        wp_register_script('moment', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js');
        wp_enqueue_script('moment');

        wp_register_script('fancybox', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js', array('jquery'));
        wp_enqueue_script('fancybox');

        wp_register_script('datatables', 'https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js', array('jquery'));
        wp_enqueue_script('datatables');

        wp_register_script('datatables-btns', 'https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js', array('jquery'));
        wp_enqueue_script('datatables-btns');

        wp_register_script('datatables-btf-flash', 'https://cdn.datatables.net/buttons/1.6.5/js/buttons.flash.min.js', array('jquery'));
        wp_enqueue_script('datatables-btf-flash');

        wp_register_script('datatables-jszip', 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js', array('jquery'));
        wp_enqueue_script('datatables-jszip');

        wp_register_script('datatables-pdf', 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js', array('jquery'));
        wp_enqueue_script('datatables-pdf');

        wp_register_script('datatables-fonts', 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js', array('jquery'));
        wp_enqueue_script('datatables-fonts');

        wp_register_script('datatables-btns-html', 'https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js', array('jquery'));
        wp_enqueue_script('datatables-btns-html');

        wp_register_script('datatables-print', 'https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js', array('jquery'));
        wp_enqueue_script('datatables-print');

        wp_register_script('datatables-select', 'https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js', array('jquery', 'datatables'));
        wp_enqueue_script('datatables-select');

        wp_register_script('bootstrap-select', 'https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js', array('jquery', 'bootstrap', 'popper'));
        wp_enqueue_script('bootstrap-select');

        wp_register_script('jquery-ui', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js', array('jquery'));
        wp_enqueue_script('jquery-ui');

        wp_register_script('jquery-fullscreen-plugin', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-fullscreen-plugin/1.1.5/jquery.fullscreen-min.js', array('jquery'));
        wp_enqueue_script('jquery-fullscreen-plugin');

        wp_register_script('bootstrap-daterangepicker', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.js', array('jquery', 'moment', 'bootstrap', 'popper'));
        wp_enqueue_script('bootstrap-daterangepicker');

        wp_register_script('link-building-system-frontend', $this->plugin_url . 'assets/front-end/js/scripts.js', array(
            'jquery',
            'bootstrap-daterangepicker',
            'jquery-fullscreen-plugin',
            'jquery-ui',
            'bootstrap-select',
            'datatables-select',
            'datatables-print',
            'datatables-btns-html',
            'datatables-fonts',
            'datatables-pdf',
            'datatables-jszip',
            'datatables-btns',
            'datatables',
            'fancybox',
            'moment',
            'bootstrap',
            'popper',
            ));
        wp_enqueue_script('link-building-system-frontend');
    }

}