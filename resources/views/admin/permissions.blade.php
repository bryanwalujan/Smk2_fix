
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manajemen Izin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100 font-sans antialiased">
    @include('layouts.navbar-admin')

    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <!-- Header -->
        <header class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 md:text-3xl">Manajemen Izin</h1>
        </header>

        <!-- Messages -->
        <section class="mb-8">
            @if (session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-green-500 mr-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            @endif
            
            @if (session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-red-500 mr-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            @endif
        </section>

        <!-- Role and Permission Management -->
        @can('manage_roles')
            <section class="bg-white rounded-xl shadow-sm p-6" x-data="permissionManager()">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Kelola Izin Pengguna</h2>
                <p class="text-sm text-gray-600 mb-6">Kelola izin untuk role teacher dan student. Klik tombol izin untuk menambah atau menghapus izin.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach (['teacher', 'student'] as $roleName)
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ ucfirst($roleName) }} Permissions</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($permissions as $permission)
                                    <button type="button" 
                                        x-bind:class="isPermissionActive('{{ $roleName }}', '{{ $permission->name }}') ? 'bg-green-500 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'"
                                        class="px-3 py-1 rounded-md text-sm font-medium transition"
                                        x-on:click="togglePermission('{{ $roleName }}', '{{ $permission->name }}')">
                                        {{ ucfirst(str_replace('_', ' ', $permission->name)) }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                <script>
                    function permissionManager() {
                        return {
                            permissions: @json($roles->mapWithKeys(function ($role) {
                                return [$role->name => $role->permissions->pluck('name')->toArray()];
                            })),
                            isPermissionActive(role, permission) {
                                return this.permissions[role].includes(permission);
                            },
                            async togglePermission(role, permission) {
                                try {
                                    const response = await fetch('{{ route('admin.permissions.toggle') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                        },
                                        body: JSON.stringify({ role, permission }),
                                    });
                                    const data = await response.json();
                                    if (data.success) {
                                        if (this.permissions[role].includes(permission)) {
                                            this.permissions[role] = this.permissions[role].filter(p => p !== permission);
                                        } else {
                                            this.permissions[role].push(permission);
                                        }
                                        alert(data.message);
                                    } else {
                                        alert(data.message);
                                    }
                                } catch (error) {
                                    console.error('Toggle permission error:', error);
                                    alert('Gagal memperbarui izin: ' + error.message);
                                }
                            }
                        }
                    }
                </script>
            </section>
        @endcan
    </div>
</body>
</html>
