import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:url_launcher/url_launcher.dart';
import '../api_service.dart';
import 'pagos_screen.dart';
import 'seguridad_screen.dart';
import 'gestion_screen.dart';
import 'comunidad_screen.dart';
import 'login_screen.dart';

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
  bool _sosEnviado = false;

  void _dispararSOS() async {
    final result = await ApiService.dispararSOS(widget.token);
    if (result['success'] == true) {
      setState(() => _sosEnviado = true);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(backgroundColor: Colors.green, content: Text(result['message'])),
      );
    }
  }

  void _abrirSiriShortcut() async {
    final Uri url = Uri.parse('https://www.icloud.com/shortcuts/653d6f68abc0490a81e73c2773d36a90');
    if (await canLaunchUrl(url)) {
      await launchUrl(url, mode: LaunchMode.externalApplication);
    }
  }

  void _cerrarSesion() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');

    Navigator.of(context).pushReplacement(
      MaterialPageRoute(builder: (context) => const LoginScreen()),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF060913),
      appBar: AppBar(
        backgroundColor: const Color(0xFF060913),
        elevation: 0,
        title: Image.asset('assets/logo.png', height: 32),
        actions: [
          IconButton(
            icon: const Icon(Icons.notifications_none, color: Colors.white),
            onPressed: () {},
          ),
          PopupMenuButton<String>(
            onSelected: (value) {
              if (value == 'logout') _cerrarSesion();
            },
            color: const Color(0xFF0F172A),
            child: CircleAvatar(
              backgroundColor: const Color(0xFF1E293B),
              child: Text(
                widget.vecinoNombre.isNotEmpty ? widget.vecinoNombre[0] : 'V',
                style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
              ),
            ),
            itemBuilder: (context) => [
              PopupMenuItem(
                enabled: false,
                child: Text('Dpto. ${widget.departamentoNumero}', style: const TextStyle(color: Colors.white70, fontSize: 12)),
              ),
              const PopupMenuDivider(),
              const PopupMenuItem(
                value: 'logout',
                child: Row(
                  children: [
                    Icon(Icons.logout, color: Colors.redAccent, size: 18),
                    SizedBox(width: 8),
                    Text('Cerrar Sesión', style: TextStyle(color: Colors.redAccent, fontWeight: FontWeight.bold)),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(width: 16),
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
          BottomNavigationBarItem(icon: Icon(Icons.shield), label: 'Seguridad'),
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
          // 1. Tarjeta Bienvenida + Atajo Siri
          Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              color: const Color(0xFF0F172A),
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: Colors.white10),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  '👋 ¡Bienvenido, ${widget.vecinoNombre}!',
                  style: const TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.bold),
                ),
                const SizedBox(height: 6),
                Text(
                  'Departamento ${widget.departamentoNumero} — ${widget.condominioNombre}',
                  style: const TextStyle(color: Color(0xFF38BDF8), fontSize: 14, fontWeight: FontWeight.w600),
                ),
                const SizedBox(height: 16),
                SizedBox(
                  width: double.infinity,
                  height: 42,
                  child: ElevatedButton.icon(
                    onPressed: _abrirSiriShortcut,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF0284C7),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                    ),
                    icon: const Icon(Icons.phone_iphone, color: Colors.white, size: 18),
                    label: const Text('📱 Instalar Atajo de Voz Siri (1 Clic)', style: TextStyle(color: Colors.white, fontSize: 13, fontWeight: FontWeight.bold)),
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 16),

          // 2. Tarjeta Estado de Cuenta
          Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              gradient: const LinearGradient(colors: [Color(0xFF7C3AED), Color(0xFF4C1D95)]),
              borderRadius: BorderRadius.circular(16),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: const [
                Text('ESTADO DE CUENTA', style: TextStyle(color: Colors.white70, fontSize: 11, fontWeight: FontWeight.bold)),
                SizedBox(height: 6),
                Text('S/ 0.00', style: TextStyle(color: Colors.white, fontSize: 28, fontWeight: FontWeight.bold)),
                SizedBox(height: 6),
                Text('✅ ¡Estás al día!', style: TextStyle(color: Colors.greenAccent, fontSize: 13, fontWeight: FontWeight.bold)),
              ],
            ),
          ),
          const SizedBox(height: 16),

          // 3. Tarjeta Último Comunicado
          Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              color: const Color(0xFF064E3B).withOpacity(0.5),
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: const Color(0xFF10B981).withOpacity(0.3)),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: const [
                Text('ÚLTIMO COMUNICADO', style: TextStyle(color: Colors.white70, fontSize: 11, fontWeight: FontWeight.bold)),
                SizedBox(height: 6),
                Text('Sin comunicados', style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
                SizedBox(height: 4),
                Text('No hay novedades por el momento.', style: TextStyle(color: Colors.white60, fontSize: 13)),
              ],
            ),
          ),
          const SizedBox(height: 16),

          // 4. Botón SOS de Pánico
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: _sosEnviado ? Colors.green.withOpacity(0.2) : Colors.red.withOpacity(0.15),
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: _sosEnviado ? Colors.green : Colors.red),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  _sosEnviado ? '🟢 AYUDA EN CAMINO' : '🚨 BOTÓN DE PÁNICO S.O.S.',
                  style: TextStyle(color: _sosEnviado ? Colors.greenAccent : Colors.redAccent, fontWeight: FontWeight.bold),
                ),
                const SizedBox(height: 8),
                SizedBox(
                  width: double.infinity,
                  height: 48,
                  child: ElevatedButton.icon(
                    onPressed: _dispararSOS,
                    style: ElevatedButton.styleFrom(backgroundColor: _sosEnviado ? Colors.green : Colors.red),
                    icon: const Icon(Icons.warning, color: Colors.white),
                    label: Text(
                      _sosEnviado ? 'ALERTA ENVIADA A PORTERÍA' : 'DISPARAR S.O.S. (1 TOQUE)',
                      style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
                    ),
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 20),

          // 5. Servicios del Condominio (Grid de 4 Tarjetas Nativas)
          const Text('SERVICIOS DEL CONDOMINIO', style: TextStyle(color: Colors.white70, fontSize: 12, fontWeight: FontWeight.bold, letterSpacing: 1)),
          const SizedBox(height: 12),

          GridView.count(
            crossAxisCount: 2,
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            crossAxisSpacing: 12,
            mainAxisSpacing: 12,
            childAspectRatio: 1.3,
            children: [
              _buildServiceCard(
                icon: Icons.account_balance_wallet,
                iconColor: const Color(0xFFA855F7),
                title: 'Mis Pagos',
                subtitle: 'Recibos y vouchers',
                onTap: () => setState(() => _currentIndex = 1),
              ),
              _buildServiceCard(
                icon: Icons.campaign,
                iconColor: const Color(0xFF10B981),
                title: 'Avisos',
                subtitle: 'Comunicados',
                onTap: () => setState(() => _currentIndex = 4),
              ),
              _buildServiceCard(
                icon: Icons.pets,
                iconColor: const Color(0xFFEC4899),
                title: 'Mascotas',
                subtitle: 'Registro',
                onTap: () => setState(() => _currentIndex = 4),
              ),
              _buildServiceCard(
                icon: Icons.chat_bubble_outline,
                iconColor: const Color(0xFF14B8A6),
                title: 'Reclamos',
                subtitle: 'Sugerencias',
                onTap: () => setState(() => _currentIndex = 4),
              ),
            ],
          ),
          const SizedBox(height: 24),
        ],
      ),
    );
  }

  Widget _buildServiceCard({
    required IconData icon,
    required Color iconColor,
    required String title,
    required String subtitle,
    required VoidCallback onTap,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(16),
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: const Color(0xFF0F172A),
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: Colors.white10),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: iconColor.withOpacity(0.15),
                borderRadius: BorderRadius.circular(10),
              ),
              child: Icon(icon, color: iconColor, size: 24),
            ),
            const SizedBox(height: 10),
            Text(title, style: const TextStyle(color: Colors.white, fontSize: 14, fontWeight: FontWeight.bold)),
            const SizedBox(height: 2),
            Text(subtitle, style: const TextStyle(color: Colors.white54, fontSize: 11)),
          ],
        ),
      ),
    );
  }
}