import 'dart:async';
import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'login_screen.dart';
import 'dashboard_screen.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({Key? key}) : super(key: key);

  @override
  _SplashScreenState createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> {
  @override
  void initState() {
    super.initState();
    _checkSesionGuardada();
  }

  void _checkSesionGuardada() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('auth_token');
    final vecinoNombre = prefs.getString('vecino_nombre') ?? 'Vecino';
    final departamentoNumero = prefs.getString('departamento_numero') ?? 'S/N';
    final condominioNombre = prefs.getString('condominio_nombre') ?? 'LIVO';

    Timer(const Duration(seconds: 5), () {
      if (token != null && token.isNotEmpty) {
        // Si ya está logueado, pasa directo al Dashboard
        Navigator.of(context).pushReplacement(
          MaterialPageRoute(
            builder: (context) => DashboardScreen(
              token: token,
              vecinoNombre: vecinoNombre,
              departamentoNumero: departamentoNumero,
              condominioNombre: condominioNombre,
            ),
          ),
        );
      } else {
        // Si no hay sesión, va al Login
        Navigator.of(context).pushReplacement(
          MaterialPageRoute(builder: (context) => const LoginScreen()),
        );
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF060913),
      body: Stack(
        children: [
          Positioned.fill(
            child: Image.asset(
              'assets/splash.png',
              fit: BoxFit.cover,
            ),
          ),
          Container(
            color: Colors.black.withOpacity(0.3),
          ),
        ],
      ),
    );
  }
}