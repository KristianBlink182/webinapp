import 'package:flutter/material.dart';
import '../api_service.dart';

// 1. PANTALLA DETALLE AVISOS Y COMUNICADOS
class ComunicadosListScreen extends StatefulWidget {
  final String token;
  const ComunicadosListScreen({Key? key, required this.token}) : super(key: key);

  @override
  _ComunicadosListScreenState createState() => _ComunicadosListScreenState();
}

class _ComunicadosListScreenState extends State<ComunicadosListScreen> {
  List<dynamic> _items = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _cargar();
  }

  void _cargar() async {
    final res = await ApiService.getComunicados(widget.token);
    if (res['success'] == true) {
      setState(() { _items = res['data'] ?? []; _isLoading = false; });
    } else {
      setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF060913),
      appBar: AppBar(backgroundColor: const Color(0xFF060913), title: const Text('Muro de Comunicados')),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: Color(0xFF0284C7)))
          : _items.isEmpty
              ? _buildVacio('Sin Comunicados', 'No hay avisos recientes publicados por la junta.')
              : ListView.builder(
                  padding: const EdgeInsets.all(16),
                  itemCount: _items.length,
                  itemBuilder: (context, i) {
                    final item = _items[i];
                    return Container(
                      margin: const EdgeInsets.only(bottom: 12),
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(color: const Color(0xFF0F172A), borderRadius: BorderRadius.circular(16), border: Border.all(color: Colors.white10)),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(item['titulo'] ?? 'Aviso', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
                          const SizedBox(height: 6),
                          Text(item['contenido'] ?? '', style: const TextStyle(color: Colors.white70, fontSize: 13)),
                          const SizedBox(height: 10),
                          Text(item['fecha'] ?? '', style: const TextStyle(color: Color(0xFF38BDF8), fontSize: 11)),
                        ],
                      ),
                    );
                  },
                ),
    );
  }
}

// 2. PANTALLA DETALLE MASCOTAS
class MascotasListScreen extends StatefulWidget {
  final String token;
  const MascotasListScreen({Key? key, required this.token}) : super(key: key);

  @override
  _MascotasListScreenState createState() => _MascotasListScreenState();
}

class _MascotasListScreenState extends State<MascotasListScreen> {
  List<dynamic> _items = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _cargar();
  }

  void _cargar() async {
    final res = await ApiService.getMascotas(widget.token);
    if (res['success'] == true) {
      setState(() { _items = res['data'] ?? []; _isLoading = false; });
    } else {
      setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF060913),
      appBar: AppBar(backgroundColor: const Color(0xFF060913), title: const Text('Registro de Mascotas')),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: Color(0xFF0284C7)))
          : _items.isEmpty
              ? _buildVacio('Padrón de Mascotas', 'No hay mascotas registradas para su departamento.')
              : ListView.builder(
                  padding: const EdgeInsets.all(16),
                  itemCount: _items.length,
                  itemBuilder: (context, i) {
                    final item = _items[i];
                    return Container(
                      margin: const EdgeInsets.only(bottom: 12),
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(color: const Color(0xFF0F172A), borderRadius: BorderRadius.circular(16), border: Border.all(color: Colors.white10)),
                      child: Row(
                        children: [
                          const CircleAvatar(backgroundColor: Color(0xFF1E293B), child: Icon(Icons.pets, color: Color(0xFFEC4899))),
                          const SizedBox(width: 16),
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(item['nombre'] ?? 'Mascota', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
                              Text('${item['tipo']} — Raza: ${item['raza']}', style: const TextStyle(color: Colors.white54, fontSize: 12)),
                            ],
                          ),
                        ],
                      ),
                    );
                  },
                ),
    );
  }
}

// 3. PANTALLA DETALLE RECLAMOS
class ReclamosListScreen extends StatefulWidget {
  final String token;
  const ReclamosListScreen({Key? key, required this.token}) : super(key: key);

  @override
  _ReclamosListScreenState createState() => _ReclamosListScreenState();
}

class _ReclamosListScreenState extends State<ReclamosListScreen> {
  List<dynamic> _items = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _cargar();
  }

  void _cargar() async {
    final res = await ApiService.getReclamos(widget.token);
    if (res['success'] == true) {
      setState(() { _items = res['data'] ?? []; _isLoading = false; });
    } else {
      setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF060913),
      appBar: AppBar(backgroundColor: const Color(0xFF060913), title: const Text('Buzón de Reclamos')),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: Color(0xFF0284C7)))
          : _items.isEmpty
              ? _buildVacio('Buzón de Reclamos', 'No ha enviado sugerencias o reclamos recientes.')
              : ListView.builder(
                  padding: const EdgeInsets.all(16),
                  itemCount: _items.length,
                  itemBuilder: (context, i) {
                    final item = _items[i];
                    return Container(
                      margin: const EdgeInsets.only(bottom: 12),
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(color: const Color(0xFF0F172A), borderRadius: BorderRadius.circular(16), border: Border.all(color: Colors.white10)),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(item['asunto'] ?? 'Sugerencia', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
                          const SizedBox(height: 6),
                          Text(item['descripcion'] ?? '', style: const TextStyle(color: Colors.white70, fontSize: 13)),
                        ],
                      ),
                    );
                  },
                ),
    );
  }
}

// 4. PANTALLA CÁMARA DE SEGURIDAD EN VIVO
class CamaraScreen extends StatelessWidget {
  final String token;
  const CamaraScreen({Key? key, required this.token}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF060913),
      appBar: AppBar(backgroundColor: const Color(0xFF060913), title: const Text('Cámara de Seguridad')),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Container(
          height: 220,
          decoration: BoxDecoration(color: const Color(0xFF0F172A), borderRadius: BorderRadius.circular(16), border: Border.all(color: Colors.white10)),
          child: const Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(Icons.videocam, color: Colors.redAccent, size: 48),
                SizedBox(height: 8),
                Text('🔴 EN VIVO — Puerta Principal', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                SizedBox(height: 4),
                Text('Transmisión segura en tiempo real', style: TextStyle(color: Colors.white54, fontSize: 12)),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

// 5. PANTALLA RESERVA ÁREAS COMUNES
class AreasComunesListScreen extends StatelessWidget {
  final String token;
  const AreasComunesListScreen({Key? key, required this.token}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF060913),
      appBar: AppBar(backgroundColor: const Color(0xFF060913), title: const Text('Reserva de Áreas Comunes')),
      body: _buildVacio('Áreas Comunes', 'Parrillas, SUM y Gimnasio disponibles para reserva.'),
    );
  }
}

// 6. PANTALLA MIS INVITADOS
class InvitadosListScreen extends StatelessWidget {
  final String token;
  const InvitadosListScreen({Key? key, required this.token}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF060913),
      appBar: AppBar(backgroundColor: const Color(0xFF060913), title: const Text('Mis Invitados')),
      body: _buildVacio('Control de Accesos', 'Genera pases de acceso rápido para la Portería.'),
    );
  }
}

Widget _buildVacio(String titulo, String desc) {
  return Center(
    child: Column(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        const Icon(Icons.info_outline, color: Colors.white38, size: 48),
        const SizedBox(height: 12),
        Text(titulo, style: const TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
        const SizedBox(height: 6),
        Text(desc, style: const TextStyle(color: Colors.white54, fontSize: 13)),
      ],
    ),
  );
}