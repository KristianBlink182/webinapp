import 'dart:convert';
import 'package:http/http.dart' as http;
import 'api_config.dart';

class ApiService {
  static Future<Map<String, dynamic>> login(String email, String password) async {
    try {
      final response = await http.post(
        Uri.parse(ApiConfig.login),
        headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
        body: jsonEncode({'email': email, 'password': password}),
      );

      final data = jsonDecode(response.body);
      if (response.statusCode == 200) {
        return data;
      } else {
        return {
          'success': false,
          'message': data['message'] ?? 'Error de autenticación (${response.statusCode}).',
        };
      }
    } catch (e) {
      return {'success': false, 'message': 'Error de conexión: $e'};
    }
  }

  static Future<Map<String, dynamic>> getDashboard(String token) async {
    try {
      final response = await http.get(
        Uri.parse(ApiConfig.dashboard),
        headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'Authorization': 'Bearer $token'},
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Error al cargar dashboard.'};
    }
  }

  static Future<Map<String, dynamic>> getPagos(String token) async {
    try {
      final response = await http.get(
        Uri.parse(ApiConfig.pagos),
        headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'Authorization': 'Bearer $token'},
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Error al cargar pagos.'};
    }
  }

  static Future<Map<String, dynamic>> dispararSOS(String token) async {
    try {
      final response = await http.post(
        Uri.parse(ApiConfig.sos),
        headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'Authorization': 'Bearer $token'},
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Error al enviar alerta S.O.S.'};
    }
  }

  static Future<Map<String, dynamic>> getComunicados(String token) async {
    try {
      final response = await http.get(
        Uri.parse(ApiConfig.comunicados),
        headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'Authorization': 'Bearer $token'},
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Error al cargar comunicados.'};
    }
  }

  static Future<Map<String, dynamic>> getMascotas(String token) async {
    try {
      final response = await http.get(
        Uri.parse(ApiConfig.mascotas),
        headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'Authorization': 'Bearer $token'},
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Error al cargar mascotas.'};
    }
  }

  static Future<Map<String, dynamic>> getReclamos(String token) async {
    try {
      final response = await http.get(
        Uri.parse(ApiConfig.reclamos),
        headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'Authorization': 'Bearer $token'},
      );
      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Error al cargar reclamos.'};
    }
  }
}