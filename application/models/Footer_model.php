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
        return; // Jangan jalankan DDL CREATE TABLE di database
    }

    /**
     * Ambil data setting footer saat ini
     * @return object
     */
    public function get_settings()
    {
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
