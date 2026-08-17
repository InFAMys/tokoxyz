<?php

namespace App\Http\Controllers\Owner;

use App\Console\Commands\NotifyActiveDiscounts;
use App\Http\Controllers\Controller;
use App\Models\Diskon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KelolaDiskonController extends Controller
{
    public function listDiskons(Request $request)
    {
        $q = trim($request->query('q', ''));

        $diskon = Diskon::query()
            ->when($q, fn ($query) => $query
                ->where('nama_diskon', 'like', "%$q%")
                ->orWhere('kode_diskon', 'like', "%$q%"))
            ->get();

        if ($request->ajax()) {
            return view('owner.kelola._diskon_rows', compact('diskon'))->render();
        }

        return view('owner.kelola.k_diskon', compact('diskon'));
    }

    public function tambahDiskon()
    {

        return view('owner.kelola.tambah.tambahDiskon');
    }

    public function addDiskon(Request $request)
    {

        $data = $request->validate([
            'nama_diskon' => ['required', 'string', 'max:30'],
            'jumlah_diskon' => ['required', 'numeric', 'min:1', 'max:95'],
            'kode_diskon' => ['required', 'string', 'max:10', 'regex:/^[A-Za-z0-9]+$/'],
            'mulai_diskon' => ['required', 'date'],
            'akhir_diskon' => ['required', 'date'],
        ],
            [
                'nama_diskon.required' => 'Masukkan Nama Diskon!',
                'nama_diskon.max' => 'Panjang Nama Diskon Maksimal 30 Karakter!',
                'jumlah_diskon.required' => 'Masukkan Jumlah Diskon!',
                'jumlah_diskon.max' => 'Jumlah Diskon Maksimal 95% !',
                'jumlah_diskon.min' => 'Jumlah Diskon Minimal 1% !',
                'kode_diskon.required' => 'Masukkan Kode Diskon!',
                'kode_diskon.max' => 'Panjang Kode Diskon Maksimal 10 Karakter!',
                'kode_diskon.maxregex' => 'Hanya huruf dan angka yang diperbolehkan untuk Username.!',
                'mulai_diskon.required' => 'Masukkan Mulai Diskon!',
                'akhir_diskon.required' => 'Masukkan Akhir Diskon!',
            ]);

        $diskon = Diskon::create([
            'nama_diskon' => $data['nama_diskon'],
            'jumlah_diskon' => $data['jumlah_diskon'],
            'kode_diskon' => strtoupper($data['kode_diskon']),
            'mulai_diskon' => $data['mulai_diskon'],
            'akhir_diskon' => $data['akhir_diskon'],
        ]);

        return back()->with('astatus', 'Diskon Berhasil Ditambahkan!');

    }

    public function editDiskon($id)
    {
        $diskon = Diskon::where('id_diskon', $id)->first();
        $statusOptions = Diskon::statusOptions();

        return view('owner.kelola.edit.editDiskon', compact('diskon', 'statusOptions'));
    }

    public function updateDiskon(Request $request, $id)
    {
        $diskon = Diskon::where('id_diskon', $id)->first();

        $data = $request->validate([
            'nama_diskon' => ['required', 'string', 'max:30'],
            'jumlah_diskon' => ['required', 'numeric', 'min:1', 'max:95'],
            'kode_diskon' => ['required', 'string', 'max:10', 'regex:/^[A-Za-z0-9]+$/'],
            'mulai_diskon' => ['required', 'date'],
            'akhir_diskon' => ['required', 'date'],
            'status_diskon' => ['required', Rule::in(Diskon::statusOptions())],
        ],
            [
                'nama_diskon.required' => 'Masukkan Nama Diskon!',
                'nama_diskon.max' => 'Panjang Nama Diskon Maksimal 30 Karakter!',
                'jumlah_diskon.required' => 'Masukkan Jumlah Diskon!',
                'jumlah_diskon.max' => 'Jumlah Diskon Maksimal 95% !',
                'jumlah_diskon.min' => 'Jumlah Diskon Minimal 1% !',
                'kode_diskon.required' => 'Masukkan Kode Diskon!',
                'kode_diskon.max' => 'Panjang Kode Diskon Maksimal 10 Karakter!',
                'kode_diskon.maxregex' => 'Hanya huruf dan angka yang diperbolehkan untuk Username.!',
                'mulai_diskon.required' => 'Masukkan Mulai Diskon!',
                'akhir_diskon.required' => 'Masukkan Akhir Diskon!',
            ]);

        $diskon->nama_diskon = $data['nama_diskon'];
        $diskon->jumlah_diskon = $data['jumlah_diskon'];
        $diskon->kode_diskon = strtoupper($data['kode_diskon']);
        $diskon->mulai_diskon = $data['mulai_diskon'];
        $diskon->akhir_diskon = $data['akhir_diskon'];
        $diskon->status_diskon = $data['status_diskon'];
        $diskon->update();

        NotifyActiveDiscounts::notifyActive($diskon);

        return back()->with('estatus', 'Diskon Berhasil Di Edit!');
    }

    public function deleteDiskon($id)
    {
        Diskon::where('id_diskon', $id)->first()->delete();

        // //// Pegawai::where('id', $id)->forceDelete();        // Delete Permanently

        return back()->with('delStatus', 'Diskon Berhasil Di Hapus!');
    }
}
