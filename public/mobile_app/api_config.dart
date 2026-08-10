class ApiConfig {
  // URL Oficial de la API RESTful en Producción
  static const String baseUrl = 'https://vecino.livo.com.pe/api/v1';

  // Endpoints
  static const String login = '$baseUrl/auth/login';
  static const String me = '$baseUrl/auth/me';
  static const String logout = '$baseUrl/auth/logout';

  static const String dashboard = '$baseUrl/vecino/dashboard';
  static const String pagos = '$baseUrl/vecino/pagos';
  static const String reportarPago = '$baseUrl/vecino/pagos'; // /{id}/reportar
  static const String sos = '$baseUrl/vecino/sos';
  static const String comunicados = '$baseUrl/vecino/comunicados';
  static const String mascotas = '$baseUrl/vecino/mascotas';
  static const String reclamos = '$baseUrl/vecino/reclamos';
}