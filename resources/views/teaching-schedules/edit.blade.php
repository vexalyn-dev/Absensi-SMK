@extends('layouts.app')

@section('page-title', 'Edit Jadwal Mengajar - ' . $teacher->name)

@section('content')
    <div class="fade-in space-y-6">

        <!-- Header -->
        <div class="flex items-center gap-4">
            <a href="{{ route('teaching-schedules.index') }}"
                class="p-2.5 hover:bg-slate-100 dark:hover:bg-slate-700/50 rounded-xl transition-all hover:-translate-x-1 group">
                <i data-lucide="arrow-left"
                    class="w-5 h-5 text-slate-600 dark:text-slate-400 group-hover:text-navy-800 dark:group-hover:text-gold-400 transition-colors"></i>
            </a>

            <div class="flex items-center gap-3">
                <img src="{{ $teacher->photo_url }}"
                    class="w-14 h-14 rounded-2xl object-cover border-2 border-white dark:border-slate-700 shadow-lg">
                <div>
                    <h1 class="text-xl font-bold text-navy-800 dark:text-white">{{ $teacher->name }}</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Atur jadwal mengajar per kelas</p>
                </div>
            </div>
        </div>

        <!-- Validation Errors -->
        @if ($errors->any())
            <div
                class="card p-4 bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 border border-red-200 dark:border-red-800">
                <div class="flex items-start gap-3">
                    <div
                        class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0 text-red-600 dark:text-red-400">
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

        <!-- Form -->
        <form action="{{ route('teaching-schedules.update', $teacher) }}" method="POST" id="schedule-form">
            @csrf
            @method('PUT')

            <div class="card p-6">
                <!-- Header Section -->
                <div class="flex items-center justify-between mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-11 h-11 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg">
                            <i data-lucide="calendar-range" class="w-5 h-5 text-white dark:text-navy-900"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-navy-800 dark:text-white">Jadwal Mengajar Mingguan</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Atur kelas, mata pelajaran, dan jam
                                mengajar</p>
                        </div>
                    </div>
                </div>

                <!-- Existing Schedules Display -LIST LAYOUT -->
                    @if($schedules->count() > 0)
                        <div class="mb-6">
                            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-4 flex items-center gap-2">
                                <i data-lucide="check-circle" class="w-4 h-4 text-green-500"></i>
                                Jadwal Saat Ini ({{ $schedules->count() }})
                            </h3>

                            @php
                                $groupedSchedules = $schedules->groupBy('day_of_week');
                                $dayNames = [
                                    1 => 'Senin',
                                    2 => 'Selasa',
                                    3 => 'Rabu',
                                    4 => 'Kamis',
                                    5 => 'Jumat',
                                    6 => 'Sabtu',
                                    0 => 'Minggu'
                                ];
                                $dayAbbr = [
                                    1 => 'SEN',
                                    2 => 'SEL',
                                    3 => 'RAB',
                                    4 => 'KAM',
                                    5 => 'JUM',
                                    6 => 'SAB',
                                    0 => 'MGU'
                                ];
                                $dayIcons = [
                                    1 => 'sunrise',
                                    2 => 'sunrise',
                                    3 => 'sun',
                                    4 => 'sun',
                                    5 => 'sunset',
                                    6 => 'sunset',
                                    0 => 'sun'
                                ];
                                $sortedDays = [1, 2, 3, 4, 5, 6, 0];
                            @endphp

                            <div class="space-y-4" id="existing-schedules-list">
                                @foreach($sortedDays as $dayIndex)
                                    @php
                                        $daySchedules = $groupedSchedules->get($dayIndex);
                                    @endphp

                                    @if($daySchedules)
                                        <!-- Day Section Header (Not Card) -->
                                        <div class="flex items-center gap-3 pt-4 pb-2 border-b border-slate-200 dark:border-slate-700">
                                            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 flex items-center justify-center flex-shrink-0">
                                                <span class="text-[10px] font-black text-white dark:text-navy-900 tracking-wider">{{ $dayAbbr[$dayIndex] }}</span>
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="text-sm font-bold text-navy-800 dark:text-white">{{ $dayNames[$dayIndex] }}</h4>
                                            </div>
                                            <span class="text-xs text-slate-500 dark:text-slate-400">{{ $daySchedules->count() }} jadwal</span>
                                        </div>

                                        <!-- Schedules List -->
                                        <div class="space-y-2">
                                            @foreach($daySchedules->sortBy('period') as $schedule)
                                                <div class="schedule-card p-4 bg-green-50 dark:bg-green-900/10 rounded-xl border border-green-200 dark:border-green-800 transition-all block"
                                                     data-schedule-id="{{ $schedule->id }}"
                                                     data-day="{{ $schedule->day_of_week }}"
                                                     data-classroom="{{ $schedule->classroom_id }}"
                                                     data-subject="{{ $schedule->subject_id ?? '' }}"
                                                     data-period="{{ $schedule->period }}"
                                                     data-start="{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}"
                                                     data-end="{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}">
                                                    
                                                    <div class="flex flex-col md:flex-row gap-4 items-start md:items-center justify-between w-full">
                                                        <div class="flex-1 min-w-0 w-full">
                                                            <!-- View Mode -->
                                                            <div class="schedule-view flex items-center gap-4">
                                                                <!-- Icon -->
                                                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 flex items-center justify-center flex-shrink-0 shadow-lg">
                                                                    <span class="schedule-view-day text-sm font-black text-white dark:text-navy-900 uppercase tracking-wider">
                                                                        {{ substr(\App\Models\TeachingSchedule::getDayName($schedule->day_of_week), 0, 3) }}
                                                                    </span>
                                                                </div>

                                                                <!-- Info -->
                                                                <div class="flex-1 min-w-0">
                                                                    <p class="schedule-view-classroom text-base font-bold text-navy-800 dark:text-white mb-1">{{ $schedule->classroom->name }}</p>
                                                                    <p class="schedule-view-period-subject text-xs text-slate-500 dark:text-slate-400">
                                                                        Jam {{ $schedule->period }} • {{ $schedule->subject?->name ?? '-' }}
                                                                    </p>
                                                                    <div class="schedule-view-time text-sm text-slate-600 dark:text-slate-400 font-mono font-medium mt-1.5 flex items-center gap-1.5">
                                                                        <i data-lucide="clock" class="w-4 h-4"></i>
                                                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Edit Mode (Hidden by default) -->
                                                            <div class="schedule-edit hidden mt-0 pt-0 border-t-0">
                                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                                                    <!-- Hari -->
                                                                    <div>
                                                                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Hari</label>
                                                                        <div x-data="{ 
                                                                                open: false, 
                                                                                value: '{{ $schedule->day_of_week }}', 
                                                                                label: '',
                                                                                options: [
                                                                                    {v: '1', l: 'Senin'}, {v: '2', l: 'Selasa'}, {v: '3', l: 'Rabu'},
                                                                                    {v: '4', l: 'Kamis'}, {v: '5', l: 'Jumat'}, {v: '6', l: 'Sabtu'}, {v: '0', l: 'Minggu'}
                                                                                ],
                                                                                init() {
                                                                                    this.$watch('value', val => {
                                                                                        let selected = this.options.find(o => o.v == val);
                                                                                        if(selected) this.label = selected.l;
                                                                                    });
                                                                                    let selected = this.options.find(o => o.v == this.value);
                                                                                    if(selected) this.label = selected.l;
                                                                                },
                                                                                select(opt) { 
                                                                                    this.value = opt.v; 
                                                                                    this.open = false; 
                                                                                }
                                                                            }" 
                                                                            class="relative group" @click.away="open = false">
                                                                            
                                                                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none z-10">
                                                                                <i data-lucide="calendar" class="w-4 h-4 text-slate-400 transition-colors" :class="open ? 'text-navy-800 dark:text-gold-500' : 'group-focus-within:text-navy-800 dark:group-focus-within:text-gold-500'"></i>
                                                                            </div>
                                                                            
                                                                            <input type="hidden" name="schedules[edit_{{ $schedule->id }}][day_of_week]" x-model="value" class="schedule-day">
                                                                            
                                                                            <button type="button" @click="open = !open"
                                                                                    class="w-full pl-10 pr-8 py-3 bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700 border-2 rounded-xl text-sm font-medium focus:outline-none transition-all text-left flex items-center justify-between"
                                                                                    :class="open ? 'border-navy-800 dark:border-gold-500 text-navy-800 dark:text-gold-400 shadow-sm' : 'border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200'">
                                                                                <span x-text="label" class="block truncate"></span>
                                                                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                                                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180 text-navy-800 dark:text-gold-500' : ''"></i>
                                                                                </div>
                                                                            </button>
                                                                            
                                                                            <div x-show="open" style="display: none;"
                                                                                 x-transition:enter="transition ease-out duration-200"
                                                                                 x-transition:enter-start="opacity-0 translate-y-2"
                                                                                 x-transition:enter-end="opacity-100 translate-y-0"
                                                                                 x-transition:leave="transition ease-in duration-150"
                                                                                 x-transition:leave-start="opacity-100 translate-y-0"
                                                                                 x-transition:leave-end="opacity-0 translate-y-2"
                                                                                 class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl shadow-xl overflow-hidden">
                                                                                <ul class="max-h-60 overflow-y-auto p-1.5 space-y-0.5 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                                                                                    <template x-for="opt in options" :key="opt.v">
                                                                                        <li>
                                                                                            <button type="button" @click="select(opt)" 
                                                                                                    class="w-full text-left px-3 py-2.5 text-sm font-medium rounded-lg transition-colors flex items-center gap-2"
                                                                                                    :class="value == opt.v ? 'bg-navy-50 text-navy-800 dark:bg-navy-900/50 dark:text-gold-400' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'">
                                                                                                <svg class="w-4 h-4 flex-shrink-0" :class="value == opt.v ? 'opacity-100' : 'opacity-0'" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                                                                <span x-text="opt.l" class="truncate"></span>
                                                                                            </button>
                                                                                        </li>
                                                                                    </template>
                                                                                </ul>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <!-- Kelas -->
                                                                    <div>
                                                                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Kelas</label>
                                                                        <div x-data="{ 
                                                                                open: false, 
                                                                                value: '{{ $schedule->classroom_id }}', 
                                                                                label: '',
                                                                                options: [
                                                                                    {v: '', l: 'Pilih Kelas'},
                                                                                    @foreach($classrooms as $cr)
                                                                                    {v: '{{ $cr->id }}', l: '{{ addslashes($cr->name) }}'},
                                                                                    @endforeach
                                                                                ],
                                                                                init() {
                                                                                    this.$watch('value', val => {
                                                                                        let selected = this.options.find(o => o.v == val);
                                                                                        if(selected) this.label = selected.l;
                                                                                    });
                                                                                    let selected = this.options.find(o => o.v == this.value);
                                                                                    if(selected) this.label = selected.l;
                                                                                },
                                                                                select(opt) { 
                                                                                    this.value = opt.v; 
                                                                                    this.open = false; 
                                                                                }
                                                                            }" 
                                                                            class="relative group" @click.away="open = false">
                                                                            
                                                                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none z-10">
                                                                                <i data-lucide="school" class="w-4 h-4 text-slate-400 transition-colors" :class="open ? 'text-navy-800 dark:text-gold-500' : 'group-focus-within:text-navy-800 dark:group-focus-within:text-gold-500'"></i>
                                                                            </div>
                                                                            
                                                                            <input type="hidden" name="schedules[edit_{{ $schedule->id }}][classroom_id]" x-model="value" class="schedule-classroom">
                                                                            
                                                                            <button type="button" @click="open = !open"
                                                                                    class="w-full pl-10 pr-8 py-3 bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700 border-2 rounded-xl text-sm font-medium focus:outline-none transition-all text-left flex items-center justify-between"
                                                                                    :class="open ? 'border-navy-800 dark:border-gold-500 text-navy-800 dark:text-gold-400 shadow-sm' : 'border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200'">
                                                                                <span x-text="label" class="block truncate"></span>
                                                                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                                                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180 text-navy-800 dark:text-gold-500' : ''"></i>
                                                                                </div>
                                                                            </button>
                                                                            
                                                                            <div x-show="open" style="display: none;"
                                                                                 x-transition:enter="transition ease-out duration-200"
                                                                                 x-transition:enter-start="opacity-0 translate-y-2"
                                                                                 x-transition:enter-end="opacity-100 translate-y-0"
                                                                                 x-transition:leave="transition ease-in duration-150"
                                                                                 x-transition:leave-start="opacity-100 translate-y-0"
                                                                                 x-transition:leave-end="opacity-0 translate-y-2"
                                                                                 class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl shadow-xl overflow-hidden">
                                                                                <ul class="max-h-60 overflow-y-auto p-1.5 space-y-0.5 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                                                                                    <template x-for="opt in options" :key="opt.v">
                                                                                        <li>
                                                                                            <button type="button" @click="select(opt)" 
                                                                                                    class="w-full text-left px-3 py-2.5 text-sm font-medium rounded-lg transition-colors flex items-center gap-2"
                                                                                                    :class="value == opt.v ? 'bg-navy-50 text-navy-800 dark:bg-navy-900/50 dark:text-gold-400' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'">
                                                                                                <svg class="w-4 h-4 flex-shrink-0" :class="value == opt.v ? 'opacity-100' : 'opacity-0'" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                                                                <span x-text="opt.l" class="truncate"></span>
                                                                                            </button>
                                                                                        </li>
                                                                                    </template>
                                                                                </ul>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Jam Pelajaran -->
                                                                    <div>
                                                                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Jam Pelajaran</label>
                                                                        <div x-data="{ 
                                                                                open: false, 
                                                                                value: '{{ $schedule->period }}', 
                                                                                label: '',
                                                                                options: [
                                                                                    @for($p = 1; $p <= 15; $p++)
                                                                                    {v: '{{ $p }}', l: 'Jam {{ $p }}'},
                                                                                    @endfor
                                                                                ],
                                                                                init() {
                                                                                    this.$watch('value', val => {
                                                                                        let selected = this.options.find(o => o.v == val);
                                                                                        if(selected) this.label = selected.l;
                                                                                    });
                                                                                    let selected = this.options.find(o => o.v == this.value);
                                                                                    if(selected) this.label = selected.l;
                                                                                },
                                                                                select(opt) { 
                                                                                    this.value = opt.v; 
                                                                                    this.open = false; 
                                                                                }
                                                                            }" 
                                                                            class="relative group" @click.away="open = false">
                                                                            
                                                                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none z-10">
                                                                                <i data-lucide="clock-4" class="w-4 h-4 text-slate-400 transition-colors" :class="open ? 'text-navy-800 dark:text-gold-500' : 'group-focus-within:text-navy-800 dark:group-focus-within:text-gold-500'"></i>
                                                                            </div>
                                                                            
                                                                            <input type="hidden" name="schedules[edit_{{ $schedule->id }}][period]" x-model="value" class="schedule-period">
                                                                            
                                                                            <button type="button" @click="open = !open"
                                                                                    class="w-full pl-10 pr-8 py-3 bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700 border-2 rounded-xl text-sm font-medium focus:outline-none transition-all text-left flex items-center justify-between"
                                                                                    :class="open ? 'border-navy-800 dark:border-gold-500 text-navy-800 dark:text-gold-400 shadow-sm' : 'border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200'">
                                                                                <span x-text="label" class="block truncate"></span>
                                                                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                                                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180 text-navy-800 dark:text-gold-500' : ''"></i>
                                                                                </div>
                                                                            </button>
                                                                            
                                                                            <div x-show="open" style="display: none;"
                                                                                 x-transition:enter="transition ease-out duration-200"
                                                                                 x-transition:enter-start="opacity-0 translate-y-2"
                                                                                 x-transition:enter-end="opacity-100 translate-y-0"
                                                                                 x-transition:leave="transition ease-in duration-150"
                                                                                 x-transition:leave-start="opacity-100 translate-y-0"
                                                                                 x-transition:leave-end="opacity-0 translate-y-2"
                                                                                 class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl shadow-xl overflow-hidden">
                                                                                <ul class="max-h-60 overflow-y-auto p-1.5 space-y-0.5 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                                                                                    <template x-for="opt in options" :key="opt.v">
                                                                                        <li>
                                                                                            <button type="button" @click="select(opt)" 
                                                                                                    class="w-full text-left px-3 py-2.5 text-sm font-medium rounded-lg transition-colors flex items-center gap-2"
                                                                                                    :class="value == opt.v ? 'bg-navy-50 text-navy-800 dark:bg-navy-900/50 dark:text-gold-400' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'">
                                                                                                <svg class="w-4 h-4 flex-shrink-0" :class="value == opt.v ? 'opacity-100' : 'opacity-0'" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                                                                <span x-text="opt.l" class="truncate"></span>
                                                                                            </button>
                                                                                        </li>
                                                                                    </template>
                                                                                </ul>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                                                    <!-- Jam Masuk & Pulang -->
                                                                    <div class="flex items-end gap-3">
                                                                        <div class="flex-1">
                                                                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Jam Masuk</label>
                                                                            <div class="relative group">
                                                                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                                                                    <i data-lucide="clock" class="w-4 h-4 text-slate-400 group-focus-within:text-navy-800 dark:group-focus-within:text-gold-500 transition-colors"></i>
                                                                                </div>
                                                                                <input type="time" name="schedules[edit_{{ $schedule->id }}][start_time]" required
                                                                                       value="{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}"
                                                                                       class="schedule-start w-full pl-10 pr-4 py-3 bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm font-mono font-medium text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-navy-800/20 dark:focus:ring-gold-500/20 focus:border-navy-800 dark:focus:border-gold-500 transition-all">
                                                                            </div>
                                                                        </div>
                                                                        <div class="pb-3 text-slate-400">
                                                                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                                                        </div>
                                                                        <div class="flex-1">
                                                                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Jam Pulang</label>
                                                                            <div class="relative group">
                                                                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                                                                    <i data-lucide="clock" class="w-4 h-4 text-slate-400 group-focus-within:text-navy-800 dark:group-focus-within:text-gold-500 transition-colors"></i>
                                                                                </div>
                                                                                <input type="time" name="schedules[edit_{{ $schedule->id }}][end_time]" required
                                                                                       value="{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}"
                                                                                       class="schedule-end w-full pl-10 pr-4 py-3 bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm font-mono font-medium text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-navy-800/20 dark:focus:ring-gold-500/20 focus:border-navy-800 dark:focus:border-gold-500 transition-all">
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Mata Pelajaran -->
                                                                    <div>
                                                                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Mata Pelajaran</label>
                                                                        <div x-data="{ 
                                                                                open: false, 
                                                                                value: '{{ $schedule->subject_id ?? '' }}', 
                                                                                label: '',
                                                                                options: [
                                                                                    {v: '', l: 'Pilih Mapel'},
                                                                                    @foreach($subjects as $subject)
                                                                                    {v: '{{ $subject->id }}', l: '{{ addslashes($subject->name) }}'},
                                                                                    @endforeach
                                                                                ],
                                                                                init() {
                                                                                    this.$watch('value', val => {
                                                                                        let selected = this.options.find(o => o.v == val);
                                                                                        if(selected) this.label = selected.l;
                                                                                    });
                                                                                    let selected = this.options.find(o => o.v == this.value);
                                                                                    if(selected) this.label = selected.l;
                                                                                },
                                                                                select(opt) { 
                                                                                    this.value = opt.v; 
                                                                                    this.open = false; 
                                                                                }
                                                                            }" 
                                                                            class="relative group" @click.away="open = false">
                                                                            
                                                                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none z-10">
                                                                                <i data-lucide="book-open" class="w-4 h-4 text-slate-400 transition-colors" :class="open ? 'text-navy-800 dark:text-gold-500' : 'group-focus-within:text-navy-800 dark:group-focus-within:text-gold-500'"></i>
                                                                            </div>
                                                                            
                                                                            <input type="hidden" name="schedules[edit_{{ $schedule->id }}][subject_id]" x-model="value" class="schedule-subject">
                                                                            
                                                                            <button type="button" @click="open = !open"
                                                                                    class="w-full pl-10 pr-8 py-3 bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700 border-2 rounded-xl text-sm font-medium focus:outline-none transition-all text-left flex items-center justify-between"
                                                                                    :class="open ? 'border-navy-800 dark:border-gold-500 text-navy-800 dark:text-gold-400 shadow-sm' : 'border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200'">
                                                                                <span x-text="label" class="block truncate"></span>
                                                                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                                                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180 text-navy-800 dark:text-gold-500' : ''"></i>
                                                                                </div>
                                                                            </button>
                                                                            
                                                                            <div x-show="open" style="display: none;"
                                                                                 x-transition:enter="transition ease-out duration-200"
                                                                                 x-transition:enter-start="opacity-0 translate-y-2"
                                                                                 x-transition:enter-end="opacity-100 translate-y-0"
                                                                                 x-transition:leave="transition ease-in duration-150"
                                                                                 x-transition:leave-start="opacity-100 translate-y-0"
                                                                                 x-transition:leave-end="opacity-0 translate-y-2"
                                                                                 class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl shadow-xl overflow-hidden">
                                                                                <ul class="max-h-60 overflow-y-auto p-1.5 space-y-0.5 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                                                                                    <template x-for="opt in options" :key="opt.v">
                                                                                        <li>
                                                                                            <button type="button" @click="select(opt)" 
                                                                                                    class="w-full text-left px-3 py-2.5 text-sm font-medium rounded-lg transition-colors flex items-center gap-2"
                                                                                                    :class="value == opt.v ? 'bg-navy-50 text-navy-800 dark:bg-navy-900/50 dark:text-gold-400' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'">
                                                                                                <svg class="w-4 h-4 flex-shrink-0" :class="value == opt.v ? 'opacity-100' : 'opacity-0'" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                                                                <span x-text="opt.l" class="truncate"></span>
                                                                                            </button>
                                                                                        </li>
                                                                                    </template>
                                                                                </ul>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                
                                                                <input type="hidden" name="schedules[edit_{{ $schedule->id }}][schedule_id]" value="{{ $schedule->id }}">
                                                            </div>
                                                </div>

                                                <!-- Action Buttons -->
                                                <div class="flex items-center gap-1 flex-shrink-0 w-full md:w-auto justify-end">
                                                    <button type="button" 
                                                            onclick="viewSchedule(this)"
                                                            class="view-btn p-2 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors"
                                                            title="Lihat Detail">
                                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                                    </button>
                                                    <button type="button" 
                                                            onclick="toggleEdit(this)"
                                                            class="edit-btn p-2 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 rounded-lg hover:bg-yellow-200 dark:hover:bg-yellow-900/50 transition-colors"
                                                            title="Edit Jadwal">
                                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                                    </button>
                                                    <button type="button" 
                                                            onclick="deleteSchedule(this)"
                                                            class="delete-btn p-2 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors"
                                                            title="Hapus Jadwal">
                                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                    </button>
                                                    <button type="button" 
                                                            onclick="cancelEdit(this)"
                                                            class="cancel-btn hidden p-2 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors"
                                                            title="Batal Edit">
                                                        <i data-lucide="x" class="w-4 h-4"></i>
                                                    </button>
                                                    <button type="button" 
                                                            onclick="saveEdit(this)"
                                                            class="save-btn hidden p-2 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-lg hover:bg-green-200 dark:hover:bg-green-900/50 transition-colors"
                                                            title="Simpan Perubahan">
                                                        <i data-lucide="check" class="w-4 h-4"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Hidden delete input -->
                                            <input type="hidden" class="delete-input" value="">
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Add New Schedule Section -->
                    <div id="schedule-container" class="space-y-4">
                        <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-3 flex items-center gap-2">
                            <i data-lucide="plus-circle" class="w-4 h-4 text-blue-500"></i>
                            Tambah Jadwal Baru
                        </h3>
                    </div>

                    <button type="button" 
                            onclick="addScheduleRow()"
                            class="mt-4 w-full px-4 py-3 border-2 border-dashed border-slate-300 dark:border-slate-600 hover:border-navy-800 dark:hover:border-gold-400 text-slate-500 dark:text-slate-400 hover:text-navy-800 dark:hover:text-gold-400 rounded-xl text-sm font-semibold transition-all flex items-center justify-center gap-2">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Tambah Jadwal</span>
                    </button>

                    <input type="hidden" id="delete-schedule-id" value="">

                    <!-- Action Buttons -->
                    <div class="flex gap-3 mt-8 pt-6 border-t border-slate-200 dark:border-slate-700">
                        <a href="{{ route('teaching-schedules.index') }}" 
                           class="flex-1 px-6 py-3.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold transition-all hover:-translate-y-0.5 text-center flex items-center justify-center gap-2">
                            <i data-lucide="x" class="w-4 h-4"></i>
                            <span>Batal</span>
                        </a>
                        <button type="submit" 
                                class="flex-1 px-6 py-3.5 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 hover:from-navy-900 hover:to-slate-900 dark:hover:from-gold-500 dark:hover:to-gold-600 text-white dark:text-navy-900 rounded-xl text-sm font-bold transition-all shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30 hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            <span>{{ $schedules->count() > 0 ? 'Update Jadwal' : 'Simpan Jadwal' }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="delete-modal"
             style="display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; padding:1rem;">
            
            {{-- Backdrop --}}
            <div id="delete-modal-backdrop"
                 style="position:absolute; inset:0; background:rgba(15,23,42,0.65); backdrop-filter:blur(4px);">
            </div>

            {{-- Modal Card --}}
            <div id="delete-modal-box"
                 class="bg-white dark:bg-slate-800 rounded-2xl p-6 max-w-md w-full shadow-2xl border border-slate-200 dark:border-slate-700"
                 style="position:relative; z-index:1; transition:transform 0.25s ease, opacity 0.25s ease; transform:scale(0.9); opacity:0;">
                
                <div class="flex items-center gap-4 mb-5">
                    <div class="w-14 h-14 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="alert-triangle" class="w-7 h-7 text-red-600 dark:text-red-400"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-navy-800 dark:text-white">Hapus Jadwal?</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Tindakan ini tidak dapat dibatalkan</p>
                    </div>
                </div>
                
                <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-4 mb-5">
                    <p class="text-sm text-slate-700 dark:text-slate-300">
                        Yakin ingin menghapus jadwal ini? Data yang dihapus tidak dapat dikembalikan.
                    </p>
                </div>
                
                <div class="flex gap-3">
                    <button type="button" onclick="closeDeleteModal()" 
                            class="flex-1 px-4 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold transition-all">
                        Batal
                    </button>
                    <button type="button" onclick="confirmDelete()" 
                            class="flex-1 px-4 py-2.5 bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white rounded-xl text-sm font-bold transition-all shadow-lg shadow-red-500/30 flex items-center justify-center gap-1">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                        <span>Hapus</span>
                    </button>
                </div>
            </div>
        </div>

        <script>
            const classrooms = @json($classrooms);
            const subjects = @json($subjects);
            let scheduleCount = 0;

            // View Schedule - toggle detail info panel
            function viewSchedule(btn) {
                const card = btn.closest('.schedule-card');
                const existing = card.querySelector('.view-detail-panel');

                if (existing) {
                    existing.remove();
                    btn.classList.remove('!bg-blue-200');
                    return;
                }

                const classroom = card.querySelector('.schedule-view-classroom')?.innerText || '-';
                const periodSubject = card.querySelector('.schedule-view-period-subject')?.innerText || '-';
                const timeEl = card.querySelector('.schedule-view-time');
                const time = timeEl ? timeEl.innerText.trim() : '-';
                const dayNames = {'0':'Minggu','1':'Senin','2':'Selasa','3':'Rabu','4':'Kamis','5':'Jumat','6':'Sabtu'};
                const day = dayNames[card.dataset.day] || '-';

                const panel = document.createElement('div');
                panel.className = 'view-detail-panel mt-3 pt-3 border-t border-green-200 dark:border-green-700 grid grid-cols-2 gap-2';
                panel.innerHTML = `
                    <div class="bg-white dark:bg-slate-800 rounded-lg p-2.5 border border-slate-100 dark:border-slate-700">
                        <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wide mb-0.5">Hari</p>
                        <p class="text-xs font-bold text-navy-800 dark:text-white">${day}</p>
                    </div>
                    <div class="bg-white dark:bg-slate-800 rounded-lg p-2.5 border border-slate-100 dark:border-slate-700">
                        <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wide mb-0.5">Kelas</p>
                        <p class="text-xs font-bold text-navy-800 dark:text-white">${classroom}</p>
                    </div>
                    <div class="bg-white dark:bg-slate-800 rounded-lg p-2.5 border border-slate-100 dark:border-slate-700">
                        <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wide mb-0.5">Jam & Mapel</p>
                        <p class="text-xs font-bold text-navy-800 dark:text-white">${periodSubject}</p>
                    </div>
                    <div class="bg-white dark:bg-slate-800 rounded-lg p-2.5 border border-slate-100 dark:border-slate-700">
                        <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wide mb-0.5">Waktu</p>
                        <p class="text-xs font-bold text-navy-800 dark:text-white font-mono">${time}</p>
                    </div>
                `;
                card.appendChild(panel);
                btn.classList.add('!bg-blue-200');
            }

            // Toggle Edit Mode
            function toggleEdit(btn) {
                const card = btn.closest('.schedule-card');
                const viewMode = card.querySelector('.schedule-view');
                const editMode = card.querySelector('.schedule-edit');
                const viewBtn = card.querySelector('.view-btn');
                const editBtn = card.querySelector('.edit-btn');
                const deleteBtn = card.querySelector('.delete-btn');
                const cancelBtn = card.querySelector('.cancel-btn');
                const saveBtn = card.querySelector('.save-btn');

                // Close view detail panel if open
                const existingPanel = card.querySelector('.view-detail-panel');
                if (existingPanel) existingPanel.remove();

                // Populate form with current data
                const day = card.dataset.day;
                const classroom = card.dataset.classroom;
                const subject = card.dataset.subject;
                const period = card.dataset.period;
                const start = card.dataset.start;
                const end = card.dataset.end;

                const fields = [
                    { name: 'day', val: day },
                    { name: 'classroom', val: classroom },
                    { name: 'subject', val: subject },
                    { name: 'period', val: period },
                    { name: 'start', val: start },
                    { name: 'end', val: end }
                ];

                fields.forEach(f => {
                    const input = card.querySelector('.schedule-' + f.name);
                    if (input) {
                        input.value = f.val;
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                });

                // Toggle visibility
                viewMode.classList.add('hidden');
                editMode.classList.remove('hidden');
                viewBtn.classList.add('hidden');
                editBtn.classList.add('hidden');
                deleteBtn.classList.add('hidden');
                cancelBtn.classList.remove('hidden');
                saveBtn.classList.remove('hidden');

                // Change card background
                card.classList.remove('bg-green-50', 'dark:bg-green-900/10', 'border-green-200', 'dark:border-green-800');
                card.classList.add('bg-yellow-50', 'dark:bg-yellow-900/10', 'border-yellow-200', 'dark:border-yellow-800');

                if (window.lucide) lucide.createIcons();
            }

            // Cancel Edit
            function cancelEdit(btn) {
                const card = btn.closest('.schedule-card');
                const viewMode = card.querySelector('.schedule-view');
                const editMode = card.querySelector('.schedule-edit');
                const viewBtn = card.querySelector('.view-btn');
                const editBtn = card.querySelector('.edit-btn');
                const deleteBtn = card.querySelector('.delete-btn');
                const cancelBtn = card.querySelector('.cancel-btn');
                const saveBtn = card.querySelector('.save-btn');

                // Toggle visibility
                viewMode.classList.remove('hidden');
                editMode.classList.add('hidden');
                viewBtn.classList.remove('hidden');
                editBtn.classList.remove('hidden');
                deleteBtn.classList.remove('hidden');
                cancelBtn.classList.add('hidden');
                saveBtn.classList.add('hidden');

                // Restore card background
                card.classList.add('bg-green-50', 'dark:bg-green-900/10', 'border-green-200', 'dark:border-green-800');
                card.classList.remove('bg-yellow-50', 'dark:bg-yellow-900/10', 'border-yellow-200', 'dark:border-yellow-800');

                if (window.lucide) lucide.createIcons();
            }

            // Save Edit (update view representation and dataset values, then toggle mode)
            function saveEdit(btn) {
                const card = btn.closest('.schedule-card');

                // Get form elements
                const daySelect = card.querySelector('.schedule-day');
                const classroomSelect = card.querySelector('.schedule-classroom');
                const subjectSelect = card.querySelector('.schedule-subject');
                const periodSelect = card.querySelector('.schedule-period');
                const startInput = card.querySelector('.schedule-start');
                const endInput = card.querySelector('.schedule-end');

                // 1. Update datasets
                card.dataset.day = daySelect.value;
                card.dataset.classroom = classroomSelect.value;
                card.dataset.subject = subjectSelect.value;
                card.dataset.period = periodSelect.value;
                card.dataset.start = startInput.value;
                card.dataset.end = endInput.value;

                // 2. Update visual texts in schedule-view
                const dayText = daySelect.closest('[x-data]').querySelector('button span').innerText.substring(0, 3).toUpperCase();
                const classroomText = classroomSelect.closest('[x-data]').querySelector('button span').innerText;
                const subjectText = subjectSelect.value ? subjectSelect.closest('[x-data]').querySelector('button span').innerText : '-';
                const periodValue = periodSelect.value;
                const timeRange = `${startInput.value} - ${endInput.value}`;

                card.querySelector('.schedule-view-day').innerText = dayText;
                card.querySelector('.schedule-view-classroom').innerText = classroomText;
                card.querySelector('.schedule-view-period-subject').innerText = `Jam ${periodValue} • ${subjectText}`;
                card.querySelector('.schedule-view-time').innerHTML = `<i data-lucide="clock" class="w-4 h-4"></i>${timeRange}`;

                // 3. Toggle back to view mode
                cancelEdit(btn);
            }

            let scheduleToDelete = null;

            // Delete Schedule - Show Modal
            function deleteSchedule(btn) {
                const card = btn.closest('.schedule-card');
                scheduleToDelete = {
                    card: card,
                    scheduleId: card.dataset.scheduleId
                };
                openDeleteModal();
            }

            // Open Delete Modal
            function openDeleteModal() {
                const modal = document.getElementById('delete-modal');
                const box = document.getElementById('delete-modal-box');
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                requestAnimationFrame(() => requestAnimationFrame(() => {
                    box.style.transform = 'scale(1)';
                    box.style.opacity = '1';
                }));
                if (window.lucide) lucide.createIcons();
            }

            // Close Delete Modal
            function closeDeleteModal() {
                const modal = document.getElementById('delete-modal');
                const box = document.getElementById('delete-modal-box');
                box.style.transform = 'scale(0.9)';
                box.style.opacity = '0';
                setTimeout(() => {
                    modal.style.display = 'none';
                    document.body.style.overflow = '';
                    scheduleToDelete = null;
                }, 250);
            }

            // Confirm Delete
            function confirmDelete() {
                if (scheduleToDelete) {
                    const card = scheduleToDelete.card;
                    const scheduleId = scheduleToDelete.scheduleId;
                    const deleteInput = card.querySelector('.delete-input');
                    
                    deleteInput.setAttribute('name', 'delete_schedules[]');
                    deleteInput.value = scheduleId;
                    card.style.display = 'none';
                    card.querySelectorAll('input:not(.delete-input), select').forEach(el => el.disabled = true);
                    
                    closeDeleteModal();
                }
            }

            // Close modal when clicking on backdrop or pressing Escape
            document.addEventListener('DOMContentLoaded', () => {
                const backdrop = document.getElementById('delete-modal-backdrop');
                if (backdrop) {
                    backdrop.addEventListener('click', closeDeleteModal);
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeDeleteModal();
                }
            });

            // Add New Schedule Row
            function addScheduleRow() {
                const container = document.getElementById('schedule-container');
                const index = scheduleCount++;

                const dayOptions = [
                    {v: '1', l: 'Senin'}, {v: '2', l: 'Selasa'}, {v: '3', l: 'Rabu'},
                    {v: '4', l: 'Kamis'}, {v: '5', l: 'Jumat'}, {v: '6', l: 'Sabtu'}, {v: '0', l: 'Minggu'}
                ];
                const classroomOptions = [
                    {v: '', l: 'Pilih Kelas'},
                    ...classrooms.map(c => ({v: c.id.toString(), l: c.name}))
                ];
                const periodOptions = Array.from({length: 15}, (_, i) => ({v: (i+1).toString(), l: `Jam ${i+1}`}));
                const subjectOptions = [
                    {v: '', l: 'Pilih Mapel'},
                    ...subjects.map(s => ({v: s.id.toString(), l: s.name}))
                ];

                const div = document.createElement('div');
                div.className = 'schedule-row p-4 bg-slate-50 dark:bg-slate-700/30 rounded-xl border border-slate-200 dark:border-slate-700 space-y-3';
                div.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <!-- Hari -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Hari</label>
                            <div x-data="{ 
                                    open: false, 
                                    value: '1', 
                                    label: 'Senin',
                                    options: ${JSON.stringify(dayOptions).replace(/"/g, '&quot;')},
                                    init() {
                                        this.$watch('value', val => {
                                            let selected = this.options.find(o => o.v == val);
                                            if(selected) this.label = selected.l;
                                        });
                                    },
                                    select(opt) { 
                                        this.value = opt.v; 
                                        this.open = false; 
                                    }
                                }" 
                                class="relative group" @click.away="open = false">
                                
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                    <i data-lucide="calendar" class="w-4 h-4 text-slate-400 transition-colors" :class="open ? 'text-navy-800 dark:text-gold-500' : ''"></i>
                                </div>
                                
                                <input type="hidden" name="schedules[new_\${index}][day_of_week]" x-model="value">
                                
                                <button type="button" @click="open = !open"
                                        class="w-full pl-9 pr-8 py-2.5 bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700 border-2 rounded-lg text-sm font-medium focus:outline-none transition-all text-left flex items-center justify-between"
                                        :class="open ? 'border-navy-800 dark:border-gold-500 text-navy-800 dark:text-gold-400 shadow-sm' : 'border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200'">
                                    <span x-text="label" class="block truncate"></span>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180 text-navy-800 dark:text-gold-500' : ''"></i>
                                    </div>
                                </button>
                                
                                <div x-show="open" style="display: none;"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 translate-y-2"
                                     class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl shadow-xl overflow-hidden">
                                    <ul class="max-h-60 overflow-y-auto p-1.5 space-y-0.5 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                                        <template x-for="opt in options" :key="opt.v">
                                            <li>
                                                <button type="button" @click="select(opt)" 
                                                        class="w-full text-left px-3 py-2 text-sm font-medium rounded-md transition-colors flex items-center gap-2"
                                                        :class="value == opt.v ? 'bg-navy-50 text-navy-800 dark:bg-navy-900/50 dark:text-gold-400' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'">
                                                    <svg class="w-3.5 h-3.5 flex-shrink-0" :class="value == opt.v ? 'opacity-100' : 'opacity-0'" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                    <span x-text="opt.l" class="truncate"></span>
                                                </button>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Kelas -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Kelas</label>
                            <div x-data="{ 
                                    open: false, 
                                    value: '', 
                                    label: 'Pilih Kelas',
                                    options: ${JSON.stringify(classroomOptions).replace(/"/g, '&quot;')},
                                    init() {
                                        this.$watch('value', val => {
                                            let selected = this.options.find(o => o.v == val);
                                            if(selected) this.label = selected.l;
                                        });
                                    },
                                    select(opt) { 
                                        this.value = opt.v; 
                                        this.open = false; 
                                    }
                                }" 
                                class="relative group" @click.away="open = false">
                                
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                    <i data-lucide="school" class="w-4 h-4 text-slate-400 transition-colors" :class="open ? 'text-navy-800 dark:text-gold-500' : ''"></i>
                                </div>
                                
                                <input type="hidden" name="schedules[new_\${index}][classroom_id]" x-model="value" required>
                                
                                <button type="button" @click="open = !open"
                                        class="w-full pl-9 pr-8 py-2.5 bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700 border-2 rounded-lg text-sm font-medium focus:outline-none transition-all text-left flex items-center justify-between"
                                        :class="open ? 'border-navy-800 dark:border-gold-500 text-navy-800 dark:text-gold-400 shadow-sm' : 'border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200'">
                                    <span x-text="label" class="block truncate"></span>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180 text-navy-800 dark:text-gold-500' : ''"></i>
                                    </div>
                                </button>
                                
                                <div x-show="open" style="display: none;"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 translate-y-2"
                                     class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl shadow-xl overflow-hidden">
                                    <ul class="max-h-60 overflow-y-auto p-1.5 space-y-0.5 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                                        <template x-for="opt in options" :key="opt.v">
                                            <li>
                                                <button type="button" @click="select(opt)" 
                                                        class="w-full text-left px-3 py-2 text-sm font-medium rounded-md transition-colors flex items-center gap-2"
                                                        :class="value == opt.v ? 'bg-navy-50 text-navy-800 dark:bg-navy-900/50 dark:text-gold-400' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'">
                                                    <svg class="w-3.5 h-3.5 flex-shrink-0" :class="value == opt.v ? 'opacity-100' : 'opacity-0'" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                    <span x-text="opt.l" class="truncate"></span>
                                                </button>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Jam Pelajaran -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Jam Pelajaran</label>
                            <div x-data="{ 
                                    open: false, 
                                    value: '1', 
                                    label: 'Jam 1',
                                    options: ${JSON.stringify(periodOptions).replace(/"/g, '&quot;')},
                                    init() {
                                        this.$watch('value', val => {
                                            let selected = this.options.find(o => o.v == val);
                                            if(selected) this.label = selected.l;
                                        });
                                    },
                                    select(opt) { 
                                        this.value = opt.v; 
                                        this.open = false; 
                                    }
                                }" 
                                class="relative group" @click.away="open = false">
                                
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                    <i data-lucide="clock-4" class="w-4 h-4 text-slate-400 transition-colors" :class="open ? 'text-navy-800 dark:text-gold-500' : ''"></i>
                                </div>
                                
                                <input type="hidden" name="schedules[new_\${index}][period]" x-model="value" required>
                                
                                <button type="button" @click="open = !open"
                                        class="w-full pl-9 pr-8 py-2.5 bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700 border-2 rounded-lg text-sm font-medium focus:outline-none transition-all text-left flex items-center justify-between"
                                        :class="open ? 'border-navy-800 dark:border-gold-500 text-navy-800 dark:text-gold-400 shadow-sm' : 'border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200'">
                                    <span x-text="label" class="block truncate"></span>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180 text-navy-800 dark:text-gold-500' : ''"></i>
                                    </div>
                                </button>
                                
                                <div x-show="open" style="display: none;"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 translate-y-2"
                                     class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl shadow-xl overflow-hidden">
                                    <ul class="max-h-60 overflow-y-auto p-1.5 space-y-0.5 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                                        <template x-for="opt in options" :key="opt.v">
                                            <li>
                                                <button type="button" @click="select(opt)" 
                                                        class="w-full text-left px-3 py-2 text-sm font-medium rounded-md transition-colors flex items-center gap-2"
                                                        :class="value == opt.v ? 'bg-navy-50 text-navy-800 dark:bg-navy-900/50 dark:text-gold-400' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'">
                                                    <svg class="w-3.5 h-3.5 flex-shrink-0" :class="value == opt.v ? 'opacity-100' : 'opacity-0'" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                    <span x-text="opt.l" class="truncate"></span>
                                                </button>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                        <!-- Jam Masuk -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Jam Masuk</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i data-lucide="clock" class="w-4 h-4 text-slate-400"></i>
                                </div>
                                <input type="time" name="schedules[new_\${index}][start_time]" required
                                       value="07:00"
                                       class="w-full pl-9 pr-3 py-2.5 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-600 rounded-lg text-sm focus:outline-none focus:border-navy-800 dark:focus:border-gold-500 transition-all">
                            </div>
                        </div>

                        <!-- Jam Pulang -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Jam Pulang</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i data-lucide="clock" class="w-4 h-4 text-slate-400"></i>
                                </div>
                                <input type="time" name="schedules[new_\${index}][end_time]" required
                                       value="15:00"
                                       class="w-full pl-9 pr-3 py-2.5 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-600 rounded-lg text-sm focus:outline-none focus:border-navy-800 dark:focus:border-gold-500 transition-all">
                            </div>
                        </div>

                        <!-- Mata Pelajaran -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Mata Pelajaran (Opsional)</label>
                            <div x-data="{ 
                                    open: false, 
                                    value: '', 
                                    label: 'Pilih Mapel',
                                    options: ${JSON.stringify(subjectOptions).replace(/"/g, '&quot;')},
                                    init() {
                                        this.$watch('value', val => {
                                            let selected = this.options.find(o => o.v == val);
                                            if(selected) this.label = selected.l;
                                        });
                                    },
                                    select(opt) { 
                                        this.value = opt.v; 
                                        this.open = false; 
                                    }
                                }" 
                                class="relative group" @click.away="open = false">
                                
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                    <i data-lucide="book-open" class="w-4 h-4 text-slate-400 transition-colors" :class="open ? 'text-navy-800 dark:text-gold-500' : ''"></i>
                                </div>
                                
                                <input type="hidden" name="schedules[new_\${index}][subject_id]" x-model="value">
                                
                                <button type="button" @click="open = !open"
                                        class="w-full pl-9 pr-8 py-2.5 bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700 border-2 rounded-lg text-sm font-medium focus:outline-none transition-all text-left flex items-center justify-between"
                                        :class="open ? 'border-navy-800 dark:border-gold-500 text-navy-800 dark:text-gold-400 shadow-sm' : 'border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200'">
                                    <span x-text="label" class="block truncate"></span>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180 text-navy-800 dark:text-gold-500' : ''"></i>
                                    </div>
                                </button>
                                
                                <div x-show="open" style="display: none;"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 translate-y-2"
                                     class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl shadow-xl overflow-hidden">
                                    <ul class="max-h-60 overflow-y-auto p-1.5 space-y-0.5 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                                        <template x-for="opt in options" :key="opt.v">
                                            <li>
                                                <button type="button" @click="select(opt)" 
                                                        class="w-full text-left px-3 py-2 text-sm font-medium rounded-md transition-colors flex items-center gap-2"
                                                        :class="value == opt.v ? 'bg-navy-50 text-navy-800 dark:bg-navy-900/50 dark:text-gold-400' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'">
                                                    <svg class="w-3.5 h-3.5 flex-shrink-0" :class="value == opt.v ? 'opacity-100' : 'opacity-0'" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                    <span x-text="opt.l" class="truncate"></span>
                                                </button>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="button" onclick="this.closest('.schedule-row').remove()" 
                                class="px-3 py-1.5 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg text-xs font-semibold hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors flex items-center gap-1">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            Hapus Baris
                        </button>
                    </div>
                `;

                container.appendChild(div);
                if (window.lucide) lucide.createIcons();
            }

            document.addEventListener('DOMContentLoaded', () => {
                if (window.lucide) lucide.createIcons();

                // Form Validation on Submit
                const form = document.getElementById('schedule-form');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        // Check existing schedules (only if not deleted)
                        const cards = this.querySelectorAll('.schedule-card');
                        for (let card of cards) {
                            const deleteInput = card.querySelector('.delete-input');
                            if (deleteInput && deleteInput.value) {
                                // Skip deleted schedules
                                continue;
                            }

                            const startInput = card.querySelector('.schedule-start');
                            const endInput = card.querySelector('.schedule-end');

                            if (startInput && endInput && startInput.value && endInput.value) {
                                if (endInput.value <= startInput.value) {
                                    alert('Jam pulang harus lebih besar dari jam masuk untuk semua jadwal!');
                                    e.preventDefault();
                                    return;
                                }
                            }
                        }

                        // Check newly added rows
                        const rows = this.querySelectorAll('.schedule-row');
                        for (let row of rows) {
                            const startInput = row.querySelector('input[name*="[start_time]"]');
                            const endInput = row.querySelector('input[name*="[end_time]"]');

                            if (startInput && endInput && startInput.value && endInput.value) {
                                if (endInput.value <= startInput.value) {
                                    alert('Jam pulang harus lebih besar dari jam masuk untuk jadwal baru!');
                                    e.preventDefault();
                                    return;
                                }
                            }
                        }
                    });
                }
            });
        </script>

        <style>
            .fade-in {
                animation: fadeIn 0.5s ease-out forwards;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
@endsection