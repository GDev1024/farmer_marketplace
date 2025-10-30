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
      // Initialize database first
      await EnhancedStorageService.initializeDatabase();
      
      // Test login with pre-loaded accounts
      setState(() => _status = 'Testing farmer login...');
      
      UserModel? farmerUser = await EnhancedStorageService.loginUser(
        email: 'farmer@test.com',
        password: 'farmer123',
      );
      
      if (farmerUser != null) {
        setState(() => _status = 'Farmer login successful! Name: ${farmerUser.name}');
        
        // Test consumer login
        UserModel? consumerUser = await EnhancedStorageService.loginUser(
          email: 'consumer@test.com',
          password: 'consumer123',
        );
        
        if (consumerUser != null) {
          setState(() => _status = 'Both test accounts work! Consumer: ${consumerUser.name}');
          
          // Test products
          var products = await EnhancedStorageService.getProducts();
          setState(() => _status = 'Success! Found ${products.length} products. Test accounts are working.');
        } else {
          setState(() => _status = 'Farmer login works, but consumer login failed');
        }
      } else {
        setState(() => _status = 'Farmer login failed - checking if users exist...');
        
        // Try to create the test users manually
        await EnhancedStorageService.registerUser(
          name: 'John Smith',
          email: 'farmer@test.com',
          password: 'farmer123',
          userType: 'farmer',
        );
        
        await EnhancedStorageService.registerUser(
          name: 'Mary Johnson', 
          email: 'consumer@test.com',
          password: 'consumer123',
          userType: 'consumer',
        );
        
        setState(() => _status = 'Created test users manually. Try logging in now!');
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