import 'dart:convert';
import 'package:http/http.dart' as http;
import 'api_config.dart';

class ApiService {
  static const String base = 'https://admin.livo.com.pe/api/v1';

  // 1. LOGIN
  static Future<Map<String, dynamic>> login(String email, String password) async {
    try {
      final res = await http.post(
        Uri.parse(ApiConfig.login),
        headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
        body: jsonEncode({'email': email, 'password': password}),
      );
      final data = jsonDecode(res.body);
      return res.statusCode == 200 ? data : {'success': false, 'message': data['message'] ?? 'Error de inicio de sesión.'};
    } catch (e) {
      return {'success': false, 'message': 'Error de red: $e'};
    }
  }

  // 2. DASHBOARD
  static Future<Map<String, dynamic>> getDashboard(String token) async {
    try {
      final res = await http.get(Uri.parse('$base/vecino/dashboard'), headers: _headers(token));
      return jsonDecode(res.body);
    } catch (e) {
      return {'success': false};
    }
  }

  // 3. S.O.S.
  static Future<Map<String, dynamic>> dispararSOS(String token) async {
    try {
      final res = await http.post(Uri.parse('$base/vecino/sos'), headers: _headers(token));
      return jsonDecode(res.body);
    } catch (e) {
      return {'success': false, 'message': 'Error S.O.S.'};
    }
  }

  // 4. INVITADOS
  static Future<Map<String, dynamic>> getInvitados(String token) async {
    try {
      final res = await http.get(Uri.parse('$base/vecino/invitados'), headers: _headers(token));
      return jsonDecode(res.body);
    } catch (e) {
      return {'success': false};
    }
  }

  static Future<Map<String, dynamic>> registrarInvitado(String token, String nombre, String dni, String tipo) async {
    try {
      final res = await http.post(
        Uri.parse('$base/vecino/invitados'),
        headers: _headers(token),
        body: jsonEncode({'nombre': nombre, 'dni': dni, 'tipo': tipo}),
      );
      return jsonDecode(res.body);
    } catch (e) {
      return {'success': false};
    }
  }

  // 5. PAGOS
  static Future<Map<String, dynamic>> getPagos(String token) async {
    try {
      final res = await http.get(Uri.parse('$base/vecino/pagos'), headers: _headers(token));
      return jsonDecode(res.body);
    } catch (e) {
      return {'success': false};
    }
  }

  // 6. COMUNICADOS
  static Future<Map<String, dynamic>> getComunicados(String token) async {
    try {
      final res = await http.get(Uri.parse('$base/vecino/comunicados'), headers: _headers(token));
      return jsonDecode(res.body);
    } catch (e) {
      return {'success': false};
    }
  }

  // 7. MARKETPLACE
  static Future<Map<String, dynamic>> getMarketplace(String token) async {
    try {
      final res = await http.get(Uri.parse('$base/vecino/marketplace'), headers: _headers(token));
      return jsonDecode(res.body);
    } catch (e) {
      return {'success': false};
    }
  }

  static Future<Map<String, dynamic>> registrarMarketplace(String token, String titulo, String precio, String desc) async {
    try {
      final res = await http.post(
        Uri.parse('$base/vecino/marketplace'),
        headers: _headers(token),
        body: jsonEncode({'titulo': titulo, 'precio': precio, 'descripcion': desc}),
      );
      return jsonDecode(res.body);
    } catch (e) {
      return {'success': false};
    }
  }

  // 8. VOTACIONES
  static Future<Map<String, dynamic>> getVotaciones(String token) async {
    try {
      final res = await http.get(Uri.parse('$base/vecino/votaciones'), headers: _headers(token));
      return jsonDecode(res.body);
    } catch (e) {
      return {'success': false};
    }
  }

  // 9. DOCUMENTOS
  static Future<Map<String, dynamic>> getDocumentos(String token) async {
    try {
      final res = await http.get(Uri.parse('$base/vecino/documentos'), headers: _headers(token));
      return jsonDecode(res.body);
    } catch (e) {
      return {'success': false};
    }
  }

  // 10. MASCOTAS
  static Future<Map<String, dynamic>> getMascotas(String token) async {
    try {
      final res = await http.get(Uri.parse('$base/vecino/mascotas'), headers: _headers(token));
      return jsonDecode(res.body);
    } catch (e) {
      return {'success': false};
    }
  }

  static Future<Map<String, dynamic>> registrarMascota(String token, String nombre, String tipo, String raza) async {
    try {
      final res = await http.post(
        Uri.parse('$base/vecino/mascotas'),
        headers: _headers(token),
        body: jsonEncode({'nombre': nombre, 'tipo': tipo, 'raza': raza}),
      );
      return jsonDecode(res.body);
    } catch (e) {
      return {'success': false};
    }
  }

  // 11. RECLAMOS
  static Future<Map<String, dynamic>> getReclamos(String token) async {
    try {
      final res = await http.get(Uri.parse('$base/vecino/reclamos'), headers: _headers(token));
      return jsonDecode(res.body);
    } catch (e) {
      return {'success': false};
    }
  }

  static Future<Map<String, dynamic>> registrarReclamo(String token, String asunto, String desc) async {
    try {
      final res = await http.post(
        Uri.parse('$base/vecino/reclamos'),
        headers: _headers(token),
        body: jsonEncode({'asunto': asunto, 'descripcion': desc}),
      );
      return jsonDecode(res.body);
    } catch (e) {
      return {'success': false};
    }
  }

  // HEADERS AUXILIARES
  static Map<String, String> _headers(String token) => {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      };
}