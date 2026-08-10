import 'dart:async';
import 'package:flutter/material.dart';
import 'login_screen.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({Key? key}) : super(key: key);

  @override
  _SplashScreenState createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> {
  @override
  void initState() {
    super.initState();
    // Temporizador de 5 segundos de Splash Nativo
    Timer(const Duration(seconds: 5), () {
      Navigator.of(context).pushReplacement(
        MaterialPageRoute(builder: (context) => const LoginScreen()),
      );
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF060913),
      body: Stack(
        children: [
          // Imagen panorámica de fondo completa
          Positioned.fill(
            child: Image.asset(
              'public/splash.png',
              fit: BoxFit.cover,
            ),
          ),
          // Capa oscura para contraste
          Container(
            color: Colors.black.withOpacity(0.3),
          ),
        ],
      ),
    );
  }
}