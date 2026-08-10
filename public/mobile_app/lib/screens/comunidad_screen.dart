import 'package:flutter/material.dart';
import 'detalle_screens.dart';

class ComunidadScreen extends StatelessWidget {
  final String token;

  const ComunidadScreen({Key? key, required this.token}) : super(key: key);

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
        title: const Text('Comunidad & Servicios', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('SERVICIOS DE LA COMUNIDAD', style: TextStyle(color: Colors.white70, fontSize: 12, fontWeight: FontWeight.bold, letterSpacing: 1)),
            const SizedBox(height: 12),
            GridView.count(
              crossAxisCount: 2,
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              crossAxisSpacing: 12,
              mainAxisSpacing: 12,
              childAspectRatio: 1.3,
              children: [
                _buildCuadradoCard(
                  icon: Icons.campaign,
                  iconColor: const Color(0xFF10B981),
                  title: 'Muro de Avisos',
                  subtitle: 'Comunicados oficiales',
                  onTap: () => _abrir(context, ComunicadosListScreen(token: token)),
                ),
                _buildCuadradoCard(
                  icon: Icons.shopping_bag_outlined,
                  iconColor: const Color(0xFFF59E0B),
                  title: 'Marketplace Vecinal',
                  subtitle: 'Compra y venta',
                  onTap: () => _abrir(context, MarketplaceListScreen(token: token)),
                ),
                _buildCuadradoCard(
                  icon: Icons.how_to_vote_outlined,
                  iconColor: const Color(0xFF38BDF8),
                  title: 'Votaciones & Acuerdos',
                  subtitle: 'Decisiones junta',
                  onTap: () => _abrir(context, VotacionesListScreen(token: token)),
                ),
                _buildCuadradoCard(
                  icon: Icons.folder_open,
                  iconColor: const Color(0xFFA855F7),
                  title: 'Biblioteca Documentos',
                  subtitle: 'Reglamentos PDF',
                  onTap: () => _abrir(context, DocumentosListScreen(token: token)),
                ),
                _buildCuadradoCard(
                  icon: Icons.pets,
                  iconColor: const Color(0xFFEC4899),
                  title: 'Mis Mascotas',
                  subtitle: 'Registro del padrón',
                  onTap: () => _abrir(context, MascotasListScreen(token: token)),
                ),
                _buildCuadradoCard(
                  icon: Icons.chat_bubble_outline,
                  iconColor: const Color(0xFF14B8A6),
                  title: 'Reclamos & Reportes',
                  subtitle: 'Sugerencias a la junta',
                  onTap: () => _abrir(context, ReclamosListScreen(token: token)),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildCuadradoCard({
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
              decoration: BoxDecoration(color: iconColor.withOpacity(0.15), borderRadius: BorderRadius.circular(10)),
              child: Icon(icon, color: iconColor, size: 24),
            ),
            const SizedBox(height: 10),
            Text(title, style: const TextStyle(color: Colors.white, fontSize: 13, fontWeight: FontWeight.bold)),
            const SizedBox(height: 2),
            Text(subtitle, style: const TextStyle(color: Colors.white54, fontSize: 10)),
          ],
        ),
      ),
    );
  }
}