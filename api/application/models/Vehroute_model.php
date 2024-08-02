<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Vehroute_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    public function get($id = null)
    {
        $this->db->select('vehicle_routes.*,transport_route.id as transport_id,transport_route.route_title,transport_route.fare')->from('vehicle_routes');
        $this->db->join('transport_route', 'transport_route.id = vehicle_routes.route_id');
        if ($id != null) {
            $this->db->where('vehicle_routes.route_id', $id);
        } else {
            $this->db->order_by('vehicle_routes.id', 'DESC');
        }

        $query = $this->db->get();
        if ($id != null) {
            $vehicle_routes = $query->result_array();

            $array = array();
            if (!empty($vehicle_routes)) {
                foreach ($vehicle_routes as $vehicle_key => $vehicle_value) {
                    $vec_route              = new stdClass();
                    $vec_route->id          = $vehicle_value['id'];
                    $vec_route->route_title = $vehicle_value['route_title'];
                    $vec_route->fare        = $vehicle_value['fare'];
                    $vec_route->route_id    = $vehicle_value['route_id'];
                    $vec_route->vehicles    = $this->getVechileByRoute($vehicle_value['route_id']);
                    $array[]                = $vec_route;
                }
            }
            return $array;
        } else {
            $vehicle_routes = $query->result_array();

            $array = array();
            if (!empty($vehicle_routes)) {
                foreach ($vehicle_routes as $vehicle_key => $vehicle_value) {
                    $vec_route              = new stdClass();
                    $vec_route->id          = $vehicle_value['id'];
                    $vec_route->route_title = $vehicle_value['route_title'];
                    $vec_route->fare        = $vehicle_value['fare'];
                    $vec_route->route_id    = $vehicle_value['route_id'];
                    $vec_route->vehicles    = $this->getVechileByRoute($vehicle_value['route_id']);
                    $array[$vehicle_value['route_id']] = $vec_route;
                }
            }
            return $array;
        }
    }

    public function getVechileByRoute($route_id)
    {
        $this->db->select('vehicle_routes.id as vec_route_id,vehicles.*')->from('vehicle_routes');
        $this->db->join('vehicles', 'vehicles.id = vehicle_routes.vehicle_id');
        $this->db->where('vehicle_routes.route_id', $route_id);
        $this->db->order_by('vehicle_routes.id', 'DESC');
        $query                 = $this->db->get();
        return $vehicle_routes = $query->result();
    }

    public function listroute()
    {
        $this->db->select()->from('transport_route');
        $listtransport = $this->db->get();

        $listroute = $listtransport->result_array();
        if (!empty($listroute)) {
            foreach ($listroute as $route_key => $route_value) {
                $vehicles                          = $this->getVechileByRoute($route_value['id']);
                $listroute[$route_key]['vehicles'] = $vehicles;
            }
        }
        return $listroute;
    }

}
