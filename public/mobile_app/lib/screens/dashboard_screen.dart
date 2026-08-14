import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:url_launcher/url_launcher.dart';
import '../api_service.dart';
import 'pagos_screen.dart';
import 'seguridad_screen.dart';
import 'gestion_screen.dart';
import 'comunidad_screen.dart';
import 'login_screen.dart';
import 'detalle_screens.dart';

class DashboardScreen extends StatefulWidget {
  final String token;
  final String vecinoNombre;
  final String departamentoNumero;
  final String condominioNombre;

  const DashboardScreen({
    Key? key,
    required this.token,
    required this.vecinoNombre,
    required this.departamentoNumero,
    required this.condominioNombre,
  }) : super(key: key);

  @override
  _DashboardScreenState createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  int _currentIndex = 0;
  String _montoDeudaStr = 'S/ 0.00';
  bool _hasDeuda = false;
  bool _isLoading = true;
  bool _isSosActivo = false;

  @override
  void initState() {
    super.initState();
    _cargarDashboardData();
  }

  void _cargarDashboardData() async {
    final res = await ApiService.getDashboard(widget.token);
    if (res['success'] == true) {
      setState(() {
        _montoDeudaStr = res['monto_formateado'] ?? 'S/ 0.00';
        _hasDeuda = (res['deuda_acumulada'] ?? 0) > 0;
        _isLoading = false;
      });
    } else {
      setState(() {
        _isLoading = false;
      });
    }
  }

  // Modal de Confirmación S.O.S.
  void _confirmarYDispararSOS() {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        backgroundColor: const Color(0xFF0F172A),
        title: const Text(
          '🚨 ALERTA DE EMERGENCIA S.O.S.',
          style: TextStyle(color: Colors.redAccent, fontWeight: FontWeight.bold, fontSize: 18),
        ),
        content: const Text(
          '¿Estás seguro de enviar una señal de auxilio a la Portería del edificio?\n\nEl vigilante recibirá una alerta sonora y visual en tiempo real.',
          style: TextStyle(color: Colors.white, fontSize: 13),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx),
            child: const Text('Cancelar', style: TextStyle(color: Colors.white54)),
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: Colors.redAccent),
            onPressed: () {
              Navigator.pop(ctx);
              _dispararSOS();
            },
            child: const Text('🚨 SÍ, DISPARAR S.O.S.', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
          ),
        ],
      ),
    );
  }

 void _dispararSOS() async {
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        backgroundColor: Colors.redAccent,
        duration: Duration(seconds: 2),
        content: Row(
          children: [
            SizedBox(width: 16, height: 16, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2)),
            SizedBox(width: 10),
            Text('🚨 Enviando Alerta S.O.S. a Portería...', style: TextStyle(fontWeight: FontWeight.bold)),
          ],
        ),
      ),
    );

    final result = await ApiService.dispararSOS(widget.token);

    if (result['success'] == true) {
      ScaffoldMessenger.of(context).hideCurrentSnackBar();
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          backgroundColor: Colors.redAccent,
          duration: const Duration(seconds: 5),
          content: Row(
            children: [
              const Icon(Icons.warning, color: Colors.white, size: 24),
              const SizedBox(width: 10),
              Expanded(
                child: Text(
                  result['message'] ?? '¡ALERTA S.O.S. ENVIADA A PORTERÍA!',
                  style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13),
                ),
              ),
            ],
          ),
        ),
      );
    } else {
      ScaffoldMessenger.of(context).hideCurrentSnackBar();
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          backgroundColor: Colors.orange,
          content: Text(result['message'] ?? 'Error al conectar con Portería.'),
        ),
      );
    }
  }

  void _abrirSiriShortcut() async {
    final Uri url = Uri.parse("https://www.icloud.com/shortcuts/851684fa88d9489a8c12a7776f8eabf2");
    if (await canLaunchUrl(url)) {
      await launchUrl(url, mode: LaunchMode.externalApplication);
    }
  }

  void _cerrarSesion() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.clear();

    Navigator.of(context).pushReplacement(
      MaterialPageRoute(builder: (context) => const LoginScreen()),
    );
  }

  void _abrirPantalla(Widget screen) {
    Navigator.of(context).push(
      MaterialPageRoute(builder: (context) => screen),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF060913),
      appBar: AppBar(
        backgroundColor: const Color(0xFF0F172A),
        elevation: 0,
        title: Row(
          children: [
            Image.asset('assets/logo.png', height: 28, errorBuilder: (c, e, s) => const Icon(Icons.apartment, color: Colors.blueAccent)),
            const SizedBox(width: 8),
            const Text('LIVO', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.notifications_none, color: Colors.white),
            onPressed: () {},
          ),
          PopupMenuButton<String>(
            onSelected: (value) {
              if (value == 'logout') _cerrarSesion();
            },
            icon: CircleAvatar(
              backgroundColor: const Color(0xFF0284C7),
              child: Text(
                widget.vecinoNombre.isNotEmpty ? widget.vecinoNombre[0].toUpperCase() : 'V',
                style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
              ),
            ),
            itemBuilder: (context) => [
              PopupMenuItem(
                enabled: false,
                child: Text('Hola, ${widget.vecinoNombre}', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
              ),
              PopupMenuItem(
                enabled: false,
                child: Text('Dpto. ${widget.departamentoNumero} - ${widget.condominioNombre}', style: const TextStyle(color: Colors.white70, fontSize: 12)),
              ),
              const PopupMenuDivider(),
              const PopupMenuItem(
                value: 'logout',
                child: Row(
                  children: [
                    Icon(Icons.exit_to_app, color: Colors.redAccent, size: 18),
                    SizedBox(width: 8),
                    Text('Cerrar Sesión', style: TextStyle(color: Colors.redAccent, fontWeight: FontWeight.bold)),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(width: 14),
        ],
      ),
      body: IndexedStack(
        index: _currentIndex,
        children: [
          _buildEscritorioTab(),
          PagosScreen(token: widget.token),
          SeguridadScreen(token: widget.token),
          GestionScreen(token: widget.token),
          ComunidadScreen(token: widget.token),
        ],
      ),
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: _currentIndex,
        onTap: (index) => setState(() => _currentIndex = index),
        backgroundColor: const Color(0xFF0F172A),
        selectedItemColor: const Color(0xFF0284C7),
        unselectedItemColor: Colors.white54,
        type: BottomNavigationBarType.fixed,
        items: const [
          BottomNavigationBarItem(icon: Icon(Icons.home), label: 'Escritorio'),
          BottomNavigationBarItem(icon: Icon(Icons.account_balance_wallet), label: 'Finanzas'),
          BottomNavigationBarItem(icon: Icon(Icons.security), label: 'Seguridad'),
          BottomNavigationBarItem(icon: Icon(Icons.settings), label: 'Gestión'),
          BottomNavigationBarItem(icon: Icon(Icons.people), label: 'Comunidad'),
        ],
      ),
    );
  }

  Widget _buildEscritorioTab() {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(16.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // 1. Tarjeta Bienvenida Siri
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: const Color(0xFF0F172A),
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: Colors.white12),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('👋 ¡Bienvenido, ${widget.vecinoNombre}!', style: const TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.bold)),
                const SizedBox(height: 4),
                Text('Departamento ${widget.departamentoNumero} — ${widget.condominioNombre}', style: const TextStyle(color: Color(0xFF38BDF8), fontSize: 13, fontWeight: FontWeight.w600)),
                const SizedBox(height: 14),
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton.icon(
                    onPressed: _abrirSiriShortcut,
                    icon: const Icon(Icons.phone_iphone, color: Colors.white, size: 18),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF0284C7),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                    ),
                    label: const Text('📱 Instalar Atajo de Voz Siri (1 Clic)', style: TextStyle(color: Colors.white, fontSize: 13, fontWeight: FontWeight.bold)),
                  ),
                ),
              ],
            ),
          ),

          const SizedBox(height: 14),

          // 2. Tarjeta Estado de Cuenta
          Container(
            width: double.infinity,
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              gradient: LinearGradient(
                colors: _hasDeuda ? [const Color(0xFFDC2626), const Color(0xFF991B1B)] : [const Color(0xFF16A34A), const Color(0xFF15803D)],
              ),
              borderRadius: BorderRadius.circular(16),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text('ESTADO DE CUENTA', style: TextStyle(color: Colors.white70, fontSize: 11, fontWeight: FontWeight.bold)),
                const SizedBox(height: 4),
                Text(_montoDeudaStr, style: const TextStyle(color: Colors.white, fontSize: 28, fontWeight: FontWeight.bold)),
                const SizedBox(height: 4),
                Text(
                  _hasDeuda ? '⚠️ Tienes cuotas pendientes de pago' : '🟢 Estás al día en tus pagos',
                  style: const TextStyle(color: Colors.amberAccent, fontSize: 12, fontWeight: FontWeight.bold),
                ),
              ],
            ),
          ),

          const SizedBox(height: 14),

          // 3. Botón S.O.S. de Pánico
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: const Color(0xFF1E1B1E),
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: Colors.red.withOpacity(0.4)),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: const [
                    Icon(Icons.campaign, color: Colors.redAccent, size: 20),
                    SizedBox(width: 8),
                    Text('BOTÓN DE PÁNICO S.O.S.', style: TextStyle(color: Colors.redAccent, fontWeight: FontWeight.bold, fontSize: 12)),
                  ],
                ),
                const SizedBox(height: 12),
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton.icon(
                    onPressed: _dispararSOS,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.redAccent,
                      padding: const EdgeInsets.symmetric(vertical: 14),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                    ),
                    icon: const Icon(Icons.warning, color: Colors.white, size: 20),
                    label: Text(
                      _isSosActivo ? '🚨 S.O.S. ACTIVADO' : '🚨 DISPARAR S.O.S. (1 TOQUE)',
                      style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 14),
                    ),
                  ),
                ),
              ],
            ),
          ),

          const SizedBox(height: 20),

          // 4. Servicios del Condominio Grid
          const Text('SERVICIOS DEL CONDOMINIO', style: TextStyle(color: Colors.white70, fontSize: 11, fontWeight: FontWeight.bold, letterSpacing: 1)),
          const SizedBox(height: 10),

          GridView.count(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            crossAxisCount: 2,
            crossAxisSpacing: 12,
            mainAxisSpacing: 12,
            childAspectRatio: 1.2,
            children: [
              _buildServiceCard('Mis Pagos', 'Recibos y vouchers', Icons.account_balance_wallet, const Color(0xFF0284C7), () {
                setState(() => _currentIndex = 1);
              }),
              _buildServiceCard('Avisos', 'Comunicados', Icons.campaign, const Color(0xFF10B981), () {
                setState(() => _currentIndex = 4);
              }),
             _buildServiceCard('Mascotas', 'Padrón de mascotas', Icons.pets, const Color(0xFFF59E0B), () {
  _abrirPantalla(MascotasListScreen(token: widget.token));
}),
_buildServiceCard('Reclamos', 'Sugerencias', Icons.chat_bubble_outline, const Color(0xFF8B5CF6), () {
  _abrirPantalla(ReclamosListScreen(token: widget.token));
}),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildServiceCard(String title, String subtitle, IconData icon, Color color, VoidCallback onTap) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(16),
      child: Container(
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: const Color(0xFF0F172A),
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: Colors.white12),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(color: color.withOpacity(0.2), borderRadius: BorderRadius.circular(10)),
              child: Icon(icon, color: color, size: 24),
            ),
            const SizedBox(height: 10),
            Text(title, style: const TextStyle(color: Colors.white, fontSize: 14, fontWeight: FontWeight.bold)),
            Text(subtitle, style: const TextStyle(color: Colors.white54, fontSize: 11)),
          ],
        ),
      ),
    );
  }
}