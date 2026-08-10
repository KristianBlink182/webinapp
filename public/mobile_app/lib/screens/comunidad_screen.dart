import 'package:flutter/material.dart';

class ComunidadScreen extends StatelessWidget {
  final String token;

  const ComunidadScreen({Key? key, required this.token}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return DefaultTabController(
      length: 3,
      child: Scaffold(
        backgroundColor: const Color(0xFF060913),
        appBar: AppBar(
          backgroundColor: const Color(0xFF0F172A),
          title: const Text('Comunidad & Servicios', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
          bottom: const TabBar(
            indicatorColor: Color(0xFF0284C7),
            tabs: [
              Tab(icon: Icon(Icons.campaign), text: 'Avisos'),
              Tab(icon: Icon(Icons.pets), text: 'Mascotas'),
              Tab(icon: Icon(Icons.chat_bubble), text: 'Reclamos'),
            ],
          ),
        ),
        body: TabBarView(
          children: [
            _buildLista(item: 'Muro de Comunicados', desc: 'No hay avisos recientes.'),
            _buildLista(item: 'Registro de Mascotas', desc: 'Sin mascotas registradas.'),
            _buildLista(item: 'Buzón de Reclamos', desc: 'No hay sugerencias enviadas.'),
          ],
        ),
      ),
    );
  }

  Widget _buildLista({required String item, required String desc}) {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          const Icon(Icons.info_outline, color: Colors.white38, size: 48),
          const SizedBox(height: 12),
          Text(item, style: const TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
          const SizedBox(height: 6),
          Text(desc, style: const TextStyle(color: Colors.white54, fontSize: 13)),
        ],
      ),
    );
  }
}