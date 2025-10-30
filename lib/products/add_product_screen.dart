import 'package:flutter/material.dart';
import '../../models/user_model.dart';
import '../../models/product_model.dart';
import '../../service/storage_service.dart';

/// Screen to allow farmers to add new products to the marketplace
class AddProductScreen extends StatefulWidget {
  const AddProductScreen({super.key});

  @override
  State<AddProductScreen> createState() => _AddProductScreenState();
}

class _AddProductScreenState extends State<AddProductScreen> {
  // ------------------------------------------------------------
  // Form Key and Controllers
  // ------------------------------------------------------------
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _priceController = TextEditingController();
  final _descriptionController = TextEditingController();
  final _locationController = TextEditingController();

  // ------------------------------------------------------------
  // Dropdown selections
  // ------------------------------------------------------------
  String _selectedCategory = 'Vegetables';
  String _selectedUnit = 'kg';
  bool _isLoading = false;

  final List<String> _categories = [
    'Vegetables',
    'Fruits',
    'Dairy & Eggs',
    'Grains',
    'Meat',
  ];

  final List<String> _units = ['kg', 'lbs', 'dozen', 'bunch', 'piece'];

  @override
  void dispose() {
    // Dispose controllers to prevent memory leaks
    _nameController.dispose();
    _priceController.dispose();
    _descriptionController.dispose();
    _locationController.dispose();
    super.dispose();
  }

  // ------------------------------------------------------------
  // Save Product Function
  // ------------------------------------------------------------
  Future<void> _saveProduct() async {
    if (_formKey.currentState!.validate()) {
      setState(() => _isLoading = true);

      // Get the current logged-in user
      UserModel? user = await StorageService.getCurrentUser();

      if (user == null) {
        setState(() => _isLoading = false);
        return;
      }

      // Create a new ProductModel
      ProductModel product = ProductModel(
        id: DateTime.now().millisecondsSinceEpoch.toString(),
        farmerId: user.id,
        farmerName: user.name,
        name: _nameController.text,
        category: _selectedCategory,
        price: double.parse(_priceController.text),
        unit: _selectedUnit,
        description: _descriptionController.text,
        location: _locationController.text.isEmpty ? null : _locationController.text,
        createdAt: DateTime.now(),
      );

      // Save the product
      await StorageService.saveProduct(product);

      if (!mounted) return;

      // Show confirmation
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Product added successfully!')),
      );

      // Return to previous screen
      Navigator.pop(context);
    }
  }

  // ------------------------------------------------------------
  // Build UI
  // ------------------------------------------------------------
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF1F8E9),
      appBar: AppBar(
        title: const Text('Add Product'),
        backgroundColor: const Color(0xFF2E7D32),
        foregroundColor: Colors.white,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // --------------------------------------------------------
              // Product Image Placeholder
              // --------------------------------------------------------
              Container(
                height: 150,
                decoration: BoxDecoration(
                  color: const Color(0xFFA5D6A7),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: const Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(Icons.add_photo_alternate, size: 50, color: Colors.white),
                    SizedBox(height: 8),
                    Text(
                      'Add Product Image',
                      style: TextStyle(color: Colors.white, fontSize: 16),
                    ),
                    SizedBox(height: 4),
                    Text(
                      '(Feature coming soon)',
                      style: TextStyle(color: Colors.white70, fontSize: 12),
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 20),

              // --------------------------------------------------------
              // Product Name
              // --------------------------------------------------------
              TextFormField(
                controller: _nameController,
                decoration: InputDecoration(
                  labelText: 'Product Name',
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                  filled: true,
                  fillColor: Colors.white,
                ),
                validator: (value) {
                  if (value == null || value.isEmpty) return 'Please enter product name';
                  return null;
                },
              ),

              const SizedBox(height: 16),

              // --------------------------------------------------------
              // Category Dropdown
              // --------------------------------------------------------
              DropdownButtonFormField<String>(
                initialValue: _selectedCategory,
                decoration: InputDecoration(
                  labelText: 'Category',
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                  filled: true,
                  fillColor: Colors.white,
                ),
                items: _categories.map((category) {
                  return DropdownMenuItem(value: category, child: Text(category));
                }).toList(),
                onChanged: (value) => setState(() => _selectedCategory = value!),
              ),

              const SizedBox(height: 16),

              // --------------------------------------------------------
              // Price and Unit
              // --------------------------------------------------------
              Row(
                children: [
                  Expanded(
                    flex: 2,
                    child: TextFormField(
                      controller: _priceController,
                      keyboardType: TextInputType.number,
                      decoration: InputDecoration(
                        labelText: 'Price',
                        prefixText: '\$ ',
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                        filled: true,
                        fillColor: Colors.white,
                      ),
                      validator: (value) {
                        if (value == null || value.isEmpty) return 'Enter price';
                        if (double.tryParse(value) == null) return 'Invalid price';
                        return null;
                      },
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: DropdownButtonFormField<String>(
                      initialValue: _selectedUnit,
                      decoration: InputDecoration(
                        labelText: 'Unit',
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                        filled: true,
                        fillColor: Colors.white,
                      ),
                      items: _units.map((unit) {
                        return DropdownMenuItem(value: unit, child: Text(unit));
                      }).toList(),
                      onChanged: (value) => setState(() => _selectedUnit = value!),
                    ),
                  ),
                ],
              ),

              const SizedBox(height: 16),

              // --------------------------------------------------------
              // Description
              // --------------------------------------------------------
              TextFormField(
                controller: _descriptionController,
                maxLines: 4,
                decoration: InputDecoration(
                  labelText: 'Description',
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                  filled: true,
                  fillColor: Colors.white,
                ),
                validator: (value) {
                  if (value == null || value.isEmpty) return 'Please enter product description';
                  return null;
                },
              ),

              const SizedBox(height: 16),

              // --------------------------------------------------------
              // Optional Location
              // --------------------------------------------------------
              TextFormField(
                controller: _locationController,
                decoration: InputDecoration(
                  labelText: 'Location (Optional)',
                  prefixIcon: const Icon(Icons.location_on),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                  filled: true,
                  fillColor: Colors.white,
                ),
              ),

              const SizedBox(height: 24),

              // --------------------------------------------------------
              // Submit Button
              // --------------------------------------------------------
              SizedBox(
                height: 56,
                child: ElevatedButton(
                  onPressed: _isLoading ? null : _saveProduct,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF2E7D32),
                    foregroundColor: Colors.white,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(28)),
                  ),
                  child: _isLoading
                      ? const CircularProgressIndicator(color: Colors.white)
                      : const Text(
                          'Add Product',
                          style: TextStyle(fontSize: 18, fontWeight: FontWeight.w600),
                        ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
