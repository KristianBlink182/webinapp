import 'package:flutter/material.dart';
import '../api_service.dart';

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
          CircleAvatar(
            backgroundColor: const Color(0xFF1E293B),
            child: Text(
              widget.vecinoNombre.isNotEmpty ? widget.vecinoNombre[0] : 'V',
              style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
            ),
          ),
          const SizedBox(width: 16),
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
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
                ],
              ),
            ),
            const SizedBox(height: 16),
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                gradient: const LinearGradient(colors: [Color(0xFF7C3AED), Color(0xFF4C1D95)]),
                borderRadius: BorderRadius.circular(16),
              ),
              child: const Column(
                crossAxisAlignment: Alignment.start,
                children: [
                  Text('ESTADO DE CUENTA', style: TextStyle(color: Colors.white70, fontSize: 11, fontWeight: FontWeight.bold)),
                  SizedBox(height: 6),
                  Text('S/ 0.00', style: TextStyle(color: Colors.white, fontSize: 28, fontWeight: FontWeight.bold)),
                  SizedBox(height: 6),
                  Text('✅ ¡Estás al día!', style: TextStyle(color: Colors.greenAccent, fontSize: 13, fontWeight: FontWeight.bold)),
                ],
              ),
            ),
            const SizedBox(height: 16),
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: _sosEnviado ? Colors.green.withOpacity(0.2) : Colors.red.withOpacity(0.15),
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: _sosEnviado ? Colors.green : Colors.red),
              ),
              child: Column(
                crossAxisAlignment: Alignment.start,
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
          ],
        ),
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
}