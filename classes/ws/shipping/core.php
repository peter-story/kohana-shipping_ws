<?php defined('SYSPATH') or die('No direct script access.');

class Ws_Shipping_Core {
    protected $_carrier;

    protected $_service;

    protected $_config;

    protected $_client;

    protected $_request = array();

    protected $_params  = array();

    function __construct($carrier, $service, array $params = array()) {
        $this->_carrier = Ws_Shipping::normalize_string($carrier);

        $this->_service = Ws_Shipping::normalize_string($service);

        $this->_config  = Kohana::config('shipping-ws.'.strtolower($this->_carrier));

        $this->_params  = $params;

        $this->_client = new SoapClient(
            $this->_path_to_wsdl(),
            array(
                'trace'              => 1,
                'cache_wsdl'         => WSDL_CACHE_NONE,
                'connection_timeout' => 2
            )
        );

        // this really shouldn't be overwritten
        $this->_build_basic_request();

        // this should be customized for each service
        $this->_build_service_request();
    }

    static function factory($carrier, $service, array $params = array()) {
        $ws_name = 'Ws_Shipping_'.Ws_Shipping::normalize_string($carrier.'_'.$service);

        if ( ! class_exists($ws_name)) {
            return "ERROR: Class $ws_name does not exist.";
        }

        return new $ws_name($carrier, $service, $params);
    }

    static function normalize_string($carrier) {
        return preg_replace('/\s+/', '_', trim(ucwords(str_replace('_', ' ', strtolower($carrier)))));
    }

    static function string_to_path($string) {
        return str_replace('_', '/', strtolower($string));
    }

    protected function _path_to_wsdl() {
        return MODPATH.'shipping-ws/classes/ws/shipping/'.Ws_Shipping::string_to_path($this->_carrier).'/wsdl/'.Ws_Shipping::string_to_path($this->_service).'.wsdl';
    }

    protected function _build_basic_request() {
        $this->_request = array();
    }

    protected function _build_service_request() {}

    function execute() {
        try {
            $this->_response = $this->_call_soap();

            return $this;
        } catch (SoapFault $e) {
            return $e->getMessage();
        }
    }

    function terminate() {
        unset($this->_client);
    }

    protected function _call_soap() {}

    function response() {
        return $this->_response;
    }
}