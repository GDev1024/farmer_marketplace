/// Product model representing a marketplace product
class ProductModel {
  // ------------------------------------------------------------
  // Basic Product Info
  // ------------------------------------------------------------
  final String id;          // Unique product ID
  final String farmerId;    // ID of the farmer who added the product
  final String farmerName;  // Farmer's display name
  final String name;        // Product name
  final String category;    // Product category, e.g., Vegetables, Fruits
  final double price;       // Product price
  final String unit;        // Measurement unit: kg, lbs, dozen, etc.
  final String description; // Product description
  final String? imageUrl;   // Optional product image URL

  // ------------------------------------------------------------
  // Additional Info
  // ------------------------------------------------------------
  final bool isAvailable;   // Product availability
  final String? location;   // Optional location of the product
  final DateTime createdAt; // Timestamp when the product was created

  // ------------------------------------------------------------
  // Constructor
  // ------------------------------------------------------------
  ProductModel({
    required this.id,
    required this.farmerId,
    required this.farmerName,
    required this.name,
    required this.category,
    required this.price,
    required this.unit,
    required this.description,
    this.imageUrl,
    this.isAvailable = true,
    this.location,
    required this.createdAt,
  });

  // ------------------------------------------------------------
  // Convert ProductModel to Map
  // Useful for saving to Firestore, SQLite, or other storage
  // ------------------------------------------------------------
  Map<String, dynamic> toMap() {
    return {
      'id': id,
      'farmerId': farmerId,
      'farmerName': farmerName,
      'name': name,
      'category': category,
      'price': price,
      'unit': unit,
      'description': description,
      'imageUrl': imageUrl,
      'isAvailable': isAvailable,
      'location': location,
      'createdAt': createdAt.toIso8601String(), // Convert DateTime to string
    };
  }

  // ------------------------------------------------------------
  // Create ProductModel from Map
  // Useful for reading from Firestore, JSON, or other storage
  // ------------------------------------------------------------
  factory ProductModel.fromMap(Map<String, dynamic> map) {
    return ProductModel(
      id: map['id'],
      farmerId: map['farmerId'],
      farmerName: map['farmerName'],
      name: map['name'],
      category: map['category'],
      price: map['price'].toDouble(),
      unit: map['unit'],
      description: map['description'],
      imageUrl: map['imageUrl'],
      isAvailable: map['isAvailable'] ?? true,
      location: map['location'],
      createdAt: DateTime.parse(map['createdAt']),
    );
  }

  // ------------------------------------------------------------
  // Developer Notes
  // ------------------------------------------------------------
  /*
   * DevNotes:
   * - `id` is generated using millisecondsSinceEpoch for simplicity.
   * - `imageUrl` is optional; implement storage/upload functionality later.
   * - `isAvailable` defaults to true; consider adding toggle feature in the app.
   * - `location` is optional; can be used for filtering products by region.
   * - `toMap()` and `fromMap()` are essential for converting between Dart objects and database entries.
   * - Make sure `price` is always stored as double to avoid runtime errors.
   * - When adding new fields in the future, update both `toMap()` and `fromMap()`.
   */
}
