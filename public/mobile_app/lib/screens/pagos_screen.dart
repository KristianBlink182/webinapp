import 'package:flutter/material.dart';
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
              : ListView.builder(
                  padding: const EdgeInsets.all(16.0),
                  itemCount: _pagos.length,
                  itemBuilder: (context, index) {
                    final item = _pagos[index];
                    return _buildReciboCard(
                      concepto: item['concepto'] ?? 'Cuota de Mantenimiento',
                      monto: item['monto_formateado'] ?? 'S/ 0.00',
                      vencimiento: item['fecha_vencimiento'] ?? '12 de cada mes',
                      estado: item['estado'] ?? 'Pendiente',
                    );
                  },
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

  Widget _buildReciboCard({
    required String concepto,
    required String monto,
    required String vencimiento,
    required String estado,
  }) {
    final isPagado = estado.toLowerCase() == 'pagado';
    final isRevision = estado.toLowerCase().contains('revis');

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
                child: Text(concepto, style: const TextStyle(color: Colors.white, fontSize: 14, fontWeight: FontWeight.bold)),
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: colorEstado.withOpacity(0.2),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Text(textoEstado, style: TextStyle(color: colorEstado, fontSize: 11, fontWeight: FontWeight.bold)),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Text('Monto Total: $monto', style: const TextStyle(color: Color(0xFF38BDF8), fontSize: 16, fontWeight: FontWeight.bold)),
          Text('Vencimiento: $vencimiento', style: const TextStyle(color: Colors.white54, fontSize: 12)),
          const SizedBox(height: 16),
          Row(
            children: [
              Expanded(
                child: OutlinedButton.icon(
                  onPressed: () {},
                  icon: const Icon(Icons.picture_as_pdf, color: Colors.blueAccent, size: 18),
                  label: const Text('Ver PDF', style: TextStyle(color: Colors.blueAccent)),
                ),
              ),
              const SizedBox(width: 8),
              Expanded(
                child: ElevatedButton.icon(
                  onPressed: isPagado || isRevision ? null : () {},
                  style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF10B981)),
                  icon: Icon(isPagado ? Icons.check_circle : Icons.credit_card, color: Colors.white, size: 18),
                  label: Text(isPagado ? '🟢 Pagado' : (isRevision ? '🟡 Validando' : '💳 Pagar Recibo'), style: const TextStyle(color: Colors.white)),
                ),
              ),
            ],
          )
        ],
      ),
    );
  }
}