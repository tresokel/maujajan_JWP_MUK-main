<?php

namespace App\Http\Controllers;

// Import model yang digunakan untuk berinteraksi dengan database
use App\Models\Order;       // Model untuk tabel orders (data pesanan utama)
use App\Models\Food;        // Model untuk tabel foods (data makanan/menu)
use App\Models\OrderDetail; // Model untuk tabel order_details (rincian item dalam pesanan)
use Illuminate\Support\Facades\DB; // Facade DB untuk mengelola transaksi database (commit/rollback)
use Illuminate\Http\Request;       // Class Request untuk menangani data yang dikirim dari form input

class OrderController extends Controller
{
    /**
     * Menampilkan katalog menu dan halaman pemesanan untuk pelanggan.
     * URL: GET /
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Mengambil semua data makanan dari tabel 'foods'
        $foods = Food::all();

        // Variabel alias $food untuk kompatibilitas jika ada view yang menggunakannya
        $food = $foods;

        // Mengirimkan data makanan ke view 'resources/views/customer/index.blade.php'
        return view('customer.index', compact('foods', 'food'));
    }

    /**
     * Show the form for creating a new resource.
     * (Opsional / Tidak digunakan karena form pemesanan langsung ada di index)
     */
    public function create()
    {
        //
    }

    /**
     * Memproses dan menyimpan pesanan baru dari pelanggan ke database.
     * URL: POST /checkout
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // 1. Validasi input form: memastikan nama, meja, dan daftar item sesuai aturan
        $request->validate([
            'customer_name' => 'required|string|max:255',  // Nama pemesan wajib diisi teks maks 255 karakter
            'table_number' => 'required|integer|min:1',     // Nomor meja wajib diisi angka minimal 1
            'items' => 'required|array',             // Data item harus berupa array [food_id => quantity]
            'items.*' => 'nullable|integer|min:0',     // Setiap jumlah item bernilai angka >= 0
        ]);

        // 2. Filter menu yang dipesan: hanya ambil item dengan jumlah porsi (quantity) lebih dari 0
        $orderedItems = array_filter($request->items, fn($qty) => $qty > 0);

        // Jika pelanggan tidak memilih menu satupun (semua porsi 0), kembalikan dengan pesan error
        if (empty($orderedItems)) {
            return back()->with('error', 'Pilih minimal satu menu makanan!');
        }

        // 3. Memulai Database Transaction
        // Tujuannya agar jika salah satu proses insert gagal, seluruh data yang sudah masuk akan dibatalkan (rollback)
        DB::beginTransaction();
        try {
            // 4. Membuat record pesanan baru di tabel 'orders'
            $order = Order::create([
                'customer_name' => $request->customer_name, // Nama pelanggan
                'table_number' => $request->table_number,  // Nomor meja
                'total_price' => 0,                       // Diinisialisasi 0, akan dihitung setelah looping rincian
                'status' => 'Pending',                 // Status awal pesanan: 'Pending' (Sesuai ENUM database: 'Pending','Diproses','Selesai')
            ]);

            $totalPrice = 0; // Variabel penampung akumulasi total harga

            // 5. Looping setiap menu yang dipesan untuk menghitung subtotal dan menyimpan ke 'order_details'
            foreach ($orderedItems as $foodId => $quantity) {
                // Cari data makanan berdasarkan ID, jika tidak ditemukan akan menghasilkan 404
                $food = Food::findOrFail($foodId);

                // Hitung subtotal: harga menu dikali jumlah pesanan
                $subtotal = $food->price * $quantity;
                $totalPrice += $subtotal; // Tambahkan ke total akumulasi

                // Simpan rincian item ke tabel 'order_details'
                OrderDetail::create([
                    'order_id' => $order->id, // ID pesanan utama sebagai foreign key
                    'food_id' => $food->id,  // ID menu makanan
                    'quantity' => $quantity,  // Jumlah porsi yang dipesan
                    'subtotal' => $subtotal,  // Total harga untuk item ini
                ]);
            }

            // 6. Update total harga akhir pada pesanan utama
            $order->update(['total_price' => $totalPrice]);

            // Jika semua query berhasil tanpa error, simpan permanen ke database
            DB::commit();

            // Redirect kembali ke halaman menu dengan notifikasi sukses
            return redirect()->route('customer.index')
                ->with('success', 'Pesanan berhasil dibuat! Nomor meja: ' . $order->table_number);

        } catch (\Exception $e) {
            // Jika terjadi kesalahan query/sistem, batalkan seluruh perubahan database
            DB::rollBack();

            // Kembalikan ke halaman form dengan pesan error detail
            return back()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan dashboard admin yang berisi rekapitulasi semua pesanan masuk.
     * URL: GET /dashboard atau GET /admin/dashboard
     *
     * @return \Illuminate\View\View
     */
    public function adminDashboard()
    {
        // Mengambil semua data pesanan dari database:
        // - 'with('orderDetails.food')': Eager Loading untuk memuat relasi rincian pesanan sekaligus relasi datanya dengan tabel foods (mencegah query N+1)
        // - 'latest()': Mengurutkan pesanan dari yang paling baru dibuat
        // - 'get()': Menjalankan query dan mengambil hasilnya dalam bentuk collection
        $orders = Order::with('orderDetails.food')->latest()->get();

        // Mengirimkan data $orders ke view dashboard
        return view('dashboard', compact('orders'));
    }

    /**
     * Memperbarui status pesanan (contoh: Pending -> Diproses -> Selesai).
     * URL: PATCH /admin/orders/{id}/status
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, $id)
    {
        // Validasi input status: wajib diisi dan nilainya harus salah satu dari ENUM database ('Pending', 'Diproses', 'Selesai', 'Dibatalkan')
        $request->validate([
            'status' => 'required|string|in:Pending,Diproses,Selesai,Dibatalkan'
        ]);

        // Cari pesanan berdasarkan ID yang dikirim melalui parameter URL
        $order = Order::findOrFail($id);

        // Perbarui kolom 'status' pada data pesanan
        $order->update(['status' => $request->status]);

        // Kembalikan ke halaman sebelumnya dengan pesan sukses
        return back()->with('success', 'Status pesanan #' . $order->id . ' berhasil diubah menjadi ' . $order->status . '!');
    }

    /**
     * Display the specified resource.
     * (Disediakan oleh resource controller jika dibutuhkan di kemudian hari)
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     * (Disediakan oleh resource controller jika dibutuhkan di kemudian hari)
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * (Disediakan oleh resource controller jika dibutuhkan di kemudian hari)
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * (Disediakan oleh resource controller jika dibutuhkan di kemudian hari)
     */
    public function destroy(Order $order)
    {
        //
    }
}
