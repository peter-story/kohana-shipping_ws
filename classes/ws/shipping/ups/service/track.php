<?php defined('SYSPATH') or die('No direct script access.');

class Ws_Shipping_Ups_Service_Track extends Ws_Shipping_Ups {
    protected $_operation             = 'ProcessTrack';

    protected $_request_option        = '15';

    protected $_transaction_reference = 'Add description here';

    protected function _get_service_request_array() {
        $reply                   = parent::_get_service_request_array();

        // leave blank to get the last activity only
        $reply['TrackingOption'] = Arr::get($this->_params, 'tracking_option');

        $reply['InquiryNumber']  = Arr::get($this->_params, 'tracking_number');

        return $reply;
    }

    function response() {
        if ( ! parent::response() || ! is_object($this->_response->Shipment))
            return FALSE;

        $package = $this->_response->Shipment->Package;

        if (is_array($package)) {
            foreach ($this->_response->Shipment->Package as $pk) {
                if ($pk->TrackingNumber == Arr::get($this->_params, 'tracking_number')) {
                    $package = $pk;

                    break;
                }
            }

            if (is_array($package)) {
                return FALSE;
            }
        }

        $this->_response = $package;

        return $this->_response;
    }

    function get_status_code() {
        $activity = $this->_response->Activity;

        if (is_array($activity)) {
            $activity = reset($activity);
        }

        return $activity->Status->Type;
    }

    function get_all_event_codes() {
        $reply = array();

        $activity = $this->_response->Activity;

        if ( ! is_array($activity)) {
            $activity = array($activity);
        }

        foreach ($activity as $act) {
            $reply[$act->Status->Type] = $act->Status->Type;
        }

        return $reply;
    }
}