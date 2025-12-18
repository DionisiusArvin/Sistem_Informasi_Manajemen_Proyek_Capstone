<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Detail Tugas: {{ $task->name }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ showForm: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6">
                    {{-- Header Kartu --}}
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Daftar Tugas Harian</h3>
                        @if(auth()->user()->role === 'kepala_divisi' && $task->divisions->contains('id', auth()->user()->division_id))
                            <button @click="showForm = !showForm" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold shadow-md hover:bg-blue-700 transition">
                                <span x-show="!showForm">+ Tambah Tugas Harian</span>
                                <span x-show="showForm" style="display: none;">Batal</span>
                            </button>
                        @endif
                    </div>

                    {{-- Form Tambah Tugas (Bisa disembunyikan) --}}
                    <div x-show="showForm" x-transition class="border-b dark:border-gray-700 mb-6 pb-6">
                        <form method="POST" action="{{ route('tasks.dailytasks.store', $task->id) }}">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="md:col-span-2">
                                    <x-input-label for="name" value="Nama Tugas Harian" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required />
                                </div>
                                <div>
                                    <x-input-label for="due_date" value="Batas Waktu" />
                                    <x-text-input id="due_date" class="block mt-1 w-full" type="date" name="due_date" :value="old('due_date')" required />
                                </div>
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <x-primary-button>Simpan Tugas</x-primary-button>
                            </div>
                        </form>
                    </div>
                    
                    {{-- Tabel Daftar Tugas --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white dark:bg-gray-800">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm text-gray-600 dark:text-gray-300">Tugas & Catatan</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm text-gray-600 dark:text-gray-300">Pekerja</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm text-gray-600 dark:text-gray-300">Progress</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm text-gray-600 dark:text-gray-300">File/Link</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm text-gray-600 dark:text-gray-300">Status</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm text-gray-600 dark:text-gray-300">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 dark:text-gray-400 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($task->dailyTasks as $dailyTask)
                                    <tr x-data="{ showRevisionModal: false, showUploadModal: false }" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="py-4 px-4">
                                            <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $dailyTask->name }}</p>
                                            @php
                                                $lastUpload = $dailyTask->activities->where('activity_type', 'upload_pekerjaan')->last();
                                            @endphp
                                            @if($lastUpload && $lastUpload->notes)
                                                <p class="text-sm text-gray-500 mt-1 italic">Catatan: "{{ $lastUpload->notes }}"</p>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4">{{ $dailyTask->assignedToStaff->name ?? 'Belum Diambil' }}</td>
                                        <td class="py-4 px-4">
                                            <div class="flex items-center">
                                                <span class="mr-2 text-sm">{{ $dailyTask->status_based_progress }}%</span>
                                                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $dailyTask->status_based_progress }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            @if($lastUpload)
                                                <div class="flex items-center space-x-4 text-sm">
                                                    {{-- Tampilkan link file jika ada --}}
                                                    @if($lastUpload->file_path)
                                                        <a href="{{ asset('storage/' . $lastUpload->file_path) }}" target="_blank" class="font-medium text-green-600 hover:underline">
                                                            Lihat File
                                                        </a>
                                                    @endif

                                                    {{-- Tampilkan link URL jika ada --}}
                                                    @if($lastUpload->link_url)
                                                        <a href="{{ $lastUpload->link_url }}" target="_blank" class="font-medium text-blue-600 hover:underline">
                                                            Lihat Link
                                                        </a>
                                                    @endif
                                                </div>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 font-semibold">
                                            @if ($dailyTask->status === 'Selesai')
                                                <span class="text-green-600">
                                                    Selesai
                                                    <span class="font-normal italic capitalize">
                                                        ({{ str_replace('_', ' ', $dailyTask->completion_status) }})
                                                    </span>
                                                </span>
                                            @else
                                                <span class="
                                                    @if($dailyTask->status === 'Menunggu Validasi') text-orange-500 @endif
                                                    @if($dailyTask->status === 'Revisi') text-red-600 @endif
                                                    @if($dailyTask->status === 'Belum Dikerjakan') text-gray-500 @endif
                                                    @if($dailyTask->status === 'Tersedia') text-blue-500 @endif
                                                ">
                                                    {{ $dailyTask->status }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4">
                                            @if(auth()->user()->role === 'kepala_divisi')
                                                @if(!$dailyTask->assignedToStaff)
                                                    <button @click="showUploadModal = true" type="button" class="text-sm text-green-600 hover:underline font-semibold">Kerjakan Sendiri</button>
                                                @elseif($dailyTask->status === 'Menunggu Validasi')
                                                    <div class="flex items-center space-x-2">
                                                        <form action="{{ route('dailytasks.approve', $dailyTask->id) }}" method="POST">
                                                            @csrf @method('PATCH')
                                                            <button type="submit" class="text-sm text-green-600 hover:underline font-semibold">Approve</button>
                                                        </form>
                                                        <button @click="showRevisionModal = true" type="button" class="text-sm text-red-600 hover:underline font-semibold">Revisi</button>
                                                    </div>
                                                @elseif($dailyTask->assigned_to_staff_id === auth()->id())
                                                    <a href="{{ route('dailytasks.upload.form', $dailyTask->id) }}" class="text-blue-600 hover:underline">Upload</a>
                                                @else
                                                    -
                                                @endif
                                            @endif

                                            <div x-show="showRevisionModal" @keydown.escape.window="showRevisionModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
                                                <div @click.away="showRevisionModal = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Catatan Revisi untuk: {{ $dailyTask->name }}</h3>
                                                    <form action="{{ route('dailytasks.reject', $dailyTask->id) }}" method="POST">
                                                        @csrf @method('PATCH')
                                                        <textarea name="revision_notes" rows="4" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Jelaskan bagian yang perlu direvisi..." required></textarea>
                                                        <div class="mt-4 flex justify-end space-x-2">
                                                            <button type="button" @click="showRevisionModal = false" class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                                                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Kirim Revisi</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>

                                            <div x-show="showUploadModal" @keydown.escape.window="showUploadModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
                                                <div @click.away="showUploadModal = false" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md">
                                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Kerjakan & Upload: {{ $dailyTask->name }}</h3>
                                                    <form action="{{ route('dailytasks.claim_and_upload', $dailyTask->id) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="space-y-4">
                                                            {{-- Input Link URL (Wajib) --}}
                                                            <div>
                                                                <x-input-label for="link_url_{{ $dailyTask->id }}" value="Cantumkan Link (Wajib)" />
                                                                <x-text-input id="link_url_{{ $dailyTask->id }}" class="block mt-1 w-full" type="url" name="link_url" placeholder="https://..." required />
                                                            </div>
                                                            {{-- Input File (Opsional) --}}
                                                            <div>
                                                                <x-input-label for="file_{{ $dailyTask->id }}" value="Upload File (Opsional)" />
                                                                <input id="file_{{ $dailyTask->id }}" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" type="file" name="file">
                                                            </div>
                                                            {{-- Input Catatan (Opsional) --}}
                                                            <div>
                                                                <x-input-label for="notes_{{ $dailyTask->id }}" value="Catatan (Opsional)" />
                                                                <textarea name="notes" id="notes_{{ $dailyTask->id }}" rows="3" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" placeholder="Catatan..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="mt-6 flex justify-end space-x-2">
                                                            <button type="button" @click="showUploadModal = false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 rounded-md">Batal</button>
                                                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">Kirim Pekerjaan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-6 text-gray-500">Belum ada tugas harian.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>