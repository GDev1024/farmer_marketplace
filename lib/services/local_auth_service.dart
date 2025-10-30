import '../models/user_model.dart';
import 'local_database_service.dart';

class LocalAuthService {
  static const String _table = 'users';

  // Register new user
  static Future<UserModel?> registerUser({
    required String name,
    required String email,
    required String password,
    required String userType,
  }) async {
    try {
      final db = await LocalDatabaseService.database;
      
      // Check if email already exists
      List<Map<String, dynamic>> existing = await db.query(
        _table,
        where: 'email = ?',
        whereArgs: [email],
      );
      
      if (existing.isNotEmpty) {
        print('Email already exists');
        return null;
      }
      
      // Create new user
      String userId = 'user_${DateTime.now().millisecondsSinceEpoch}';
      UserModel user = UserModel(
        id: userId,
        name: name,
        email: email,
        userType: userType,
      );
      
      Map<String, dynamic> userMap = user.toMap();
      userMap['password'] = password; // Add password to database
      
      await db.insert(_table, userMap);
      return user;
    } catch (e) {
      print('Registration error: $e');
      return null;
    }
  }

  // Login user
  static Future<UserModel?> loginUser({
    required String email,
    required String password,
  }) async {
    try {
      final db = await LocalDatabaseService.database;
      
      // Debug: Check if any users exist
      List<Map<String, dynamic>> allUsers = await db.query(_table);
      print('Total users in database: ${allUsers.length}');
      for (var user in allUsers) {
        print('User: ${user['email']} (${user['userType']})');
      }
      
      List<Map<String, dynamic>> results = await db.query(
        _table,
        where: 'email = ? AND password = ?',
        whereArgs: [email, password],
      );
      
      print('Login attempt: $email / $password');
      print('Query results: ${results.length}');
      
      if (results.isNotEmpty) {
        Map<String, dynamic> userMap = Map<String, dynamic>.from(results.first);
        userMap.remove('password'); // Remove password from returned data
        print('Login successful for: ${userMap['email']}');
        return UserModel.fromMap(userMap);
      }
      
      print('Login failed: No matching user found');
      return null;
    } catch (e) {
      print('Login error: $e');
      return null;
    }
  }

  // Get user by ID
  static Future<UserModel?> getUserById(String userId) async {
    try {
      final db = await LocalDatabaseService.database;
      
      List<Map<String, dynamic>> results = await db.query(
        _table,
        where: 'id = ?',
        whereArgs: [userId],
      );
      
      if (results.isNotEmpty) {
        Map<String, dynamic> userMap = Map<String, dynamic>.from(results.first);
        userMap.remove('password'); // Remove password from returned data
        return UserModel.fromMap(userMap);
      }
      
      return null;
    } catch (e) {
      print('Get user error: $e');
      return null;
    }
  }

  // Update user profile
  static Future<bool> updateUser(UserModel user) async {
    try {
      final db = await LocalDatabaseService.database;
      
      int result = await db.update(
        _table,
        user.toMap(),
        where: 'id = ?',
        whereArgs: [user.id],
      );
      
      return result > 0;
    } catch (e) {
      print('Update user error: $e');
      return false;
    }
  }
}