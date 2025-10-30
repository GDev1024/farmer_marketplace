import 'package:flutter/material.dart';
import '../services/enhanced_storage_service.dart';
import '../models/user_model.dart';

class DatabaseTestScreen extends StatefulWidget {
  const DatabaseTestScreen({super.key});

  @override
  State<DatabaseTestScreen> createState() => _DatabaseTestScreenState();
}

class _DatabaseTestScreenState extends State<DatabaseTestScreen> {
  String _status = 'Ready to test...';

  Future<void> _testDatabase() async {
    setState(() => _status = 'Testing database...');

    try {
      // Test registration
      UserModel? newUser = await EnhancedStorageService.registerUser(
        name: 'Test Farmer',
        email: 'test.farmer@local.com',
        password: 'password123',
        userType: 'farmer',
      );

      if (newUser != null) {
        setState(() => _status = 'Registration successful! User ID: ${newUser.id}');
        
        // Test login
        UserModel? loginUser = await EnhancedStorageService.loginUser(
          email: 'test.farmer@local.com',
          password: 'password123',
        );
        
        if (loginUser != null) {
          setState(() => _status = 'Login successful! Welcome ${loginUser.name}');
          
          // Test products
          var products = await EnhancedStorageService.getProducts();
          setState(() => _status = 'Found ${products.length} products in database');
        }
      }
    } catch (e) {
      setState(() => _status = 'Error: $e');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Database Test')),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          children: [
            Text(
              _status,
              style: const TextStyle(fontSize: 16),
            ),
            const SizedBox(height: 20),
            ElevatedButton(
              onPressed: _testDatabase,
              child: const Text('Test Local Database'),
            ),
          ],
        ),
      ),
    );
  }
}