<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Header_model extends CI_Model {

    public function get_settings() {
        if (!$this->db->table_exists('header_settings')) {
            return (object)[
                'id' => 1,
                'autoplay' => 1,
                'interval_time' => 5000,
                'show_indicators' => 1,
                'show_arrows' => 1
            ];
        }
        return $this->db->get_where('header_settings', ['id' => 1])->row();
    }

    public function update_settings($data) {
        if (!$this->db->table_exists('header_settings')) {
            return false;
        }
        $this->db->where('id', 1);
        return $this->db->update('header_settings', $data);
    }

    public function get_slides() {
        if (!$this->db->table_exists('header_slides')) {
            return [];
        }
        $this->db->order_by('order_num', 'ASC');
        return $this->db->get('header_slides')->result();
    }

    public function get_slide($id) {
        return $this->db->get_where('header_slides', ['id' => $id])->row();
    }

    public function add_slide($data) {
        return $this->db->insert('header_slides', $data);
    }

    public function delete_slide($id) {
        $this->db->where('id', $id);
        return $this->db->delete('header_slides');
    }

    public function update_slide_order($id, $order_num) {
        $this->db->where('id', $id);
        return $this->db->update('header_slides', ['order_num' => $order_num]);
    }
}
