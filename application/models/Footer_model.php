<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Footer_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->_ensure_tables();
    }

    /**
     * Memastikan tabel footer_settings ada dan memiliki data default baris 1
     */
    private function _ensure_tables()
    {
        if (!$this->db->table_exists('footer_settings')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `footer_settings` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `brand_badge` VARCHAR(100) DEFAULT 'TELKOM UNIVERSITY',
                `brand_title` VARCHAR(255) DEFAULT 'Fakultas Industri Kreatif',
                `brand_desc` TEXT NULL,
                `instagram_url` VARCHAR(255) NULL,
                `youtube_url` VARCHAR(255) NULL,
                `linkedin_url` VARCHAR(255) NULL,
                `alamat_kampus` TEXT NULL,
                `email_resmi` VARCHAR(150) DEFAULT 'fik@telkomuniversity.ac.id',
                `telepon` VARCHAR(50) DEFAULT '(022) 756 5923',
                `maps_embed_url` TEXT NULL,
                `maps_link_url` VARCHAR(255) NULL,
                `copyright_text` VARCHAR(255) DEFAULT 'Fakultas Industri Kreatif - Telkom University. All rights reserved.',
                `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }

        // Cek apakah data ID 1 sudah ada
        $existing = $this->db->get_where('footer_settings', ['id' => 1])->row();
        if (!$existing) {
            $default_settings = [
                'id'             => 1,
                'brand_badge'    => 'TELKOM UNIVERSITY',
                'brand_title'    => 'Fakultas Industri Kreatif',
                'brand_desc'     => 'Pusat unggulan pendidikan industri kreatif yang menghasilkan lulusan berkarakter, inovatif, dan siap bersaing di tingkat global.',
                'instagram_url'  => 'https://www.instagram.com/telkomuniversity/',
                'youtube_url'    => 'https://www.youtube.com/@TelkomUniversityOfficial',
                'linkedin_url'   => 'https://www.linkedin.com/school/telkom-university/',
                'alamat_kampus'  => 'Gedung Sebatik (FIK), Telkom University, Bandung, Jawa Barat 40287',
                'email_resmi'    => 'fik@telkomuniversity.ac.id',
                'telepon'        => '(022) 756 5923',
                'maps_embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.301384074211!2d107.63211517587637!3d-6.973715893026955!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e9ad2c8c67c5%3A0xf6031fa15c26e108!2sTelkom%20University%20Fakultas%20Industri%20Kreatif!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid',
                'maps_link_url'  => 'https://maps.google.com/?q=Telkom+University+Fakultas+Industri+Kreatif',
                'copyright_text' => 'Fakultas Industri Kreatif - Telkom University. All rights reserved.',
                'updated_at'     => date('Y-m-d H:i:s')
            ];
            $this->db->query("INSERT IGNORE INTO `footer_settings` (`id`, `brand_badge`, `brand_title`, `brand_desc`, `instagram_url`, `youtube_url`, `linkedin_url`, `alamat_kampus`, `email_resmi`, `telepon`, `maps_embed_url`, `maps_link_url`, `copyright_text`, `updated_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
                $default_settings['id'],
                $default_settings['brand_badge'],
                $default_settings['brand_title'],
                $default_settings['brand_desc'],
                $default_settings['instagram_url'],
                $default_settings['youtube_url'],
                $default_settings['linkedin_url'],
                $default_settings['alamat_kampus'],
                $default_settings['email_resmi'],
                $default_settings['telepon'],
                $default_settings['maps_embed_url'],
                $default_settings['maps_link_url'],
                $default_settings['copyright_text'],
                $default_settings['updated_at']
            ]);
        }
    }

    /**
     * Ambil data setting footer saat ini
     * @return object
     */
    public function get_settings()
    {
        $this->_ensure_tables();
        $settings = $this->db->get_where('footer_settings', ['id' => 1])->row();
        
        if (!$settings) {
            return (object) [
                'id'             => 1,
                'brand_badge'    => 'TELKOM UNIVERSITY',
                'brand_title'    => 'Fakultas Industri Kreatif',
                'brand_desc'     => 'Pusat unggulan pendidikan industri kreatif yang menghasilkan lulusan berkarakter, inovatif, dan siap bersaing di tingkat global.',
                'instagram_url'  => 'https://www.instagram.com/telkomuniversity/',
                'youtube_url'    => 'https://www.youtube.com/@TelkomUniversityOfficial',
                'linkedin_url'   => 'https://www.linkedin.com/school/telkom-university/',
                'alamat_kampus'  => 'Gedung Sebatik (FIK), Telkom University, Bandung, Jawa Barat 40287',
                'email_resmi'    => 'fik@telkomuniversity.ac.id',
                'telepon'        => '(022) 756 5923',
                'maps_embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.301384074211!2d107.63211517587637!3d-6.973715893026955!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e9ad2c8c67c5%3A0xf6031fa15c26e108!2sTelkom%20University%20Fakultas%20Industri%20Kreatif!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid',
                'maps_link_url'  => 'https://maps.google.com/?q=Telkom+University+Fakultas+Industri+Kreatif',
                'copyright_text' => 'Fakultas Industri Kreatif - Telkom University. All rights reserved.',
                'updated_at'     => date('Y-m-d H:i:s')
            ];
        }

        return $settings;
    }

    /**
     * Update data setting footer
     * @param array $data
     * @return bool
     */
    public function update_settings($data)
    {
        $this->_ensure_tables();
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', 1);
        return $this->db->update('footer_settings', $data);
    }

    /**
     * Reset data setting footer ke kondisi awal
     * @return bool
     */
    public function reset_defaults()
    {
        $default_settings = [
            'brand_badge'    => 'TELKOM UNIVERSITY',
            'brand_title'    => 'Fakultas Industri Kreatif',
            'brand_desc'     => 'Pusat unggulan pendidikan industri kreatif yang menghasilkan lulusan berkarakter, inovatif, dan siap bersaing di tingkat global.',
            'instagram_url'  => 'https://www.instagram.com/telkomuniversity/',
            'youtube_url'    => 'https://www.youtube.com/@TelkomUniversityOfficial',
            'linkedin_url'   => 'https://www.linkedin.com/school/telkom-university/',
            'alamat_kampus'  => 'Gedung Sebatik (FIK), Telkom University, Bandung, Jawa Barat 40287',
            'email_resmi'    => 'fik@telkomuniversity.ac.id',
            'telepon'        => '(022) 756 5923',
            'maps_embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.301384074211!2d107.63211517587637!3d-6.973715893026955!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e9ad2c8c67c5%3A0xf6031fa15c26e108!2sTelkom%20University%20Fakultas%20Industri%20Kreatif!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid',
            'maps_link_url'  => 'https://maps.google.com/?q=Telkom+University+Fakultas+Industri+Kreatif',
            'copyright_text' => 'Fakultas Industri Kreatif - Telkom University. All rights reserved.',
            'updated_at'     => date('Y-m-d H:i:s')
        ];

        return $this->update_settings($default_settings);
    }
}
