<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PegawaiController extends Controller
{
    /**
     * 1. INDEX - Display a listing of the resource.
     */
    public function index()
    {
        try {
            $pegawai = User::where('role', '!=', 'user')
                ->orderBy('name')
                ->get();

            return view('admin.pegawai.index', compact('pegawai'));
        } catch (\Exception $e) {
            Log::error('Error fetching pegawai list: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data pegawai.');
        }
    }

    /**
     * 2. CREATE - Show the form for creating a new resource.
     */
    public function create()
    {
       
        $agencies = Agency::where('is_active', true)->get();
        return view('admin.pegawai.create', compact('agencies'));
    }

    /**
     * 3. STORE - Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('Store Request Data:', $request->all());
        // Set default password

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin_dinas,superadmin',
            'agency_id' => 'nullable|exists:agencies,id',
        ], [
            'name.required' => 'Nama pegawai wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'role.required' => 'Hak akses wajib dipilih.',
            'role.in' => 'Hak akses yang dipilih tidak valid.',
            'agency_id.exists' => 'Dinas yang dipilih tidak valid.',
        ]);

        DB::beginTransaction();
        Log::info('Data sebelum create:', $validated);

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' =>'Password123!',
                'role' => $validated['role'],
                'email_verified_at' => now(),
                'agency_id' => ($validated['role'] === 'admin_dinas' && isset($validated['agency_id']))
                    ? $validated['agency_id']
                    : null,
            ]);

            DB::commit();

            Log::info('Pegawai created successfully', [
                'user_id' => $user->id,
                'name' => $user->name
            ]);

            return redirect()->route('admin.pegawai.index')
                ->with('success', 'Pegawai ' . $user->name . ' berhasil ditambahkan! Password default: Password123!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating pegawai: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    /**
     * 4. SHOW - Display the specified resource.
     */
    public function show($id)
    {
        try {
            $pegawai = User::findOrFail($id);
            return view('admin.pegawai.show', compact('pegawai'));
        } catch (\Exception $e) {
            Log::error('Error fetching pegawai details: ' . $e->getMessage());
            return redirect()->route('admin.pegawai.index')
                ->with('error', 'Pegawai tidak ditemukan.');
        }
    }

    /**
     * 5. EDIT - Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $pegawai = User::with('agency')->findOrFail($id);
        $agencies = Agency::where('is_active', true)->get();

        Log::info('Edit User Data:', [
        'id' => $pegawai->id,
        'name' => $pegawai->name,
        'role' => $pegawai->role,
        'agency_id' => $pegawai->agency_id,
        'agency_name' => $pegawai->agency ? $pegawai->agency->name : 'No Agency'
    ]);
        try {
            
            return view('admin.pegawai.edit', compact('pegawai', 'agencies'));
        } catch (\Exception $e) {
            Log::error('Error fetching pegawai for edit: ' . $e->getMessage());
            return redirect()->route('admin.pegawai.index')
                ->with('error', 'Pegawai tidak ditemukan.');
        }
    }

    /**
     * 6. UPDATE - Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:admin_dinas,superadmin',
            'agency_id' => 'nullable|required_if:role,admin_dinas|exists:agencies,id',
            'password' => 'nullable|min:6|confirmed', 
            
        ], [
            'name.required' => 'Nama pegawai wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh pegawai lain.',
            'role.required' => 'Hak akses wajib dipilih.',
            'role.in' => 'Hak akses yang dipilih tidak valid.',
            'agency_id.required_if' => 'Dinas wajib dipilih untuk role Admin Dinas.', 
            'agency_id.exists' => 'Dinas yang dipilih tidak valid.',
            
        ]);

        DB::beginTransaction();

        try {
            $pegawai = User::findOrFail($id);

            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'agency_id' => $validated['role'] === 'admin_dinas' ? $validated['agency_id'] : null,
            ];

            // Update password hanya jika diisi
            if (!empty($validated['password'])) {
                $updateData['password'] = $validated['password'];
            }

            $pegawai->update($updateData);

            DB::commit();

            Log::info('Pegawai updated successfully', [
                'user_id' => $pegawai->id,
                'name' => $pegawai->name,
                'role' => $pegawai->role,
                'agency_id' => $pegawai->agency_id
            ]);

            return redirect()->route('admin.pegawai.index')
                ->with('success', 'Data pegawai ' . $pegawai->name . ' berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating pegawai: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    /**
     * 7. DESTROY - Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $pegawai = User::findOrFail($id);
            $pegawaiName = $pegawai->name;

            // Safety check: jangan hapus superadmin terakhir
            if ($pegawai->role === 'superadmin') {
                $superadminCount = User::where('role', 'superadmin')->count();
                if ($superadminCount <= 1) {
                    return redirect()->route('admin.pegawai.index')
                        ->with('error', 'Tidak dapat menghapus superadmin terakhir.');
                }
            }

            $pegawai->delete();

            DB::commit();

            Log::info('Pegawai deleted', [
                'user_id' => $id,
                'name' => $pegawaiName
            ]);

            return redirect()->route('admin.pegawai.index')
                ->with('success', 'Pegawai ' . $pegawaiName . ' berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting pegawai: ' . $e->getMessage());

            return redirect()->route('admin.pegawai.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    /**
     * 8. RESET PASSWORD - Reset password pegawai ke default
     */
    public function resetPassword($id)
    {
        DB::beginTransaction();

        try {
            $pegawai = User::findOrFail($id);

            $pegawai->update([
                'password' => 'Password123!'
            ]);

            DB::commit();

            Log::info('Password reset', [
                'user_id' => $pegawai->id,
                'name' => $pegawai->name
            ]);

            return redirect()->route('admin.pegawai.index')
                ->with('success', 'Password ' . $pegawai->name . ' berhasil direset ke Password123!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error resetting password: ' . $e->getMessage());

            return redirect()->route('admin.pegawai.index')
                ->with('error', 'Terjadi kesalahan saat reset password.');
        }
    }
}
