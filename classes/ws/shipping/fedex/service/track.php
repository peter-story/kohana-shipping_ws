<?php defined('SYSPATH') or die('No direct script access.');

class Ws_Shipping_Fedex_Service_Track extends Ws_Shipping_Fedex {
    protected $_cust_trans_id = '*** Track Request v6 using PHP ***';

    protected $_service_id    = 'trck';

    protected $_vrsn_maj      = '6';

    protected $_vrsn_int      = '0';

    protected $_vrsn_min      = '0';

    // requires $this->_params['tracking_number']
    protected function _build_service_request() {
        $this->_request['PackageIdentifier'] = array(
            'Value' => Arr::get($this->_params, 'tracking_number'),
            'Type'  => 'TRACKING_NUMBER_OR_DOORTAG'
        );

        $this->_request['IncludeDetailedScans'] = TRUE;
    }

    function response() {
        if (parent::response() && is_object($this->_response->TrackDetails))
            return $this->_response;

        return FALSE;
    }

    function get_status_code() {
        $event = $this->_response
            ->TrackDetails
            ->Events;

        if (is_array($event)) {
            $event = reset($event);
        }

        return $event->EventType;
    }

    function get_all_event_codes() {
        $reply  = array();

        $events = $this->_response
            ->TrackDetails
            ->Events;

        if ( ! is_array($events)) {
            $events = array($events);
        }

        foreach ($events as $evt) {
            $reply[$evt->EventType] = $evt->EventType;
        }

        return $reply;
    }
}