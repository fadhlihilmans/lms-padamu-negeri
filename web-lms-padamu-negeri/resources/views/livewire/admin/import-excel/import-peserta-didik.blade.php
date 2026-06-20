<div>

    {{-- ── Header ─────────────────────────────────────────────────────────── --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-[24px] font-bold tracking-tight text-on-surface">Import Peserta Didik</h2>
            <p class="text-[14px] text-[#505f76] mt-0.5">
                Import massal data peserta didik dari file Excel (.xlsx).
            </p>
        </div>
        <button wire:click="downloadTemplate"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-[#3c50e0] text-[#3c50e0] text-[14px] font-medium hover:bg-[#EEF2FF] transition-colors cursor-pointer">
            <span class="material-symbols-outlined text-[18px]">download</span>
            Unduh Template
        </button>
    </div>

    {{-- ── Info Periode ─────────────────────────────────────────────────────── --}}
    @if ($periode)
        <div class="mb-5 flex items-center gap-2.5 bg-[#EEF2FF] border border-[#c5d0ff] rounded-lg px-4 py-3">
            <span class="material-symbols-outlined text-[18px] text-[#3c50e0]">calendar_month</span>
            <p class="text-[14px] text-[#1c33c8]">
                Data akan diimport ke Periode Ajaran:
                <strong>TA {{ $periode->tahun_ajaran }} — {{ ucfirst($periode->semester) }}</strong>.
                Rombel yang dipilih harus sesuai dengan Tahun Ajaran ini.
            </p>
        </div>
    @else
        <div class="mb-5 flex items-center gap-2.5 bg-[#ffdad6] border border-[#ba1a1a]/30 rounded-lg px-4 py-3">
            <span class="material-symbols-outlined text-[18px] text-[#ba1a1a]">warning</span>
            <p class="text-[14px] text-[#93000a]">Tidak ada Periode Ajaran aktif. Atur periode aktif terlebih dahulu sebelum import.</p>
        </div>
    @endif

    @if (! $processed)
        {{-- ── Form Upload ──────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-6">
            <h3 class="text-[16px] font-semibold text-on-surface mb-4">Upload File Excel</h3>

            {{-- Panduan kolom --}}
            <div class="mb-5 bg-[#f8f9fb] border border-[#c5c5d7] rounded-lg p-4">
                <p class="text-[13px] font-semibold text-[#505f76] mb-2">Format kolom (A–Z):</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-1 text-[12px] text-[#505f76]">
                    <span><b>A</b> — No (diabaikan)</span>
                    <span><b>B</b> — NIPD <span class="text-[#ba1a1a]">*</span></span>
                    <span><b>C</b> — NISN</span>
                    <span><b>D</b> — NIK</span>
                    <span><b>E</b> — Nama Lengkap <span class="text-[#ba1a1a]">*</span></span>
                    <span><b>F</b> — Jenis Kelamin (L/P) <span class="text-[#ba1a1a]">*</span></span>
                    <span><b>G</b> — Tempat Lahir</span>
                    <span><b>H</b> — Tanggal Lahir (YYYY-MM-DD)</span>
                    <span><b>I</b> — Agama</span>
                    <span><b>J</b> — No HP</span>
                    <span><b>K</b> — Email (tidak disimpan)</span>
                    <span><b>L</b> — Wilayah <span class="text-[#ba1a1a]">*</span></span>
                    <span><b>M</b> — Paket <span class="text-[#ba1a1a]">*</span></span>
                    <span><b>N</b> — Tingkat <span class="text-[#ba1a1a]">*</span></span>
                    <span><b>O</b> — Alamat</span>
                    <span><b>P</b> — RT</span>
                    <span><b>Q</b> — RW</span>
                    <span><b>R</b> — Dusun</span>
                    <span><b>S</b> — Kelurahan</span>
                    <span><b>T</b> — Kecamatan</span>
                    <span><b>U</b> — Kode Pos</span>
                    <span><b>V</b> — Nama Ayah</span>
                    <span><b>W</b> — No HP Ayah</span>
                    <span><b>X</b> — Nama Ibu</span>
                    <span><b>Y</b> — No HP Ibu</span>
                    <span><b>Z</b> — Nama Wali</span>
                </div>
                <p class="text-[11px] text-[#757686] mt-2"><span class="text-[#ba1a1a]">*</span> wajib diisi. Header di baris 1, data mulai baris 2.</p>
            </div>

            {{-- Input file --}}
            <div x-data="{ dragging: false }" class="mb-5">
                <label
                    @dragover.prevent="dragging = true"
                    @dragleave.prevent="dragging = false"
                    @drop.prevent="dragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                    :class="dragging ? 'border-[#3c50e0] bg-[#EEF2FF]' : 'border-[#c5c5d7] bg-[#f8f9fb] hover:bg-[#f0f4f8]'"
                    class="flex flex-col items-center justify-center gap-2 border-2 border-dashed rounded-xl p-8 text-center cursor-pointer transition-colors">
                    <span class="material-symbols-outlined text-[40px] text-[#3c50e0]">upload_file</span>
                    <p class="text-[14px] font-medium text-on-surface">
                        Drag & drop file ke sini atau <span class="text-[#3c50e0] underline">klik untuk pilih</span>
                    </p>
                    <p class="text-[12px] text-[#757686]">Format: .xlsx atau .xls · Maks. 5 MB</p>
                    <input x-ref="fileInput" type="file" wire:model="file" accept=".xlsx,.xls" class="hidden">
                </label>

                @if ($file)
                    <div class="mt-3 flex items-center gap-2 px-3 py-2 bg-green-50 border border-green-200 rounded-lg text-[13px] text-green-800">
                        <span class="material-symbols-outlined text-[16px] text-green-600">check_circle</span>
                        <span class="font-medium">{{ $file->getClientOriginalName() }}</span>
                        <span class="text-green-600 ml-auto">{{ round($file->getSize() / 1024, 1) }} KB</span>
                    </div>
                @endif

                @error('file')
                    <p class="mt-2 text-[13px] text-[#ba1a1a] flex items-center gap-1">
                        <span class="material-symbols-outlined text-[15px]">error</span>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="flex justify-end">
                <button wire:click="import"
                        wire:loading.attr="disabled"
                        wire:target="import,file"
                        {{ ! $file ? 'disabled' : '' }}
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-[#3c50e0] text-white text-[14px] font-medium hover:bg-[#1c33c8] transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="import" class="material-symbols-outlined text-[18px]">upload</span>
                    <span wire:loading wire:target="import" class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span>
                    <span wire:loading.remove wire:target="import">Proses Import</span>
                    <span wire:loading wire:target="import">Memproses...</span>
                </button>
            </div>
        </div>

    @else
        {{-- ── Hasil Import ─────────────────────────────────────────────────── --}}

        {{-- Ringkasan --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-[#f0f4f8] flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-[24px] text-[#505f76]">list_alt</span>
                </div>
                <div>
                    <p class="text-[12px] text-[#757686] uppercase tracking-wide">Total Baris</p>
                    <p class="text-[28px] font-bold text-on-surface leading-tight">{{ count($results) }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-green-200 shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-[24px] text-green-600" style="font-variation-settings:'FILL' 1;">check_circle</span>
                </div>
                <div>
                    <p class="text-[12px] text-green-700 uppercase tracking-wide">Berhasil</p>
                    <p class="text-[28px] font-bold text-green-800 leading-tight">{{ $totalBerhasil }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl border {{ $totalGagal > 0 ? 'border-[#ba1a1a]/30' : 'border-[#c5c5d7]' }} shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full {{ $totalGagal > 0 ? 'bg-[#ffdad6]' : 'bg-[#f0f4f8]' }} flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-[24px] {{ $totalGagal > 0 ? 'text-[#ba1a1a]' : 'text-[#757686]' }}" style="font-variation-settings:'FILL' 1;">
                        {{ $totalGagal > 0 ? 'cancel' : 'check_circle' }}
                    </span>
                </div>
                <div>
                    <p class="text-[12px] {{ $totalGagal > 0 ? 'text-[#93000a]' : 'text-[#757686]' }} uppercase tracking-wide">Gagal</p>
                    <p class="text-[28px] font-bold {{ $totalGagal > 0 ? 'text-[#93000a]' : 'text-on-surface' }} leading-tight">{{ $totalGagal }}</p>
                </div>
            </div>
        </div>

        {{-- Detail baris --}}
        <div class="bg-white rounded-xl border border-[#c5c5d7] shadow-sm overflow-hidden mb-5">
            <div class="px-5 py-4 border-b border-[#c5c5d7] flex items-center justify-between">
                <h3 class="text-[15px] font-semibold text-on-surface">Detail Hasil per Baris</h3>
                @if ($totalGagal > 0)
                    <span class="text-[12px] text-[#ba1a1a] font-medium">{{ $totalGagal }} baris perlu diperbaiki</span>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-[14px]">
                    <thead class="bg-[#f8f9fb] border-b border-[#c5c5d7]">
                        <tr>
                            <th class="px-4 py-3 text-left text-[12px] font-semibold text-[#505f76] uppercase tracking-wide w-16">Baris</th>
                            <th class="px-4 py-3 text-left text-[12px] font-semibold text-[#505f76] uppercase tracking-wide w-24">Status</th>
                            <th class="px-4 py-3 text-left text-[12px] font-semibold text-[#505f76] uppercase tracking-wide">NIPD</th>
                            <th class="px-4 py-3 text-left text-[12px] font-semibold text-[#505f76] uppercase tracking-wide">Nama</th>
                            <th class="px-4 py-3 text-left text-[12px] font-semibold text-[#505f76] uppercase tracking-wide">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e8e8f0]">
                        @forelse ($results as $r)
                            <tr class="{{ $r['status'] === 'berhasil' ? 'bg-white' : 'bg-[#fff8f7]' }}">
                                <td class="px-4 py-3 font-mono text-[13px] text-[#505f76]">{{ $r['row'] }}</td>
                                <td class="px-4 py-3">
                                    @if ($r['status'] === 'berhasil')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-green-100 text-green-800">
                                            <span class="material-symbols-outlined text-[12px]" style="font-variation-settings:'FILL' 1;">check_circle</span>
                                            Berhasil
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-[#ffdad6] text-[#93000a]">
                                            <span class="material-symbols-outlined text-[12px]" style="font-variation-settings:'FILL' 1;">cancel</span>
                                            Gagal
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-mono text-[13px]">{{ $r['nipd'] ?: '—' }}</td>
                                <td class="px-4 py-3 text-[14px]">{{ $r['nama'] ?: '—' }}</td>
                                <td class="px-4 py-3 text-[13px] {{ $r['status'] === 'gagal' ? 'text-[#ba1a1a]' : 'text-[#505f76]' }}">
                                    {{ $r['status'] === 'berhasil' ? 'Import berhasil, akun dibuat.' : ($r['alasan'] ?? '—') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-[14px] text-[#757686]">Tidak ada data yang diproses.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-end">
            <button wire:click="resetForm" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-[#c5c5d7] text-[14px] text-[#505f76] hover:bg-[#f0f4f8] transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">upload_file</span>
                Import File Baru
            </button>
        </div>
    @endif

</div>
