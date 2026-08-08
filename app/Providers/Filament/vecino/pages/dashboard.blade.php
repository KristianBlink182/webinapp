<x-filament-panels::page>
    <div class="space-y-8 font-sans">

        <!-- 🚀 HERO / TARJETAS NEÓN DE ALTO IMPACTO -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            
            <!-- Card 1: Deuda / Estado de Cuenta (Gradiente Magenta/Neón) -->
            <div class="relative overflow-hidden rounded-3xl p-6 bg-gradient-to-br from-fuchsia-600 via-purple-600 to-indigo-800 text-white shadow-2xl shadow-purple-900/40 border border-purple-400/20">
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
                
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-purple-200">Estado de Cuenta</span>
                        <h2 class="text-3xl font-black mt-2 text-white">S/ {{ number_format($deudaTotal ?? 0, 2) }}</h2>
                        <p class="text-xs mt-1 text-purple-200 font-medium">
                            {{ ($deudaTotal ?? 0) > 0 ? '⚠️ Deuda pendiente' : '🎉 ¡Estás al día!' }}
                        </p>
                    </div>
                    <span class="p-3 bg-white/10 rounded-2xl backdrop-blur-md">
                        <x-heroicon-m-banknotes class="w-6 h-6 text-white" />
                    </span>
                </div>

                @if(!empty($pagoPendiente) && !empty($condominio))
                    <div class="mt-6 pt-4 border-t border-white/15 flex items-center justify-between relative z-10">
                        <span class="text-xs text-purple-200 truncate max-w-[140px]">{{ $pagoPendiente->concepto }}</span>
                        <a href="/vecino/edificio/{{ $condominio->nombre }}/pagos" class="px-4 py-2 bg-white text-purple-950 font-black text-xs rounded-xl shadow-lg hover:bg-purple-100 transition transform active:scale-95">
                            PAGAR AHORA
                        </a>
                    </div>
                @endif
            </div>

            <!-- Card 2: Comunicados Oficiales (Gradiente Cian/Azul) -->
            <div class="relative overflow-hidden rounded-3xl p-6 bg-gradient-to-br from-cyan-500 via-teal-600 to-blue-800 text-white shadow-2xl shadow-cyan-900/30 border border-cyan-400/20">
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-cyan-100">Último Comunicado</span>
                        <h3 class="text-lg font-bold mt-2 text-white truncate max-w-[200px]">
                            {{ !empty($ultimoAviso) ? $ultimoAviso->titulo : 'Sin comunicados' }}
                        </h3>
                        <p class="text-xs mt-1 text-cyan-200">
                            {{ !empty($ultimoAviso) ? $ultimoAviso->created_at->diffForHumans() : 'No hay novedades' }}
                        </p>
                    </div>
                    <span class="p-3 bg-white/10 rounded-2xl backdrop-blur-md">
                        <x-heroicon-m-megaphone class="w-6 h-6 text-white" />
                    </span>
                </div>
                
                @if(!empty($condominio))
                    <div class="mt-6 pt-4 border-t border-white/15 relative z-10">
                        <a href="/vecino/edificio/{{ $condominio->nombre }}/comunicados" class="text-xs font-bold text-cyan-100 hover:text-white flex items-center gap-1">
                            Ver todos los comunicados →
                        </a>
                    </div>
                @endif
            </div>

            <!-- Card 3: Mi Departamento (Gradiente Oscuro/Azul Noche) -->
            <div class="relative overflow-hidden rounded-3xl p-6 bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-950 text-white shadow-2xl border border-slate-700/60">
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Mi Unidad</span>
                        <h2 class="text-2xl font-black mt-2 text-white">Dpto. {{ auth()->user()->departamento?->numero ?? 'N/A' }}</h2>
                        <p class="text-xs mt-1 text-indigo-400 font-medium">{{ auth()->user()->departamento?->condominio?->nombre ?? 'Edificio' }}</p>
                    </div>
                    <span class="p-3 bg-indigo-500/20 text-indigo-400 rounded-2xl">
                        <x-heroicon-m-home-modern class="w-6 h-6" />
                    </span>
                </div>
            </div>

        </div>

        <!-- 📱 MENÚ DE ACCESOS RÁPIDOS MÓVIL / DESKTOP (BOTONES TIPO APP) -->
        <div>
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Servicios del Condominio</h3>
            
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-4">

                @php
                    $condoNombre = auth()->user()->departamento?->condominio?->nombre;
                @endphp

                @if($condoNombre)
                    <!-- Botón 1: Mis Pagos -->
                    <a href="/vecino/edificio/{{ $condoNombre }}/pagos" class="group relative bg-gray-900/90 hover:bg-gray-800 border border-gray-800 hover:border-purple-500/50 rounded-2xl p-5 transition-all duration-300 shadow-lg flex flex-col items-center text-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-purple-500/10 text-purple-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <x-heroicon-o-banknotes class="w-6 h-6" />
                        </div>
                        <div>
                            <span class="font-bold text-sm text-white block">Mis Pagos</span>
                            <span class="text-[10px] text-gray-400">Recibos y vouchers</span>
                        </div>
                    </a>

                    <!-- Botón 2: Muro de Avisos -->
                    <a href="/vecino/edificio/{{ $condoNombre }}/comunicados" class="group relative bg-gray-900/90 hover:bg-gray-800 border border-gray-800 hover:border-cyan-500/50 rounded-2xl p-5 transition-all duration-300 shadow-lg flex flex-col items-center text-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <x-heroicon-o-megaphone class="w-6 h-6" />
                        </div>
                        <div>
                            <span class="font-bold text-sm text-white block">Avisos</span>
                            <span class="text-[10px] text-gray-400">Comunicados oficiales</span>
                        </div>
                    </a>

                    <!-- Botón 3: Mis Mascotas -->
                    <a href="/vecino/edificio/{{ $condoNombre }}/mascotas" class="group relative bg-gray-900/90 hover:bg-gray-800 border border-gray-800 hover:border-pink-500/50 rounded-2xl p-5 transition-all duration-300 shadow-lg flex flex-col items-center text-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-pink-500/10 text-pink-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <x-heroicon-o-heart class="w-6 h-6" />
                        </div>
                        <div>
                            <span class="font-bold text-sm text-white block">Mascotas</span>
                            <span class="text-[10px] text-gray-400">Registro de mascotas</span>
                        </div>
                    </a>

                    <!-- Botón 4: Reclamos -->
                    <a href="/vecino/edificio/{{ $condoNombre }}/reclamos" class="group relative bg-gray-900/90 hover:bg-gray-800 border border-gray-800 hover:border-emerald-500/50 rounded-2xl p-5 transition-all duration-300 shadow-lg flex flex-col items-center text-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <x-heroicon-o-chat-bubble-bottom-center-text class="w-6 h-6" />
                        </div>
                        <div>
                            <span class="font-bold text-sm text-white block">Reclamos</span>
                            <span class="text-[10px] text-gray-400">Reportes a la directiva</span>
                        </div>
                    </a>
                @endif

            </div>
        </div>

    </div>
</x-filament-panels::page>