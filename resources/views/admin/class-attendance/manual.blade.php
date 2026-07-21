@extends('layouts.app')

@section('page-title', 'Manual Presensi Kelas')

@section('content')
<div class="fade-in space-y-6">

    <!-- Header -->
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
            <i data-lucide="edit-3" class="w-6 h-6 text-white dark:text-navy-900"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Manual Presensi Kelas</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Tambah presensi kelas secara manual</p>
        </div>
    </div>

    <!-- Flash notification ditangani oleh layout app.blade.php -->

    <!-- Form Tambah Manual -->
    <div class="card p-6">
        <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                <i data-lucide="plus-circle" class="w-5 h-5 text-white"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-navy-800 dark:text-white">Tambah Presensi Manual</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Isi form di bawah untuk menambahkan presensi</p>
            </div>
        </div>
        
        <form method="POST" action="{{ route('admin.class-attendance.manual.store') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Guru -->
                <div>
                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                        Guru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative" x-data="customSelect('user_id', {{ $teachers->map(fn($t) => ['value' => (string)$t->id, 'label' => $t->name])->values()->toJson() }})">
                        <button type="button" @click="open = !open"
                                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600 flex items-center justify-between group">
                            <div class="flex items-center gap-3">
                                <i data-lucide="user" class="w-4 h-4 text-slate-400"></i>
                                <span class="text-slate-700 dark:text-slate-300" x-text="selectedLabel || 'Pilih guru...'"></span>
                            </div>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180': open}"></i>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                             class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl overflow-hidden" x-cloak>
                            <div class="p-2 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50">
                                <div class="relative">
                                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"></i>
                                    <input type="text" x-model="search" placeholder="Cari guru..." class="w-full pl-9 pr-3 py-2 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                                </div>
                            </div>
                            <div class="max-h-60 overflow-y-auto p-1.5 space-y-1">
                                <template x-for="option in filteredOptions" :key="option.value">
                                    <button type="button" @click="selectOption(option); open = false"
                                            class="w-full px-3 py-2.5 rounded-lg text-left text-sm transition-all flex items-center gap-3 group"
                                            :class="selectedValue === option.value ? 'bg-navy-50 dark:bg-navy-900/30 text-navy-700 dark:text-navy-300' : 'hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300'">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 flex items-center justify-center flex-shrink-0">
                                            <span class="text-white dark:text-navy-900 text-xs font-bold" x-text="option.label.charAt(0)"></span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium truncate" x-text="option.label"></p>
                                        </div>
                                        <i data-lucide="check" class="w-4 h-4 text-navy-800 dark:text-gold-400" x-show="selectedValue === option.value"></i>
                                    </button>
                                </template>
                                <div x-show="filteredOptions.length === 0" class="p-4 text-center text-xs text-slate-500 dark:text-slate-400">
                                    Tidak ada guru ditemukan
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="user_id" :value="selectedValue" required>
                    </div>
                    @error('user_id')
                    <p class="mt-2 text-xs text-red-500 flex items-center gap-1">
                        <i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- Kelas -->
                <div>
                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                        Kelas <span class="text-red-500">*</span>
                    </label>
                    <div class="relative" x-data="customSelect('classroom_id', {{ $classrooms->map(fn($c) => ['value' => (string)$c->id, 'label' => $c->name])->values()->toJson() }})">
                        <button type="button" @click="open = !open"
                                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600 flex items-center justify-between group">
                            <div class="flex items-center gap-3">
                                <i data-lucide="school" class="w-4 h-4 text-slate-400"></i>
                                <span class="text-slate-700 dark:text-slate-300" x-text="selectedLabel || 'Pilih kelas...'"></span>
                            </div>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180': open}"></i>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                             class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl overflow-hidden" x-cloak>
                            <div class="p-2 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50">
                                <div class="relative">
                                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"></i>
                                    <input type="text" x-model="search" placeholder="Cari kelas..." class="w-full pl-9 pr-3 py-2 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                                </div>
                            </div>
                            <div class="max-h-60 overflow-y-auto p-1.5 space-y-1">
                                <template x-for="option in filteredOptions" :key="option.value">
                                    <button type="button" @click="selectOption(option); open = false"
                                            class="w-full px-3 py-2.5 rounded-lg text-left text-sm transition-all flex items-center gap-3 group"
                                            :class="selectedValue === option.value ? 'bg-navy-50 dark:bg-navy-900/30 text-navy-700 dark:text-navy-300' : 'hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300'">
                                        <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                            <i data-lucide="school" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium truncate" x-text="option.label"></p>
                                        </div>
                                        <i data-lucide="check" class="w-4 h-4 text-navy-800 dark:text-gold-400" x-show="selectedValue === option.value"></i>
                                    </button>
                                </template>
                                <div x-show="filteredOptions.length === 0" class="p-4 text-center text-xs text-slate-500 dark:text-slate-400">
                                    Tidak ada kelas ditemukan
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="classroom_id" :value="selectedValue" required>
                    </div>
                    @error('classroom_id')
                    <p class="mt-2 text-xs text-red-500 flex items-center gap-1">
                        <i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- Tanggal -->
                <div>
                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                        Tanggal <span class="text-red-500">*</span>
                    </label>
                    <div class="relative group">
                        <i data-lucide="calendar" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                        <input type="date" name="date" required value="{{ old('date', now()->format('Y-m-d')) }}"
                               class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600">
                    </div>
                    @error('date')
                    <p class="mt-2 text-xs text-red-500 flex items-center gap-1">
                        <i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- Jam Ke -->
                <div>
                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                        Jam Ke- <span class="text-red-500">*</span>
                    </label>
                    <div class="relative group">
                        <i data-lucide="hash" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                        <input type="number" name="period" required min="1" max="12" value="{{ old('period') }}" placeholder="1-12"
                               class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600">
                    </div>
                    @error('period')
                    <p class="mt-2 text-xs text-red-500 flex items-center gap-1">
                        <i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- Jam Masuk -->
                <div>
                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                        Jam Masuk <span class="text-red-500">*</span>
                    </label>
                    <div class="relative group">
                        <i data-lucide="log-in" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-green-600 dark:group-focus-within:text-green-400 transition-colors"></i>
                        <input type="time" name="check_in_time" required value="{{ old('check_in_time') }}"
                               class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600">
                    </div>
                    @error('check_in_time')
                    <p class="mt-2 text-xs text-red-500 flex items-center gap-1">
                        <i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- Jam Keluar -->
                <div>
                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                        Jam Keluar <span class="text-xs text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <div class="relative group">
                        <i data-lucide="log-out" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-red-600 dark:group-focus-within:text-red-400 transition-colors"></i>
                        <input type="time" name="check_out_time" value="{{ old('check_out_time') }}"
                               class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600">
                    </div>
                    @error('check_out_time')
                    <p class="mt-2 text-xs text-red-500 flex items-center gap-1">
                        <i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <div class="relative" x-data="customSelect('status')">
                        <button type="button" @click="open = !open"
                                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600 flex items-center justify-between group">
                            <div class="flex items-center gap-3">
                                <i data-lucide="check-circle" class="w-4 h-4 text-slate-400"></i>
                                <span class="text-slate-700 dark:text-slate-300" x-text="selectedLabel || 'Pilih status...'"></span>
                            </div>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180': open}"></i>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                             class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl overflow-hidden" x-cloak>
                            <div class="p-1.5 space-y-1">
                                <button type="button" @click="selectOption({value: 'Hadir', label: 'Hadir'}); open = false"
                                        class="w-full px-3 py-2.5 rounded-lg text-left text-sm transition-all flex items-center gap-3 group hover:bg-green-50 dark:hover:bg-green-900/20 text-slate-700 dark:text-slate-300">
                                    <div class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="check" class="w-4 h-4 text-green-600 dark:text-green-400"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium">Hadir</p>
                                        <p class="text-[10px] text-slate-500">Tepat waktu</p>
                                    </div>
                                </button>
                                <button type="button" @click="selectOption({value: 'Terlambat', label: 'Terlambat'}); open = false"
                                        class="w-full px-3 py-2.5 rounded-lg text-left text-sm transition-all flex items-center gap-3 group hover:bg-yellow-50 dark:hover:bg-yellow-900/20 text-slate-700 dark:text-slate-300">
                                    <div class="w-8 h-8 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="clock" class="w-4 h-4 text-yellow-600 dark:text-yellow-400"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium">Terlambat</p>
                                        <p class="text-[10px] text-slate-500">Melebihi jam masuk</p>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <input type="hidden" name="status" :value="selectedValue" value="Hadir" required>
                    </div>
                    @error('status')
                    <p class="mt-2 text-xs text-red-500 flex items-center gap-1">
                        <i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- Catatan -->
                <div>
                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                        Catatan <span class="text-xs text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <div class="relative group">
                        <i data-lucide="message-square" class="absolute left-4 top-4 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                        <textarea name="notes" rows="3" value="{{ old('notes') }}" placeholder="Alasan manual input..."
                                  class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all resize-none hover:border-navy-300 dark:hover:border-gold-600">{{ old('notes') }}</textarea>
                    </div>
                    @error('notes')
                    <p class="mt-2 text-xs text-red-500 flex items-center gap-1">
                        <i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}
                    </p>
                    @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end pt-4 border-t border-slate-200 dark:border-slate-700">
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 rounded-xl text-sm font-bold transition-all shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30 hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 active:scale-95 flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Tambah Presensi
                </button>
            </div>
        </form>
    </div>

    <!-- Daftar Presensi Hari Ini -->
    <div class="card p-6">
        <div class="flex items-center justify-between mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/30">
                    <i data-lucide="list" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-navy-800 dark:text-white">Presensi Hari Ini</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
                </div>
            </div>
            <span class="px-3 py-1.5 bg-navy-100 dark:bg-navy-900/30 text-navy-700 dark:text-navy-300 rounded-full text-xs font-bold">
                {{ $schedules->count() }} Jadwal
            </span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Guru</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kelas</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jam Ke</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Masuk</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Keluar</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($schedules as $schedule)
                    @php
                        $att = $schedule->todayAttendance;
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $schedule->user->teacher->photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($schedule->user->name) . '&background=0F172A&color=fff' }}" 
                                     class="w-8 h-8 rounded-full object-cover border-2 border-slate-200 dark:border-slate-700">
                                <div>
                                    <p class="text-sm font-bold text-navy-800 dark:text-white">{{ $schedule->user->name }}</p>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400">{{ $schedule->subject->name ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2.5 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-lg text-xs font-bold">
                                {{ $schedule->classroom->name }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm font-semibold text-navy-800 dark:text-white">{{ $schedule->period }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @if($att && $att->check_in_time)
                                <span class="inline-flex items-center gap-1 text-sm font-semibold text-green-600 dark:text-green-400">
                                    <i data-lucide="log-in" class="w-3.5 h-3.5"></i>
                                    {{ $att->check_in_time->format('H:i') }}
                                </span>
                            @else
                                <span class="text-sm text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($att && $att->check_out_time)
                                <span class="inline-flex items-center gap-1 text-sm font-semibold text-red-600 dark:text-red-400">
                                    <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                                    {{ $att->check_out_time->format('H:i') }}
                                </span>
                            @else
                                <span class="text-sm text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($att)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold {{ $att->status === 'Hadir' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $att->status === 'Hadir' ? 'bg-green-500' : 'bg-yellow-500' }}"></span>
                                    {{ $att->status }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    Belum
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($att)
                            <form method="POST" action="{{ route('admin.class-attendance.manual.destroy', $att->id) }}" 
                                  onsubmit="return confirm('Yakin hapus presensi ini?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 bg-red-100 dark:bg-red-900/30 hover:bg-red-200 dark:hover:bg-red-900/50 rounded-lg transition-all" title="Hapus">
                                    <i data-lucide="trash-2" class="w-4 h-4 text-red-600 dark:text-red-400"></i>
                                </button>
                            </form>
                            @else
                            <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <i data-lucide="inbox" class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3"></i>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Tidak ada jadwal hari ini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Custom Select Component
    function customSelect(name, initialOptions = []) {
        return {
            open: false,
            search: '',
            selectedValue: '',
            selectedLabel: '',
            options: initialOptions,

            init() {
                // Set initial value dari old() jika ada
                const hiddenInput = this.$el.querySelector(`input[name="${name}"]`);
                if (hiddenInput && hiddenInput.dataset.old) {
                    this.selectedValue = hiddenInput.dataset.old;
                    const opt = this.options.find(o => o.value === this.selectedValue);
                    if (opt) this.selectedLabel = opt.label;
                }
            },

            get filteredOptions() {
                if (!this.search) return this.options;
                return this.options.filter(opt => 
                    opt.label.toLowerCase().includes(this.search.toLowerCase())
                );
            },

            selectOption(option) {
                this.selectedValue = option.value;
                this.selectedLabel = option.label;
                this.search = '';
            },

            close() {
                this.open = false;
                this.search = '';
            }
        };
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) lucide.createIcons();
    });
</script>

<style>
    .fade-in {
        animation: fadeIn 0.5s ease-out forwards;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    [x-cloak] { display: none !important; }
</style>
@endsection