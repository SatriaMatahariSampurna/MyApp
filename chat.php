<!-- satria-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat App with Sidebar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style_chat.css" rel="stylesheet">
</head>
<body>
    <div class="sidebar">
        <div class="logo">My App</div>
        <ul>
            <li><a href="profil.php">Profil</a></li>
            <li><a href="chat.php">Chat</a></li>
            <?php if (isset($_SESSION['username'])): ?>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="index.php">Logout</a></li>
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

        function loadConversation(friendId, friendName) {
            currentFriend = friendId;
            document.getElementById('chatBox').innerHTML = `<h5>Chatting with ${friendName}</h5>`;
            fetch(`db_chat.php?friend_id=${friendId}`)
                .then(response => response.json())
                .then(data => {
                    const chatBox = document.getElementById('chatBox');
                    chatBox.innerHTML = `<h5>Chatting with ${friendName}</h5>`;
                    data.forEach(msg => {
                        const div = document.createElement('div');
                        div.className = `message ${msg.sender}`;
                        div.textContent = msg.message;
                        chatBox.appendChild(div);
                    });
                    chatBox.scrollTop = chatBox.scrollHeight;
                });
        }

        document.getElementById('chatForm').addEventListener('submit', e => {
            e.preventDefault();
            const message = document.getElementById('messageInput').value;
            fetch('kirim_chat.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ pengirim: 'me', penerima: currentFriend, message })
            }).then(() => {
                document.getElementById('messageInput').value = '';
                loadConversation(currentFriend);
            });
        });
    </script>
</body>
</html>
