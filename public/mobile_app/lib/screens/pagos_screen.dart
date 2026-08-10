import 'package:flutter/material.dart';

class PagosScreen extends StatefulWidget {
  final String token;

  const PagosScreen({Key? key, required this.token}) : super(key: key);

  @override
  _PagosScreenState createState() => _PagosScreenState();
}

class _PagosScreenState extends State<PagosScreen> {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF060913),
      appBar: AppBar(
        backgroundColor: const Color(0xFF060913),
        elevation: 0,
        title: const Text('Mis Pagos & Recibos', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
      ),
      body: ListView(
        padding: const EdgeInsets.all(16.0),
        children: [
          _buildReciboCard(
            concepto: 'Cuota de Mantenimiento - Febrero 2026',
            monto: 'S/ 345.45',
            vencimiento: '12 de cada mes',
            estado: 'Pendiente',
            colorEstado: Colors.red,
          ),
          const SizedBox(height: 12),
          _buildReciboCard(
            concepto: 'Cuota de Mantenimiento - Enero 2026',
            monto: 'S/ 345.45',
            vencimiento: '12 de cada mes',
            estado: 'Pagado',
            colorEstado: Colors.green,
          ),
        ],
      ),
    );
  }

  Widget _buildReciboCard({
    required String concepto,
    required String monto,
    required String vencimiento,
    required String estado,
    required Color colorEstado,
  }) {
    return Container(
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
                  concepto,
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
                  estado,
                  style: TextStyle(color: colorEstado, fontSize: 11, fontWeight: FontWeight.bold),
                ),
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
                  onPressed: estado == 'Pagado' ? null : () {},
                  style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF10B981)),
                  icon: const Icon(Icons.credit_card, color: Colors.white, size: 18),
                  label: Text(estado == 'Pagado' ? 'Pagado' : 'Pagar Recibo', style: const TextStyle(color: Colors.white)),
                ),
              ),
            ],
          )
        ],
      ),
    );
  }
}