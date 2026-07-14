<?php

namespace App\Models;

use CodeIgniter\Model;

class UsersModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'iduser';
    protected $allowedFields = [
        'username', 'password', 'privuser', 'idlembaga', 'nama_user',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'u_created_at';
    protected $updatedField  = 'u_updated_at';
}
