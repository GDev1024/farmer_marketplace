class UserModel {
  final String id;
  final String name;
  final String email;
  final String userType; // 'farmer' or 'consumer'
  final String? phone;
  final String? location;
  final String? profileImage;
  final String? bio;

  UserModel({
    required this.id,
    required this.name,
    required this.email,
    required this.userType,
    this.phone,
    this.location,
    this.profileImage,
    this.bio,
  });

  Map<String, dynamic> toMap() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'userType': userType,
      'phone': phone,
      'location': location,
      'profileImage': profileImage,
      'bio': bio,
    };
  }

  factory UserModel.fromMap(Map<String, dynamic> map) {
    return UserModel(
      id: map['id'],
      name: map['name'],
      email: map['email'],
      userType: map['userType'],
      phone: map['phone'],
      location: map['location'],
      profileImage: map['profileImage'],
      bio: map['bio'],
    );
  }
}
