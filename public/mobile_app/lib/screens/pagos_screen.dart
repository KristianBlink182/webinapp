import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:url_launcher/url_launcher.dart';
import '../api_service.dart';

class PagosScreen extends StatefulWidget {
  final String token;

  const PagosScreen({Key? key, required this.token}) : super(key: key);

  @override
  _PagosScreenState createState() => _PagosScreenState();
}

class _PagosScreenState extends State<PagosScreen> {
  List<dynamic> _pagos = [];
  bool _isLoading = true;
  XFile? _voucherImage;

  @override
  void initState() {
    super.initState();
    _cargarPagos();
  }

  void _cargarPagos() async {
    final result = await ApiService.getPagos(widget.token);
    if (result['success'] == true) {
      setState(() {
        _pagos = result['data'] ?? [];
        _isLoading = false;
      });
    } else {
      setState(() => _isLoading = false);
    }
  }

  void _abrirPdf(String url) async {
    final Uri pdfUri = Uri.parse(url);
    if (await canLaunchUrl(pdfUri)) {
      await launchUrl(pdfUri, mode: LaunchMode.externalApplication);
    }
  }

  void _modalPagar(dynamic pago) {
    _voucherImage = null;

    showDialog(
      context: context,
      builder: (ctx) => StatefulWidget(
        builder: (context, setModalState) => AlertDialog(
          backgroundColor: const Color(0xFF0F172A),
          title: const Text('💳 Reportar Comprobante de Pago', style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text('Concepto:', style: TextStyle(color: Colors.white70, fontSize: 12)),
              Text(pago['concepto'] ?? '', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 14)),
              const SizedBox(height: 12),
              const Text('Monto Total:', style: TextStyle(color: Colors.white70, fontSize: 12)),
              Text(pago['monto_formateado'] ?? '', style: const TextStyle(color: Color(0xFF38BDF8), fontWeight: FontWeight.bold, fontSize: 18)),
              const SizedBox(height: 16),
              OutlinedButton.icon(
                onPressed: () async {
                  final picker = ImagePicker();
                  final picked = await picker.pickImage(source: ImageSource.gallery);
                  if (picked != null) {
                    setModalState(() => _voucherImage = picked);
                  }
                },
                icon: Icon(_voucherImage != null ? Icons.check_circle : Icons.camera_alt, color: const Color(0xFF10B981)),
                label: Text(
                  _voucherImage != null ? '✅ Comprobante Adjuntado' : '📸 Adjuntar Voucher Yape/Plin',
                  style: const TextStyle(color: Color(0xFF10B981)),
                ),
              ),
            ],
          ),
          actions: [
            TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Cancelar', style: TextStyle(color: Colors.white54))),
            ElevatedButton(
              style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF10B981)),
              onPressed: () {
                Navigator.pop(ctx);
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(backgroundColor: Colors.green, content: Text('Comprobante enviado a la administración. Estado actual: Validando Pago.')),
                );
                _cargarPagos();
              },
              child: const Text('🚀 Enviar Pago', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
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
        backgroundColor: const Color(0xFF060913),
        elevation: 0,
        title: const Text('Mis Pagos & Recibos', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: Color(0xFF0284C7)))
          : _pagos.isEmpty
              ? _buildListaVacia()
              : RefreshIndicator(
                  onRefresh: () async => _cargarPagos(),
                  child: ListView.builder(
                    padding: const EdgeInsets.all(16.0),
                    itemCount: _pagos.length,
                    itemBuilder: (context, index) {
                      final item = _pagos[index];
                      return _buildReciboCard(item);
                    },
                  ),
                ),
    );
  }

  Widget _buildListaVacia() {
    return const Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.check_circle_outline, color: Colors.greenAccent, size: 48),
          SizedBox(height: 12),
          Text('¡Estás al día!', style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
          SizedBox(height: 6),
          Text('No hay recibos pendientes de pago.', style: TextStyle(color: Colors.white54, fontSize: 13)),
        ],
      ),
    );
  }

  Widget _buildReciboCard(dynamic item) {
    final estado = (item['estado'] ?? 'Pendiente').toString().toLowerCase();
    final isPagado = estado == 'pagado' || estado == 'aprobado';
    final isRevision = estado.contains('revis') || estado.contains('proces');

    Color colorEstado = Colors.red;
    String textoEstado = 'Pendiente';

    if (isPagado) {
      colorEstado = Colors.green;
      textoEstado = 'Pagado';
    } else if (isRevision) {
      colorEstado = Colors.amber;
      textoEstado = 'Validando Pago';
    }

    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: const Color(0xFF0F172A),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.white10),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Expanded(
                child: Text(
                  item['concepto'] ?? 'Cuota de Mantenimiento',
                  style: const TextStyle(color: Colors.white, fontSize: 14, fontWeight: FontWeight.bold),
                ),
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: colorEstado.withOpacity(0.2),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Text(
                  textoEstado,
                  style: TextStyle(color: colorEstado, fontSize: 11, fontWeight: FontWeight.bold),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Text(
            'Monto Total: ${item['monto_formateado'] ?? 'S/ 0.00'}',
            style: const TextStyle(color: Color(0xFF38BDF8), fontSize: 16, fontWeight: FontWeight.bold),
          ),
          Text(
            'Vencimiento: ${item['fecha_vencimiento'] ?? '12 de cada mes'}',
            style: const TextStyle(color: Colors.white54, fontSize: 12),
          ),
          const SizedBox(height: 16),
          Row(
            children: [
              Expanded(
                child: OutlinedButton.icon(
                  onPressed: () => _abrirPdf(item['recibo_pdf_url'] ?? '#'),
                  icon: const Icon(Icons.picture_as_pdf, color: Colors.blueAccent, size: 18),
                  label: const Text('Ver PDF', style: TextStyle(color: Colors.blueAccent)),
                ),
              ),
              const SizedBox(width: 8),
              Expanded(
                child: ElevatedButton.icon(
                  onPressed: isPagado || isRevision ? null : () => _modalPagar(item),
                  style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF10B981)),
                  icon: Icon(isPagado ? Icons.check_circle : (isRevision ? Icons.access_time : Icons.credit_card), color: Colors.white, size: 18),
                  label: Text(
                    isPagado ? '🟢 Pagado' : (isRevision ? '🟡 Validando' : '💳 Pagar Recibo'),
                    style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
                  ),
                ),
              ),
            ],
          )
        ],
      ),
    );
  }
}