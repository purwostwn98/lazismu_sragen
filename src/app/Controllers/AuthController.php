<?php

namespace App\Controllers;

use App\Models\UsersModel;

/**
 * Login mechanism modeled after lazismu_reborn's Iniauth controller: a
 * simple math "are you human" challenge alongside username/password,
 * session-based auth, and a privuser flag gating admin access.
 *
 * Unlike reborn, passwords are verified with password_hash()/password_verify()
 * (bcrypt) rather than sha1() — the request was to mirror the login UX/flow,
 * not carry insecure hashing into new code.
 */
class AuthController extends BaseController
{
    protected UsersModel $userModel;

    public function __construct()
    {
        $this->userModel = new UsersModel();
    }

    public function index()
    {
        if (session()->get('login')) {
            return redirect()->to(base_url('dashboard'));
        }

        $a = random_int(1, 9);
        $b = random_int(1, 9);
        $opr = random_int(0, 1) === 0 ? '+' : 'x';

        $angka = [
            1 => 'satu', 2 => 'dua', 3 => 'tiga', 4 => 'empat', 5 => 'lima',
            6 => 'enam', 7 => 'tujuh', 8 => 'delapan', 9 => 'sembilan',
        ];

        if ($opr === 'x') {
            $hasil    = $a * $b;
            $textOpr  = 'dikali';
        } else {
            $hasil    = $a + $b;
            $textOpr  = 'ditambah';
        }

        $data = [
            'title'     => 'Login',
            'captchaText' => 'Berapa ' . $angka[$a] . ' ' . $textOpr . ' ' . $angka[$b] . '?',
            'captchaHash' => sha1((string) $hasil),
            'errorUser'     => session()->getFlashdata('errorUser'),
            'errorPassword' => session()->getFlashdata('errorPassword'),
            'errorHitung'   => session()->getFlashdata('errorHitung'),
        ];

        return view('login/index', $data);
    }

    public function attempt()
    {
        $jawaban  = (string) $this->request->getPost('jawabCpt');
        $hslbenar = (string) $this->request->getPost('hslbenar');

        if (sha1($jawaban) !== $hslbenar) {
            session()->setFlashdata('errorHitung', 'Maaf, hasil perhitungan Anda salah.');

            return redirect()->to(base_url('login'))->withInput();
        }

        $valid = [
            'username' => ['label' => 'Username', 'rules' => 'required', 'errors' => ['required' => '{field} tidak boleh kosong']],
            'password' => ['label' => 'Password', 'rules' => 'required', 'errors' => ['required' => '{field} tidak boleh kosong']],
        ];

        if (!$this->validate($valid)) {
            session()->setFlashdata('errorUser', $this->validator->getError('username'));
            session()->setFlashdata('errorPassword', $this->validator->getError('password'));

            return redirect()->to(base_url('login'))->withInput();
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $this->userModel->where('username', $username)->first();

        if (!$user) {
            session()->setFlashdata('errorUser', 'Maaf, username tidak ditemukan.');

            return redirect()->to(base_url('login'))->withInput();
        }

        if (!password_verify($password, $user['password'])) {
            session()->setFlashdata('errorPassword', 'Maaf, password yang Anda masukkan salah.');

            return redirect()->to(base_url('login'))->withInput();
        }

        session()->regenerate();
        session()->set([
            'login'     => true,
            'iduser'    => $user['iduser'],
            'priv_user' => $user['privuser'],
            'idlembaga' => $user['idlembaga'],
            'nama'      => $user['nama_user'],
        ]);

        return redirect()->to(base_url('dashboard'));
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to(base_url('/'));
    }
}
