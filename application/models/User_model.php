<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->database();
        $this->_ensure_columns();
    }

    private function _ensure_columns()
    {
        if (!$this->db->table_exists('users')) return;
        $fields = $this->db->list_fields('users');
        $user_cols = array(
            'password_changed' => "TINYINT(1) NOT NULL DEFAULT 0",
            'token'            => "VARCHAR(255) NULL",
            'email_status'     => "VARCHAR(20) DEFAULT 'belum'",
            'email_sent_at'    => "DATETIME NULL"
        );
        foreach ($user_cols as $col => $def) {
            if (!in_array($col, $fields)) {
                $this->db->query("ALTER TABLE `users` ADD COLUMN `{$col}` {$def}");
            }
        }
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
     * Store password reset token for a user
     * @param string $email
     * @param string $token
     * @return bool
     */
    public function set_reset_token($email, $token)
    {
        $this->db->where('email', strtolower(trim($email)));
        return $this->db->update('users', [
            'token'      => $token,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Verify if password reset token matches for an email
     * @param string $email
     * @param string $token
     * @return object|null
     */
    public function verify_reset_token($email, $token)
    {
        $user = $this->get_by_email($email);
        if ($user && !empty($user->token) && $user->token === $token) {
            return $user;
        }
        return null;
    }

    /**
     * Reset user password using token
     * @param string $email
     * @param string $token
     * @param string $newHashedPassword
     * @return bool
     */
    public function reset_password_by_token($email, $token, $newHashedPassword)
    {
        $user = $this->verify_reset_token($email, $token);
        if (!$user) return false;

        $this->db->where('id', $user->id);
        return $this->db->update('users', [
            'password'         => $newHashedPassword,
            'password_changed' => 1,
            'token'            => null,
            'updated_at'       => date('Y-m-d H:i:s')
        ]);
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
     * Bulk Insert or Update user records in high-performance transactions
     * @param array $accounts
     * @return array ['imported' => int, 'updated' => int]
     */
    public function upsert_users_bulk($accounts)
    {
        if (empty($accounts)) {
            return ['imported' => 0, 'updated' => 0];
        }

        @set_time_limit(300);
        @ini_set('memory_limit', '256M');

        // Cache role IDs
        $roleQuery = $this->db->get('roles')->result_array();
        $roleMap = [];
        foreach ($roleQuery as $r) {
            $roleMap[strtolower(trim($r['name']))] = (int)$r['id'];
        }

        // Collect unique valid emails
        $emails = [];
        foreach ($accounts as $acc) {
            $email = isset($acc['email']) ? strtolower(trim($acc['email'])) : '';
            if (!empty($email) && preg_match('/@(student\.)?telkomuniversity\.ac\.id$/i', $email)) {
                $emails[] = $email;
            }
        }
        $emails = array_unique($emails);

        if (empty($emails)) {
            return ['imported' => 0, 'updated' => 0];
        }

        // Single query to find existing users
        $existingMap = [];
        $emailChunks = array_chunk($emails, 200);
        foreach ($emailChunks as $chunk) {
            $this->db->where_in('email', $chunk);
            $found = $this->db->get('users')->result_array();
            foreach ($found as $u) {
                $existingMap[strtolower(trim($u['email']))] = $u;
            }
        }

        $now = date('Y-m-d H:i:s');
        $toInsert = [];
        $importedCount = 0;
        $updatedCount = 0;

        $this->db->trans_start();

        foreach ($accounts as $acc) {
            $email = isset($acc['email']) ? strtolower(trim($acc['email'])) : '';
            if (empty($email) || !preg_match('/@(student\.)?telkomuniversity\.ac\.id$/i', $email)) {
                continue;
            }

            $roleName = isset($acc['role']) ? strtolower(trim($acc['role'])) : 'mahasiswa';
            $roleId = isset($roleMap[$roleName]) ? $roleMap[$roleName] : 5;

            $token = isset($acc['token']) && !empty($acc['token']) ? trim($acc['token']) : null;
            $name = isset($acc['name']) && !empty($acc['name']) ? trim($acc['name']) : 'User';
            $nimNip = isset($acc['nim_nip']) ? trim($acc['nim_nip']) : '';
            $emailStatus = isset($acc['email_status']) ? $acc['email_status'] : 'belum';

            if (isset($existingMap[$email])) {
                // Update existing user
                $existing = $existingMap[$email];
                $updateData = [
                    'name' => $name,
                    'role_id' => $roleId,
                    'nidn_nim' => $nimNip,
                    'updated_at' => $now
                ];
                if ($token && empty($existing['password_changed'])) {
                    $updateData['token'] = $token;
                    $updateData['password'] = password_hash($token, PASSWORD_DEFAULT, ['cost' => 8]);
                }
                $this->db->where('id', $existing['id']);
                $this->db->update('users', $updateData);
                $updatedCount++;
            } else {
                // Queue for bulk insert
                $rawPwd = $token ? $token : 'Telkom#123';
                $toInsert[] = [
                    'role_id' => $roleId,
                    'name' => $name,
                    'email' => $email,
                    'password' => password_hash($rawPwd, PASSWORD_DEFAULT, ['cost' => 8]),
                    'nidn_nim' => $nimNip,
                    'token' => $token,
                    'password_changed' => 0,
                    'email_status' => $emailStatus,
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now
                ];
                $importedCount++;

                // If batch reaches 100, insert chunk
                if (count($toInsert) >= 100) {
                    $this->db->insert_batch('users', $toInsert);
                    $toInsert = [];
                }
            }
        }

        if (!empty($toInsert)) {
            $this->db->insert_batch('users', $toInsert);
            $toInsert = [];
        }

        $this->db->trans_complete();

        return [
            'imported' => $importedCount,
            'updated' => $updatedCount
        ];
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
                $updateData['password'] = password_hash($data['token'], PASSWORD_DEFAULT, ['cost' => 8]);
            }
            $this->db->where('id', $existing->id);
            $this->db->update('users', $updateData);
            return $existing->id;
        } else {
            $insertData = [
                'role_id' => isset($data['role_id']) ? $data['role_id'] : 5,
                'name' => $data['name'],
                'email' => $email,
                'password' => password_hash(isset($data['token']) ? $data['token'] : 'Telkom#123', PASSWORD_DEFAULT, ['cost' => 8]),
                'nidn_nim' => isset($data['nidn_nim']) ? $data['nidn_nim'] : '',
                'token' => isset($data['token']) ? $data['token'] : null,
                'password_changed' => 0,
                'email_status' => isset($data['email_status']) ? $data['email_status'] : 'belum',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $this->db->insert('users', $insertData);
            return $this->db->insert_id();
        }
    }

    /**
     * Bulk update tokens for multiple users in a single transaction
     * @param array $updates [['id' => 1, 'token' => '...'], ...]
     * @return int
     */
    public function update_user_tokens_bulk($updates)
    {
        if (empty($updates)) return 0;
        @set_time_limit(300);
        $this->db->trans_start();
        $count = 0;
        $now = date('Y-m-d H:i:s');
        foreach ($updates as $item) {
            $id = $item['id'];
            $token = $item['token'];
            $this->db->where('id', $id);
            $this->db->where('password_changed', 0);
            $this->db->update('users', [
                'token' => $token,
                'password' => password_hash($token, PASSWORD_DEFAULT, ['cost' => 8]),
                'updated_at' => $now
            ]);
            if ($this->db->affected_rows() > 0) $count++;
        }
        $this->db->trans_complete();
        return $count;
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
            'password' => password_hash($token, PASSWORD_DEFAULT, ['cost' => 8]),
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
