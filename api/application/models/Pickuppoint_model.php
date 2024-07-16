<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Pickuppoint_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();

    }

    public function getPickupPointByRouteID($id)
    {
        $this->db->select('route_pickup_point.*,pickup_point.name as pickup_point,transport_route.route_title')
            ->from('route_pickup_point')
            ->join('transport_route', 'route_pickup_point.transport_route_id=transport_route.id')
            ->join('pickup_point', 'pickup_point.id=route_pickup_point.pickup_point_id')
            ->where('route_pickup_point.transport_route_id', $id)
            ->order_by('order_number', 'des');
        $route_pickup_point = $this->db->get();
        return $route_pickup_point->result_array();
    }

}
