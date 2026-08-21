<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->database();
    }

    /**
     * Get user by email address
     * @param string $email
     * @return object|null
     */
    public function get_by_email($email)
    {
        return $this->db->get_where('users', ['email' => strtolower(trim($email))])->row();
    }

    /**
     * Get user by ID
     * @param int $id
     * @return object|null
     */
    public function get_by_id($id)
    {
        return $this->db->get_where('users', ['id' => $id])->row();
    }

    /**
     * Update user record
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('users', $data);
    }

    /**
     * Check if user exists by NIM/NIDN or Name
     * @param string $identifier
     * @return bool
     */
    public function check_user_exists($identifier)
    {
        $identifier = trim($identifier);
        
        $this->db->group_start();
        $this->db->where('nidn_nim', $identifier);
        $this->db->or_where('name', $identifier);
        $this->db->group_end();
        
        $query = $this->db->get('users');
        return $query->num_rows() > 0;
    }

    /**
     * Get all users joined with roles table
     * @return array
     */
    public function get_all_users_with_roles()
    {
        $this->db->select('users.*, roles.name as role_slug, roles.display_name as role_display_name');
        $this->db->from('users');
        $this->db->join('roles', 'users.role_id = roles.id', 'left');
        $this->db->order_by('users.id', 'DESC');
        return $this->db->get()->result_array();
    }

    /**
     * Get role_id by string name / slug
     * @param string $roleName
     * @return int
     */
    public function get_role_id_by_name($roleName)
    {
        $roleName = strtolower(trim($roleName));
        if (strpos($roleName, 'dosen') !== false) return 4;
        if (strpos($roleName, 'mahasiswa') !== false) return 5;
        if (strpos($roleName, 'laboran') !== false) return 2;
        if (strpos($roleName, 'kaur') !== false || strpos($roleName, 'ka. ur') !== false) return 3;
        if (strpos($roleName, 'koordinator') !== false || strpos($roleName, 'koordinatorta') !== false) return 6;
        if (strpos($roleName, 'admin') !== false) return 1;

        $role = $this->db->get_where('roles', ['name' => $roleName])->row();
        return $role ? (int)$role->id : 5; // Default to Mahasiswa (5)
    }

    /**
     * Insert or update user record by email
     * @param array $data
     * @return int User ID
     */
    public function upsert_user($data)
    {
        $email = strtolower(trim($data['email']));
        $existing = $this->db->get_where('users', ['email' => $email])->row();

        if ($existing) {
            $updateData = [
                'name' => $data['name'],
                'role_id' => isset($data['role_id']) ? $data['role_id'] : $existing->role_id,
                'nidn_nim' => isset($data['nidn_nim']) ? $data['nidn_nim'] : $existing->nidn_nim,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            if (isset($data['token']) && !$existing->password_changed) {
                $updateData['token'] = $data['token'];
            }
            $this->db->where('id', $existing->id);
            $this->db->update('users', $updateData);
            return $existing->id;
        } else {
            $insertData = [
                'role_id' => isset($data['role_id']) ? $data['role_id'] : 5,
                'name' => $data['name'],
                'email' => $email,
                'password' => password_hash(isset($data['token']) ? $data['token'] : 'Telkom#123', PASSWORD_DEFAULT),
                'nidn_nim' => isset($data['nidn_nim']) ? $data['nidn_nim'] : '',
                'token' => isset($data['token']) ? $data['token'] : null,
                'password_changed' => 0,
                'email_status' => isset($data['email_status']) ? $data['email_status'] : 'belum',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s')
            ];
            $this->db->insert('users', $insertData);
            return $this->db->insert_id();
        }
    }

    /**
     * Update user token and password hash
     * @param int $id
     * @param string $token
     * @return bool
     */
    public function update_user_token($id, $token)
    {
        $user = $this->get_by_id($id);
        if (!$user || $user->password_changed == 1) {
            return false;
        }

        $updateData = [
            'token' => $token,
            'password' => password_hash($token, PASSWORD_DEFAULT),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->where('id', $id);
        return $this->db->update('users', $updateData);
    }

    /**
     * Update email status and sent_at timestamp
     * @param int $id
     * @param string $status ('terkirim', 'gagal', 'belum')
     * @return bool
     */
    public function update_email_status($id, $status)
    {
        $data = [
            'email_status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        if ($status === 'terkirim') {
            $data['email_sent_at'] = date('Y-m-d H:i:s');
        }

        $this->db->where('id', $id);
        return $this->db->update('users', $data);
    }

    /**
     * Bulk delete users by array of IDs
     * @param array $ids
     * @return bool
     */
    public function delete_users_batch($ids)
    {
        if (empty($ids)) return false;
        $this->db->where_in('id', $ids);
        return $this->db->delete('users');
    }

    /**
     * Reset imported users (keeps default seeded accounts ID 1-6)
     * @return bool
     */
    public function reset_imported_users()
    {
        $this->db->where('id >', 6);
        return $this->db->delete('users');
    }
}
