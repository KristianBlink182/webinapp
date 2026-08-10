import 'package:flutter/material.dart';

class ComunidadScreen extends StatelessWidget {
  final String token;
  final int initialTabIndex;

  const ComunidadScreen({Key? key, required this.token, this.initialTabIndex = 0}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return DefaultTabController(
      length: 5,
      initialIndex: initialTabIndex,
      child: Scaffold(
        backgroundColor: const Color(0xFF060913),
        appBar: AppBar(
          backgroundColor: const Color(0xFF0F172A),
          elevation: 0,
          title: const Text('Comunidad & Servicios', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
          bottom: const TabBar(
            isScrollable: true,
            indicatorColor: Color(0xFF0284C7),
            tabs: [
              Tab(icon: Icon(Icons.campaign), text: 'Avisos'),
              Tab(icon: Icon(Icons.event_seat), text: 'Áreas Comunes'),
              Tab(icon: Icon(Icons.pets), text: 'Mascotas'),
              Tab(icon: Icon(Icons.chat_bubble_outline), text: 'Reclamos'),
              Tab(icon: Icon(Icons.badge_outlined), text: 'Invitados'),
            ],
          ),
        ),
        body: TabBarView(
          children: [
            _buildOpcionDirecta(icon: Icons.campaign, title: 'Muro de Comunicados', subtitle: 'No hay avisos recientes del condominio.'),
            _buildOpcionDirecta(icon: Icons.event_seat, title: 'Reserva de Áreas Comunes', subtitle: 'Parrillas, SUM y Gimnasio disponibles.'),
            _buildOpcionDirecta(icon: Icons.pets, title: 'Registro de Mascotas', subtitle: 'Padrón oficial de mascotas del edificio.'),
            _buildOpcionDirecta(icon: Icons.chat_bubble_outline, title: 'Buzón de Reclamos', subtitle: 'Envía tus sugerencias directamente a la junta.'),
            _buildOpcionDirecta(icon: Icons.badge_outlined, title: 'Mis Invitados', subtitle: 'Genera pases de ingreso para la Portería.'),
          ],
        ),
      ),
    );
  }

  Widget _buildOpcionDirecta({required IconData icon, required String title, required String subtitle}) {
    return Padding(
      padding: const EdgeInsets.all(16.0),
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              color: const Color(0xFF0F172A),
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: Colors.white10),
            ),
            child: Row(
              children: [
                Icon(icon, color: const Color(0xFF0284C7), size: 32),
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
              ],
            ),
          ),
        ],
      ),
    );
  }
}