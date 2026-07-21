@extends('layouts.app')

@section('page-title', 'Edit Kelas / Ruangan')

@section('content')
    <div class="fade-in max-w-7xl mx-auto" x-data="classroomForm()">

        <!-- Header -->
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('classrooms.index') }}"
               class="p-2.5 hover:bg-slate-100 dark:hover:bg-slate-700/50 rounded-xl transition-all hover:-translate-x-1 group">
                <i data-lucide="arrow-left" class="w-5 h-5 text-slate-600 dark:text-slate-400 group-hover:text-navy-800 dark:group-hover:text-gold-400 transition-colors"></i>
            </a>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Edit Kelas / Ruangan</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $classroom->name }}</p>
            </div>
        </div>

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="card p-4 mb-6 bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 border border-red-200 dark:border-red-800">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0 text-red-600 dark:text-red-400">
                        <i data-lucide="alert-circle" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-red-800 dark:text-red-300">Terjadi Kesalahan Validasi</h4>
                        <ul class="list-disc list-inside text-xs text-red-700 dark:text-red-400 mt-1 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('classrooms.update', $classroom) }}" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @csrf
            @method('PUT')

            <!-- Left Column: Form Fields -->
            <div class="lg:col-span-2 space-y-6">
                <div class="card p-6">
                    <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                            <i data-lucide="edit-3" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-navy-800 dark:text-white">Informasi Kelas</h3>
                            <p class="text-xs text-slate-500">Sesuaikan informasi kelas/ruangan di bawah ini</p>
                        </div>
                    </div>

                    <!-- Tipe Kelas -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-3">
                            Tipe Kelas <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="type" value="regular" x-model="classType" class="peer sr-only">
                                <div class="p-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 peer-checked:border-navy-800 dark:peer-checked:border-gold-400 peer-checked:bg-navy-50 dark:peer-checked:bg-navy-900/20 transition-all hover:border-navy-300 dark:hover:border-gold-600">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                            <i data-lucide="school" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-navy-800 dark:text-white">Kelas Reguler</p>
                                            <p class="text-[10px] text-slate-500 dark:text-slate-400">Kelas dengan tingkat & jurusan</p>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <label class="relative cursor-pointer">
                                <input type="radio" name="type" value="shared" x-model="classType" class="peer sr-only">
                                <div class="p-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 peer-checked:border-navy-800 dark:peer-checked:border-gold-400 peer-checked:bg-navy-50 dark:peer-checked:bg-navy-900/20 transition-all hover:border-navy-300 dark:hover:border-gold-600">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                            <i data-lucide="building-2" class="w-5 h-5 text-purple-600 dark:text-purple-400"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-navy-800 dark:text-white">Ruangan Bersama</p>
                                            <p class="text-[10px] text-slate-500 dark:text-slate-400">Aula, Gor, Mushola, dll</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Nama Kelas/Ruangan -->
                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                            Nama Kelas / Ruangan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" x-model="className" required
                               :placeholder="classType === 'shared' ? 'Contoh: Aula, Gor, Mushola' : 'Contoh: XI FARMASI, XII RPL 1'"
                               class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600">
                        <p class="mt-2 text-xs text-slate-500 dark:text-slate-400" x-text="classType === 'shared' ? 'Nama ruangan bersama yang akan ditampilkan' : 'Nama lengkap kelas yang akan ditampilkan'"></p>
                    </div>

                    <!-- Fields untuk Kelas Reguler -->
                    <template x-if="classType === 'regular'">
                        <div class="space-y-5">
                            <!-- Jurusan -->
                            <div>
                                <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                    Jurusan / Kompetensi Keahlian <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="major" x-model="major"
                                       placeholder="CONTOH: RPL, TKJ, FAR, AKL"
                                       class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600">
                                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Kode jurusan (RPL, TKJ, Farmasi, Akuntansi, dll). Kode kelas akan otomatis digenerate.</p>
                            </div>

                            <!-- Tingkat Kelas -->
                            <div>
                                <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                    Tingkat Kelas <span class="text-red-500">*</span>
                                </label>
                                <div class="relative" x-data="customSelect('level', '{{ old('level', $classroom->level) }}', '{{ old('level', $classroom->level) === 'X' ? 'X (Sepuluh)' : (old('level', $classroom->level) === 'XI' ? 'XI (Sebelas)' : (old('level', $classroom->level) === 'XII' ? 'XII (Dua Belas)' : '')) }}')">
                                    <button type="button" @click="open = !open"
                                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600 flex items-center justify-between group">
                                        <div class="flex items-center gap-3">
                                            <i data-lucide="layers" class="w-4 h-4 text-slate-400"></i>
                                            <span class="text-slate-700 dark:text-slate-300" x-text="selectedLabel || 'Pilih Tingkat Kelas'"></span>
                                        </div>
                                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180': open}"></i>
                                    </button>

                                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                                         class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl overflow-hidden" x-cloak>
                                        <div class="p-1.5 space-y-1">
                                            <button type="button" @click="selectOption({value: 'X', label: 'X (Sepuluh)'}); open = false"
                                                    class="w-full px-3 py-2.5 rounded-lg text-left text-sm transition-all flex items-center gap-3 group hover:bg-navy-50 dark:hover:bg-navy-900/20 text-slate-700 dark:text-slate-300">
                                                <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                                    <span class="text-blue-600 dark:text-blue-400 font-bold text-xs">X</span>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="font-medium">X (Sepuluh)</p>
                                                    <p class="text-[10px] text-slate-500">Tingkat kelas 10</p>
                                                </div>
                                                <i data-lucide="check" class="w-4 h-4 text-navy-800 dark:text-gold-400" x-show="selectedValue === 'X'"></i>
                                            </button>
                                            <button type="button" @click="selectOption({value: 'XI', label: 'XI (Sebelas)'}); open = false"
                                                    class="w-full px-3 py-2.5 rounded-lg text-left text-sm transition-all flex items-center gap-3 group hover:bg-navy-50 dark:hover:bg-navy-900/20 text-slate-700 dark:text-slate-300">
                                                <div class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                                                    <span class="text-green-600 dark:text-green-400 font-bold text-xs">XI</span>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="font-medium">XI (Sebelas)</p>
                                                    <p class="text-[10px] text-slate-500">Tingkat kelas 11</p>
                                                </div>
                                                <i data-lucide="check" class="w-4 h-4 text-navy-800 dark:text-gold-400" x-show="selectedValue === 'XI'"></i>
                                            </button>
                                            <button type="button" @click="selectOption({value: 'XII', label: 'XII (Dua Belas)'}); open = false"
                                                    class="w-full px-3 py-2.5 rounded-lg text-left text-sm transition-all flex items-center gap-3 group hover:bg-navy-50 dark:hover:bg-navy-900/20 text-slate-700 dark:text-slate-300">
                                                <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center flex-shrink-0">
                                                    <span class="text-purple-600 dark:text-purple-400 font-bold text-xs">XII</span>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="font-medium">XII (Dua Belas)</p>
                                                    <p class="text-[10px] text-slate-500">Tingkat kelas 12</p>
                                                </div>
                                                <i data-lucide="check" class="w-4 h-4 text-navy-800 dark:text-gold-400" x-show="selectedValue === 'XII'"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <input type="hidden" name="level" :value="selectedValue" required>
                                </div>
                                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Pilih tingkat kelas untuk pengelompokan</p>
                            </div>

                            <!-- Kode Kelas (Otomatis) -->
                            <div>
                                <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                    Kode Kelas <span class="text-xs text-slate-400 font-normal">(Otomatis)</span>
                                </label>
                                <input type="text" name="code" :value="generatedCode" readonly
                                       class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm font-mono font-bold text-navy-800 dark:text-gold-400 cursor-not-allowed">
                                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Kode kelas digenerate dari Tingkat + Jurusan (contoh: X-RPL, XI-TKJ)</p>
                            </div>
                        </div>
                    </template>

                    <!-- Fields untuk Ruangan Bersama -->
                    <template x-if="classType === 'shared'">
                        <div class="space-y-5">
                            <!-- Tipe Ruangan -->
                            <div>
                                <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                    Tipe Ruangan <span class="text-red-500">*</span>
                                </label>
                                <div class="relative" x-data="customSelect('location_type', '{{ old('location_type', $classroom->location_type) }}', '{{ old('location_type', $classroom->location_type) === 'auditorium' ? 'Aula / Auditorium' : (old('location_type', $classroom->location_type) === 'gym' ? 'Gor / Lapangan Olahraga' : (old('location_type', $classroom->location_type) === 'prayer_room' ? 'Mushola / Masjid' : (old('location_type', $classroom->location_type) === 'laboratory' ? 'Laboratorium' : (old('location_type', $classroom->location_type) === 'library' ? 'Perpustakaan' : (old('location_type', $classroom->location_type) === 'other' ? 'Lainnya' : ''))))) }}')">
                                    <button type="button" @click="open = !open"
                                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600 flex items-center justify-between group">
                                        <div class="flex items-center gap-3">
                                            <i data-lucide="building-2" class="w-4 h-4 text-slate-400"></i>
                                            <span class="text-slate-700 dark:text-slate-300" x-text="selectedLabel || 'Pilih tipe ruangan...'"></span>
                                        </div>
                                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180': open}"></i>
                                    </button>

                                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                                         class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl overflow-hidden" x-cloak>
                                        <div class="p-1.5 space-y-1">
                                            <button type="button" @click="selectOption({value: 'auditorium', label: 'Aula / Auditorium'}); open = false"
                                                    class="w-full px-3 py-2.5 rounded-lg text-left text-sm transition-all flex items-center gap-3 group hover:bg-purple-50 dark:hover:bg-purple-900/20 text-slate-700 dark:text-slate-300">
                                                <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center flex-shrink-0">
                                                    <i data-lucide="presentation" class="w-4 h-4 text-purple-600 dark:text-purple-400"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="font-medium">Aula / Auditorium</p>
                                                    <p class="text-[10px] text-slate-500">Ruangan serbaguna</p>
                                                </div>
                                                <i data-lucide="check" class="w-4 h-4 text-navy-800 dark:text-gold-400" x-show="selectedValue === 'auditorium'"></i>
                                            </button>
                                            <button type="button" @click="selectOption({value: 'gym', label: 'Gor / Lapangan Olahraga'}); open = false"
                                                    class="w-full px-3 py-2.5 rounded-lg text-left text-sm transition-all flex items-center gap-3 group hover:bg-purple-50 dark:hover:bg-purple-900/20 text-slate-700 dark:text-slate-300">
                                                <div class="w-8 h-8 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center flex-shrink-0">
                                                    <i data-lucide="dumbbell" class="w-4 h-4 text-orange-600 dark:text-orange-400"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="font-medium">Gor / Lapangan Olahraga</p>
                                                    <p class="text-[10px] text-slate-500">Ruangan olahraga</p>
                                                </div>
                                                <i data-lucide="check" class="w-4 h-4 text-navy-800 dark:text-gold-400" x-show="selectedValue === 'gym'"></i>
                                            </button>
                                            <button type="button" @click="selectOption({value: 'prayer_room', label: 'Mushola / Masjid'}); open = false"
                                                    class="w-full px-3 py-2.5 rounded-lg text-left text-sm transition-all flex items-center gap-3 group hover:bg-purple-50 dark:hover:bg-purple-900/20 text-slate-700 dark:text-slate-300">
                                                <div class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                                                    <i data-lucide="heart" class="w-4 h-4 text-green-600 dark:text-green-400"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="font-medium">Mushola / Masjid</p>
                                                    <p class="text-[10px] text-slate-500">Ruangan ibadah</p>
                                                </div>
                                                <i data-lucide="check" class="w-4 h-4 text-navy-800 dark:text-gold-400" x-show="selectedValue === 'prayer_room'"></i>
                                            </button>
                                            <button type="button" @click="selectOption({value: 'laboratory', label: 'Laboratorium'}); open = false"
                                                    class="w-full px-3 py-2.5 rounded-lg text-left text-sm transition-all flex items-center gap-3 group hover:bg-purple-50 dark:hover:bg-purple-900/20 text-slate-700 dark:text-slate-300">
                                                <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                                    <i data-lucide="flask-conical" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="font-medium">Laboratorium</p>
                                                    <p class="text-[10px] text-slate-500">Ruangan praktikum</p>
                                                </div>
                                                <i data-lucide="check" class="w-4 h-4 text-navy-800 dark:text-gold-400" x-show="selectedValue === 'laboratory'"></i>
                                            </button>
                                            <button type="button" @click="selectOption({value: 'library', label: 'Perpustakaan'}); open = false"
                                                    class="w-full px-3 py-2.5 rounded-lg text-left text-sm transition-all flex items-center gap-3 group hover:bg-purple-50 dark:hover:bg-purple-900/20 text-slate-700 dark:text-slate-300">
                                                <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center flex-shrink-0">
                                                    <i data-lucide="book-open" class="w-4 h-4 text-indigo-600 dark:text-indigo-400"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="font-medium">Perpustakaan</p>
                                                    <p class="text-[10px] text-slate-500">Ruangan baca</p>
                                                </div>
                                                <i data-lucide="check" class="w-4 h-4 text-navy-800 dark:text-gold-400" x-show="selectedValue === 'library'"></i>
                                            </button>
                                            <button type="button" @click="selectOption({value: 'other', label: 'Lainnya'}); open = false"
                                                    class="w-full px-3 py-2.5 rounded-lg text-left text-sm transition-all flex items-center gap-3 group hover:bg-purple-50 dark:hover:bg-purple-900/20 text-slate-700 dark:text-slate-300">
                                                <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center flex-shrink-0">
                                                    <i data-lucide="more-horizontal" class="w-4 h-4 text-slate-600 dark:text-slate-400"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="font-medium">Lainnya</p>
                                                    <p class="text-[10px] text-slate-500">Ruangan lain</p>
                                                </div>
                                                <i data-lucide="check" class="w-4 h-4 text-navy-800 dark:text-gold-400" x-show="selectedValue === 'other'"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <input type="hidden" name="location_type" :value="selectedValue" required>
                                </div>
                                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Pilih tipe ruangan untuk identifikasi</p>
                            </div>

                            <!-- Tingkat Kelas -->
                            <div>
                                <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                    Tingkat Kelas <span class="text-red-500">*</span>
                                </label>
                                <div class="relative" x-data="customSelect('level', '{{ old('level', $classroom->level) }}', '{{ old('level', $classroom->level) === 'X' ? 'X (Sepuluh)' : (old('level', $classroom->level) === 'XI' ? 'XI (Sebelas)' : (old('level', $classroom->level) === 'XII' ? 'XII (Dua Belas)' : '')) }}')">
                                    <button type="button" @click="open = !open"
                                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600 flex items-center justify-between group">
                                        <div class="flex items-center gap-3">
                                            <i data-lucide="layers" class="w-4 h-4 text-slate-400"></i>
                                            <span class="text-slate-700 dark:text-slate-300" x-text="selectedLabel || 'Pilih Tingkat Kelas'"></span>
                                        </div>
                                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180': open}"></i>
                                    </button>

                                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                                         class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl overflow-hidden" x-cloak>
                                        <div class="p-1.5 space-y-1">
                                            <button type="button" @click="selectOption({value: 'X', label: 'X (Sepuluh)'}); open = false"
                                                    class="w-full px-3 py-2.5 rounded-lg text-left text-sm transition-all flex items-center gap-3 group hover:bg-navy-50 dark:hover:bg-navy-900/20 text-slate-700 dark:text-slate-300">
                                                <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                                    <span class="text-blue-600 dark:text-blue-400 font-bold text-xs">X</span>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="font-medium">X (Sepuluh)</p>
                                                    <p class="text-[10px] text-slate-500">Tingkat kelas 10</p>
                                                </div>
                                                <i data-lucide="check" class="w-4 h-4 text-navy-800 dark:text-gold-400" x-show="selectedValue === 'X'"></i>
                                            </button>
                                            <button type="button" @click="selectOption({value: 'XI', label: 'XI (Sebelas)'}); open = false"
                                                    class="w-full px-3 py-2.5 rounded-lg text-left text-sm transition-all flex items-center gap-3 group hover:bg-navy-50 dark:hover:bg-navy-900/20 text-slate-700 dark:text-slate-300">
                                                <div class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                                                    <span class="text-green-600 dark:text-green-400 font-bold text-xs">XI</span>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="font-medium">XI (Sebelas)</p>
                                                    <p class="text-[10px] text-slate-500">Tingkat kelas 11</p>
                                                </div>
                                                <i data-lucide="check" class="w-4 h-4 text-navy-800 dark:text-gold-400" x-show="selectedValue === 'XI'"></i>
                                            </button>
                                            <button type="button" @click="selectOption({value: 'XII', label: 'XII (Dua Belas)'}); open = false"
                                                    class="w-full px-3 py-2.5 rounded-lg text-left text-sm transition-all flex items-center gap-3 group hover:bg-navy-50 dark:hover:bg-navy-900/20 text-slate-700 dark:text-slate-300">
                                                <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center flex-shrink-0">
                                                    <span class="text-purple-600 dark:text-purple-400 font-bold text-xs">XII</span>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="font-medium">XII (Dua Belas)</p>
                                                    <p class="text-[10px] text-slate-500">Tingkat kelas 12</p>
                                                </div>
                                                <i data-lucide="check" class="w-4 h-4 text-navy-800 dark:text-gold-400" x-show="selectedValue === 'XII'"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <input type="hidden" name="level" :value="selectedValue" required>
                                </div>
                                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Pilih tingkat kelas untuk pengelompokan</p>
                            </div>

                            <!-- Kode Ruangan (Auto-generate) -->
                            <div>
                                <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                    Kode Ruangan <span class="text-xs text-slate-400 font-normal">(Otomatis)</span>
                                </label>
                                <input type="text" name="code" :value="sharedCode" readonly
                                       class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm font-mono font-bold text-navy-800 dark:text-gold-400 cursor-not-allowed">
                                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Kode digenerate otomatis dari nama ruangan</p>
                            </div>
                        </div>
                    </template>

                    <!-- Deskripsi -->
                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                            Deskripsi <span class="text-xs text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <div class="relative group">
                            <i data-lucide="message-square" class="absolute left-4 top-4 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                            <textarea name="description" rows="3" placeholder="Deskripsi singkat tentang kelas/ruangan..."
                                      class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all resize-none hover:border-navy-300 dark:hover:border-gold-600">{{ old('description', $classroom->description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Status & Info -->
            <div class="space-y-6">
                <!-- Status Kelas -->
                <div class="card p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-green-500/30">
                            <i data-lucide="toggle-right" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-navy-800 dark:text-white">Status Kelas</h3>
                            <p class="text-xs text-slate-500">Aktifkan atau nonaktifkan</p>
                        </div>
                    </div>

                    <label class="flex items-start gap-3 p-4 bg-slate-50 dark:bg-slate-750 border border-slate-200 dark:border-slate-600 rounded-xl cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-all">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $classroom->is_active) ? 'checked' : '' }}
                               class="w-5 h-5 rounded border-slate-300 text-navy-800 focus:ring-navy-800 dark:focus:ring-gold-500 transition-all mt-0.5">
                        <div>
                            <span class="block text-sm font-bold text-navy-800 dark:text-white">Kelas Aktif</span>
                            <span class="block text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                                Kelas yang tidak aktif tidak akan muncul dalam jadwal dan tidak bisa melakukan absensi
                            </span>
                        </div>
                    </label>
                </div>

                <!-- Informasi Tambahan -->
                <div class="card p-6 border-l-4 border-blue-500 bg-blue-50/50 dark:bg-blue-950/20">
                    <div class="flex gap-3">
                        <i data-lucide="info" class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5"></i>
                        <div>
                            <h4 class="text-sm font-bold text-blue-800 dark:text-blue-300 mb-2">Informasi Penting</h4>
                            <ul class="text-xs text-blue-700 dark:text-blue-400/90 space-y-2 leading-relaxed list-disc pl-4">
                                <li><strong>Nama Kelas:</strong> Pastikan penamaan jelas untuk memudahkan guru.</li>
                                <li><strong>Kode:</strong> Kode digenerate secara unik untuk setiap kelas.</li>
                                <li><strong>QR Code:</strong> QR Code lama akan tetap terhubung meskipun Anda mengubah nama.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="col-span-1 lg:col-span-3 flex gap-4 pt-4">
                <a href="{{ route('classrooms.index') }}"
                   class="flex-1 px-6 py-3.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold transition-all text-center flex items-center justify-center gap-2">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    Batal
                </a>
                <button type="submit"
                        class="flex-1 px-6 py-3.5 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 hover:from-navy-900 hover:to-slate-900 dark:hover:from-gold-500 dark:hover:to-gold-600 text-white dark:text-navy-900 rounded-xl text-sm font-bold transition-all shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30 hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <script>
        function classroomForm() {
            return {
                classType: '{{ old('type', $classroom->type) }}',
                className: '{{ old('name', $classroom->name) }}',
                major: '{{ old('major', $classroom->major) }}',
                level: '{{ old('level', $classroom->level) }}',
                locationType: '{{ old('location_type', $classroom->location_type) }}',
                
                init() {
                    this.$watch('classType', () => {
                        this.$nextTick(() => {
                            if (window.lucide) lucide.createIcons();
                        });
                    });
                },

                get generatedCode() {
                    if (this.level && this.major) {
                        return `${this.level}-${this.major.toUpperCase()}`;
                    }
                    return 'Akan digenerate otomatis...';
                },

                get sharedCode() {
                    if (this.className) {
                        let base = this.className.toUpperCase().replace(/\s+/g, '_');
                        if (this.level) {
                            return `${this.level}-${base}`;
                        }
                        return base;
                    }
                    return 'Akan digenerate otomatis...';
                }
            };
        }

        function customSelect(name, defaultValue, defaultLabel) {
            return {
                open: false,
                selectedValue: defaultValue || '',
                selectedLabel: defaultLabel || '',

                selectOption(option) {
                    this.selectedValue = option.value;
                    this.selectedLabel = option.label;
                    this.open = false;
                    
                    // Update parent scope properties
                    if (name === 'level') {
                        this.$parent.level = option.value;
                    } else if (name === 'location_type') {
                        this.$parent.locationType = option.value;
                    }
                }
            };
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) lucide.createIcons();
        });
    </script>

    <style>
        .fade-in { animation: fadeIn 0.5s ease-out forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        [x-cloak] { display: none !important; }
    </style>
@endsection