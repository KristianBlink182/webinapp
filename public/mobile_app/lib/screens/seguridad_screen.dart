import 'package:flutter/material.dart';
import 'detalle_screens.dart';

class SeguridadScreen extends StatelessWidget {
  final String token;

  const SeguridadScreen({Key? key, required this.token}) : super(key: key);

  void _abrir(BuildContext context, Widget screen) {
    Navigator.of(context).push(MaterialPageRoute(builder: (context) => screen));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF060913),
      appBar: AppBar(
        backgroundColor: const Color(0xFF060913),
        elevation: 0,
        title: const Text('Seguridad y Accesos', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
      ),
      body: ListView(
        padding: const EdgeInsets.all(16.0),
        children: [
          _buildOpcionCard(
            icon: Icons.badge_outlined,
            iconColor: const Color(0xFF38BDF8),
            title: 'Mis Invitados',
            subtitle: 'Genera pases de ingreso para la Portería.',
            onTap: () => _abrir(context, InvitadosListScreen(token: token)),
          ),
          const SizedBox(height: 12),
          _buildOpcionCard(
            icon: Icons.videocam_outlined,
            iconColor: const Color(0xFF10B981),
            title: 'Cámara de Seguridad en Vivo',
            subtitle: 'Transmisión de la puerta principal (🔴 EN VIVO)',
            onTap: () => _abrir(context, CamaraScreen(token: token)),
          ),
        ],
      ),
    );
  }

  Widget _buildOpcionCard({required IconData icon, required Color iconColor, required String title, required String subtitle, required VoidCallback onTap}) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(16),
      child: Container(
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          color: const Color(0xFF0F172A),
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: Colors.white10),
        ),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(color: iconColor.withOpacity(0.15), borderRadius: BorderRadius.circular(12)),
              child: Icon(icon, color: iconColor, size: 28),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(title, style: const TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.bold)),
                  const SizedBox(height: 4),
                  Text(subtitle, style: const TextStyle(color: Colors.white54, fontSize: 12)),
                ],
              ),
            ),
            const Icon(Icons.arrow_forward_ios, color: Colors.white24, size: 16),
          ],
        ),
      ),
    );
  }
}