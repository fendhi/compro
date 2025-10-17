<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-red-50 to-orange-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
        <div class="bg-white rounded-2xl shadow-2xl p-8 md:p-12 text-center">
            <!-- Icon -->
            <div class="mb-6">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-red-100 rounded-full">
                    <i class="fas fa-lock text-5xl text-red-500"></i>
                </div>
            </div>

            <!-- Title -->
            <h1 class="text-6xl font-bold text-gray-800 mb-4">403</h1>
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Akses Ditolak</h2>
            
            <!-- Message -->
            <p class="text-gray-600 mb-8 text-lg">
                {{ $exception->getMessage() ?: 'Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.' }}
            </p>

            <!-- Additional Info -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-8">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-red-500 mt-1"></i>
                    <div class="text-left text-sm text-gray-700">
                        <p class="font-semibold mb-2">Informasi:</p>
                        <ul class="space-y-1">
                            <li>• Halaman ini hanya dapat diakses oleh <strong>Administrator</strong></li>
                            <li>• Anda login sebagai: <strong>{{ Auth::check() ? ucfirst(Auth::user()->role) : 'Guest' }}</strong></li>
                            <li>• Silakan hubungi administrator jika Anda merasa ini adalah kesalahan</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all shadow-lg hover:shadow-xl transform hover:scale-105">
                    <i class="fas fa-home"></i>
                    <span>Kembali ke Dashboard</span>
                </a>
                <button onclick="history.back()" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all">
                    <i class="fas fa-arrow-left"></i>
                    <span>Halaman Sebelumnya</span>
                </button>
            </div>

            <!-- User Info -->
            @auth
            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="flex items-center justify-center gap-3 text-sm text-gray-600">
                    <i class="fas fa-user-circle text-lg"></i>
                    <span>Login sebagai: <strong>{{ Auth::user()->name }}</strong> ({{ ucfirst(Auth::user()->role) }})</span>
                </div>
            </div>
            @endauth
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 text-sm text-gray-600">
            <p>Butuh bantuan? <a href="mailto:admin@orindpos.com" class="text-blue-600 hover:text-blue-700 underline">Hubungi Administrator</a></p>
        </div>
    </div>
</body>
</html>
