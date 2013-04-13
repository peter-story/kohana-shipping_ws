<?php defined('SYSPATH') or die('No direct script access.');

class Ws_Shipping_Ups_Core extends Ws_Shipping {
    protected $_operation;

    protected $_request_option;

    protected $_transaction_reference;

    protected function _build_basic_request() {
        $this->_client->__setLocation($this->_get_endpoint_url());

        $upss = array(
            'UsernameToken' => array(
                'Username' => Arr::get($this->_config, 'username'),
                'Password' => Arr::get($this->_config, 'password')
            ),
            'ServiceAccessToken' => array(
                'AccessLicenseNumber' => Arr::get($this->_config, 'access_key')
            )
        );

        $header = new SoapHeader('http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0', 'UPSSecurity', $upss);

        $this->_client->__setSoapHeaders($header);
    }

    protected function _get_endpoint_url() {
        return 'https://wwwcie.ups.com/webservices/'.ucfirst($this->_service);
    }

    protected function _get_service_request_array() {
        return array(
            'Request'        => array(
                'RequestOption'        => $this->_request_option,
                'TransactionReference' => $this->_transaction_reference
            ),
        );
    }

    protected function _call_soap() {
        return $this->_client->__soapCall($this->_operation, array($this->_get_service_request_array()));
    }

    function response() {
        if ( ! is_object($this->_response->Response))
            return FALSE;

        if ( ! is_object($this->_response->Response->ResponseStatus))
            return FALSE;

        if ($this->_response->Response->ResponseStatus->Description != 'Success')
            return FALSE;

        return $this->_response;
    }
}