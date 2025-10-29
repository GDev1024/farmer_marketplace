import 'package:flutter/material.dart';

class ChatScreen extends StatelessWidget {
  final String? receiverId;
  final String? receiverName;
  
  const ChatScreen({super.key, this.receiverId, this.receiverName});

  @override
  Widget build(BuildContext context) {
    // Mock data - in production, fetch from backend
    final mockConversations = [
      {
        'name': 'John Smith',
        'lastMessage': 'The tomatoes are ready for pickup',
        'time': '2h ago',
        'unread': 2,
      },
      {
        'name': 'Mary Johnson',
        'lastMessage': 'Thank you for your order!',
        'time': '1d ago',
        'unread': 0,
      },
    ];

    return SafeArea(
      child: Column(
        children: [
          const Padding(
            padding: EdgeInsets.all(16.0),
            child: Text(
              'Messages',
              style: TextStyle(
                fontSize: 24,
                fontWeight: FontWeight.bold,
                color: Color(0xFF333333),
              ),
            ),
          ),
          Expanded(
            child: mockConversations.isEmpty
                ? const Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(Icons.chat_bubble_outline, size: 80, color: Color(0xFFA5D6A7)),
                        SizedBox(height: 16),
                        Text(
                          'No messages yet',
                          style: TextStyle(fontSize: 18, color: Color(0xFF666666)),
                        ),
                        SizedBox(height: 8),
                        Text(
                          'Start a conversation with farmers',
                          style: TextStyle(fontSize: 14, color: Color(0xFF999999)),
                        ),
                      ],
                    ),
                  )
                : ListView.builder(
                    padding: const EdgeInsets.symmetric(horizontal: 16),
                    itemCount: mockConversations.length,
                    itemBuilder: (context, index) {
                      final conversation = mockConversations[index];
                      return Card(
                        margin: const EdgeInsets.only(bottom: 8),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                        child: ListTile(
                          contentPadding: const EdgeInsets.all(12),
                          leading: CircleAvatar(
                            radius: 28,
                            backgroundColor: const Color(0xFF2E7D32),
                            child: Text(
                              conversation['name'].toString()[0],
                              style: const TextStyle(
                                color: Colors.white,
                                fontSize: 20,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          ),
                          title: Text(
                            conversation['name'].toString(),
                            style: const TextStyle(
                              fontWeight: FontWeight.bold,
                              fontSize: 16,
                            ),
                          ),
                          subtitle: Text(
                            conversation['lastMessage'].toString(),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style: TextStyle(
                              color: conversation['unread'] as int > 0
                                  ? const Color(0xFF333333)
                                  : const Color(0xFF999999),
                            ),
                          ),
                          trailing: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            crossAxisAlignment: CrossAxisAlignment.end,
                            children: [
                              Text(
                                conversation['time'].toString(),
                                style: const TextStyle(
                                  fontSize: 12,
                                  color: Color(0xFF999999),
                                ),
                              ),
                              if (conversation['unread'] as int > 0) ...[
                                const SizedBox(height: 4),
                                Container(
                                  padding: const EdgeInsets.all(6),
                                  decoration: const BoxDecoration(
                                    color: Color(0xFF2E7D32),
                                    shape: BoxShape.circle,
                                  ),
                                  child: Text(
                                    conversation['unread'].toString(),
                                    style: const TextStyle(
                                      color: Colors.white,
                                      fontSize: 10,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                ),
                              ],
                            ],
                          ),
                          onTap: () {
                            // Navigate to chat screen
                          },
                        ),
                      );
                    },
                  ),
          ),
        ],
      ),
    );
  }
}
