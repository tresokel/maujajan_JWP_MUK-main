<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Restoran - MauJajan</title>
    <!-- Memuat Tailwind CSS melalui CDN untuk styling antarmuka -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 font-sans">
    <div class="max-w-5xl mx-auto">
        
        <!-- ============================================================= -->
        <!-- HEADER HALAMAN: Judul dan deskripsi singkat untuk pelanggan -->
        <!-- ============================================================= -->
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Menu Restoran</h1>
            <p class="text-gray-500 text-sm mt-1">Pilih menu makanan dan masukkan nomor meja Anda</p>
        </div>

        <!-- ============================================================= -->
        <!-- NOTIFIKASI FEEDBACK: Flash message sukses, error, atau validasi -->
        <!-- ============================================================= -->
        {{-- Menampilkan pesan sukses setelah pesanan berhasil dibuat --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6 text-center font-semibold">
                {{ session('success') }}
            </div>
        @endif

        {{-- Menampilkan pesan error umum dari controller (misal: gagal transaksi) --}}
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6 text-center font-semibold">
                {{ session('error') }}
            </div>
        @endif

        {{-- Menampilkan daftar error validasi jika ada input yang tidak sesuai aturan --}}
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6 font-semibold">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- ============================================================= -->
        <!-- FILTER KATEGORI MENU (Dioperasikan melalui JavaScript)       -->
        <!-- Mengubah tampilan menu berdasarkan kategori tanpa reload halaman -->
        <!-- ============================================================= -->
        <div class="flex flex-wrap justify-center gap-3 mb-8">
            <button type="button" onclick="filterCategory('all', this)" class="btn-category px-5 py-2 rounded-full font-semibold text-sm transition bg-blue-600 text-white shadow-md">
                Semua Menu
            </button>
            <button type="button" onclick="filterCategory('Makanan', this)" class="btn-category px-5 py-2 rounded-full font-semibold text-sm transition bg-white text-gray-600 hover:bg-gray-200 border">
                Makanan
            </button>
            <button type="button" onclick="filterCategory('Minuman', this)" class="btn-category px-5 py-2 rounded-full font-semibold text-sm transition bg-white text-gray-600 hover:bg-gray-200 border">
                Minuman
            </button>
            <button type="button" onclick="filterCategory('Cemilan', this)" class="btn-category px-5 py-2 rounded-full font-semibold text-sm transition bg-white text-gray-600 hover:bg-gray-200 border">
                Cemilan
            </button>
        </div>

        <!-- ============================================================= -->
        <!-- FORM PEMESANAN: Mengirim data nama, meja, dan kuantiti pesanan -->
        <!-- Mengirim data ke route('customer.checkout') dengan method POST -->
        <!-- ============================================================= -->
        <form id="orderForm" action="{{ route('customer.checkout') }}" method="POST">
            {{-- Token keamanan wajib Laravel untuk melindungi form dari serangan CSRF --}}
            @csrf

            <!-- --------------------------------------------------------- -->
            <!-- BAGIAN 1: INFORMASI PEMESAN (Nama & Nomor Meja)           -->
            <!-- --------------------------------------------------------- -->
            <div class="bg-white p-6 rounded-xl shadow-sm border mb-6">
                <h2 class="text-lg font-bold text-gray-700 mb-4 pb-2 border-b">1. Informasi Pemesan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Input Nama Lengkap Pemesan --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Nama Lengkap</label>
                        <input type="text" id="customer_name" name="customer_name" required placeholder="Masukkan nama pemesan" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    {{-- Input Nomor Meja Pelanggan --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Nomor Meja</label>
                        <input type="number" id="table_number" name="table_number" required placeholder="Contoh: 5" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>
            </div>

            <!-- --------------------------------------------------------- -->
            <!-- BAGIAN 2: DAFTAR KATALOG MENU MAKANAN / MINUMAN           -->
            <!-- --------------------------------------------------------- -->
            <h2 class="text-lg font-bold text-gray-700 mb-4">2. Pilih Menu</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Looping semua data makanan yang dikirim dari controller ($foods) --}}
                @foreach($foods as $food)
                    {{-- Setiap kartu diberi atribut 'data-category' agar bisa disaring oleh fungsi JavaScript filterCategory --}}
                    <div class="food-card bg-white rounded-xl shadow-sm border overflow-hidden flex flex-col justify-between" data-category="{{ $food->category ?? 'Makanan' }}">
                        <div>
                            {{-- Menampilkan gambar menu dari storage jika ada, atau placeholder abu-abu jika tidak ada gambar --}}
                            @if($food->image)
                                <img src="{{ asset('storage/' . $food->image) }}" alt="{{ $food->name }}" class="w-full h-40 object-cover">
                            @else
                                <div class="bg-gray-200 h-40 flex items-center justify-center text-gray-400 font-medium">Tanpa Gambar</div>
                            @endif

                            {{-- Informasi detail menu: kategori, harga, nama makanan, dan deskripsi --}}
                            <div class="p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs bg-blue-100 text-blue-700 font-semibold px-2.5 py-0.5 rounded">{{ $food->category ?? 'Makanan' }}</span>
                                    <span class="font-bold text-green-600">Rp {{ number_format($food->price) }}</span>
                                </div>
                                <h3 class="font-bold text-gray-800 text-lg item-name">{{ $food->name }}</h3>
                                <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $food->description }}</p>
                            </div>
                        </div>

                        {{-- Input jumlah porsi yang dipesan untuk masing-masing menu --}}
                        {{-- Nama input menggunakan format array: items[id_makanan] --}}
                        {{-- data-name dan data-price digunakan oleh JavaScript modal konfirmasi --}}
                        <div class="p-4 bg-gray-50 border-t">
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Jumlah Porsi</label>
                            <input type="number" name="items[{{ $food->id }}]" min="0" value="0" data-name="{{ $food->name }}" data-price="{{ $food->price }}" class="item-qty w-full border rounded-lg px-3 py-1.5 text-center font-bold text-gray-700 focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Tombol trigger untuk menampilkan modal konfirmasi sebelum submit form -->
            <div class="mt-8 text-right">
                <button type="button" onclick="showConfirmationModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-3 rounded-xl shadow-md transition">Pesan Sekarang</button>
            </div>

            <!-- ============================================================= -->
            <!-- MODAL POPUP KONFIRMASI PESANAN (POPUP SEBELUM SUBMIT KE DB)    -->
            <!-- Berada di dalam tag <form> agar tombol submit dapat mengirim data -->
            <!-- ============================================================= -->
            <div id="confirmModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
                <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl transform transition-all">
                    <h3 class="text-xl font-bold text-gray-800 border-b pb-3 mb-4">Konfirmasi Pesanan</h3>
                    
                    {{-- Ringkasan nama dan nomor meja pelanggan --}}
                    <div class="space-y-2 text-sm text-gray-600 mb-4">
                        <div class="flex justify-between"><span class="font-semibold">Nama:</span> <span id="modalName" class="text-gray-900 font-bold"></span></div>
                        <div class="flex justify-between"><span class="font-semibold">No. Meja:</span> <span id="modalTable" class="text-gray-900 font-bold"></span></div>
                    </div>

                    {{-- Daftar rincian menu yang dipesan (diisi dinamis oleh JavaScript) --}}
                    <div class="border-t border-b py-3 mb-4 max-h-48 overflow-y-auto">
                        <p class="font-semibold text-xs text-gray-400 uppercase mb-2">Rincian Item</p>
                        <ul id="modalItemList" class="space-y-2 text-sm"></ul>
                    </div>

                    {{-- Ringkasan total biaya yang harus dibayar --}}
                    <div class="flex justify-between items-center text-lg font-bold text-gray-800 mb-6">
                        <span>Total Pembayaran:</span>
                        <span id="modalTotalPrice" class="text-green-600 text-xl">Rp 0</span>
                    </div>

                    {{-- Tombol aksi: Batal (tutup modal) atau Ya (submit form ke controller) --}}
                    <div class="flex gap-3">
                        <button type="button" onclick="closeConfirmationModal()" class="w-1/2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2.5 rounded-xl transition">Batal</button>
                        <button type="submit" class="w-1/2 bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 rounded-xl shadow transition">Ya, Kirim Pesanan</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- ================================================================= -->
    <!-- LOGIKA JAVASCRIPT: Filter kategori menu & Kalkulasi Popup Modal    -->
    <!-- ================================================================= -->
    <script>
        /**
         * Menyaring kartu menu berdasarkan kategori yang dipilih.
         * @param {string} category - Kategori yang dipilih ('all', 'Makanan', 'Minuman', 'Cemilan')
         * @param {HTMLElement} element - Elemen tombol yang sedang diklik
         */
        function filterCategory(category, element) {
            // 1. Reset tampilan semua tombol filter menjadi gaya default (putih)
            document.querySelectorAll('.btn-category').forEach(btn => {
                btn.className = "btn-category px-5 py-2 rounded-full font-semibold text-sm transition bg-white text-gray-600 hover:bg-gray-200 border";
            });

            // 2. Berikan gaya aktif (biru) pada tombol yang baru saja diklik
            element.className = "btn-category px-5 py-2 rounded-full font-semibold text-sm transition bg-blue-600 text-white shadow-md";

            // 3. Tampilkan atau sembunyikan setiap kartu menu sesuai kesesuaian kategori
            const cards = document.querySelectorAll('.food-card');
            cards.forEach(card => {
                const cardCategory = card.getAttribute('data-category');
                // Jika kategori 'all' atau cocok dengan data-category, tampilkan dengan flex; jika tidak, sembunyikan
                card.style.display = (category === 'all' || cardCategory === category) ? 'flex' : 'none';
            });
        }

        /**
         * Memvalidasi input dan menampilkan popup ringkasan pesanan sebelum dikirim.
         */
        function showConfirmationModal() {
            // 1. Ambil nilai nama dan nomor meja dari input form
            const name = document.getElementById('customer_name').value.trim();
            const table = document.getElementById('table_number').value.trim();

            // 2. Validasi: pastikan nama dan nomor meja tidak kosong
            if (!name || !table) {
                alert('Silakan isi Nama Lengkap dan Nomor Meja terlebih dahulu!');
                return;
            }

            // 3. Ambil semua input jumlah porsi makanan
            const items = document.querySelectorAll('.item-qty');
            let itemListHtml = '';
            let grandTotal = 0;
            let hasOrder = false;

            // 4. Looping untuk mencari menu yang kuantitasnya > 0
            items.forEach(input => {
                const qty = parseInt(input.value) || 0;
                if (qty > 0) {
                    hasOrder = true;
                    // Ambil nama menu dan harga dari atribut data-*
                    const itemName = input.getAttribute('data-name');
                    const price = parseFloat(input.getAttribute('data-price'));
                    const subtotal = qty * price;
                    grandTotal += subtotal;

                    // Susun elemen HTML daftar item untuk ditampilkan di dalam modal
                    itemListHtml += `<li class="flex justify-between items-center">
                        <div>
                            <span class="font-bold text-gray-800">${itemName}</span>
                            <span class="text-xs text-gray-500 block">x${qty} @ Rp ${price.toLocaleString('id-ID')}</span>
                        </div>
                        <span class="font-semibold text-gray-700">Rp ${subtotal.toLocaleString('id-ID')}</span>
                    </li>`;
                }
            });

            // 5. Validasi: pastikan pelanggan memilih minimal 1 menu makanan/minuman
            if (!hasOrder) {
                alert('Pilih minimal 1 menu makanan/minuman dengan jumlah lebih dari 0!');
                return;
            }

            // 6. Masukkan data hasil kalkulasi ke dalam elemen-elemen modal konfirmasi
            document.getElementById('modalName').textContent = name;
            document.getElementById('modalTable').textContent = table;
            document.getElementById('modalItemList').innerHTML = itemListHtml;
            document.getElementById('modalTotalPrice').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');

            // 7. Tampilkan modal dengan menghapus class 'hidden' dan menambahkan 'flex'
            const modal = document.getElementById('confirmModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        /**
         * Menutup popup modal konfirmasi pesanan jika pengguna menekan tombol 'Batal'.
         */
        function closeConfirmationModal() {
            const modal = document.getElementById('confirmModal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    </script>
</body>
</html>
