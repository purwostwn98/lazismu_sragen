<?php

namespace App\Controllers;

use App\Models\UsersModel;

class UserController extends BaseController
{
    protected UsersModel $userModel;

    public function __construct()
    {
        $this->userModel = new UsersModel();
    }

    public function index()
    {
        $data = [
            'title'      => 'Manajemen User',
            'activeMenu' => 'user',
            'users'      => $this->userModel->orderBy('nama_user', 'ASC')->findAll(),
        ];

        return view('user/index', $data);
    }

    /** Password is required on create, but optional on update (blank keeps the current one). */
    private function rules(?int $id = null): array
    {
        $unique = 'is_unique[users.username' . ($id !== null ? ',iduser,' . $id : '') . ']';

        return [
            'nama_user' => [
                'label'  => 'Nama',
                'rules'  => 'required',
                'errors' => ['required' => '{field} tidak boleh kosong'],
            ],
            'username' => [
                'label'  => 'Username',
                'rules'  => 'required|' . $unique,
                'errors' => ['required' => '{field} tidak boleh kosong', 'is_unique' => 'Username sudah digunakan'],
            ],
            'password' => [
                'label'  => 'Password',
                'rules'  => $id === null ? 'required|min_length[6]' : 'permit_empty|min_length[6]',
                'errors' => ['required' => '{field} tidak boleh kosong', 'min_length' => '{field} minimal 6 karakter'],
            ],
        ];
    }

    public function store()
    {
        if (!$this->validate($this->rules())) {
            session()->setFlashdata('gagal', implode(' ', $this->validator->getErrors()));
        } else {
            $ok = $this->userModel->insert([
                'nama_user' => $this->request->getPost('nama_user'),
                'username'  => $this->request->getPost('username'),
                'password'  => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            ]) !== false;

            session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'User berhasil disimpan!' : 'GAGAL menyimpan data!');
        }

        return redirect()->to(base_url('user'));
    }

    public function update(int $id)
    {
        if (!$this->validate($this->rules($id))) {
            session()->setFlashdata('gagal', implode(' ', $this->validator->getErrors()));

            return redirect()->to(base_url('user'));
        }

        $data = [
            'nama_user' => $this->request->getPost('nama_user'),
            'username'  => $this->request->getPost('username'),
        ];

        $password = (string) $this->request->getPost('password');
        if ($password !== '') {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $ok = $this->userModel->update($id, $data);
        session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'User berhasil diperbarui!' : 'GAGAL menyimpan data!');

        return redirect()->to(base_url('user'));
    }

    public function delete(int $id)
    {
        if ($id === (int) session()->get('iduser')) {
            session()->setFlashdata('gagal', 'Tidak bisa menghapus akun yang sedang Anda gunakan.');
        } elseif ($this->userModel->countAllResults() <= 1) {
            session()->setFlashdata('gagal', 'Tidak bisa menghapus satu-satunya user yang tersisa.');
        } else {
            $ok = $this->userModel->delete($id);
            session()->setFlashdata($ok ? 'berhasil' : 'gagal', $ok ? 'User berhasil dihapus!' : 'GAGAL menghapus data!');
        }

        return redirect()->to(base_url('user'));
    }
}
