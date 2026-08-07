document.getElementById("send-btn").addEventListener("click", function() {
    const message = document.getElementById("msg-text").value;
    if (message.trim()) {
        sendMessageToServer(message);
    }
});

function sendMessageToServer(message) {
    fetch('send_message.php', {
        method: 'POST',
        body: JSON.stringify({ message }),
        headers: {
            'Content-Type': 'application/json'
        }
    }).then(response => response.json()).then(data => {
        // Append message to chat
        const chatBox = document.getElementById("chat-messages");
        const newMessage = document.createElement("div");
        newMessage.className = "message";
        newMessage.textContent = data.message;
        chatBox.appendChild(newMessage);
        document.getElementById("msg-text").value = ''; // Clear input field
    }).catch(error => {
        console.error('Error sending message:', error);
        alert('Failed to send message. Please try again.');
    });
}