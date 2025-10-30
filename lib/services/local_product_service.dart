import '../models/product_model.dart';
import 'local_database_service.dart';

class LocalProductService {
  static const String _table = 'products';

  // Get all products
  static Future<List<ProductModel>> getAllProducts() async {
    try {
      final db = await LocalDatabaseService.database;
      
      List<Map<String, dynamic>> results = await db.query(
        _table,
        orderBy: 'createdAt DESC',
      );
      
      return results.map((map) => ProductModel.fromMap(map)).toList();
    } catch (e) {
      print('Get products error: $e');
      return [];
    }
  }

  // Get products by farmer ID
  static Future<List<ProductModel>> getProductsByFarmerId(String farmerId) async {
    try {
      final db = await LocalDatabaseService.database;
      
      List<Map<String, dynamic>> results = await db.query(
        _table,
        where: 'farmerId = ?',
        whereArgs: [farmerId],
        orderBy: 'createdAt DESC',
      );
      
      return results.map((map) => ProductModel.fromMap(map)).toList();
    } catch (e) {
      print('Get farmer products error: $e');
      return [];
    }
  }

  // Add new product
  static Future<bool> addProduct(ProductModel product) async {
    try {
      final db = await LocalDatabaseService.database;
      
      await db.insert(_table, product.toMap());
      return true;
    } catch (e) {
      print('Add product error: $e');
      return false;
    }
  }

  // Update product
  static Future<bool> updateProduct(ProductModel product) async {
    try {
      final db = await LocalDatabaseService.database;
      
      int result = await db.update(
        _table,
        product.toMap(),
        where: 'id = ?',
        whereArgs: [product.id],
      );
      
      return result > 0;
    } catch (e) {
      print('Update product error: $e');
      return false;
    }
  }

  // Delete product
  static Future<bool> deleteProduct(String productId) async {
    try {
      final db = await LocalDatabaseService.database;
      
      int result = await db.delete(
        _table,
        where: 'id = ?',
        whereArgs: [productId],
      );
      
      return result > 0;
    } catch (e) {
      print('Delete product error: $e');
      return false;
    }
  }

  // Get product by ID
  static Future<ProductModel?> getProductById(String productId) async {
    try {
      final db = await LocalDatabaseService.database;
      
      List<Map<String, dynamic>> results = await db.query(
        _table,
        where: 'id = ?',
        whereArgs: [productId],
      );
      
      if (results.isNotEmpty) {
        return ProductModel.fromMap(results.first);
      }
      
      return null;
    } catch (e) {
      print('Get product error: $e');
      return null;
    }
  }

  // Search products
  static Future<List<ProductModel>> searchProducts(String searchTerm) async {
    try {
      final db = await LocalDatabaseService.database;
      
      List<Map<String, dynamic>> results = await db.query(
        _table,
        where: 'name LIKE ? OR category LIKE ? OR description LIKE ?',
        whereArgs: ['%$searchTerm%', '%$searchTerm%', '%$searchTerm%'],
        orderBy: 'createdAt DESC',
      );
      
      return results.map((map) => ProductModel.fromMap(map)).toList();
    } catch (e) {
      print('Search products error: $e');
      return [];
    }
  }
}