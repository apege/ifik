<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DosenTicketing_model extends CI_Model {

    private $table = 'dosen_ticketing';

    public function __construct() {
        parent::__construct();
    }

    /**
     * Generate unique ticket code: TIK-YYYYMMDD-XXXX
     */
    public function generate_kode() {
        $datePrefix = 'TIK-' . date('Ymd') . '-';
        do {
            $randomStr = strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
            $kode = $datePrefix . $randomStr;
            $this->db->where('kode_tiket', $kode);
            $exists = $this->db->count_all_results($this->table);
        } while ($exists > 0);

        return $kode;
    }

    /**
     * Insert new ticket
     */
    public function insert($data) {
        if (empty($data['kode_tiket'])) {
            $data['kode_tiket'] = $this->generate_kode();
        }
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Get tickets for user or all if superadmin
     */
    public function get_tickets($user_id = null, $nidn = null) {
        $this->db->from($this->table);
        if ($user_id !== null && $nidn !== null) {
            $this->db->group_start();
            $this->db->where('id_user', $user_id);
            $this->db->or_where('nidn', $nidn);
            $this->db->group_end();
        } elseif ($user_id !== null) {
            $this->db->where('id_user', $user_id);
        } elseif ($nidn !== null) {
            $this->db->where('nidn', $nidn);
        }
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get ticket by ID or kode_tiket
     */
    public function get_by_id($id_or_kode) {
        $this->db->from($this->table);
        if (is_numeric($id_or_kode)) {
            $this->db->where('id', (int)$id_or_kode);
        } else {
            $this->db->where('kode_tiket', $id_or_kode);
        }
        return $this->db->get()->row();
    }

    /**
     * Get ticket statistics
     */
    public function get_stats($user_id = null, $nidn = null) {
        $stats = [
            'total'    => 0,
            'menunggu' => 0,
            'diproses' => 0,
            'selesai'  => 0,
            'ditutup'  => 0
        ];

        // Total
        $this->db->from($this->table);
        if ($user_id !== null && $nidn !== null) {
            $this->db->group_start()->where('id_user', $user_id)->or_where('nidn', $nidn)->group_end();
        } elseif ($user_id !== null) {
            $this->db->where('id_user', $user_id);
        } elseif ($nidn !== null) {
            $this->db->where('nidn', $nidn);
        }
        $stats['total'] = $this->db->count_all_results();

        // Menunggu
        $this->db->from($this->table);
        if ($user_id !== null && $nidn !== null) {
            $this->db->group_start()->where('id_user', $user_id)->or_where('nidn', $nidn)->group_end();
        } elseif ($user_id !== null) {
            $this->db->where('id_user', $user_id);
        } elseif ($nidn !== null) {
            $this->db->where('nidn', $nidn);
        }
        $this->db->where('status', 'Menunggu');
        $stats['menunggu'] = $this->db->count_all_results();

        // Diproses
        $this->db->from($this->table);
        if ($user_id !== null && $nidn !== null) {
            $this->db->group_start()->where('id_user', $user_id)->or_where('nidn', $nidn)->group_end();
        } elseif ($user_id !== null) {
            $this->db->where('id_user', $user_id);
        } elseif ($nidn !== null) {
            $this->db->where('nidn', $nidn);
        }
        $this->db->where('status', 'Diproses');
        $stats['diproses'] = $this->db->count_all_results();

        // Selesai
        $this->db->from($this->table);
        if ($user_id !== null && $nidn !== null) {
            $this->db->group_start()->where('id_user', $user_id)->or_where('nidn', $nidn)->group_end();
        } elseif ($user_id !== null) {
            $this->db->where('id_user', $user_id);
        } elseif ($nidn !== null) {
            $this->db->where('nidn', $nidn);
        }
        $this->db->where('status', 'Selesai');
        $stats['selesai'] = $this->db->count_all_results();

        // Ditutup
        $this->db->from($this->table);
        if ($user_id !== null && $nidn !== null) {
            $this->db->group_start()->where('id_user', $user_id)->or_where('nidn', $nidn)->group_end();
        } elseif ($user_id !== null) {
            $this->db->where('id_user', $user_id);
        } elseif ($nidn !== null) {
            $this->db->where('nidn', $nidn);
        }
        $this->db->where('status', 'Ditutup');
        $stats['ditutup'] = $this->db->count_all_results();

        return $stats;
    }
}
