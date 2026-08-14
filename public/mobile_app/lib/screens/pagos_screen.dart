import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:http/http.dart' as http;
import '../api_service.dart';
import '../api_config.dart';

class PagosScreen extends StatefulWidget {
  final String token;
  const PagosScreen({Key? key, required this.token}) : super(key: key);

  @override
  _PagosScreenState createState() => _PagosScreenState();
}

class _PagosScreenState extends State<PagosScreen> {
  List<dynamic> _pagos = [];
  Map<String, dynamic> _cuentasBancarias = {};
  bool _isLoading = true;
  XFile? _voucherImage;
  bool _isUploading = false;

  @override
  void initState() {
    super.initState();
    _cargarPagos();
  }

  void _cargarPagos() async {
    final res = await ApiService.getPagos(widget.token);
    if (res['success'] == true) {
      setState(() {
        _pagos = res['data'] ?? [];
        _cuentasBancarias = res['cuentas_bancarias'] ?? {};
        _isLoading = false;
      });
    } else {
      setState(() {
        _isLoading = false;
      });
    }
  }

  void _abrirPdf(String url) async {
    final Uri pdfUrl = Uri.parse(url);
    if (await canLaunchUrl(pdfUrl)) {
      await launchUrl(pdfUrl, mode: LaunchMode.externalApplication);
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('No se pudo abrir el archivo PDF.')),
      );
    }
  }

  void _modalPagar(dynamic pago) {
    _voucherImage = null;

    String conceptoTxt = (pago['concepto'] ?? 'Cuota de Mantenimiento').toString();
    String mesTxt = (pago['mes'] ?? '').toString();
    if (!conceptoTxt.contains(mesTxt) && mesTxt.isNotEmpty) {
      conceptoTxt = "$conceptoTxt - $mesTxt";
    }

    showDialog(
      context: context,
      builder: (BuildContext ctx) {
        return StatefulBuilder(
          builder: (BuildContext context, StateSetter setModalState) {
            return AlertDialog(
              backgroundColor: const Color(0xFF0F172A),
              title: const Text(
                '💳 Adjuntar Comprobante de Pago',
                style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold),
              ),
              content: SingleChildScrollView(
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Concepto: $conceptoTxt',
                      style: const TextStyle(color: Colors.white70, fontSize: 13),
                    ),
                    Text(
                      'Monto Total: ${pago['monto_formateado'] ?? 'S/ 0.00'}',
                      style: const TextStyle(color: Color(0xFF38BDF8), fontWeight: FontWeight.bold, fontSize: 16),
                    ),
                    const SizedBox(height: 14),

                    // Cuentas Bancarias Oficiales del Edificio
                    if (_cuentasBancarias.isNotEmpty)
                      Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          color: const Color(0xFF1E293B),
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(color: Colors.white12),
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text('🏦 Cuentas Oficiales de Depósito:', style: TextStyle(color: Colors.amberAccent, fontSize: 12, fontWeight: FontWeight.bold)),
                            const SizedBox(height: 6),
                            if (_cuentasBancarias['titular'] != null)
                              Text('Titular: ${_cuentasBancarias['titular']}', style: const TextStyle(color: Colors.white70, fontSize: 11)),
                            if (_cuentasBancarias['banco'] != null)
                              Text('Banco: ${_cuentasBancarias['banco']} - N° ${_cuentasBancarias['numero_cuenta'] ?? ''}', style: const TextStyle(color: Colors.white70, fontSize: 11)),
                            if (_cuentasBancarias['cci'] != null && _cuentasBancarias['cci'] != 'N/A')
                              Text('CCI: ${_cuentasBancarias['cci']}', style: const TextStyle(color: Colors.white70, fontSize: 11)),
                            if (_cuentasBancarias['yape_plin'] != null)
                              Text('Yape/Plin: ${_cuentasBancarias['yape_plin']}', style: const TextStyle(color: Colors.greenAccent, fontSize: 11, fontWeight: FontWeight.bold)),
                          ],
                        ),
                      ),

                    const SizedBox(height: 16),

                    // BOTÓN ÚNICO SEGURO DE GALERÍA: SUBIR PAGO
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton.icon(
                        onPressed: () async {
                          final picker = ImagePicker();
                          final picked = await picker.pickImage(source: ImageSource.gallery);
                          if (picked != null) {
                            setModalState(() {
                              _voucherImage = picked;
                            });
                          }
                        },
                        icon: const Icon(Icons.photo_library, size: 20),
                        label: const Text('📷 SUBIR PAGO', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: const Color(0xFF0284C7),
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 14),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                        ),
                      ),
                    ),

                    const SizedBox(height: 12),

                    // Previsualización de Comprobante Adjunto
                    if (_voucherImage != null)
                      Container(
                        padding: const EdgeInsets.all(10),
                        decoration: BoxDecoration(
                          color: Colors.green.withOpacity(0.2),
                          borderRadius: BorderRadius.circular(8),
                          border: Border.all(color: Colors.green.withOpacity(0.4)),
                        ),
                        child: Row(
                          children: [
                            const Icon(Icons.check_circle, color: Colors.greenAccent, size: 20),
                            const SizedBox(width: 8),
                            Expanded(
                              child: Text(
                                'Comprobante listo: ${_voucherImage!.name}',
                                style: const TextStyle(color: Colors.greenAccent, fontSize: 12, fontWeight: FontWeight.bold),
                                overflow: TextOverflow.ellipsis,
                              ),
                            ),
                          ],
                        ),
                      )
                    else
                      const Text(
                        'Selecciona la foto de tu Yape, Plin o Transferencia.',
                        style: TextStyle(color: Colors.white54, fontSize: 12),
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
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF10B981),
                    foregroundColor: Colors.white,
                  ),
                  onPressed: (_voucherImage == null || _isUploading)
                      ? null
                      : () async {
                          setModalState(() {
                            _isUploading = true;
                          });

                          try {
                            var uri = Uri.parse('${ApiConfig.baseUrl}/vecino/pagos/reportar');
                            var request = http.MultipartRequest('POST', uri);
                            request.headers['Authorization'] = 'Bearer ${widget.token}';
                            request.headers['Accept'] = 'application/json';

                            request.fields['pago_id'] = pago['id'].toString();
                            request.files.add(
                              await http.MultipartFile.fromPath('voucher', _voucherImage!.path),
                            );

                            var response = await request.send();

                            Navigator.pop(ctx);

                            if (response.statusCode == 200) {
                              ScaffoldMessenger.of(context).showSnackBar(
                                const SnackBar(
                                  backgroundColor: Colors.green,
                                  content: Text('Comprobante adjuntado con éxito. Queda en estado Validando.'),
                                ),
                              );
                              _cargarPagos();
                            } else {
                              ScaffoldMessenger.of(context).showSnackBar(
                                const SnackBar(
                                  backgroundColor: Colors.red,
                                  content: Text('Error al enviar comprobante al servidor.'),
                                ),
                              );
                            }
                          } catch (e) {
                            Navigator.pop(ctx);
                            ScaffoldMessenger.of(context).showSnackBar(
                              SnackBar(
                                backgroundColor: Colors.red,
                                content: Text('Error de conexión: $e'),
                              ),
                            );
                          } finally {
                            _isUploading = false;
                          }
                        },
                  child: _isUploading
                      ? const SizedBox(width: 16, height: 16, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                      : const Text('Enviar Comprobante'),
                ),
              ],
            );
          },
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF060913),
      appBar: AppBar(
        backgroundColor: const Color(0xFF0F172A),
        elevation: 0,
        title: const Text('Mis Pagos & Recibos', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: Color(0xFF0284C7)))
          : _pagos.isEmpty
              ? Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: const [
                      Icon(Icons.check_circle_outline, color: Colors.greenAccent, size: 48),
                      SizedBox(height: 12),
                      Text('¡Estás al día!', style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
                      SizedBox(height: 4),
                      Text('No hay recibos pendientes de pago.', style: TextStyle(color: Colors.white54, fontSize: 13)),
                    ],
                  ),
                )
              : RefreshIndicator(
                  onRefresh: () async => _cargarPagos(),
                  child: ListView.builder(
                    padding: const EdgeInsets.all(16),
                    itemCount: _pagos.length,
                    itemBuilder: (context, index) {
                      final item = _pagos[index];
                      return _buildReciboCard(item);
                    },
                  ),
                ),
    );
  }

  Widget _buildReciboCard(dynamic item) {
    final String estado = (item['estado'] ?? 'Pendiente').toString();
    final bool isPagado = estado.toLowerCase() == 'pagado' || estado.toLowerCase() == 'al dia';
    final bool isRevision = estado.toLowerCase() == 'revision' || estado.toLowerCase() == 'validando' || estado.toLowerCase() == 'en_revision';

    Color colorEstado = Colors.red;
    String textoEstado = "Pendiente";

    if (isPagado) {
      colorEstado = Colors.green;
      textoEstado = "Pagado";
    } else if (isRevision) {
      colorEstado = Colors.amber;
      textoEstado = "Validando";
    }

    String conceptoBase = (item['concepto'] ?? 'Cuota de Mantenimiento').toString();
    String mesStr = (item['mes'] ?? '').toString();
    if (!conceptoBase.contains(mesStr) && mesStr.isNotEmpty) {
      conceptoBase = "$conceptoBase - $mesStr";
    }

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
              Expanded(
                child: Text(
                  conceptoBase,
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
          const SizedBox(height: 14),
          Row(
            children: [
              Expanded(
                child: OutlinedButton.icon(
                  onPressed: () => _abrirPdf(item['recibo_pdf_url'] ?? '#'),
                  icon: const Icon(Icons.picture_as_pdf, color: Colors.lightBlueAccent, size: 18),
                  label: const Text('Ver PDF', style: TextStyle(color: Colors.lightBlueAccent)),
                ),
              ),
              const SizedBox(width: 8),
              Expanded(
                child: ElevatedButton.icon(
                  onPressed: (isPagado || isRevision) ? null : () => _modalPagar(item),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF10B981),
                    disabledBackgroundColor: isPagado ? Colors.green.withOpacity(0.3) : Colors.amber.withOpacity(0.3),
                  ),
                  icon: Icon(
                    isPagado ? Icons.check_circle : (isRevision ? Icons.access_time : Icons.upload_file),
                    color: Colors.white,
                    size: 18,
                  ),
                  label: Text(
                    isPagado ? 'Pagado' : (isRevision ? 'Validando' : '📷 SUBIR PAGO'),
                    style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}