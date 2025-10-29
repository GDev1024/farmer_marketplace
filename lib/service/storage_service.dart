import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/user_model.dart';
import '../models/product_model.dart';
import '../models/message_model.dart';

class StorageService {
  static Future<void> saveCurrentUser(UserModel user) async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    await prefs.setString('currentUser', jsonEncode(user.toMap()));
    await prefs.setBool('isLoggedIn', true);
    await prefs.setString('userType', user.userType);
  }

  static Future<UserModel?> getCurrentUser() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    String? userData = prefs.getString('currentUser');
    if (userData != null) {
      return UserModel.fromMap(jsonDecode(userData));
    }
    return null;
  }

  static Future<void> logout() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    await prefs.remove('currentUser');
    await prefs.setBool('isLoggedIn', false);
    await prefs.remove('userType');
  }

  // Mock database storage
  static Future<List<ProductModel>> getProducts() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    String? productsJson = prefs.getString('products');
    if (productsJson != null) {
      List<dynamic> productsList = jsonDecode(productsJson);
      return productsList.map((p) => ProductModel.fromMap(p)).toList();
    }
    return _getMockProducts();
  }

  static Future<void> saveProduct(ProductModel product) async {
    List<ProductModel> products = await getProducts();
    products.add(product);
    SharedPreferences prefs = await SharedPreferences.getInstance();
    await prefs.setString('products', 
      jsonEncode(products.map((p) => p.toMap()).toList()));
  }

  static Future<void> deleteProduct(String productId) async {
    List<ProductModel> products = await getProducts();
    products.removeWhere((p) => p.id == productId);
    SharedPreferences prefs = await SharedPreferences.getInstance();
    await prefs.setString('products', 
      jsonEncode(products.map((p) => p.toMap()).toList()));
  }

  static List<ProductModel> _getMockProducts() {
    return [
      ProductModel(
        id: '1',
        farmerId: 'farmer1',
        farmerName: 'John Smith',
        name: 'Fresh Tomatoes',
        category: 'Vegetables',
        price: 3.50,
        unit: 'kg',
        description: 'Organic, locally grown tomatoes',
        location: 'Green Valley Farm',
        isAvailable: true,
        createdAt: DateTime.now().subtract(const Duration(days: 2)),
      ),
      ProductModel(
        id: '2',
        farmerId: 'farmer2',
        farmerName: 'Mary Johnson',
        name: 'Farm Fresh Eggs',
        category: 'Dairy & Eggs',
        price: 4.00,
        unit: 'dozen',
        description: 'Free-range chicken eggs',
        location: 'Sunny Side Farm',
        isAvailable: true,
        createdAt: DateTime.now().subtract(const Duration(days: 1)),
      ),
      ProductModel(
        id: '3',
        farmerId: 'farmer1',
        farmerName: 'John Smith',
        name: 'Sweet Corn',
        category: 'Vegetables',
        price: 2.50,
        unit: 'kg',
        description: 'Fresh sweet corn, picked daily',
        location: 'Green Valley Farm',
        isAvailable: true,
        createdAt: DateTime.now(),
      ),
    ];
  }
}