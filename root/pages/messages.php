<?php
// Get selected conversation
$selectedUserId = isset($_GET['user']) ? intval($_GET['user']) : null;
$conversations = [];
$selectedConversation = null;

// Get all unique conversations
$stmt = $pdo->prepare("
    SELECT DISTINCT 
        CASE 
            WHEN sender_id = ? THEN receiver_id 
            ELSE sender_id 
        END as other_user_id,
        u.name, u.id,
        MAX(m.created_at) as last_message_time
    FROM messages m
    JOIN users u ON (
        CASE 
            WHEN m.sender_id = ? THEN u.id = m.receiver_id 
            ELSE u.id = m.sender_id 
        END
    )
    WHERE m.sender_id = ? OR m.receiver_id = ?
    GROUP BY other_user_id, u.name, u.id
    ORDER BY last_message_time DESC
");
$stmt->execute([$_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id']]);
$conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// If conversation selected, get messages
if($selectedUserId) {
    $stmt = $pdo->prepare("
        SELECT m.*, u.name as sender_name, u.id as sender_id
        FROM messages m
        JOIN users u ON m.sender_id = u.id
        WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
        ORDER BY m.created_at ASC
    ");
    $stmt->execute([$_SESSION['user_id'], $selectedUserId, $selectedUserId, $_SESSION['user_id']]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get other user info
    $stmt = $pdo->prepare("SELECT id, name FROM users WHERE id = ?");
    $stmt->execute([$selectedUserId]);
    $selectedConversation = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Mark messages as read
    $stmt = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE receiver_id = ? AND sender_id = ?");
    $stmt->execute([$_SESSION['user_id'], $selectedUserId]);
}

// Display message if set
$msg = getAndClearMessage();
if($msg['message']): ?>
    <div class="alert alert-<?= $msg['type'] ?>" role="alert" aria-live="polite">
        <?= ($msg['type'] === 'success' ? '✓' : '⚠') ?> <?= htmlspecialchars($msg['message']) ?>
    </div>
<?php endif; ?>

<div class="page page--messages">
  <main class="page__main">
    <?php include 'includes/page-navigation.php'; ?>
    
    <div class="page__header">
      <div class="page__title-section">
        <h1 class="page__title">
          <span class="page__title-icon" aria-hidden="true">💬</span>
          Messages
        </h1>
        <p class="page__subtitle">
          Communicate with buyers and sellers
        </p>
      </div>
      <div class="page__actions">
        <button class="btn btn--secondary btn--small messages-toggle" onclick="toggleConversationsList()" aria-label="Toggle conversations list">
          <span class="btn__icon" aria-hidden="true">📋</span>
          <span class="btn__text">Conversations</span>
        </button>
      </div>
    </div>

    <div class="messages-layout">
      <!-- Conversations Sidebar -->
      <aside class="conversations-sidebar" id="conversationsSidebar" aria-label="Conversations list">
        <header class="conversations-sidebar__header">
          <h2 class="conversations-sidebar__title">
            <span class="conversations-sidebar__icon" aria-hidden="true">💬</span>
            Conversations
          </h2>
        </header>
        
        <div class="conversations-sidebar__body">
          <?php if(empty($conversations)): ?>
            <div class="empty-state empty-state--small">
              <div class="empty-state__icon" aria-hidden="true">💬</div>
              <h3 class="empty-state__title">No conversations yet</h3>
              <p class="empty-state__description">
                Messages will appear here when you start chatting with other users.
              </p>
            </div>
          <?php else: ?>
            <div class="conversations-list" role="list">
              <?php foreach($conversations as $conv): ?>
                <a href="index.php?page=messages&user=<?= $conv['id'] ?>" 
                   class="conversation-item <?= $selectedUserId == $conv['id'] ? 'conversation-item--active' : '' ?>"
                   role="listitem"
                   aria-label="Conversation with <?= htmlspecialchars($conv['name']) ?>">
                  <div class="conversation-item__avatar" aria-hidden="true">
                    <?= strtoupper(substr($conv['name'], 0, 1)) ?>
                  </div>
                  <div class="conversation-item__content">
                    <h3 class="conversation-item__name"><?= htmlspecialchars($conv['name']) ?></h3>
                    <time class="conversation-item__time" datetime="<?= $conv['last_message_time'] ?>">
                      <?= date('M d, Y', strtotime($conv['last_message_time'])) ?>
                    </time>
                  </div>
                </a>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </aside>

      <!-- Chat Area -->
      <section class="chat-area" aria-label="Chat messages">
        <?php if($selectedConversation): ?>
          <div class="chat-container">
            <header class="chat-header">
              <div class="chat-header__user">
                <div class="chat-header__avatar" aria-hidden="true">
                  <?= strtoupper(substr($selectedConversation['name'], 0, 1)) ?>
                </div>
                <div class="chat-header__info">
                  <h2 class="chat-header__name"><?= htmlspecialchars($selectedConversation['name']) ?></h2>
                  <div class="chat-header__status">Online</div>
                </div>
              </div>
              <button class="btn btn--secondary btn--small" onclick="toggleConversationsList()" aria-label="Show conversations list">
                <span class="btn__icon" aria-hidden="true">📋</span>
              </button>
            </header>
            
            <!-- Messages Area -->
            <div class="chat-messages" id="chatMessages" role="log" aria-live="polite" aria-label="Chat messages">
              <?php if(empty($messages)): ?>
                <div class="empty-state">
                  <div class="empty-state__icon" aria-hidden="true">💬</div>
                  <h3 class="empty-state__title">Start a conversation</h3>
                  <p class="empty-state__description">
                    Send a message to begin chatting with <?= htmlspecialchars($selectedConversation['name']) ?>.
                  </p>
                </div>
              <?php else: ?>
                <?php foreach($messages as $message): ?>
                  <div class="message <?= $message['sender_id'] == $_SESSION['user_id'] ? 'message--sent' : 'message--received' ?>">
                    <div class="message__bubble">
                      <p class="message__text"><?= htmlspecialchars($message['message']) ?></p>
                      <time class="message__time" datetime="<?= $message['created_at'] ?>">
                        <?= date('H:i', strtotime($message['created_at'])) ?>
                      </time>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
            
            <!-- Message Input -->
            <footer class="chat-input">
              <form method="POST" action="actions.php" class="chat-input__form" onsubmit="return handleMessageSubmit(event)">
                <input type="hidden" name="receiverId" value="<?= $selectedConversation['id'] ?>">
                <div class="chat-input__field">
                  <label for="messageInput" class="sr-only">Type your message</label>
                  <textarea 
                    name="message" 
                    id="messageInput"
                    class="chat-input__textarea"
                    placeholder="Type your message..." 
                    required
                    rows="1"
                    aria-label="Message input"></textarea>
                </div>
                <button type="submit" name="sendMessage" class="btn btn--primary btn--with-icon chat-input__send" aria-label="Send message">
                  <span class="btn__icon" aria-hidden="true">📤</span>
                  <span class="btn__text sr-only">Send</span>
                </button>
              </form>
            </footer>
          </div>
        <?php else: ?>
          <div class="chat-placeholder">
            <div class="empty-state">
              <div class="empty-state__icon" aria-hidden="true">💬</div>
              <h2 class="empty-state__title">Select a conversation</h2>
              <p class="empty-state__description">
                Choose a conversation from the sidebar to start messaging, or start a new conversation with someone.
              </p>
              <?php if(!empty($conversations)): ?>
                <button class="btn btn--primary" onclick="toggleConversationsList()">
                  View Conversations
                </button>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>
      </section>
    </div>
  </main>
</div>

<script>
function toggleConversationsList() {
  const sidebar = document.getElementById('conversationsSidebar');
  const isVisible = sidebar.classList.contains('conversations-sidebar--visible');
  
  if (isVisible) {
    sidebar.classList.remove('conversations-sidebar--visible');
  } else {
    sidebar.classList.add('conversations-sidebar--visible');
  }
}

function handleMessageSubmit(event) {
  const textarea = event.target.querySelector('textarea[name="message"]');
  const message = textarea.value.trim();
  
  if (!message) {
    event.preventDefault();
    return false;
  }
  
  // Auto-resize textarea back to single line
  textarea.style.height = 'auto';
  return true;
}

// Auto-resize textarea
document.addEventListener('DOMContentLoaded', function() {
  const textarea = document.getElementById('messageInput');
  if (textarea) {
    textarea.addEventListener('input', function() {
      this.style.height = 'auto';
      this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });
    
    // Handle Enter key to send message (Shift+Enter for new line)
    textarea.addEventListener('keydown', function(event) {
      if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        this.closest('form').submit();
      }
    });
  }
  
  // Auto-scroll to bottom of messages
  const messagesContainer = document.getElementById('chatMessages');
  if (messagesContainer && messagesContainer.children.length > 0) {
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
  }
});

// Close conversations sidebar when clicking outside on mobile
document.addEventListener('click', function(event) {
  const sidebar = document.getElementById('conversationsSidebar');
  const toggle = document.querySelector('.messages-toggle');
  
  if (window.innerWidth <= 768 && 
      sidebar.classList.contains('conversations-sidebar--visible') &&
      !sidebar.contains(event.target) && 
      !toggle.contains(event.target)) {
    sidebar.classList.remove('conversations-sidebar--visible');
  }
});
</script>