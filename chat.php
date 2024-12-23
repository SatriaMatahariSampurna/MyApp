<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat App with Sidebar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="css/style_chat.css" rel="stylesheet">
  
</head>
<body>
<div class="sidebar">
    <div class="logo"><i class="fas fa-comments"></i> My App</div>
    <ul>
        <li><a href="profil.php"><i class="fas fa-user"></i> Profil</a></li>
        <li><a href="chat.php"><i class="fas fa-comments"></i> Chat</a></li>
        <li><a href="saldo.php"><i class="fas fa-wallet"></i> Dompet Digital</a></li>
        <?php if (isset($_SESSION['username'])): ?>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        <?php else: ?>
            <li><a href="index.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
        <?php endif; ?>
    </ul>
</div>

<div class="friend-sidebar">
    <h5 class="p-3">Friends</h5>
    <ul id="friendList" class="list-group"></ul>
</div>

<div class="chat-area">
    <div id="chatBox" class="chat-box"></div>
    <form id="chatForm" class="chat-input">
        <div class="input-group">
            <input type="text" id="messageInput" class="form-control" placeholder="Type a message" required>
            <button class="btn btn-primary" type="submit">Send</button>
        </div>
    </form>
</div>

<script>
    let currentFriend = null;

    // Fetch the list of friends
    fetch('list_teman.php')
        .then(response => response.json())
        .then(data => {
            const friendList = document.getElementById('friendList');
            data.forEach(friend => {
                const li = document.createElement('li');
                li.className = 'list-group-item';
                li.textContent = friend.username;
                li.onclick = () => loadConversation(friend.id, friend.username);
                friendList.appendChild(li);
            });
        });

    // Load the conversation with the selected friend
    function loadConversation(friendId, friendName) {
        currentFriend = friendId;
        document.getElementById('chatBox').innerHTML = `<h5>Chatting with ${friendName}</h5>`;

        // Fetch the chat messages
        fetch(`db_chat.php?friend_id=${friendId}`)
            .then(response => response.json())
            .then(data => {
                const chatBox = document.getElementById('chatBox');
                let newMessages = document.createElement('div');
                newMessages.innerHTML = `<h5>Chatting with ${friendName}</h5>`;

                data.forEach(msg => {
                    const div = document.createElement('div');
                    div.className = `message ${msg.sender}`;
                    div.textContent = msg.message;
                    newMessages.appendChild(div);
                });

                chatBox.innerHTML = newMessages.innerHTML;
                chatBox.scrollTop = chatBox.scrollHeight;
            });
    }

    // Auto refresh the conversation every 3 seconds without disturbing the page layout
    function autoRefresh() {
        if (currentFriend !== null) {
            loadConversation(currentFriend);
        }
    }

    setInterval(autoRefresh, 3000); // Refresh every 3 seconds

    // Handle message submission
    document.getElementById('chatForm').addEventListener('submit', e => {
        e.preventDefault();
        const message = document.getElementById('messageInput').value;
        fetch('kirim_chat.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ pengirim: 'me', penerima: currentFriend, message })
        }).then(() => {
            document.getElementById('messageInput').value = '';
            loadConversation(currentFriend); // Reload conversation after sending a message
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
</body>
</html>
