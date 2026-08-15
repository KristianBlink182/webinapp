import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:url_launcher/url_launcher.dart';
import '../api_service.dart';

// 1. INVITADOS (PORTERÍA)
class InvitadosListScreen extends StatefulWidget {
  final String token;
  const InvitadosListScreen({Key? key, required this.token}) : super(key: key);
  @override _InvitadosListScreenState createState() => _InvitadosListScreenState();
}

class _InvitadosListScreenState extends State<InvitadosListScreen> {
  List<dynamic> _items = []; bool _isLoading = true;
  @override void initState() { super.initState(); _cargar(); }

  void _cargar() async {
    final res = await ApiService.getInvitados(widget.token);
    if (res['success'] == true) setState(() { _items = res['data'] ?? []; _isLoading = false; });
    else setState(() => _isLoading = false);
  }

  void _modalNuevo() {
    final _nCtrl = TextEditingController(); final _dCtrl = TextEditingController();
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        backgroundColor: const Color(0xFF0F172A),
        title: const Text('➕ Pre-Autorizar Invitado', style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('Nombre del visitante', style: TextStyle(color: Colors.white70, fontSize: 12)),
            const SizedBox(height: 4),
            TextField(controller: _nCtrl, style: const TextStyle(color: Colors.white), decoration: const InputDecoration(filled: true, fillColor: Color(0xFF1E293B), hintText: 'Ej: Juan Pérez', hintStyle: TextStyle(color: Colors.white30))),
            const SizedBox(height: 12),
            const Text('DNI / Documento', style: TextStyle(color: Colors.white70, fontSize: 12)),
            const SizedBox(height: 4),
            TextField(controller: _dCtrl, style: const TextStyle(color: Colors.white), decoration: const InputDecoration(filled: true, fillColor: Color(0xFF1E293B), hintText: 'Ej: 78945612', hintStyle: TextStyle(color: Colors.white30))),
          ],
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Cancelar', style: TextStyle(color: Colors.white54))),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF0284C7)),
            onPressed: () async {
              if (_nCtrl.text.trim().isEmpty) return;
              Navigator.pop(ctx);
              setState(() => _isLoading = true);
              final res = await ApiService.registrarInvitado(widget.token, _nCtrl.text.trim(), _dCtrl.text.trim(), 'Peatonal');
              if (res['success'] == true) { ScaffoldMessenger.of(context).showSnackBar(SnackBar(backgroundColor: Colors.green, content: Text(res['message']))); _cargar(); }
            },
            child: const Text('Autorizar en Portería'),
          )
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF060913),
      appBar: AppBar(backgroundColor: const Color(0xFF060913), title: const Text('Mis Invitados')),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: Color(0xFF0284C7)))
          : _items.isEmpty
              ? _buildVacio('Sin Invitados Pre-Autorizados', 'Pre-autoriza un invitado para que ingrese sin demoras en Portería.')
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
                          const CircleAvatar(backgroundColor: Color(0xFF1E293B), child: Icon(Icons.badge_outlined, color: Color(0xFF38BDF8))),
                          const SizedBox(width: 16),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(item['nombre_visitante'] ?? 'Invitado', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
                                Text('DNI: ${item['dni_visitante']} — ${item['tipo_visita']}', style: const TextStyle(color: Colors.white54, fontSize: 12)),
                              ],
                            ),
                          ),
                          Container(padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4), decoration: BoxDecoration(color: Colors.green.withOpacity(0.2), borderRadius: BorderRadius.circular(20)), child: const Text('Pre-Autorizado', style: TextStyle(color: Colors.green, fontSize: 11, fontWeight: FontWeight.bold))),
                        ],
                      ),
                    );
                  },
                ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: _modalNuevo,
        backgroundColor: const Color(0xFF0284C7),
        icon: const Icon(Icons.add, color: Colors.white),
        label: const Text('Pre-Autorizar Invitado', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
      ),
    );
  }
}

// 2. MARKETPLACE VECINAL (CON FOTOS REALES Y BOTÓN WHATSAPP)
class MarketplaceListScreen extends StatefulWidget {
  final String token;
  const MarketplaceListScreen({Key? key, required this.token}) : super(key: key);
  @override _MarketplaceListScreenState createState() => _MarketplaceListScreenState();
}

class _MarketplaceListScreenState extends State<MarketplaceListScreen> {
  List<dynamic> _items = []; bool _isLoading = true;

  @override void initState() { super.initState(); _cargar(); }

  void _cargar() async {
    final res = await ApiService.getMarketplace(widget.token);
    if (res['success'] == true) setState(() { _items = res['data'] ?? []; _isLoading = false; });
    else setState(() => _isLoading = false);
  }

  void _abrirWhatsApp(String telefono, String tituloProducto) async {
    final cleanPhone = telefono.replaceAll(RegExp(r'[^\d]'), '');
    final phone = cleanPhone.startsWith('51') ? cleanPhone : '51$cleanPhone';
    final mensaje = Uri.encodeComponent('¡Hola! Vi tu publicación "$tituloProducto" en el Marketplace de LIVO Vecinos.');
    final Uri url = Uri.parse('https://wa.me/$phone?text=$mensaje');

    if (await canLaunchUrl(url)) {
      await launchUrl(url, mode: LaunchMode.externalApplication);
    }
  }

 void _modalPublicar() {
    final _pCtrl = TextEditingController();
    final _precioCtrl = TextEditingController();
    final _telfCtrl = TextEditingController();
    final _dCtrl = TextEditingController();
    XFile? _imagenSeleccionada;

    showDialog(
      context: context,
      builder: (ctx) => StatefulBuilder(
        builder: (ctx, setModalState) => AlertDialog(
          backgroundColor: const Color(0xFF0F172A),
          title: const Text('🛒 Publicar Producto', style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                TextField(controller: _pCtrl, style: const TextStyle(color: Colors.white), decoration: const InputDecoration(hintText: 'Nombre del producto / servicio', hintStyle: TextStyle(color: Colors.white30))),
                const SizedBox(height: 8),
                TextField(controller: _precioCtrl, keyboardType: TextInputType.number, style: const TextStyle(color: Colors.white), decoration: const InputDecoration(hintText: 'Precio (S/)', hintStyle: TextStyle(color: Colors.white30))),
                const SizedBox(height: 8),
                TextField(controller: _telfCtrl, keyboardType: TextInputType.phone, style: const TextStyle(color: Colors.white), decoration: const InputDecoration(hintText: 'Teléfono / WhatsApp de contacto', hintStyle: TextStyle(color: Colors.white30))),
                const SizedBox(height: 8),
                TextField(controller: _dCtrl, maxLines: 2, style: const TextStyle(color: Colors.white), decoration: const InputDecoration(hintText: 'Descripción del producto...', hintStyle: TextStyle(color: Colors.white30))),
                const SizedBox(height: 12),
                OutlinedButton.icon(
                  onPressed: () async {
                    final picker = ImagePicker();
                    final picked = await picker.pickImage(source: ImageSource.gallery);
                    if (picked != null) {
                      setModalState(() {
                        _imagenSeleccionada = picked;
                      });
                    }
                  },
                  icon: Icon(_imagenSeleccionada != null ? Icons.check_circle : Icons.camera_alt, color: const Color(0xFFFF9E00)),
                  label: Text(_imagenSeleccionada != null ? '📷 Foto Seleccionada' : '📸 Adjuntar Foto del Producto', style: const TextStyle(color: Color(0xFFFF9E00))),
                ),
              ],
            ),
          ),
          actions: [
            TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Cancelar', style: TextStyle(color: Colors.white54))),
            ElevatedButton(
              style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFFFF9E00)),
              onPressed: () async {
                if (_pCtrl.text.isEmpty) return;
                Navigator.pop(ctx);
                if (mounted) setState(() { _isLoading = true; });
                final res = await ApiService.registrarMarketplace(
                  widget.token,
                  _pCtrl.text.trim(),
                  _precioCtrl.text.trim(),
                  _telfCtrl.text.trim(),
                  _dCtrl.text.trim(),
                  _imagenSeleccionada != null ? _imagenSeleccionada!.path : null,
                );
                if (mounted) {
                  ScaffoldMessenger.of(context).showSnackBar(SnackBar(backgroundColor: Colors.green, content: Text(res['message'] ?? 'Producto publicado.')));
                  _cargar(); // <--- ¡AQUÍ DICE _cargar()!
                }
              },
              child: const Text('Publicar', style: TextStyle(color: Colors.black, fontWeight: FontWeight.bold)),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF060913),
      appBar: AppBar(backgroundColor: const Color(0xFF060913), title: const Text('Marketplace Vecinal')),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: Color(0xFFF59E0B)))
          : _items.isEmpty
              ? _buildVacio('Marketplace Vecinal', 'No hay productos publicados por los vecinos actualmente.')
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
                          if (item['imagen_url'] != null) ...[
                            ClipRRect(
                              borderRadius: BorderRadius.circular(12),
                              child: Image.network(
                                item['imagen_url'],
                                height: 160,
                                width: double.infinity,
                                fit: BoxFit.cover,
                                errorBuilder: (c, e, s) => const SizedBox(),
                              ),
                            ),
                            const SizedBox(height: 12),
                          ],
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Expanded(child: Text(item['producto'] ?? 'Producto', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16))),
                              Text(item['precio_formateado'] ?? 'S/ 0.00', style: const TextStyle(color: Color(0xFFF59E0B), fontWeight: FontWeight.bold, fontSize: 16)),
                            ],
                          ),
                          const SizedBox(height: 6),
                          Text(item['descripcion'] ?? '', style: const TextStyle(color: Colors.white70, fontSize: 13)),
                          const SizedBox(height: 8),
                          Text('Vendedor: ${item['vendedor']}', style: const TextStyle(color: Colors.white54, fontSize: 12)),
                          const SizedBox(height: 12),
                         Row(
                      children: [
                        Expanded(
                          child: ElevatedButton.icon(
                            onPressed: () => _abrirWhatsapp(item['telefono_whatsapp'] ?? '987654321', item['producto'] ?? 'Producto'),
                            style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF25D366)),
                            icon: const Icon(Icons.chat, color: Colors.white, size: 18),
                            label: const Text('Contactar por WhatsApp', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                          ),
                        ),
                        const SizedBox(width: 8),
                        IconButton(
                          icon: const Icon(Icons.delete_outline, color: Colors.redAccent),
                          onPressed: () async {
                            final confirm = await showDialog<bool>(
                              context: context,
                              builder: (ctx) => AlertDialog(
                                backgroundColor: const Color(0xFF0F172A),
                                title: const Text('¿Eliminar Anuncio?', style: TextStyle(color: Colors.white)),
                                content: const Text('¿Estás seguro de quitar este producto del Marketplace?', style: TextStyle(color: Colors.white70)),
                                actions: [
                                  TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('Cancelar', style: TextStyle(color: Colors.white54))),
                                  ElevatedButton(
                                    style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
                                    onPressed: () => Navigator.pop(ctx, true),
                                    child: const Text('Eliminar', style: TextStyle(color: Colors.white)),
                                  ),
                                ],
                              ),
                            );

                            if (confirm == true) {
                              final res = await ApiService.eliminarMarketplace(widget.token, item['id'].toString());
                              if (mounted) {
                                ScaffoldMessenger.of(context).showSnackBar(SnackBar(backgroundColor: Colors.red, content: Text(res['message'] ?? 'Anuncio eliminado.')));
                                _cargar();
                              }
                            }
                          },
                        ),
                      ],
                    )
                        ],
                      ),
                    );
                  },
                ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: _modalPublicar,
        backgroundColor: const Color(0xFFF59E0B),
        icon: const Icon(Icons.add_shopping_cart, color: Colors.white),
        label: const Text('Publicar Producto', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
      ),
    );
  }
}

// 3. VOTACIONES & ACUERDOS
class VotacionesListScreen extends StatefulWidget {
  final String token;
  const VotacionesListScreen({Key? key, required this.token}) : super(key: key);
  @override _VotacionesListScreenState createState() => _VotacionesListScreenState();
}

class _VotacionesListScreenState extends State<VotacionesListScreen> {
  List<dynamic> _items = []; bool _isLoading = true;
  @override void initState() { super.initState(); _cargar(); }
  void _cargar() async {
    final res = await ApiService.getVotaciones(widget.token);
    if (res['success'] == true) setState(() { _items = res['data'] ?? []; _isLoading = false; });
    else setState(() => _isLoading = false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF060913),
      appBar: AppBar(backgroundColor: const Color(0xFF060913), title: const Text('Votaciones & Acuerdos')),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: Color(0xFF38BDF8)))
          : _items.isEmpty
              ? _buildVacio('Votaciones de la Junta', 'No hay votaciones activas en este momento.')
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
                          Text(item['titulo'] ?? '', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
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

// 4. BIBLIOTECA DE DOCUMENTOS
class DocumentosListScreen extends StatefulWidget {
  final String token;
  const DocumentosListScreen({Key? key, required this.token}) : super(key: key);
  @override _DocumentosListScreenState createState() => _DocumentosListScreenState();
}

class _DocumentosListScreenState extends State<DocumentosListScreen> {
  List<dynamic> _items = []; bool _isLoading = true;
  @override void initState() { super.initState(); _cargar(); }
  void _cargar() async {
    final res = await ApiService.getDocumentos(widget.token);
    if (res['success'] == true) setState(() { _items = res['data'] ?? []; _isLoading = false; });
    else setState(() => _isLoading = false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF060913),
      appBar: AppBar(backgroundColor: const Color(0xFF060913), title: const Text('Biblioteca de Documentos')),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: Color(0xFFA855F7)))
          : _items.isEmpty
              ? _buildVacio('Biblioteca de Documentos', 'No hay reglamentos ni actas publicadas en PDF.')
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
                          const Icon(Icons.picture_as_pdf, color: Colors.redAccent, size: 32),
                          const SizedBox(width: 16),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(item['titulo'] ?? 'Documento PDF', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 15)),
                                Text(item['tipo'] ?? 'Reglamento', style: const TextStyle(color: Colors.white54, fontSize: 12)),
                              ],
                            ),
                          ),
                          OutlinedButton(onPressed: () {}, child: const Text('Ver PDF')),
                        ],
                      ),
                    );
                  },
                ),
    );
  }
}

// 5. COMUNICADOS
class ComunicadosListScreen extends StatefulWidget {
  final String token;
  const ComunicadosListScreen({Key? key, required this.token}) : super(key: key);
  @override _ComunicadosListScreenState createState() => _ComunicadosListScreenState();
}

class _ComunicadosListScreenState extends State<ComunicadosListScreen> {
  List<dynamic> _items = []; bool _isLoading = true;
  @override void initState() { super.initState(); _cargar(); }
  void _cargar() async {
    final res = await ApiService.getComunicados(widget.token);
    if (res['success'] == true) setState(() { _items = res['data'] ?? []; _isLoading = false; });
    else setState(() => _isLoading = false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF060913),
      appBar: AppBar(backgroundColor: const Color(0xFF060913), title: const Text('Muro de Comunicados')),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: Color(0xFF10B981)))
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

// 6. MASCOTAS
class MascotasListScreen extends StatefulWidget {
  final String token;
  const MascotasListScreen({Key? key, required this.token}) : super(key: key);
  @override _MascotasListScreenState createState() => _MascotasListScreenState();
}

class _MascotasListScreenState extends State<MascotasListScreen> {
  List<dynamic> _items = []; bool _isLoading = true;
  @override void initState() { super.initState(); _cargar(); }
  void _cargar() async {
    final res = await ApiService.getMascotas(widget.token);
    if (res['success'] == true) setState(() { _items = res['data'] ?? []; _isLoading = false; });
    else setState(() => _isLoading = false);
  }

  void _modalNuevaMascota() {
    final _nCtrl = TextEditingController(); final _rCtrl = TextEditingController();
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        backgroundColor: const Color(0xFF0F172A),
        title: const Text('🐶 Registrar Mascota', style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            TextField(controller: _nCtrl, style: const TextStyle(color: Colors.white), decoration: const InputDecoration(hintText: 'Nombre de la mascota', hintStyle: TextStyle(color: Colors.white30))),
            const SizedBox(height: 8),
            TextField(controller: _rCtrl, style: const TextStyle(color: Colors.white), decoration: const InputDecoration(hintText: 'Raza / Especie', hintStyle: TextStyle(color: Colors.white30))),
          ],
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Cancelar', style: TextStyle(color: Colors.white54))),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFFEC4899)),
            onPressed: () async {
              if (_nCtrl.text.isEmpty) return;
              Navigator.pop(ctx);
              setState(() => _isLoading = true);
              final res = await ApiService.registrarMascota(widget.token, _nCtrl.text.trim(), 'Mascota', _rCtrl.text.trim());
              if (res['success'] == true) { ScaffoldMessenger.of(context).showSnackBar(SnackBar(backgroundColor: Colors.green, content: Text(res['message']))); _cargar(); }
            },
            child: const Text('Guardar'),
          )
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF060913),
      appBar: AppBar(backgroundColor: const Color(0xFF060913), title: const Text('Registro de Mascotas')),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: Color(0xFFEC4899)))
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
      floatingActionButton: FloatingActionButton(
        onPressed: _modalNuevaMascota,
        backgroundColor: const Color(0xFFEC4899),
        child: const Icon(Icons.add, color: Colors.white),
      ),
    );
  }
}

// 7. RECLAMOS
class ReclamosListScreen extends StatefulWidget {
  final String token;
  const ReclamosListScreen({Key? key, required this.token}) : super(key: key);
  @override _ReclamosListScreenState createState() => _ReclamosListScreenState();
}

class _ReclamosListScreenState extends State<ReclamosListScreen> {
  List<dynamic> _items = []; bool _isLoading = true;
  @override void initState() { super.initState(); _cargar(); }
  void _cargar() async {
    final res = await ApiService.getReclamos(widget.token);
    if (res['success'] == true) setState(() { _items = res['data'] ?? []; _isLoading = false; });
    else setState(() => _isLoading = false);
  }

  void _modalNuevoReclamo() {
    final _aCtrl = TextEditingController(); final _dCtrl = TextEditingController();
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        backgroundColor: const Color(0xFF0F172A),
        title: const Text('💬 Enviar Reclamo', style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            TextField(controller: _aCtrl, style: const TextStyle(color: Colors.white), decoration: const InputDecoration(hintText: 'Asunto (Ej: Ruido pasillo)', hintStyle: TextStyle(color: Colors.white30))),
            const SizedBox(height: 8),
            TextField(controller: _dCtrl, maxLines: 3, style: const TextStyle(color: Colors.white), decoration: const InputDecoration(hintText: 'Descripción del inconveniente...', hintStyle: TextStyle(color: Colors.white30))),
          ],
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Cancelar', style: TextStyle(color: Colors.white54))),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF14B8A6)),
            onPressed: () async {
              if (_aCtrl.text.isEmpty) return;
              Navigator.pop(ctx);
              setState(() => _isLoading = true);
              final res = await ApiService.registrarReclamo(widget.token, _aCtrl.text.trim(), _dCtrl.text.trim());
              if (res['success'] == true) { ScaffoldMessenger.of(context).showSnackBar(SnackBar(backgroundColor: Colors.green, content: Text(res['message']))); _cargar(); }
            },
            child: const Text('Enviar a la Junta'),
          )
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF060913),
      appBar: AppBar(backgroundColor: const Color(0xFF060913), title: const Text('Buzón de Reclamos')),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: Color(0xFF14B8A6)))
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
      floatingActionButton: FloatingActionButton.extended(
        onPressed: _modalNuevoReclamo,
        backgroundColor: const Color(0xFF14B8A6),
        icon: const Icon(Icons.send, color: Colors.white),
        label: const Text('Enviar Reclamo', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
      ),
    );
  }
}

// 8. CÁMARA EN VIVO
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

// 9. ÁREAS COMUNES CON DÍAS REALES Y SELECTOR DE FECHA
class AreasComunesListScreen extends StatefulWidget {
  final String token;
  const AreasComunesListScreen({Key? key, required this.token}) : super(key: key);

  @override
  _AreasComunesListScreenState createState() => _AreasComunesListScreenState();
}

class _AreasComunesListScreenState extends State<AreasComunesListScreen> {
  List<dynamic> _areas = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _cargarAreas();
  }

  void _cargarAreas() async {
    final res = await ApiService.getAreasComunes(widget.token);
    if (res['success'] == true) {
      setState(() {
        _areas = res['data'] ?? [];
        _isLoading = false;
      });
    } else {
      setState(() {
        _isLoading = false;
      });
    }
  }

  void _modalReservar(dynamic area) {
    DateTime? fechaReserva;
    XFile? voucherImage;

    showDialog(
      context: context,
      builder: (ctx) => StatefulBuilder(
        builder: (context, setModalState) => AlertDialog(
          backgroundColor: const Color(0xFF0F172A),
          title: Text(
            '📅 Reservar ${area['nombre'] ?? 'Área Común'}',
            style: const TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold),
          ),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Costo de Reserva: ${area['precio_formateado'] ?? 'Gratuito'}',
                  style: const TextStyle(color: Color(0xFF38BDF8), fontWeight: FontWeight.bold, fontSize: 14),
                ),
                const SizedBox(height: 14),

                const Text('1. Selecciona la Fecha del Evento:', style: TextStyle(color: Colors.white70, fontSize: 12)),
                const SizedBox(height: 6),

                // BOTÓN SELECTOR DE FECHA (CALENDARIO)
                SizedBox(
                  width: double.infinity,
                  child: OutlinedButton.icon(
                    onPressed: () async {
                      final picked = await showDatePicker(
                        context: context,
                        initialDate: DateTime.now().add(const Duration(days: 1)),
                        firstDate: DateTime.now(),
                        lastDate: DateTime.now().add(const Duration(days: 90)),
                      );
                      if (picked != null) {
                        setModalState(() {
                          fechaReserva = picked;
                        });
                      }
                    },
                    icon: const Icon(Icons.calendar_month, color: Color(0xFF38BDF8)),
                    label: Text(
                      fechaReserva == null
                          ? '📅 Elegir Fecha de Reserva'
                          : '📅 Fecha: ${fechaReserva!.day}/${fechaReserva!.month}/${fechaReserva!.year}',
                      style: TextStyle(color: fechaReserva == null ? Colors.white54 : Colors.greenAccent, fontWeight: FontWeight.bold),
                    ),
                  ),
                ),

                const SizedBox(height: 14),

                const Text('2. Adjuntar Comprobante de Pago (Galería):', style: TextStyle(color: Colors.white70, fontSize: 12)),
                const SizedBox(height: 6),

                // BOTÓN SUBIR VOUCHER
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton.icon(
                    onPressed: () async {
                      final picker = ImagePicker();
                      final picked = await picker.pickImage(source: ImageSource.gallery);
                      if (picked != null) {
                        setModalState(() {
                          voucherImage = picked;
                        });
                      }
                    },
                    icon: const Icon(Icons.photo_library, size: 18),
                    label: Text(voucherImage == null ? '📷 SUBIR COMPROBANTE' : '✔ Comprobante Adjuntado'),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: voucherImage == null ? const Color(0xFF334155) : Colors.green,
                      foregroundColor: Colors.white,
                    ),
                  ),
                ),
              ],
            ),
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(ctx),
              child: const Text('Cancelar', style: TextStyle(color: Colors.white54)),
            ),
           ElevatedButton(
                style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFFF8B4C7)),
                onPressed: () async {
                  if (_fechaReserva == null) {
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(backgroundColor: Colors.orange, content: Text('Por favor elige la fecha de reserva.')),
                    );
                    return;
                  }

                  Navigator.pop(ctx);

                  final fechaFormatted = "${_fechaReserva!.year}-${_fechaReserva!.month.toString().padLeft(2, '0')}-${_fechaReserva!.day.toString().padLeft(2, '0')}";

                  final res = await ApiService.reservarAreaComun(
                    widget.token,
                    area['id'].toString(),
                    fechaFormatted,
                    _voucherImage != null ? _voucherImage!.path : null,
                  );

                  if (mounted) {
                    ScaffoldMessenger.of(context).showSnackBar(
                      SnackBar(
                        backgroundColor: res['success'] == true ? Colors.green : Colors.red,
                        content: Text(res['message'] ?? 'Solicitud de reserva enviada.'),
                      ),
                    );
                  }
                },
                child: const Text('Confirmar Reserva', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
              )
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF060913),
      appBar: AppBar(
        backgroundColor: const Color(0xFF0F172A),
        elevation: 0,
        title: const Text('Reserva de Áreas Comunes', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: Color(0xFF0284C7)))
          : _areas.isEmpty
              ? _buildVacio('Áreas Comunes', 'No hay áreas comunes configuradas para este edificio.')
              : ListView.builder(
                  padding: const EdgeInsets.all(16),
                  itemCount: _areas.length,
                  itemBuilder: (context, index) {
                    final item = _areas[index];
                    return _buildAreaCard(item);
                  },
                ),
    );
  }

  Widget _buildAreaCard(dynamic item) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: const Color(0xFF0F172A),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.white12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(item['nombre'] ?? 'Área Común', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
              Text(item['precio_formateado'] ?? 'Gratuito', style: const TextStyle(color: Color(0xFF8B5CF6), fontWeight: FontWeight.bold, fontSize: 14)),
            ],
          ),
          const SizedBox(height: 6),
          Text(item['descripcion'] ?? '', style: const TextStyle(color: Colors.white70, fontSize: 13)),
          const SizedBox(height: 14),
          SizedBox(
            width: double.infinity,
            child: ElevatedButton.icon(
              onPressed: () => _modalReservar(item),
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF8B5CF6),
                foregroundColor: Colors.white,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
              ),
              icon: const Icon(Icons.calendar_month, size: 18),
              label: const Text('📅 Reservar Espacio', style: TextStyle(fontWeight: FontWeight.bold)),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildVacio(String titulo, String desc) {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          const Icon(Icons.info_outline, color: Colors.white38, size: 48),
          const SizedBox(height: 12),
          Text(titulo, style: const TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
          const SizedBox(height: 4),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 24),
            child: Text(desc, style: const TextStyle(color: Colors.white54, fontSize: 13), textAlign: TextAlign.center),
          ),
        ],
      ),
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
        const SizedBox(height: 4),
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 24),
          child: Text(desc, style: const TextStyle(color: Colors.white54, fontSize: 13), textAlign: TextAlign.center),
        ),
      ],
    ),
  );
}