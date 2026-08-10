import 'package:flutter/material.dart';
import 'screens/splash_screen.dart';

void main() {
  runApp(const LivoVecinosApp());
}

class LivoVecinosApp extends StatelessWidget {
  const LivoVecinosApp({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'LIVO Vecinos',
      debugShowCheckedModeBanner: false,
      theme: ThemeData.dark().copyWith(
        scaffoldBackgroundColor: const Color(0xFF060913),
        primaryColor: const Color(0xFF0284C7),
      ),
      home: const SplashScreen(),
    );
  }
}