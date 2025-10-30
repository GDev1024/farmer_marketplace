import 'package:sqflite/sqflite.dart';
import 'package:path/path.dart';

class LocalDatabaseService {
  static Database? _database;
  static const String _dbName = 'farmer_marketplace.db';
  static const int _dbVersion = 1;

  // Database tables
  static const String _usersTable = 'users';
  static const String _productsTable = 'products';
  static const String _messagesTable = 'messages';

  // Get database instance
  static Future<Database> get database async {
    if (_database != null) return _database!;
    _database = await _initDatabase();
    return _database!;
  }

  // Initialize database
  static Future<Database> _initDatabase() async {
    String path = join(await getDatabasesPath(), _dbName);
    return await openDatabase(
      path,
      version: _dbVersion,
      onCreate: _createTables,
    );
  }

  // Create database tables
  static Future<void> _createTables(Database db, int version) async {
    // Users table
    await db.execute('''
      CREATE TABLE $_usersTable(
        id TEXT PRIMARY KEY,
        name TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL,
        userType TEXT NOT NULL,
        phone TEXT,
        location TEXT,
        profileImage TEXT,
        bio TEXT,
        password TEXT NOT NULL
      )
    ''');

    // Products table
    await db.execute('''
      CREATE TABLE $_productsTable(
        id TEXT PRIMARY KEY,
        farmerId TEXT NOT NULL,
        farmerName TEXT NOT NULL,
        name TEXT NOT NULL,
        category TEXT NOT NULL,
        price REAL NOT NULL,
        unit TEXT NOT NULL,
        description TEXT,
        location TEXT,
        imageUrl TEXT,
        createdAt TEXT NOT NULL,
        FOREIGN KEY(farmerId) REFERENCES $_usersTable(id)
      )
    ''');

    // Messages table
    await db.execute('''
      CREATE TABLE $_messagesTable(
        id TEXT PRIMARY KEY,
        senderId TEXT NOT NULL,
        receiverId TEXT NOT NULL,
        senderName TEXT NOT NULL,
        message TEXT NOT NULL,
        timestamp TEXT NOT NULL,
        isRead INTEGER DEFAULT 0,
        FOREIGN KEY(senderId) REFERENCES $_usersTable(id),
        FOREIGN KEY(receiverId) REFERENCES $_usersTable(id)
      )
    ''');

    // Insert some sample data
    await _insertSampleData(db);
  }

  // Insert sample data for testing
  static Future<void> _insertSampleData(Database db) async {
    // Sample users
    await db.insert(_usersTable, {
      'id': 'farmer1',
      'name': 'John Smith',
      'email': 'farmer@test.com',
      'userType': 'farmer',
      'phone': '555-0101',
      'location': 'Green Valley Farm',
      'bio': 'Organic vegetable farmer with 10 years experience',
      'password': 'farmer123'
    });

    await db.insert(_usersTable, {
      'id': 'consumer1',
      'name': 'Mary Johnson',
      'email': 'consumer@test.com',
      'userType': 'consumer',
      'phone': '555-0202',
      'location': 'Downtown City',
      'bio': 'Local food enthusiast',
      'password': 'consumer123'
    });

    // Sample products
    await db.insert(_productsTable, {
      'id': 'prod1',
      'farmerId': 'farmer1',
      'farmerName': 'John Smith',
      'name': 'Fresh Tomatoes',
      'category': 'Vegetables',
      'price': 3.50,
      'unit': 'lb',
      'description': 'Organic, vine-ripened tomatoes',
      'location': 'Green Valley Farm',
      'createdAt': DateTime.now().toIso8601String(),
    });

    await db.insert(_productsTable, {
      'id': 'prod2',
      'farmerId': 'farmer1',
      'farmerName': 'John Smith',
      'name': 'Sweet Corn',
      'category': 'Vegetables',
      'price': 2.00,
      'unit': 'dozen',
      'description': 'Fresh picked sweet corn',
      'location': 'Green Valley Farm',
      'createdAt': DateTime.now().toIso8601String(),
    });
  }

  // Close database
  static Future<void> closeDatabase() async {
    if (_database != null) {
      await _database!.close();
      _database = null;
    }
  }
}