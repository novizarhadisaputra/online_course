<?php

return [
    'title' => 'Kelas Privat',
    'singular' => 'Kelas Privat',
    'plural' => 'Kelas Privat',
    'navigation_group' => 'Data Master',
    
    'fields' => [
        'name' => 'Nama',
        'slug' => 'Slug',
        'short_description' => 'Deskripsi Singkat',
        'description' => 'Deskripsi',
        'duration' => 'Durasi',
        'duration_units' => 'Satuan Durasi',
        'max_participants' => 'Maksimal Peserta',
        'start_date' => 'Tanggal Mulai',
        'end_date' => 'Tanggal Selesai',
        'status' => 'Status',
        'is_paid' => 'Berbayar',
        'image' => 'Gambar',
        'created_at' => 'Dibuat Pada',
        'updated_at' => 'Diperbarui Pada',
    ],
    
    'status' => [
        'active' => 'Aktif',
        'inactive' => 'Tidak Aktif',
    ],
    
    'payment' => [
        'paid' => 'Berbayar',
        'free' => 'Gratis',
    ],
    
    'sections' => [
        'basic_info' => 'Informasi Dasar',
        'schedule' => 'Jadwal',
        'pricing' => 'Harga',
        'metadata' => 'Metadata',
    ],
    
    'actions' => [
        'view' => 'Lihat',
        'edit' => 'Edit',
        'delete' => 'Hapus',
        'create' => 'Buat Baru',
    ],
    
    'messages' => [
        'created' => 'Kelas privat berhasil dibuat.',
        'updated' => 'Kelas privat berhasil diperbarui.',
        'deleted' => 'Kelas privat berhasil dihapus.',
    ],
    
    'validation' => [
        'name_required' => 'Nama wajib diisi.',
        'name_unique' => 'Nama sudah digunakan.',
        'max_participants_min' => 'Maksimal peserta minimal 1.',
        'start_date_required' => 'Tanggal mulai wajib diisi.',
        'end_date_after' => 'Tanggal selesai harus setelah tanggal mulai.',
    ],
];