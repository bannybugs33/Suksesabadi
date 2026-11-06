<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Alamat tujuan email pengingat
    |--------------------------------------------------------------------------
    |
    | Kosong = dikirim ke email akun pemilik kuitansi. Isi untuk mengalihkan ke
    | alamat lain; boleh lebih dari satu, dipisah koma.
    |
    */

    'email_tujuan' => env('PENGINGAT_EMAIL_TUJUAN'),

];
