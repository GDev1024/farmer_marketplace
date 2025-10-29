class ProductModel {
  final String id;
  final String farmerId;
  final String farmerName;
  final String name;
  final String category;
  final double price;
  final String unit; // kg, lbs, dozen, etc.
  final String description;
  final String? imageUrl;
  final bool isAvailable;
  final String? location;
  final DateTime createdAt;

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
      'createdAt': createdAt.toIso8601String(),
    };
  }

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
}
