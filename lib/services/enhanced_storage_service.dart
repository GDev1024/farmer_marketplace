import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/user_model.dart';
import '../models/product_model.dart';
import 'local_auth_service.dart';
import 'local_product_service.dart';

class EnhancedStorageService {
  // Session management with SharedPreferences
  static Future<void> saveCurrentUser(UserModel user) async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    await prefs.setString('currentUser', jsonEncode(user.toMap()));
    await prefs.setString('userType', user.userType);
    await prefs.setBool('isLoggedIn', true);
  }

  static Future<UserModel?> getCurrentUser() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    String? userString = prefs.getString('currentUser');
    if (userString != null) {
      Map<String, dynamic> userMap = jsonDecode(userString);
      return UserModel.fromMap(userMap);
    }
    return null;
  }

  static Future<bool> isLoggedIn() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    return prefs.getBool('isLoggedIn') ?? false;
  }

  static Future<String?> getUserType() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    return prefs.getString('userType');
  }

  static Future<void> logout() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    await prefs.remove('currentUser');
    await prefs.remove('userType');
    await prefs.setBool('isLoggedIn', false);
  }

  // Authentication methods using local database
  static Future<UserModel?> registerUser({
    required String name,
    required String email,
    required String password,
    required String userType,
  }) async {
    UserModel? user = await LocalAuthService.registerUser(
      name: name,
      email: email,
      password: password,
      userType: userType,
    );
    
    if (user != null) {
      await saveCurrentUser(user);
    }
    
    return user;
  }

  static Future<UserModel?> loginUser({
    required String email,
    required String password,
  }) async {
    UserModel? user = await LocalAuthService.loginUser(
      email: email,
      password: password,
    );
    
    if (user != null) {
      await saveCurrentUser(user);
    }
    
    return user;
  }

  // Product methods using local database
  static Future<List<ProductModel>> getProducts() async {
    return await LocalProductService.getAllProducts();
  }

  static Future<List<ProductModel>> getProductsByFarmerId(String farmerId) async {
    return await LocalProductService.getProductsByFarmerId(farmerId);
  }

  static Future<void> saveProduct(ProductModel product) async {
    await LocalProductService.addProduct(product);
  }

  static Future<void> deleteProduct(String productId) async {
    await LocalProductService.deleteProduct(productId);
  }

  static Future<ProductModel?> getProductById(String productId) async {
    return await LocalProductService.getProductById(productId);
  }

  static Future<List<ProductModel>> searchProducts(String searchTerm) async {
    return await LocalProductService.searchProducts(searchTerm);
  }


}