<div class="max-w-3xl mx-auto">

    {{-- ── Header ─────────────────────────────────────────────────────────── --}}
    <div class="mb-5 flex items-center gap-3">
        <a href="{{ route('admin.pengguna.peserta-didik') }}"
           class="w-9 h-9 flex items-center justify-center rounded-lg border cursor-pointer transition-colors"
           style="border-color: #c5c5d7; background: white; color: #505f76"
           onmouseover="this.style.background='#f0f4f8'" onmouseout="this.style.background='white'">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <div>
            <h1 class="text-[18px] font-bold" style="color: #171c1f">Import Data Peserta Didik</h1>
            <p class="text-[13px] mt-0.5" style="color: #757686">Tambah peserta didik secara massal dari file Excel.</p>
        </div>
    </div>

    {{-- ── Info Periode ─────────────────────────────────────────────────────── --}}
    @if ($periode)
        <div class="mb-4 flex items-center gap-2.5 rounded-lg px-4 py-3 border" style="background: #EEF2FF; border-color: #c5d0ff">
            <span class="material-symbols-outlined text-[18px]" style="color: #3c50e0">calendar_month</span>
            <p class="text-[13px]" style="color: #1c33c8">
                Data akan diimport ke Periode Ajaran:
                <strong>TA {{ $periode->tahun_ajaran }} — {{ ucfirst($periode->semester) }}</strong>.
                Rombel yang dipilih harus sesuai dengan Tahun Ajaran ini.
            </p>
        </div>
    @else
        <div class="mb-4 flex items-center gap-2.5 rounded-lg px-4 py-3 border" style="background: #ffdad6; border-color: #ba1a1a4d">
            <span class="material-symbols-outlined text-[18px]" style="color: #ba1a1a">warning</span>
            <p class="text-[13px]" style="color: #93000a">Tidak ada Periode Ajaran aktif. Atur periode aktif terlebih dahulu sebelum import.</p>
        </div>
    @endif

    @if (! $processed)

        {{-- ── Card: Template Excel ─────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border mb-4 p-5" style="border-color: #c5c5d7; box-shadow: 0 1px 3px rgba(0,0,0,0.05)">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-[14px] font-semibold" style="color: #171c1f">Template Excel</h2>
                    <p class="text-[13px] mt-0.5" style="color: #757686">Gunakan template resmi agar format kolom sesuai. Jangan ubah baris header.</p>
                </div>
                <button wire:click="downloadTemplate"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-[13px] font-semibold border flex-shrink-0 self-start sm:self-auto cursor-pointer transition-colors"
                        style="color: #3c50e0; border-color: #c5d0ff; background: #EEF2FF"
                        onmouseover="this.style.background='#dde5ff'" onmouseout="this.style.background='#EEF2FF'">
                    <span class="material-symbols-outlined text-[17px]">download</span>Unduh Template (.xlsx)
                </button>
            </div>

            {{-- Panduan kolom --}}
            <div class="mt-4 rounded-lg p-4 border" style="background: #f8f9fb; border-color: #c5c5d7">
                <p class="text-[12px] font-semibold mb-2" style="color: #505f76">Format kolom (A–Z):</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-1 text-[12px]" style="color: #505f76">
                    <span><b>A</b> — No (diabaikan)</span>
                    <span><b>B</b> — NIPD <span style="color: #ba1a1a">*</span></span>
                    <span><b>C</b> — NISN</span>
                    <span><b>D</b> — NIK</span>
                    <span><b>E</b> — Nama Lengkap <span style="color: #ba1a1a">*</span></span>
                    <span><b>F</b> — Jenis Kelamin (L/P) <span style="color: #ba1a1a">*</span></span>
                    <span><b>G</b> — Tempat Lahir</span>
                    <span><b>H</b> — Tanggal Lahir (YYYY-MM-DD)</span>
                    <span><b>I</b> — Agama</span>
                    <span><b>J</b> — No HP</span>
                    <span><b>K</b> — Email (tidak disimpan)</span>
                    <span><b>L</b> — Wilayah <span style="color: #ba1a1a">*</span></span>
                    <span><b>M</b> — Paket <span style="color: #ba1a1a">*</span></span>
                    <span><b>N</b> — Tingkat <span style="color: #ba1a1a">*</span></span>
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
                <p class="text-[11px] mt-2" style="color: #757686"><span style="color: #ba1a1a">*</span> wajib diisi. Header di baris 1, data mulai baris 2.</p>
            </div>
        </div>

        {{-- ── Card: Upload area ────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border mb-4 p-5" style="border-color: #c5c5d7; box-shadow: 0 1px 3px rgba(0,0,0,0.05)">
            <h2 class="text-[14px] font-semibold mb-3" style="color: #171c1f">Pilih File Excel</h2>

            <div x-data="{ dragging: false }">
                <label
                    @dragover.prevent="dragging = true"
                    @dragleave.prevent="dragging = false"
                    @drop.prevent="dragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                    class="flex flex-col items-center justify-center gap-2.5 py-10 px-4 text-center rounded-xl border-2 border-dashed cursor-pointer transition-colors"
                    :style="dragging ? 'border-color:#3c50e0; background:#EEF2FF' : 'border-color:#c5c5d7; background:#f6fafe'">
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background: #EEF2FF">
                        <span class="material-symbols-outlined text-[30px]" style="color: #3c50e0">upload_file</span>
                    </div>
                    <div>
                        <p class="text-[14px] font-semibold" style="color: #171c1f">Seret & lepas file di sini</p>
                        <p class="text-[13px] mt-0.5" style="color: #757686">atau <span style="color: #3c50e0; font-weight: 600">klik untuk memilih file</span></p>
                    </div>
                    <p class="text-[12px]" style="color: #9da4b0">.xlsx / .xls · maks. 5 MB</p>
                    <input x-ref="fileInput" type="file" wire:model="file" accept=".xlsx,.xls" class="hidden">
                </label>

                <div wire:loading wire:target="file" class="mt-3 flex items-center gap-2 text-[13px]" style="color: #757686">
                    <span class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                    Membaca & memvalidasi file...
                </div>

                @if ($file && $showPreview)
                    <div class="mt-3 flex items-center gap-3 p-3.5 rounded-lg border" style="border-color: #c5c5d7; background: #f6fafe">
                        <span class="material-symbols-outlined text-[22px] flex-shrink-0" style="color: #16a34a; font-variation-settings:'FILL' 1">check_circle</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-[13.5px] font-semibold truncate" style="color: #171c1f">{{ $file->getClientOriginalName() }}</p>
                            <p class="text-[12px]" style="color: #757686">{{ round($file->getSize() / 1024, 1) }} KB · {{ count($previewResults) }} baris data terdeteksi</p>
                        </div>
                        <button type="button" wire:click="cancelPreview"
                                class="w-7 h-7 flex items-center justify-center rounded-lg cursor-pointer flex-shrink-0 transition-colors" style="color: #757686"
                                onmouseover="this.style.background='#ffdad6'; this.style.color='#ba1a1a'" onmouseout="this.style.background='transparent'; this.style.color='#757686'">
                            <span class="material-symbols-outlined text-[17px]">close</span>
                        </button>
                    </div>
                @endif

                @error('file')
                    <p class="mt-2 text-[13px] flex items-center gap-1" style="color: #ba1a1a">
                        <span class="material-symbols-outlined text-[15px]">error</span>
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        {{-- ── Card: Pratinjau Data ─────────────────────────────────────── --}}
        @if ($showPreview)
            <div class="bg-white rounded-xl border overflow-hidden" style="border-color: #c5c5d7; box-shadow: 0 1px 3px rgba(0,0,0,0.05)">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 p-5 border-b" style="border-color: #c5c5d7">
                    <div>
                        <h2 class="text-[14px] font-semibold" style="color: #171c1f">Pratinjau Data</h2>
                        <p class="text-[13px] mt-0.5" style="color: #757686">Periksa data sebelum diimpor. Baris bermasalah ditandai merah dan akan dilewati.</p>
                    </div>
                    <div class="flex items-center gap-4 flex-shrink-0">
                        <span class="flex items-center gap-1.5 text-[12.5px] font-medium" style="color: #16a34a"><span class="w-2 h-2 rounded-full bg-green-500"></span>{{ $previewValid }} valid</span>
                        <span class="flex items-center gap-1.5 text-[12.5px] font-medium" style="color: #ba1a1a"><span class="w-2 h-2 rounded-full bg-red-500"></span>{{ $previewInvalid }} error</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left min-w-[500px]">
                        <thead>
                            <tr style="border-bottom: 1px solid #c5c5d7; background: white">
                                <th class="px-4 py-2.5 text-[11.5px] font-semibold uppercase tracking-wide w-12" style="color: #757686">Baris</th>
                                <th class="px-4 py-2.5 text-[11.5px] font-semibold uppercase tracking-wide" style="color: #757686">Nama Lengkap</th>
                                <th class="px-4 py-2.5 text-[11.5px] font-semibold uppercase tracking-wide" style="color: #757686">NIPD</th>
                                <th class="px-4 py-2.5 text-[11.5px] font-semibold uppercase tracking-wide" style="color: #757686">Rombel</th>
                                <th class="px-4 py-2.5 text-[11.5px] font-semibold uppercase tracking-wide" style="color: #757686">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#f0f4f8]">
                            @foreach ($previewResults as $r)
                                <tr @if ($r['status'] === 'gagal') style="background: #fff5f5" @endif>
                                    <td class="px-4 py-3 text-[12.5px]" style="color: {{ $r['status'] === 'gagal' ? '#ba1a1a' : '#9da4b0' }}">{{ $r['row'] }}</td>
                                    <td class="px-4 py-3 text-[13.5px] font-medium" style="color: {{ $r['status'] === 'gagal' ? '#ba1a1a' : '#171c1f' }}">{{ $r['nama'] ?: '—' }}</td>
                                    <td class="px-4 py-3 font-mono text-[12.5px]" style="color: {{ $r['status'] === 'gagal' ? '#ba1a1a' : '#505f76' }}">{{ $r['nipd'] ?: '— kosong —' }}</td>
                                    <td class="px-4 py-3 text-[13px]" style="color: {{ $r['status'] === 'gagal' ? '#ba1a1a' : '#505f76' }}">{{ $r['rombel'] ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        @if ($r['status'] === 'valid')
                                            <span class="flex items-center gap-1.5 text-[12px] font-medium" style="color: #16a34a">
                                                <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 1">check_circle</span>Valid
                                            </span>
                                        @else
                                            <span class="flex items-center gap-1.5 text-[12px] font-medium" style="color: #ba1a1a" title="{{ $r['alasan'] }}">
                                                <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 1">cancel</span>{{ $r['alasan'] }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-5 border-t" style="border-color: #c5c5d7">
                    @if ($previewInvalid > 0)
                        <div class="flex items-start gap-2.5 p-3.5 rounded-lg border mb-4" style="background: #fffbeb; border-color: #fde68a">
                            <span class="material-symbols-outlined text-[17px] flex-shrink-0 mt-0.5" style="color: #d97706; font-variation-settings:'FILL' 1">warning</span>
                            <p class="text-[12.5px] leading-relaxed" style="color: #92400e">Ditemukan <strong>{{ $previewInvalid }} baris bermasalah</strong> — baris tersebut akan <strong>dilewati</strong> saat import. Perbaiki file dan upload ulang jika ingin mengimpor semua data.</p>
                        </div>
                    @endif
                    <div class="flex flex-col sm:flex-row sm:justify-end gap-3">
                        <button type="button" wire:click="cancelPreview"
                                class="px-4 py-2.5 rounded-lg text-[13px] font-medium border cursor-pointer transition-colors" style="color: #505f76; border-color: #c5c5d7; background: white"
                                onmouseover="this.style.background='#f0f4f8'" onmouseout="this.style.background='white'">Batal</button>
                        <button wire:click="import"
                                wire:loading.attr="disabled"
                                wire:target="import"
                                {{ $previewValid === 0 ? 'disabled' : '' }}
                                class="flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg text-[13.5px] font-semibold text-white cursor-pointer transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                style="background: #3c50e0" onmouseover="this.style.background='#2e3eb0'" onmouseout="this.style.background='#3c50e0'">
                            <span wire:loading.remove wire:target="import" class="material-symbols-outlined text-[17px]">upload</span>
                            <span wire:loading wire:target="import" class="material-symbols-outlined text-[17px] animate-spin">progress_activity</span>
                            <span wire:loading.remove wire:target="import">Import {{ $previewValid }} Data Valid</span>
                            <span wire:loading wire:target="import">Memproses...</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

    @else
        {{-- ── State sukses ─────────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border p-8 text-center" style="border-color: #c5c5d7; box-shadow: 0 1px 3px rgba(0,0,0,0.05)">
            <div class="w-14 h-14 rounded-full flex items-center justify-center mb-3 mx-auto" style="background: #f0fdf4">
                <span class="material-symbols-outlined text-[32px]" style="color: #16a34a; font-variation-settings:'FILL' 1">check_circle</span>
            </div>
            <h3 class="text-[16px] font-bold mb-1" style="color: #171c1f">Import Berhasil!</h3>
            <p class="text-[13.5px]" style="color: #757686"><strong>{{ $totalBerhasil }} peserta didik</strong> berhasil ditambahkan ke sistem.
                @if ($totalGagal > 0)
                    <br>{{ $totalGagal }} baris dilewati karena tidak valid.
                @endif
            </p>
            <div class="flex justify-center gap-3 mt-5">
                <button wire:click="resetForm"
                        class="px-4 py-2.5 rounded-lg text-[13px] font-medium border cursor-pointer transition-colors" style="color: #505f76; border-color: #c5c5d7; background: white"
                        onmouseover="this.style.background='#f0f4f8'" onmouseout="this.style.background='white'">Import Lagi</button>
                <a href="{{ route('admin.pengguna.peserta-didik') }}"
                   class="px-5 py-2.5 rounded-lg text-[13.5px] font-semibold text-white transition-colors" style="background: #3c50e0"
                   onmouseover="this.style.background='#2e3eb0'" onmouseout="this.style.background='#3c50e0'">Lihat Daftar Akun</a>
            </div>
        </div>

        {{-- ── Detail hasil per baris ───────────────────────────────────── --}}
        <div class="bg-white rounded-xl border overflow-hidden mt-4" style="border-color: #c5c5d7; box-shadow: 0 1px 3px rgba(0,0,0,0.05)">
            <div class="px-5 py-4 border-b flex items-center justify-between" style="border-color: #c5c5d7">
                <h3 class="text-[14px] font-semibold" style="color: #171c1f">Detail Hasil per Baris</h3>
                @if ($totalGagal > 0)
                    <span class="text-[12px] font-medium" style="color: #ba1a1a">{{ $totalGagal }} baris perlu diperbaiki</span>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left min-w-[500px]">
                    <thead>
                        <tr style="border-bottom: 1px solid #c5c5d7; background: white">
                            <th class="px-4 py-2.5 text-[11.5px] font-semibold uppercase tracking-wide w-12" style="color: #757686">Baris</th>
                            <th class="px-4 py-2.5 text-[11.5px] font-semibold uppercase tracking-wide" style="color: #757686">Nama</th>
                            <th class="px-4 py-2.5 text-[11.5px] font-semibold uppercase tracking-wide" style="color: #757686">NIPD</th>
                            <th class="px-4 py-2.5 text-[11.5px] font-semibold uppercase tracking-wide" style="color: #757686">Rombel</th>
                            <th class="px-4 py-2.5 text-[11.5px] font-semibold uppercase tracking-wide" style="color: #757686">Status</th>
                            <th class="px-4 py-2.5 text-[11.5px] font-semibold uppercase tracking-wide" style="color: #757686">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#f0f4f8]">
                        @forelse ($results as $r)
                            <tr @if ($r['status'] === 'gagal') style="background: #fff5f5" @endif>
                                <td class="px-4 py-3 text-[12.5px]" style="color: #9da4b0">{{ $r['row'] }}</td>
                                <td class="px-4 py-3 text-[13.5px] font-medium" style="color: #171c1f">{{ $r['nama'] ?: '—' }}</td>
                                <td class="px-4 py-3 font-mono text-[12.5px]" style="color: #505f76">{{ $r['nipd'] ?: '—' }}</td>
                                <td class="px-4 py-3 text-[13px]" style="color: #505f76">{{ $r['rombel'] ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @if ($r['status'] === 'berhasil')
                                        <span class="flex items-center gap-1.5 text-[12px] font-medium" style="color: #16a34a">
                                            <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 1">check_circle</span>Berhasil
                                        </span>
                                    @else
                                        <span class="flex items-center gap-1.5 text-[12px] font-medium" style="color: #ba1a1a">
                                            <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 1">cancel</span>Gagal
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-[13px]" style="color: {{ $r['status'] === 'gagal' ? '#ba1a1a' : '#505f76' }}">
                                    {{ $r['status'] === 'berhasil' ? 'Import berhasil, akun dibuat.' : ($r['alasan'] ?? '—') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-[13px]" style="color: #757686">Tidak ada data yang diproses.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
