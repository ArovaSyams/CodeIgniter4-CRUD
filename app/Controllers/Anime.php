<?php

namespace App\Controllers;

use App\Models\AnimeModel;
use CodeIgniter\CodeIgniter;

class Anime extends BaseController
{
    protected $animeModel;
    public function __construct()
    {
        //agar dapat digunakan oleh method lain
        $this->animeModel = new AnimeModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Anime List',
            'anime' => $this->animeModel->getAnime()
        ];

        //$komikModel = new $komikModel();

        return view('anime/index', $data);
    }

    public function detail($slug)
    {
        $data = [
            'title' => 'Detail Anime',
            'anime' => $this->animeModel->getAnime($slug)
        ];

        if (empty($data['anime'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Data dari $slug Tidak Ditemukan");
        }

        return view('anime/detail', $data);
    }

    public function tambah()
    {
        //session();
        $data = [
            'title' => 'Form Tambah Data Anime',
            'validation' => \Config\Services::validation()
        ];

        return view('anime/tambah', $data);
    }

    public function saves()
    {
        //validasi
        if (!$this->validate([
            'judul' => [
                'rules' => 'required|is_unique[anime.judul]',
                'errors' => [
                    'required' => '{field} Anime Harus Diisi',
                    'is_unique' => '{field} Anime Sudah Terdaftar'
                ]
            ],
            'pengarang' => [
                'rules' => 'required|is_unique[anime.pengarang]',
                'errors' => [
                    'required' => '{field} Anime Harus Diisi',
                    'is_unique' => '{field} Anime Sudah Terdaftar'
                ]
            ],
            'studio' => [
                'rules' => 'required|is_unique[anime.studio]',
                'errors' => [
                    'required' => '{field} Anime Harus Diisi',
                    'is_unique' => '{field} Anime Sudah Terdaftar'
                ]
            ],
            'key_visual' => [
                'rules' => 'required|is_unique[anime.key_visual]',
                'errors' => [
                    'required' => '{field} Anime Harus Diisi',
                    'is_unique' => '{field} Anime Sudah Terdaftar'
                ]
            ],

        ])) {
            $validation = \Config\Services::validation();
            return redirect()->to('/anime/tambah')->withInput()->with('validation', $validation);
        }


        $slug = url_title($this->request->getVar('judul'), '-', true);
        $this->animeModel->save([
            'judul' => $this->request->getVar('judul'),
            'pengarang' => $this->request->getVar('pengarang'),
            'studio' => $this->request->getVar('studio'),
            'key_visual' => $this->request->getVar('key_visual'),
            'slug' => $slug
        ]);

        session()->setFlashData('pesan', 'Data Berhasil Ditambahkan');

        return redirect()->to('/anime');
    }

    public function delete($id)
    {
        $this->animeModel->delete($id);

        session()->setFlashData('pesan', 'Data Berhasil Dihapus');

        return redirect()->to('/anime');
    }

    public function edit($slug)
    {

        $data = [
            'title' => 'Form Tambah Data Anime',
            'validation' => \Config\Services::validation(),
            'anime' => $this->animeModel->getAnime($slug)
        ];

        return view('anime/edit', $data);
    }

    public function update($id)
    {
        $animeLama = $this->animeModel->getAnime($this->request->getVar('slug'));

        if ($animeLama['judul'] == $this->request->getVar('judul') || $animeLama['pengarang'] == $this->request->getVar('pengarang') || $animeLama['studio'] == $this->request->getVar('studio') || $animeLama['key_visual'] == $this->request->getVar('key_visual')) {

            $rules = ['required', 'required', 'required', 'required'];
        } else {
            $rules = ['required|is_unique[anime.judul]', 'required|is_unique[anime.pengarang]', 'required|is_unique[anime.studio]', 'required|is_unique[anime.key_visual]'];
        }

        //validasi
        if (!$this->validate([
            'judul' => [
                'rules' => $rules[0],
                'errors' => [
                    'required' => '{field} Anime Harus Diisi',
                    'is_unique' => '{field} Anime Sudah Terdaftar'
                ]
            ],
            'pengarang' => [
                'rules' => $rules[1],
                'errors' => [
                    'required' => '{field} Anime Harus Diisi',
                    'is_unique' => '{field} Anime Sudah Terdaftar'
                ]
            ],
            'studio' => [
                'rules' => $rules[2],
                'errors' => [
                    'required' => '{field} Anime Harus Diisi',
                    'is_unique' => '{field} Anime Sudah Terdaftar'
                ]
            ],
            'key_visual' => [
                'rules' => $rules[3],
                'errors' => [
                    'required' => '{field} Anime Harus Diisi',
                    'is_unique' => '{field} Anime Sudah Terdaftar'
                ]
            ]

        ])) {
            $validation = \Config\Services::validation();
            return redirect()->to('/anime/edit/' . $this->request->getVar('slug'))->withInput()->with('validation', $validation);
        }


        $slug = url_title($this->request->getVar('judul'), '-', true);
        $this->animeModel->save([
            'id' => $id,
            'judul' => $this->request->getVar('judul'),
            'pengarang' => $this->request->getVar('pengarang'),
            'studio' => $this->request->getVar('studio'),
            'key_visual' => $this->request->getVar('key_visual'),
            'slug' => $slug
        ]);

        session()->setFlashData('pesan', 'Data Berhasil Diubah');

        return redirect()->to('/anime');
    }
}
