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
$stmt->execute([$_SESSION['userId'], $_SESSION['userId'], $_SESSION['userId'], $_SESSION['userId']]);
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
    $stmt->execute([$_SESSION['userId'], $selectedUserId, $selectedUserId, $_SESSION['userId']]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get other user info
    $stmt = $pdo->prepare("SELECT id, name FROM users WHERE id = ?");
    $stmt->execute([$selectedUserId]);
    $selectedConversation = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Mark messages as read
    $stmt = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE receiver_id = ? AND sender_id = ?");
    $stmt->execute([$_SESSION['userId'], $selectedUserId]);
}

// Display message if set
$msg = getAndClearMessage();
if($msg['message']): ?>
    <div class="alert alert-<?= $msg['type'] ?>">
        <?= ($msg['type'] === 'success' ? '✓' : '⚠') ?> <?= htmlspecialchars($msg['message']) ?>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 300px 1fr; gap: 2rem; min-height: 600px;">
    <!-- Conversations List -->
    <div class="card">
        <h3 style="margin-bottom: 1rem;">💬 Conversations</h3>
        
        <?php if(empty($conversations)): ?>
            <div class="empty-state" style="padding: 2rem 1rem;">
                <p style="font-size: 1.5rem; margin: 0;">No conversations yet</p>
                <p style="margin-top: 0.5rem;">Messages will appear here</p>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <?php foreach($conversations as $conv): ?>
                    <a href="index.php?page=messages&user=<?= $conv['id'] ?>" 
                       style="
                           display: block;
                           padding: 1rem;
                           background: <?= $selectedUserId == $conv['id'] ? '#A1BC98' : '#f9f9f9' ?>;
                           color: <?= $selectedUserId == $conv['id'] ? 'white' : '#333' ?>;
                           border-radius: 5px;
                           text-decoration: none;
                           transition: all 0.3s;
                           border-left: 3px solid <?= $selectedUserId == $conv['id'] ? 'white' : '#D2DCB6' ?>;
                       "
                       onmouseover="this.style.background='#E8F1E4'"
                       onmouseout="this.style.background='<?= $selectedUserId == $conv['id'] ? '#A1BC98' : '#f9f9f9' ?>'">
                        <strong><?= htmlspecialchars($conv['name']) ?></strong>
                        <br>
                        <span style="font-size: 0.85rem; opacity: 0.8;">
                            <?= date('M d, Y', strtotime($conv['last_message_time'])) ?>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Chat Area -->
    <div>
        <?php if($selectedConversation): ?>
            <div class="card" style="height: 100%; display: flex; flex-direction: column;">
                <h2 style="margin-bottom: 1rem; border-bottom: 2px solid #D2DCB6; padding-bottom: 1rem;">
                    💬 <?= htmlspecialchars($selectedConversation['name']) ?>
                </h2>
                
                <!-- Messages Area -->
                <div style="
                    flex: 1;
                    overflow-y: auto;
                    display: flex;
                    flex-direction: column;
                    gap: 1rem;
                    margin-bottom: 1.5rem;
                    padding: 1rem 0;
                    background: #fafafa;
                    border-radius: 5px;
                    padding: 1rem;
                ">
                    <?php if(empty($messages)): ?>
                        <div style="text-align: center; color: #999; margin: auto;">
                            <p style="font-size: 3rem;">💬</p>
                            <p>Start a conversation</p>
                        </div>
                    <?php else: ?>
                        <?php foreach($messages as $message): ?>
                            <div style="
                                display: flex;
                                justify-content: <?= $message['sender_id'] == $_SESSION['userId'] ? 'flex-end' : 'flex-start' ?>;
                                margin-bottom: 1rem;
                            ">
                                <div style="
                                    max-width: 70%;
                                    padding: 1rem;
                                    border-radius: 10px;
                                    background: <?= $message['sender_id'] == $_SESSION['userId'] ? '#A1BC98' : 'white' ?>;
                                    color: <?= $message['sender_id'] == $_SESSION['userId'] ? 'white' : '#333' ?>;
                                    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                                ">
                                    <p style="margin: 0; word-wrap: break-word;">
                                        <?= htmlspecialchars($message['message']) ?>
                                    </p>
                                    <span style="
                                        font-size: 0.8rem;
                                        opacity: 0.7;
                                        display: block;
                                        margin-top: 0.5rem;
                                    ">
                                        <?= date('H:i', strtotime($message['created_at'])) ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Message Input -->
                <form method="POST" action="actions.php" style="display: flex; gap: 0.5rem;">
                    <input type="hidden" name="receiverId" value="<?= $selectedConversation['id'] ?>">
                    <textarea 
                        name="message" 
                        placeholder="Type your message..." 
                        style="
                            flex: 1;
                            padding: 0.8rem;
                            border: 1px solid #D2DCB6;
                            border-radius: 5px;
                            resize: vertical;
                            min-height: 50px;
                            max-height: 100px;
                            font-family: inherit;
                        "
                        required></textarea>
                    <button type="submit" name="sendMessage" class="btn btn-primary" style="height: fit-content;">
                        📤 Send
                    </button>
                </form>
            </div>
        <?php else: ?>
            <div class="card" style="display: flex; align-items: center; justify-content: center; min-height: 400px;">
                <div class="empty-state">
                    <p style="font-size: 4rem;">💬</p>
                    <p style="font-size: 1.3rem;">Select a conversation to start messaging</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    @media (max-width: 768px) {
        .container > div:last-child {
            grid-template-columns: 1fr !important;
        }
        
        .card:first-child {
            display: none;
        }
    }
</style>