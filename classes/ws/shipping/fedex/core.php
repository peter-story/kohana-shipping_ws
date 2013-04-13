<?php defined('SYSPATH') or die('No direct script access.');

class Ws_Shipping_Fedex_Core extends Ws_Shipping {
    protected $_cust_trans_id;

    protected $_service_id;

    protected $_vrsn_maj;

    protected $_vrsn_int;

    protected $_vrsn_min;

    protected function _build_basic_request() {
        $this->_request = array(
            'WebAuthenticationDetail' => $this->_web_auth_detail(),
            'ClientDetail'            => $this->_client_detail(),
            'TransactionDetail'       => $this->_trans_detail(),
            'Version'                 => $this->_version(),
        );
    }

    protected function _build_service_request() {}

    protected function _web_auth_detail() {
        return array(
            'UserCredential' =>array(
                'Key'      => Arr::get($this->_config, 'key'),
                'Password' => Arr::get($this->_config, 'password')
            )
        );
    }

    protected function _client_detail() {
        return array(
            'AccountNumber' => Arr::get($this->_config, 'acct_num'),
            'MeterNumber'   => Arr::get($this->_config, 'meter_num')
        );
    }

    protected function _trans_detail() {
        return array('CustomerTransactionId' => $this->_cust_trans_id);
    }

    protected function _version() {
        return array(
            'ServiceId'    => $this->_service_id,
            'Major'        => $this->_vrsn_maj, 
            'Intermediate' => $this->_vrsn_int, 
            'Minor'        => $this->_vrsn_min
        );
    }

    protected function _call_soap() {
        return $this->_client->{$this->_service}($this->_request);
    }

    function response() {
        if (($this->_response->HighestSeverity != 'FAILURE') && ($this->_response->HighestSeverity != 'ERROR'))
            return $this->_response;

        return FALSE;
    }
}