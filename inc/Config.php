<?php


namespace MAM\Plugin;

// Singleton class
class Config
{

    /**
     * @var string The plugin path (eg: use for require templates).
     */
    public $plugin_path;
    /**
     * @var string The plugin url (eg: use for enqueue css/js files).
     */
    public $plugin_url;
    /**
     * @var string The name (eg: use for adding links to the plugin action links).
     */
    public $plugin_basename;
    /**
     * @var array The list of currencies.
     */
    public $currencies;

    /**
     * @var string Used to get actual page URL
     */
    public $actual_url;

    /**
     * @var Config Used for singleton class setup
     */
    private static $instance;

    /**
     * Construct base configs
     */
    private final function __construct()
    {
        $this->plugin_url = plugin_dir_url(__DIR__);
        $this->plugin_path = plugin_dir_path(__DIR__);
        $this->plugin_basename = plugin_basename(plugin_dir_path(__DIR__) . '/mam-reaxml-properties.php');
        $this->currencies = $this->get_currencies();
        $this->actual_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

    /**
     * get Instance of the class
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * get the currencies list array
     * @return array list of currencies
     */
    public static function get_currencies(){
        return array(
            'USD' => 'USD',
            'GBP' => 'GBP',
            'AUD' => 'AUD',
            'EUR' => 'EUR',
            '=========' => '=========',
            'AED' => 'AED',
            'AFN' => 'AFN',
            'ALL' => 'ALL',
            'AMD' => 'AMD',
            'ANG' => 'ANG',
            'AOA' => 'AOA',
            'ARS' => 'ARS',
            'AWG' => 'AWG',
            'AZN' => 'AZN',
            'BAM' => 'BAM',
            'BBD' => 'BBD',
            'BDT' => 'BDT',
            'BGN' => 'BGN',
            'BHD' => 'BHD',
            'BIF' => 'BIF',
            'BMD' => 'BMD',
            'BND' => 'BND',
            'BOB' => 'BOB',
            'BOV' => 'BOV',
            'BRL' => 'BRL',
            'BSD' => 'BSD',
            'BTN' => 'BTN',
            'BWP' => 'BWP',
            'BYN' => 'BYN',
            'BZD' => 'BZD',
            'CAD' => 'CAD',
            'CDF' => 'CDF',
            'CHE' => 'CHE',
            'CHF' => 'CHF',
            'CHW' => 'CHW',
            'CLF' => 'CLF',
            'CLP' => 'CLP',
            'CNY' => 'CNY',
            'COP' => 'COP',
            'COU' => 'COU',
            'CRC' => 'CRC',
            'CUC' => 'CUC',
            'CUP' => 'CUP',
            'CVE' => 'CVE',
            'CZK' => 'CZK',
            'DJF' => 'DJF',
            'DKK' => 'DKK',
            'DOP' => 'DOP',
            'DZD' => 'DZD',
            'EGP' => 'EGP',
            'ERN' => 'ERN',
            'ETB' => 'ETB',
            'FJD' => 'FJD',
            'FKP' => 'FKP',
            'GEL' => 'GEL',
            'GHS' => 'GHS',
            'GIP' => 'GIP',
            'GMD' => 'GMD',
            'GNF' => 'GNF',
            'GTQ' => 'GTQ',
            'GYD' => 'GYD',
            'HKD' => 'HKD',
            'HNL' => 'HNL',
            'HRK' => 'HRK',
            'HTG' => 'HTG',
            'HUF' => 'HUF',
            'IDR' => 'IDR',
            'ILS' => 'ILS',
            'INR' => 'INR',
            'IQD' => 'IQD',
            'IRR' => 'IRR',
            'ISK' => 'ISK',
            'JMD' => 'JMD',
            'JOD' => 'JOD',
            'JPY' => 'JPY',
            'KES' => 'KES',
            'KGS' => 'KGS',
            'KHR' => 'KHR',
            'KMF' => 'KMF',
            'KPW' => 'KPW',
            'KRW' => 'KRW',
            'KWD' => 'KWD',
            'KYD' => 'KYD',
            'KZT' => 'KZT',
            'LAK' => 'LAK',
            'LBP' => 'LBP',
            'LKR' => 'LKR',
            'LRD' => 'LRD',
            'LSL' => 'LSL',
            'LYD' => 'LYD',
            'MAD' => 'MAD',
            'MDL' => 'MDL',
            'MGA' => 'MGA',
            'MKD' => 'MKD',
            'MMK' => 'MMK',
            'MNT' => 'MNT',
            'MOP' => 'MOP',
            'MRU[11]' => 'MRU[11]',
            'MUR' => 'MUR',
            'MVR' => 'MVR',
            'MWK' => 'MWK',
            'MXN' => 'MXN',
            'MXV' => 'MXV',
            'MYR' => 'MYR',
            'MZN' => 'MZN',
            'NAD' => 'NAD',
            'NGN' => 'NGN',
            'NIO' => 'NIO',
            'NOK' => 'NOK',
            'NPR' => 'NPR',
            'NZD' => 'NZD',
            'OMR' => 'OMR',
            'PAB' => 'PAB',
            'PEN' => 'PEN',
            'PGK' => 'PGK',
            'PHP' => 'PHP',
            'PKR' => 'PKR',
            'PLN' => 'PLN',
            'PYG' => 'PYG',
            'QAR' => 'QAR',
            'RON' => 'RON',
            'RSD' => 'RSD',
            'RUB' => 'RUB',
            'RWF' => 'RWF',
            'SAR' => 'SAR',
            'SBD' => 'SBD',
            'SCR' => 'SCR',
            'SDG' => 'SDG',
            'SEK' => 'SEK',
            'SGD' => 'SGD',
            'SHP' => 'SHP',
            'SLL' => 'SLL',
            'SOS' => 'SOS',
            'SRD' => 'SRD',
            'SSP' => 'SSP',
            'STN[13]' => 'STN[13]',
            'SVC' => 'SVC',
            'SYP' => 'SYP',
            'SZL' => 'SZL',
            'THB' => 'THB',
            'TJS' => 'TJS',
            'TMT' => 'TMT',
            'TND' => 'TND',
            'TOP' => 'TOP',
            'TRY' => 'TRY',
            'TTD' => 'TTD',
            'TWD' => 'TWD',
            'TZS' => 'TZS',
            'UAH' => 'UAH',
            'UGX' => 'UGX',
            'USN' => 'USN',
            'UYI' => 'UYI',
            'UYU' => 'UYU',
            'UYW' => 'UYW',
            'UZS' => 'UZS',
            'VES' => 'VES',
            'VND' => 'VND',
            'VUV' => 'VUV',
            'WST' => 'WST',
            'XAF' => 'XAF',
            'XAG' => 'XAG',
            'XAU' => 'XAU',
            'XBA' => 'XBA',
            'XBB' => 'XBB',
            'XBC' => 'XBC',
            'XBD' => 'XBD',
            'XCD' => 'XCD',
            'XDR' => 'XDR',
            'XOF' => 'XOF',
            'XPD' => 'XPD',
            'XPF' => 'XPF',
            'XPT' => 'XPT',
            'XSU' => 'XSU',
            'XTS' => 'XTS',
            'XUA' => 'XUA',
            'XXX' => 'XXX',
            'YER' => 'YER',
            'ZAR' => 'ZAR',
            'ZMW' => 'ZMW',
            'ZWL' => 'ZWL',
        );
    }
}