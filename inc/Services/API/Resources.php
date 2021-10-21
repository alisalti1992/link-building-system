<?php

namespace MAM\Plugin\Services\API;

use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use MAM\Plugin\Config;
use WP_REST_Controller;
use MAM\Plugin\Services\ServiceInterface;

class Resources extends WP_REST_Controller implements ServiceInterface
{

    /**
     * @var string plugin base url
     */
    protected $api_namespace;

    /**
     * @inheritDoc
     */
    public function register()
    {
        // set the baseurl
        $this->api_namespace = Config::getInstance()->api_namespace;
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes()
    {
        $base = 'resources';
        register_rest_route($this->api_namespace, '/' . $base, array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_resources'),
                'permission_callback' => array($this, 'get_resources_permissions_check'),
            ),
            array(
                'methods' => 'POST',
                'callback' => array($this, 'create_resource'),
                'permission_callback' => array($this, 'create_resource_permissions_check'),
            )
        ));
        register_rest_route($this->api_namespace, '/' . $base . '/(?P<id>[\d]+)', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_resource'),
                'permission_callback' => array($this, 'get_resource_permissions_check'),
                'args' => array(
                    'context' => array(
                        'default' => 'view',
                    ),
                ),
            ),
            array(
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => array($this, 'update_resource'),
                'permission_callback' => array($this, 'update_resource_permissions_check'),
                'args' => $this->get_endpoint_args_for_item_schema(false),
            ),
            array(
                'methods' => WP_REST_Server::DELETABLE,
                'callback' => array($this, 'delete_resource'),
                'permission_callback' => array($this, 'delete_resource_permissions_check'),
                'args' => array(
                    'force' => array(
                        'default' => false,
                    ),
                ),
            ),
        ));
    }

    /**
     * Get a collection of resources
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_REST_Response
     */
    public function get_resources(WP_REST_Request $request): WP_REST_Response
    {
        $data = $this->get_filtered_resources($request->get_param('filters'));
        return new WP_REST_Response($data, 200);
    }

    /**
     * Create resources
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_REST_Response
     */
    public function create_resource(WP_REST_Request $request): WP_REST_Response
    {
        $result = $this->create_resources_data_from_request($request->get_param('data'));
        return new WP_REST_Response($result, $result['code']);
    }

    /**
     * Validate data
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return array
     */
    public function create_resources_data_from_request(WP_REST_Request $request): array
    {
        $result = array();
        $data = ($request->get_param('data'));
        foreach ($data as $resource) {
            if(!isset($resource['resource']) || !$resource['resource']){
                return Resources::return_error_code('resource field is required');
            }
            if(!isset($resource['main_category']) || !$resource['main_category']){
                return Resources::return_error_code('main_category field is required');
            }
            if(!Resources::validate_category($resource['main_category'])){
                return Resources::return_error_code('main_category ' . $resource['main_category'] . ' is not valid');
            }
            if(isset($resource['other_categories']) && $resource['other_categories'] != ''){
                $other_categories = explode(',', $resource['other_categories']);
                foreach ($other_categories as $other_category){
                    if(!Resources::validate_category($other_category)){
                        return Resources::return_error_code('other_categories ' . $other_category . ' is not valid');
                    }
                }
            }
            if(!isset($resource['email']) || !$resource['email'] || !filter_var($resource['email'], FILTER_VALIDATE_EMAIL)){
                return Resources::return_error_code('email ' . $resource['email'] . ' is missing or not valid');
            }
            if(!isset($resource['price']) || !$resource['price'] || !is_numeric($resource['price'])){
                return Resources::return_error_code('price ' . $resource['price'] . ' is missing or not valid');
            }
            if(isset($resource['casino_price']) && !is_numeric($resource['casino_price'])){
                return Resources::return_error_code('casino_price ' . $resource['casino_price'] . ' is not valid');
            }
            if(isset($resource['cbd_price']) && !is_numeric($resource['cbd_price'])){
                return Resources::return_error_code('cbd_price ' . $resource['cbd_price'] . ' is not valid');
            }
            if(isset($resource['adult_price']) && !is_numeric($resource['adult_price'])){
                return Resources::return_error_code('adult_price ' . $resource['adult_price'] . ' is not valid');
            }
        }
        $result['status'] = 'success';
        $result['code'] = '200';
        $result['message'] = 'Requested successfully!';
        return $result;
    }

    public static function validate_category($category_name){
        $categories = array(
            'Art.Entertainment.Music.Movies',
            'Auto',
            'Business',
            'Crypto.BTC',
            'Dating',
            'Adult',
            'Edu',
            'Family.Personal',
            'Finance',
            'Food',
            'Gambling',
            'Games',
            'General',
            'Green.Eco',
            'Health.Beauty.Fitness',
            'Home Improvements',
            'Law',
            'Lifestyle',
            'News',
            'Pets',
            'Real Estate',
            'Seo. Web Design',
            'Shopping.Fashion',
            'Sport',
            'Tech.Mobile',
            'Travel',
            'Automotive',
            'Construction',
            'Entertainment',
            'Food & Beverages',
            'Gambling & Casinos',
            'Hospitality',
            'Health & Beauty',
            'Marketing',
            'Real Estate',
            'Retail',
            'Sports',
            'Fashion',
            'Technology',
            'Computer & IT',
            'General',
            'Language'
        );

        return in_array($category_name, $categories);
    }

    /**
     * Return error
     * @param string $message
     * @param int $code
     * @return array
     */
    public static function return_error_code(string $message, int $code = 500): array
    {
        return [
            'status' => 'error',
            'code' => $code,
            'message' => $message
        ];
    }


    /**
     * Remove www. http and https from provided url return only domain and the subdomain
     *
     * @param string $url
     * @return array|string|string[]
     */
    public static function format_resource_url(string $url)
    {
        return str_replace('www.', '', str_replace('http://', '', str_replace('https://', '', trim($url))));
    }

    /**
     * Get filtered resources from the database
     *
     * @param mixed|null $params parameters about the filter.
     * @return array Database query results
     */

    public function get_filtered_resources($params): array
    {
        global $wpdb;
        $results = array();
        $filters = array();
        $filters['limit'] = $params['limit'] ?? 50;
        $filters['page'] = $params['page'] ?? 1;
        $filters['sortby'] = $params['sortby'] ?? 'resource';
        $filters['order'] = $params['order'] ?? 'ASC';
        $filters['email'] = $params['email'] ?? '';
        $filters['currency'] = $params['currency'] ?? '';
        $filters['price'] = $params['price'] ?? '';
        $filters['casino_price'] = $params['casino_price'] ?? '';
        $filters['adult_price'] = $params['adult_price'] ?? '';
        $filters['payment_method'] = $params['payment_method'] ?? '';
        $filters['promotions'] = $params['promotions'] ?? '';
        $filters['usd_price'] = $params['usd_price'] ?? '';
        $filters['notes'] = $params['notes'] ?? '';
        $filters['da'] = $params['da'] ?? '';
        $filters['dr'] = $params['dr'] ?? '';
        $filters['rd'] = $params['rd'] ?? '';
        $filters['tr'] = $params['tr'] ?? '';
        $filters['pa'] = $params['pa'] ?? '';
        $filters['tf'] = $params['tf'] ?? '';
        $filters['cf'] = $params['cf'] ?? '';
        $filters['organic_keywords'] = $params['organic_keywords'] ?? '';
        $filters['metrics_update_date'] = $params['metrics_update_date'] ?? '';
        $filters['other_info'] = $params['other_info'] ?? '';
        $filters['social_media'] = $params['social_media'] ?? '';
        $filters['main_category'] = $params['main_category'] ?? '';
        $filters['other_categories'] = $params['other_categories'] ?? '';


        $sql = "
SELECT
	" . $wpdb->base_prefix . "lbs_providers.id, 
	" . $wpdb->base_prefix . "lbs_providers.email, 
	" . $wpdb->base_prefix . "lbs_providers.currency, 
	" . $wpdb->base_prefix . "lbs_providers.price, 
	" . $wpdb->base_prefix . "lbs_providers.casino_price, 
	" . $wpdb->base_prefix . "lbs_providers.cbd_price, 
	" . $wpdb->base_prefix . "lbs_providers.adult_price, 
	" . $wpdb->base_prefix . "lbs_providers.payment_method, 
	" . $wpdb->base_prefix . "lbs_providers.promotions, 
	" . $wpdb->base_prefix . "lbs_providers.usd_price, 
	" . $wpdb->base_prefix . "lbs_providers.notes, 
	" . $wpdb->base_prefix . "lbs_resources.resource_id, 
	" . $wpdb->base_prefix . "lbs_resources.resource, 
	" . $wpdb->base_prefix . "lbs_resources.da, 
	" . $wpdb->base_prefix . "lbs_resources.dr, 
	" . $wpdb->base_prefix . "lbs_resources.rd, 
	" . $wpdb->base_prefix . "lbs_resources.tr, 
	" . $wpdb->base_prefix . "lbs_resources.pa, 
	" . $wpdb->base_prefix . "lbs_resources.tf, 
	" . $wpdb->base_prefix . "lbs_resources.cf, 
	" . $wpdb->base_prefix . "lbs_resources.organic_keywords, 
	" . $wpdb->base_prefix . "lbs_resources.metrics_update_date, 
	" . $wpdb->base_prefix . "lbs_resources.social_media, 
	" . $wpdb->base_prefix . "lbs_resources.other_info, 
	" . $wpdb->base_prefix . "lbs_resources.main_category,
	" . $wpdb->base_prefix . "lbs_resources.other_categories
FROM
	" . $wpdb->base_prefix . "lbs_providers,
	" . $wpdb->base_prefix . "lbs_resources
WHERE
	" . $wpdb->base_prefix . "lbs_providers.resource_id = " . $wpdb->base_prefix . "lbs_resources.resource_id 
                ";
        $wpdb->get_results($sql);
        $results['total'] = $wpdb->num_rows;
        if ($filters['email']) {
            $sql .= " AND " . $wpdb->base_prefix . "lbs_providers.email LIKE '%" . $filters['email'] . "%'";
        }
        if ($filters['currency']) {
            $sql .= " AND " . $wpdb->base_prefix . "lbs_providers.currency LIKE '%" . $filters['currency'] . "%'";
        }
        if ($filters['payment_method']) {
            $sql .= " AND " . $wpdb->base_prefix . "lbs_providers.payment_method LIKE '%" . $filters['payment_method'] . "%'";
        }
        if ($filters['promotions']) {
            $sql .= " AND " . $wpdb->base_prefix . "lbs_providers.promotions LIKE '%" . $filters['promotions'] . "%'";
        }
        if ($filters['notes']) {
            $sql .= " AND " . $wpdb->base_prefix . "lbs_providers.notes LIKE '%" . $filters['notes'] . "%'";
        }
        if ($filters['other_info']) {
            $sql .= " AND " . $wpdb->base_prefix . "lbs_resources.other_info LIKE '%" . $filters['other_info'] . "%'";
        }
        if ($filters['social_media']) {
            $sql .= " AND " . $wpdb->base_prefix . "lbs_resources.social_media LIKE '%" . $filters['social_media'] . "%'";
        }
        if ($filters['main_category']) {
            $sql .= " AND " . $wpdb->base_prefix . "lbs_resources.main_category LIKE '%" . $filters['main_category'] . "%'";
        }
        if ($filters['other_categories']) {
            $sql .= " AND " . $wpdb->base_prefix . "lbs_resources.other_categories LIKE '%" . $filters['other_categories'] . "%'";
        }

        if ($filters['price']) {
            $data = explode('-', $filters['price']);
            if (count($data) == 2) {
                $sql .= " AND " . $wpdb->base_prefix . "lbs_providers.price >= " . $data[0] . "";
                $sql .= " AND " . $wpdb->base_prefix . "lbs_providers.price <= " . $data[1] . "";
            } else if (count($data) == 1) {
                $sql .= " AND " . $wpdb->base_prefix . "lbs_providers.price = " . $data[0] . "";
            }
        }

        if ($filters['casino_price']) {
            $data = explode('-', $filters['casino_price']);
            if (count($data) == 2) {
                $sql .= " AND " . $wpdb->base_prefix . "lbs_providers.casino_price >= " . $data[0] . "";
                $sql .= " AND " . $wpdb->base_prefix . "lbs_providers.casino_price <= " . $data[1] . "";
            } else if (count($data) == 1) {
                $sql .= " AND " . $wpdb->base_prefix . "lbs_providers.casino_price = " . $data[0] . "";
            }
        }

        if ($filters['adult_price']) {
            $data = explode('-', $filters['adult_price']);
            if (count($data) == 2) {
                $sql .= " AND " . $wpdb->base_prefix . "lbs_providers.adult_price >= " . $data[0] . "";
                $sql .= " AND " . $wpdb->base_prefix . "lbs_providers.adult_price <= " . $data[1] . "";
            } else if (count($data) == 1) {
                $sql .= " AND " . $wpdb->base_prefix . "lbs_providers.adult_price = " . $data[0] . "";
            }
        }

        if ($filters['usd_price']) {
            $data = explode('-', $filters['usd_price']);
            if (count($data) == 2) {
                $sql .= " AND " . $wpdb->base_prefix . "lbs_providers.usd_price >= " . $data[0] . "";
                $sql .= " AND " . $wpdb->base_prefix . "lbs_providers.usd_price <= " . $data[1] . "";
            } else if (count($data) == 1) {
                $sql .= " AND " . $wpdb->base_prefix . "lbs_providers.usd_price = " . $data[0] . "";
            }
        }

        if ($filters['da']) {
            $data = explode('-', $filters['da']);
            if (count($data) == 2) {
                $sql .= " AND " . $wpdb->base_prefix . "lbs_resources.da >= " . $data[0] . "";
                $sql .= " AND " . $wpdb->base_prefix . "lbs_resources.da <= " . $data[1] . "";
            } else if (count($data) == 1) {
                $sql .= " AND " . $wpdb->base_prefix . "lbs_resources.da = " . $data[0] . "";
            }
        }

        if ($filters['dr']) {
            $data = explode('-', $filters['dr']);
            if (count($data) == 2) {
                $sql .= " AND " . $wpdb->base_prefix . "lbs_resources.dr >= " . $data[0] . "";
                $sql .= " AND " . $wpdb->base_prefix . "lbs_resources.dr <= " . $data[1] . "";
            } else if (count($data) == 1) {
                $sql .= " AND " . $wpdb->base_prefix . "lbs_resources.dr = " . $data[0] . "";
            }
        }

        if ($filters['rd']) {
            $data = explode('-', $filters['rd']);
            if (count($data) == 2) {
                $sql .= " AND " . $wpdb->base_prefix . "lbs_resources.rd >= " . $data[0] . "";
                $sql .= " AND " . $wpdb->base_prefix . "lbs_resources.rd <= " . $data[1] . "";
            } else if (count($data) == 1) {
                $sql .= " AND " . $wpdb->base_prefix . "lbs_resources.rd = " . $data[0] . "";
            }
        }

        if ($filters['tr']) {
            $data = explode('-', $filters['tr']);
            if (count($data) == 2) {
                $sql .= " AND " . $wpdb->base_prefix . "lbs_resources.tr >= " . $data[0] . "";
                $sql .= " AND " . $wpdb->base_prefix . "lbs_resources.tr <= " . $data[1] . "";
            } else if (count($data) == 1) {
                $sql .= " AND " . $wpdb->base_prefix . "lbs_resources.tr = " . $data[0] . "";
            }
        }

        if ($filters['pa']) {
            $data = explode('-', $filters['pa']);
            if (count($data) == 2) {
                $sql .= " AND " . $wpdb->base_prefix . "lbs_resources.pa >= " . $data[0] . "";
                $sql .= " AND " . $wpdb->base_prefix . "lbs_resources.pa <= " . $data[1] . "";
            } else if (count($data) == 1) {
                $sql .= " AND " . $wpdb->base_prefix . "lbs_resources.pa = " . $data[0] . "";
            }
        }

        if ($filters['tf']) {
            $data = explode('-', $filters['tf']);
            if (count($data) == 2) {
                $sql .= " AND " . $wpdb->base_prefix . "lbs_resources.tf >= " . $data[0] . "";
                $sql .= " AND " . $wpdb->base_prefix . "lbs_resources.tf <= " . $data[1] . "";
            } else if (count($data) == 1) {
                $sql .= " AND " . $wpdb->base_prefix . "lbs_resources.tf = " . $data[0] . "";
            }
        }

        if ($filters['cf']) {
            $data = explode('-', $filters['cf']);
            if (count($data) == 2) {
                $sql .= " AND " . $wpdb->base_prefix . "lbs_resources.cf >= " . $data[0] . "";
                $sql .= " AND " . $wpdb->base_prefix . "lbs_resources.cf <= " . $data[1] . "";
            } else if (count($data) == 1) {
                $sql .= " AND " . $wpdb->base_prefix . "lbs_resources.cf = " . $data[0] . "";
            }
        }

        if ($filters['organic_keywords']) {
            $data = explode('-', $filters['organic_keywords']);
            if (count($data) == 2) {
                $sql .= " AND " . $wpdb->base_prefix . "lbs_resources.organic_keywords >= " . $data[0] . "";
                $sql .= " AND " . $wpdb->base_prefix . "lbs_resources.organic_keywords <= " . $data[1] . "";
            } else if (count($data) == 1) {
                $sql .= " AND " . $wpdb->base_prefix . "lbs_resources.organic_keywords = " . $data[0] . "";
            }
        }

        if ($filters['metrics_update_date']) {
            $data = explode(' - ', $filters['metrics_update_date']);
            if (count($data) == 2) {
                $sql .= " AND " . $wpdb->base_prefix . "lbs_resources.metrics_update_date between  '" . $data[0] . "' and '" . $data[1] . "'";
            } else if (count($data) == 1) {
                $sql .= " AND " . $wpdb->base_prefix . "lbs_resources.metrics_update_date = '" . $data[0] . "'";
            }
        }

        $limit_start = ($filters['page'] - 1) * $filters['limit'];
        $limit_end = ($filters['page']) * $filters['limit'];
        $wpdb->get_results($sql);
        $results['total_filtered'] = $wpdb->num_rows;
        $results['limit'] = $filters['limit'];
        $results['page'] = $filters['page'];
        $results['sortby'] = $filters['sortby'];
        $results['order'] = $filters['order'];
        $sql .= " ORDER BY `" . $filters['sortby'] . "` " . $filters['order'];
        $sql .= " LIMIT $limit_start,$limit_end";
        $resources = $wpdb->get_results($sql);
        $results['data_count'] = $wpdb->num_rows;
        foreach ($resources as $resource) {
            $results['data'][] = $resource;
        }
        return $results;
    }

    /**
     * Check if a given request has access to get items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return bool
     */
    public function get_resources_permissions_check(WP_REST_Request $request): bool
    {
        $request->get_params();
        if (is_user_logged_in()) {
            return true;
        }
        return false;
    }

    /**
     * Check if a given request has access to get items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return bool
     */
    public function create_resource_permissions_check(WP_REST_Request $request): bool
    {
        $request->get_params();
        if (is_user_logged_in()) {
            return true;
        }
        return false;
    }

    /**
     * Check if a given request has access to get items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return bool
     */
    public function get_resource_permissions_check(WP_REST_Request $request): bool
    {
        $request->get_params();
        if (is_user_logged_in()) {
            return true;
        }
        return false;
    }

    /**
     * Check if a given request has access to get items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return bool
     */
    public function update_resource_permissions_check(WP_REST_Request $request): bool
    {
        $request->get_params();
        if (is_user_logged_in()) {
            return true;
        }
        return false;
    }

    /**
     * Check if a given request has access to get items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return bool
     */
    public function delete_resource_permissions_check(WP_REST_Request $request): bool
    {
        $request->get_params();
        if (is_user_logged_in()) {
            return true;
        }
        return false;
    }
}