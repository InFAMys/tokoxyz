<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function home()
    {
        $barangRand = Barang::with('ukurans')->where('status', 'Ditampilkan')->inRandomOrder()->get();
        $barangNew = Barang::with('ukurans')->where('status', 'Ditampilkan')->latest()->get();

        return view('welcome', compact('barangRand', 'barangNew'));
    }

    public function cari(Request $request)
    {
        $q = trim((string) $request->query('q'));

        $barang = Barang::with(['brand', 'kategori'])
            ->where('status', 'Ditampilkan')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('nama_barang', 'like', "%{$q}%")
                        ->orWhere('kode_barang', 'like', "%{$q}%")
                        ->orWhereHas('brand', fn ($b) => $b->where('nama_brand', 'like', "%{$q}%"))
                        ->orWhereHas('kategori', fn ($k) => $k->where('nama_kategori', 'like', "%{$q}%"));
                });
            })
            ->orderByDesc('id_barang')
            ->get();

        return view('customer.barang.search', compact('barang', 'q'));
    }

    public function detailBarang(int $id)
    {
        $barang = Barang::with(['brand', 'kategori', 'ukurans'])
            ->where('status', 'Ditampilkan')
            ->where('id_barang', $id)
            ->firstOrFail();

        return view('customer.barang.detail', compact('barang'));
    }

    public function profil()
    {
        $customer = Auth::guard('customer')->user();

        if ($customer->member === 'false') {
            foreach ($customer->memberships()->where('status', 'pending')->get() as $membership) {
                app(MemberController::class)->reconcile($membership);
            }

            $customer->refresh();
        }

        return view('customer.profile.profil', [
            'customer' => $customer,
        ]);
    }

    public function profilEdit()
    {
        return view('customer.profile.edit', [
            'customer' => Auth::guard('customer')->user(),
        ]);
    }

    public function updateNama(Request $request)
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:64'],
        ],
            [
                'nama.required' => 'Masukkan Nama!',
                'nama.max' => 'Panjang Nama Maksimal 64 Karakter!',
            ]);

        $customer->nama = $data['nama'];

        $customer->save();

        return back()->with('nstatus', 'Nama Berhasil Diubah!');
    }

    public function updateEmail(Request $request)
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        $data = $request->validate([
            'email' => [
                'required', 'string', 'max:255',
                Rule::unique('customers', 'email'),
            ],
        ],
            [
                'email.required' => 'Masukkan Alamat E-Mail!',
                'email.unique' => 'E-Mail Sudah Dipakai!',
            ]);

        $customer->email = $data['email'];

        $customer->save();

        return back()->with('estatus', 'Email Berhasil Diubah!');
    }

    public function updateTelp(Request $request)
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        $data = $request->validate([
            'no_telp' => [
                'required', 'string', 'regex:/^[0-9\-]{9,12}$/', 'min:8', 'max:12',
                Rule::unique('customers', 'no_telp'),
            ],
        ],
            [
                'no_telp.required' => 'Masukkan Aalamat No. Telepon!',
                'no_telp.unique' => 'No. Telepon Sudah Dipakai!',
                'no_telp.min' => 'No. Telepon Minimal 8 Karakter!',
                'no_telp.max' => 'No. Telepon Melebihi 12 Karakter!',
                'no_telp.regex' => 'No. Telepon Hanya Menerima Angka!',
            ]);

        $customer->no_telp = $data['no_telp'];

        $customer->save();

        return back()->with('ntstatus', 'No. Telepon Berhasil Diubah!');
    }

    public function updateUsername(Request $request)
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        $data = $request->validate([
            'username' => [
                'required', 'string', 'max:15',
                Rule::unique('customers', 'username'),
            ],
        ],
            [
                'username.required' => 'Masukkan Alamat Username!',
                'username.max' => 'Panjang Username Maksimal 15 Karakter!',
                'username.unique' => 'Username '.$request->input('username').' Sudah Dipakai!',
            ]);

        $customer->username = $data['username'];

        $customer->save();

        return back()->with('ustatus', 'Username Berhasil Diubah!');
    }

    public function passwordEdit()
    {
        return view('customer.profile.editpassword', [
            'customer' => Auth::guard('customer')->user(),
        ]);
    }

    public function passwordUpdate(Request $request)
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        $data = $request->validate([
            // Only required if they're actually trying to set a new password.
            'current_password' => ['required_with:password', 'string'],
            'password' => ['string', 'min:8', 'required'],
        ], [
            'current_password.required_with' => 'Masukan password sekarang untuk merubah password!',
        ]);

        // If they filled in a new password, verify the old one matches
        // what's actually stored before touching anything.
        if (! empty($data['password'])) {
            if (! Hash::check($data['current_password'], $customer->password)) {
                return back()
                    ->withErrors(['current_password' => 'Password Salah!']);
            }
            if (Hash::check($data['password'], $customer->password)) {
                return back()
                    ->withErrors(['password' => 'Password baru harus berbeda dengan password sekarang!']);
            }

        }

        if (! empty($data['password'])) {
            $customer->password = Hash::make($data['password']);
        }

        $customer->save();

        return back()->with('pstatus', 'Password Berhasil Diubah!');
    }
}
