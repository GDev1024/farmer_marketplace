import 'package:flutter/material.dart';
import '../../models/user_model.dart';
import '../../models/product_model.dart';
import '../../services/storage_service.dart';
import '../products/add_product_screen.dart';
import '../products/product_details_screen.dart';
import '../profile/profile_screen.dart';
import '../messages/messages_list_screen.dart';

class FarmerHomeScreen extends StatefulWidget {
  const FarmerHomeScreen({super.key});

  @override
  State<FarmerHomeScreen> createState() => _FarmerHomeScreenState();
}

class _FarmerHomeScreenState extends State<FarmerHomeScreen> {
  int _selectedIndex = 0;
  UserModel? _currentUser;
  List<ProductModel> _myProducts = [];

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    _currentUser = await StorageService.getCurrentUser();
    List<ProductModel> allProducts = await StorageService.getProducts();
    setState(() {
      _myProducts = allProducts.where((p) => p.farmerId == _currentUser?.id).toList();
    });
  }

  List<Widget> get _pages => [
    _MyProductsPage(
      products: _myProducts,
      onRefresh: _loadData,
      onAddProduct: () async {
        await Navigator.push(
          context,
          MaterialPageRoute(builder: (context) => const AddProductScreen()),
        );
        _loadData();
      },
    ),
    const MessagesListScreen(),
    ProfileScreen(user: _currentUser),
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF1F8E9),
      body: _pages[_selectedIndex],
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: _selectedIndex,
        onTap: (index) => setState(() => _selectedIndex = index),
        selectedItemColor: const Color(0xFF2E7D32),
        unselectedItemColor: const Color(0xFF999999),
        items: const [
          BottomNavigationBarItem(icon: Icon(Icons.inventory), label: 'My Products'),
          BottomNavigationBarItem(icon: Icon(Icons.message), label: 'Messages'),
          BottomNavigationBarItem(icon: Icon(Icons.person), label: 'Profile'),
        ],
      ),
    );
  }
}

class _MyProductsPage extends StatelessWidget {
  final List<ProductModel> products;
  final VoidCallback onRefresh;
  final VoidCallback onAddProduct;

  const _MyProductsPage({
    required this.products,
    required this.onRefresh,
    required this.onAddProduct,
  });

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      child: Column(
        children: [
          Padding(
            padding: const EdgeInsets.all(16.0),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text(
                  'My Products',
                  style: TextStyle(
                    fontSize: 24,
                    fontWeight: FontWeight.bold,
                    color: Color(0xFF333333),
                  ),
                ),
                IconButton(
                  icon: const Icon(Icons.add_circle, size: 32, color: Color(0xFF2E7D32)),
                  onPressed: onAddProduct,
                ),
              ],
            ),
          ),
          Expanded(
            child: products.isEmpty
                ? Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        const Icon(Icons.inventory_2_outlined, size: 80, color: Color(0xFFA5D6A7)),
                        const SizedBox(height: 16),
                        const Text(
                          'No products yet',
                          style: TextStyle(fontSize: 18, color: Color(0xFF666666)),
                        ),
                        const SizedBox(height: 8),
                        TextButton(
                          onPressed: onAddProduct,
                          child: const Text('Add your first product'),
                        ),
                      ],
                    ),
                  )
                : RefreshIndicator(
                    onRefresh: () async => onRefresh(),
                    child: ListView.builder(
                      padding: const EdgeInsets.symmetric(horizontal: 16),
                      itemCount: products.length,
                      itemBuilder: (context, index) {
                        return _ProductCard(
                          product: products[index],
                          onTap: () {
                            Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder: (context) => ProductDetailsScreen(product: products[index]),
                              ),
                            );
                          },
                          onDelete: () async {
                            await StorageService.deleteProduct(products[index].id);
                            onRefresh();
                          },
                        );
                      },
                    ),
                  ),
          ),
        ],
      ),
    );
  }
}

class _ProductCard extends StatelessWidget {
  final ProductModel product;
  final VoidCallback onTap;
  final VoidCallback onDelete;

  const _ProductCard({
    required this.product,
    required this.onTap,
    required this.onDelete,
  });

  @override
  Widget build(BuildContext context) {
    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(12),
        child: Padding(
          padding: const EdgeInsets.all(12.0),
          child: Row(
            children: [
              Container(
                width: 80,
                height: 80,
                decoration: BoxDecoration(
                  color: const Color(0xFFA5D6A7),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: const Icon(Icons.image, size: 40, color: Colors.white),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      product.name,
                      style: const TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                        color: Color(0xFF333333),
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      '\$${product.price.toStringAsFixed(2)}/${product.unit}',
                      style: const TextStyle(
                        fontSize: 14,
                        color: Color(0xFF2E7D32),
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      product.category,
                      style: const TextStyle(fontSize: 12, color: Color(0xFF999999)),
                    ),
                  ],
                ),
              ),
              IconButton(
                icon: const Icon(Icons.delete, color: Colors.red),
                onPressed: () {
                  showDialog(
                    context: context,
                    builder: (context) => AlertDialog(
                      title: const Text('Delete Product'),
                      content: const Text('Are you sure you want to delete this product?'),
                      actions: [
                        TextButton(
                          onPressed: () => Navigator.pop(context),
                          child: const Text('Cancel'),
                        ),
                        TextButton(
                          onPressed: () {
                            Navigator.pop(context);
                            onDelete();
                          },
                          child: const Text('Delete', style: TextStyle(color: Colors.red)),
                        ),
                      ],
                    ),
                  );
                },
              ),
            ],
          ),
        ),
      ),
    );
  }
}