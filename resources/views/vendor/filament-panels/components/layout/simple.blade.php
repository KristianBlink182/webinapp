<x-filament-panels::layout.base :livewire="$livewire ?? null">
    <style>
        /* ESTILOS Y MAQUETACIÓN PREMIUM LIVO (IMAGEN 2) */
        .livo-login-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100vw;
            background-color: #060913;
            color: #f4f4f5;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            overflow-x: hidden;
        }

        .livo-left-banner {
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 4rem;
            background: linear-gradient(135deg, #060913 0%, #0c203b 50%, #060913 100%);
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            position: relative;
        }

        .livo-right-form {
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 3rem 2rem;
            background-color: #060913;
            position: relative;
        }

        /* RESPONSIVO MÓVIL (< 1023px): APARECE LOGO ARRIBA Y TARJETA TIPO APP */
        @media (max-width: 1023px) {
            .livo-left-banner {
                display: none !important;
            }
            .livo-right-form {
                width: 100% !important;
                padding: 2rem 1.25rem;
            }
        }

        /* CARD FLOTANTE CON EFECTO CRISTAL (GLASSMORPHISM) */
        .livo-card {
            background: rgba(12, 22, 38, 0.9);
            border: 1px solid rgba(14, 165, 233, 0.25);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 1.5rem;
            padding: 2.25rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8);
            max-width: 440px;
            width: 100%;
            margin: auto;
        }

        /* ILUMINACIÓN NEÓN CIAN DE FONDO */
        .livo-glow {
            position: absolute;
            width: 400px;
            height: 400px;
            background: rgba(14, 165, 233, 0.18);
            border-radius: 50%;
            filter: blur(110px);
            pointer-events: none;
        }

        .livo-badge {
            display: inline-block;
            padding: 0.35rem 0.85rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #38bdf8;
            background: rgba(14, 165, 233, 0.15);
            border: 1px solid rgba(14, 165, 233, 0.3);
            border-radius: 9999px;
        }
    </style>

    <div class="livo-login-wrapper">
        
        <!-- LADO IZQUIERDO: BANNER DE BIENVENIDA (SOLO PC/LAPTOP) -->
        <div class="livo-left-banner">
            <div class="livo-glow" style="top: -60px; left: -60px;"></div>
            
            <!-- Logo LIVO Protagonista -->
            <div style="position: relative; z-index: 10;">
                <img src="{{ asset('images/logo.png') }}" 
                     alt="LIVO Logo" 
                     style="height: 75px; width: auto; object-fit: contain; filter: drop-shadow(0 0 15px rgba(14, 165, 233, 0.3));">
            </div>

            <!-- Mensaje Principal con Detección Automática por URL -->
            <div style="position: relative; z-index: 10; max-width: 480px; margin: auto 0;">
                <span class="livo-badge">
                    @if(str_contains(request()->url(), 'admin'))
                        PANEL DE ADMINISTRACIÓN
                    @elseif(str_contains(request()->url(), 'master'))
                        PANEL DE SUPERADMIN
                    @elseif(str_contains(request()->url(), 'porteria'))
                        PANEL DE PORTERÍA
                    @else
                        APP DE RESIDENTES
                    @endif
                </span>

                <h1 style="font-size: 2.5rem; font-weight: 800; color: #ffffff; line-height: 1.2; margin-top: 1rem; margin-bottom: 1rem;">
                    Tu condominio en la palma de tu mano
                </h1>
                <p style="color: #94a3b8; font-size: 1rem; line-height: 1.6;">
                    Accede a la plataforma de gestión inteligente de edificios y condominios LIVO.
                </p>
            </div>

            <!-- Footer Izquierdo -->
            <div style="position: relative; z-index: 10; font-size: 0.8rem; color: #64748b;">
                LIVO &copy; {{ date('Y') }} &bull; Administración Inteligente para Edificios
            </div>
        </div>

        <!-- LADO DERECHO: FORMULARIO (MÓVIL Y PC) -->
        <div class="livo-right-form">
            <div class="livo-glow" style="bottom: -60px; right: -60px;"></div>

            <!-- Header Móvil (Solo visible en celulares con Logo) -->
            <div style="text-align: center; margin-bottom: 1.25rem;" class="lg:hidden">
                <img src="{{ asset('images/logo.png') }}" 
                     alt="LIVO" 
                     style="height: 65px; width: auto; margin: 0 auto 0.5rem auto; filter: drop-shadow(0 0 12px rgba(14, 165, 233, 0.3));">
                <p style="font-size: 0.8rem; color: #38bdf8; text-align: center; font-weight: 600; margin-top: 0.25rem;">
                    @if(str_contains(request()->url(), 'admin'))
                        Panel de Administración
                    @elseif(str_contains(request()->url(), 'master'))
                        Panel de Superadmin
                    @elseif(str_contains(request()->url(), 'porteria'))
                        Panel de Portería
                    @else
                        App de Residentes
                    @endif
                </p>
            </div>

            <!-- Card del Formulario -->
            <div class="livo-card">
                
                <div style="margin-bottom: 1.5rem;">
                    <h3 style="font-size: 1.5rem; font-weight: 800; color: #ffffff; margin-bottom: 0.25rem;">Iniciar Sesión</h3>
                    <p style="font-size: 0.875rem; color: #94a3b8;">Ingresa tus credenciales para acceder a tu cuenta.</p>
                </div>

                <!-- Formulario Oficial de Filament -->
                {{ $slot }}

                <!-- Botón de Contactar Soporte -->
                <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid rgba(255, 255, 255, 0.08); text-align: center;">
                    <a href="mailto:soporte@livo.com.pe?subject=Ayuda%20de%20Acceso%20-%20LIVO&body=Hola,%20tengo%20problemas%20para%20ingresar%20a%20mi%20cuenta.%20Mi%20nombre%20es:%20" 
                       style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; color: #94a3b8; text-decoration: none; padding: 0.5rem 1rem; background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 0.75rem;">
                        <svg style="width: 16px; height: 16px; color: #38bdf8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>¿Problemas para entrar? <strong style="color: #38bdf8;">Contactar Soporte</strong></span>
                    </a>
                </div>

            </div>

            <!-- Footer -->
            <div style="text-align: center; font-size: 0.75rem; color: #475569; margin-top: 1rem;">
                LIVO &bull; Plataforma de Gestión
            </div>
        </div>

    </div>
</x-filament-panels::layout.base>