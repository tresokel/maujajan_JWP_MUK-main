{{-- Menggunakan komponen layout default Laravel Breeze (app-layout) --}}
<x-app-layout>
    {{-- Slot 'header': Bagian judul atas dashboard admin dan tombol aksi cepat --}}
    <x-slot name="header">
        <div class="flex justify-between items-center">
            {{-- Judul Halaman Dashboard --}}
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Pesanan Masuk') }}
            </h2>

            {{-- Tombol Navigasi Cepat Admin --}}
            <div class="flex items-center gap-3">
                {{-- Tombol menuju manajemen CRUD Makanan & Minuman --}}
                <a href="{{ route('foods.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 shadow-sm transition">
                    Kelola Menu
                </a>
                {{-- Tombol untuk membuka halaman pemesanan pelanggan di tab baru --}}
                <a href="{{ route('customer.index') }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 shadow-sm transition">
                    Lihat Menu Customer
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Konten Utama Dashboard --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Notifikasi Flash Message: Menampilkan pesan jika berhasil memperbarui status pesanan --}}
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded shadow-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Notifikasi Error: Menampilkan pesan jika pembaruan status atau proses lain gagal --}}
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded shadow-sm font-medium">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Menampilkan daftar error validasi jika ada input yang tidak valid --}}
            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded shadow-sm font-medium">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Card Container Tabel Rekap Pesanan --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    {{-- Tabel Rekapitulasi Pesanan Pelanggan --}}
                    <table class="w-full text-left border-collapse">
                        {{-- Header Kolom Tabel --}}
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="p-4 border-b"># ID</th>
                                <th class="p-4 border-b">Pelanggan</th>
                                <th class="p-4 border-b">No. Meja</th>
                                <th class="p-4 border-b">Rincian Pesanan</th>
                                <th class="p-4 border-b">Total Harga</th>
                                <th class="p-4 border-b">Status</th>
                                <th class="p-4 border-b text-center">Aksi Status</th>
                            </tr>
                        </thead>

                        {{-- Isi Baris Data Pesanan --}}
                        <tbody class="divide-y text-sm">
                            {{-- Looping koleksi pesanan ($orders) menggunakan @forelse --}}
                            @forelse($orders as $order)
                                <tr class="hover:bg-gray-50">
                                    {{-- Kolom ID Pesanan --}}
                                    <td class="p-4 font-bold text-gray-700">#{{ $order->id }}</td>

                                    {{-- Kolom Nama Pelanggan --}}
                                    <td class="p-4 font-medium">{{ $order->customer_name }}</td>

                                    {{-- Kolom Nomor Meja Pelanggan --}}
                                    <td class="p-4">
                                        <span class="bg-blue-100 text-blue-800 font-bold px-2.5 py-1 rounded-full text-xs">
                                            Meja {{ $order->table_number }}
                                        </span>
                                    </td>

                                    {{-- Kolom Rincian Item: Mengambil relasi orderDetails dan food --}}
                                    <td class="p-4">
                                        <ul class="list-disc list-inside space-y-1 text-gray-600">
                                            @foreach($order->orderDetails as $detail)
                                                <li>
                                                    {{-- Menampilkan nama menu makanan --}}
                                                    <strong>{{ $detail->food->name ?? 'Menu' }}</strong> 
                                                    {{-- Menampilkan kuantiti yang dipesan --}}
                                                    x{{ $detail->quantity }} 
                                                    {{-- Menampilkan subtotal harga item --}}
                                                    <span class="text-xs text-gray-400">(Rp {{ number_format($detail->subtotal) }})</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>

                                    {{-- Kolom Total Harga Keseluruhan Pesanan --}}
                                    <td class="p-4 font-bold text-green-600">
                                        Rp {{ number_format($order->total_price) }}
                                    </td>

                                    {{-- Kolom Badge Status Pesanan (Warna dinamis sesuai status: Pending, Selesai / Lunas, Dibatalkan) --}}
                                    <td class="p-4">
                                        @if($order->status == 'Pending')
                                            <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2.5 py-1 rounded">PENDING</span>
                                        @elseif($order->status == 'Selesai')
                                            <span class="bg-green-100 text-green-800 text-xs font-bold px-2.5 py-1 rounded">SELESAI / LUNAS</span>
                                        @elseif($order->status == 'Dibatalkan')
                                            <span class="bg-red-100 text-red-800 text-xs font-bold px-2.5 py-1 rounded">DIBATALKAN</span>
                                        @elseif($order->status == 'Diproses')
                                            <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-1 rounded">DIPROSES</span>
                                        @else
                                            <span class="bg-gray-100 text-gray-800 text-xs font-bold px-2.5 py-1 rounded">{{ strtoupper($order->status) }}</span>
                                        @endif
                                    </td>

                                    {{-- Kolom Form Update Status: Mengirim request PATCH saat dropdown diubah --}}
                                    <td class="p-4 text-center">
                                        <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                                            {{-- Token keamanan CSRF --}}
                                            @csrf
                                            {{-- Method Spoofing: Mengubah method POST menjadi PATCH sesuai definisi route --}}
                                            @method('PATCH')

                                            {{-- Dropdown Pilihan Status Pesanan --}}
                                            {{-- Nilai value persis sesuai ENUM database: 'Pending', 'Selesai', 'Dibatalkan' --}}
                                            {{-- onchange="this.form.submit()": Form otomatis disubmit saat admin memilih opsi lain --}}
                                            <select name="status" onchange="this.form.submit()" class="text-xs border border-gray-300 rounded p-1.5 bg-white shadow-sm font-semibold cursor-pointer hover:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                                                <option value="Pending" {{ $order->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="Selesai" {{ $order->status == 'Selesai' ? 'selected' : '' }}>Selesai / Lunas</option>
                                                <option value="Dibatalkan" {{ $order->status == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                            </select>  
                                        </form>
                                    </td>
                                </tr>
                            {{-- Tampilan jika belum ada satupun pesanan yang masuk ke database --}}
                            @empty
                                <tr>
                                    <td colspan="7" class="p-6 text-center text-gray-500">Belum ada pesanan masuk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
