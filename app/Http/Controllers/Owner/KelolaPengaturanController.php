<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use Illuminate\Http\Request;

class KelolaPengaturanController extends Controller
{
    public function editPengaturan()
    {
        $pengaturan = Pengaturan::allAsArray();

        return view('owner.kelola.edit.editPengaturan', compact('pengaturan'));
    }

    public function updatePengaturan(Request $request)
    {
        $rules = [];
        $messages = [];

        foreach (Pengaturan::KEYS as $key => $label) {
            $rules[$key] = ['nullable', 'string'];
        }

        $data = $request->validate($rules, $messages);

        Pengaturan::upsertNilai($data);

        return back()->with('estatus', 'Profil Perusahaan Berhasil Diperbarui!');
    }
}
