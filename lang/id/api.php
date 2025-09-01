<?php

return [
    'messages' => [
        'success' => 'Berhasil',
        'error' => 'Terjadi kesalahan',
        'not_found' => 'Data tidak ditemukan',
        'unauthorized' => 'Tidak memiliki akses',
        'forbidden' => 'Akses ditolak',
        'validation_failed' => 'Validasi gagal',
        'server_error' => 'Kesalahan server internal',
        'created' => 'Data berhasil dibuat',
        'updated' => 'Data berhasil diperbarui',
        'deleted' => 'Data berhasil dihapus',
    ],
    
    'private_class' => [
        'list_success' => 'Daftar kelas privat berhasil diambil',
        'detail_success' => 'Detail kelas privat berhasil diambil',
        'not_found' => 'Kelas privat tidak ditemukan',
        'inactive' => 'Kelas privat tidak aktif',
        'add_to_cart_success' => 'Kelas privat berhasil ditambahkan ke keranjang',
        'cart_updated' => 'Keranjang berhasil diperbarui',
        'invalid_quantity' => 'Kuantitas tidak valid',
        'invalid_price' => 'Harga tidak valid',
        'max_participants_exceeded' => 'Jumlah peserta melebihi batas maksimal',
    ],
    
    'validation' => [
        'required' => 'Field :attribute wajib diisi',
        'string' => 'Field :attribute harus berupa teks',
        'integer' => 'Field :attribute harus berupa angka',
        'min' => 'Field :attribute minimal :min karakter',
        'max' => 'Field :attribute maksimal :max karakter',
        'uuid' => 'Field :attribute harus berupa UUID yang valid',
        'exists' => 'Field :attribute tidak ditemukan',
        'unique' => 'Field :attribute sudah digunakan',
        'email' => 'Field :attribute harus berupa email yang valid',
        'date' => 'Field :attribute harus berupa tanggal yang valid',
        'after' => 'Field :attribute harus setelah :date',
        'before' => 'Field :attribute harus sebelum :date',
        'numeric' => 'Field :attribute harus berupa angka',
        'boolean' => 'Field :attribute harus berupa true atau false',
        'array' => 'Field :attribute harus berupa array',
        'in' => 'Field :attribute tidak valid',
    ],
    
    'pagination' => [
        'showing' => 'Menampilkan :from sampai :to dari :total hasil',
        'previous' => 'Sebelumnya',
        'next' => 'Selanjutnya',
        'first' => 'Pertama',
        'last' => 'Terakhir',
    ],
    
    'filters' => [
        'all' => 'Semua',
        'active' => 'Aktif',
        'inactive' => 'Tidak Aktif',
        'paid' => 'Berbayar',
        'free' => 'Gratis',
        'search_placeholder' => 'Cari...',
        'sort_by' => 'Urutkan berdasarkan',
        'sort_order' => 'Urutan',
        'asc' => 'Naik',
        'desc' => 'Turun',
    ],
    
    'auth' => [
        'login_required' => 'Silakan login terlebih dahulu',
        'invalid_credentials' => 'Email atau password salah',
        'account_disabled' => 'Akun Anda telah dinonaktifkan',
        'token_expired' => 'Token telah kedaluwarsa',
        'token_invalid' => 'Token tidak valid',
    ],
];