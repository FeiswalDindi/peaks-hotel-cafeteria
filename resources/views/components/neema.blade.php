<style>
    .neema-widget { position: fixed; bottom: 30px; right: 30px; z-index: 9999; font-family: 'Figtree', sans-serif; }
    .neema-toggle { width: 60px; height: 60px; border-radius: 50%; background-color: #192C57; color: white; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.2); cursor: pointer; transition: transform 0.3s; display: flex; align-items: center; justify-content: center; }
    .neema-toggle:hover { transform: scale(1.1); background-color: #CEAA0C; color: #192C57; }
    
    .neema-window { display: none; width: 360px; height: 500px; min-width: 300px; min-height: 400px; max-width: 90vw; max-height: 85vh; background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); position: absolute; bottom: 80px; right: 0; flex-direction: column; overflow: hidden; border: 1px solid #e0e0e0; resize: both; }
    
    .neema-header { background-color: #192C57; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #CEAA0C; z-index: 10; position: relative; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .neema-header-title { font-weight: bold; margin: 0; font-size: 1.1rem; }
    .neema-header-subtitle { font-size: 0.75rem; opacity: 0.8; margin: 0; }
    
    .header-actions { display: flex; align-items: center; gap: 15px; }
    .clear-btn { background: none; border: none; color: rgba(255,255,255,0.7); cursor: pointer; transition: 0.2s; font-size: 0.9rem; padding: 5px; }
    .clear-btn:hover { color: #ff4d4d; transform: scale(1.1); }

    .neema-chat-area { flex: 1; padding: 20px 15px; overflow-y: auto; background-color: #f8f9fa; display: flex; flex-direction: column; gap: 12px; scroll-behavior: smooth; }
    .msg-bubble { max-width: 85%; padding: 12px 16px; border-radius: 15px; font-size: 0.9rem; line-height: 1.5; word-wrap: break-word; white-space: pre-wrap; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    .msg-neema { background-color: #ffffff; border: 1px solid #e9ecef; color: #333; align-self: flex-start; border-bottom-left-radius: 0; }
    .msg-user { background-color: #192C57; color: white; align-self: flex-end; border-bottom-right-radius: 0; }
    
    .neema-input-area { padding: 10px; background: white; border-top: 1px solid #e0e0e0; display: flex; gap: 10px; z-index: 10; position: relative; }
    .neema-input { flex: 1; border: 1px solid #ced4da; border-radius: 20px; padding: 10px 15px; outline: none; transition: 0.3s; }
    .neema-input:focus { border-color: #CEAA0C; box-shadow: 0 0 5px rgba(206,170,12,0.3); }
    .neema-send-btn { background-color: #CEAA0C; color: white; border: none; border-radius: 50%; width: 42px; height: 42px; display: flex; justify-content: center; align-items: center; cursor: pointer; transition: 0.2s; }
    
    .typing-indicator { display: none; align-self: flex-start; background-color: #ffffff; border: 1px solid #e9ecef; padding: 10px 15px; border-radius: 15px; border-bottom-left-radius: 0; font-size: 0.8rem; color: #6c757d; font-style: italic; margin-top: auto; margin-bottom: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
</style>

<div class="neema-widget">
    <div class="neema-window" id="neemaWindow">
        <div class="neema-header">
            <div>
                <p class="neema-header-title"><i class="fas fa-robot me-2"></i>Neema</p>
                <p class="neema-header-subtitle">KCA AI Assistant</p>
            </div>
            <div class="header-actions">
                <button class="clear-btn" title="Clear Chat History" onclick="clearNeemaHistory()">
                    <i class="fas fa-trash-alt"></i>
                </button>
                <button class="btn-close btn-close-white" onclick="toggleNeema()" style="font-size: 0.8rem;"></button>
            </div>
        </div>
        
        <div class="neema-chat-area" id="neemaChatArea">
            <div class="typing-indicator" id="neemaTyping">Neema is thinking...</div>
        </div>
        
        <div class="neema-input-area">
            <input type="text" id="neemaInput" class="neema-input" placeholder="Ask about the menu..." onkeypress="handleNeemaEnter(event)">
            <button class="neema-send-btn" onclick="sendNeemaMessage()"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>

    <button class="neema-toggle" onclick="toggleNeema()">
        <i class="fas fa-comment-dots fa-2x"></i>
    </button>
</div>

<script>
    const chatArea = document.getElementById('neemaChatArea');
    const defaultGreeting = "Welcome to Peaks Hotel Cafeteria! I'm Neema. What can I help you with?";
    
    document.addEventListener("DOMContentLoaded", function() {
        loadHistory();
    });

    function loadHistory() {
        // Clear current UI bubbles (except typing indicator)
        const bubbles = chatArea.querySelectorAll('.msg-bubble');
        bubbles.forEach(b => b.remove());

        let history = JSON.parse(sessionStorage.getItem('neemaHistory')) || [];
        if (history.length === 0) {
            history.push({ role: 'neema', text: defaultGreeting });
            sessionStorage.setItem('neemaHistory', JSON.stringify(history));
        }
        
        history.forEach(msg => {
            appendBubble(msg.role, msg.text, false);
        });
        chatArea.scrollTop = chatArea.scrollHeight;
    }

    // 🌟 THE CLEAR HISTORY LOGIC
    function clearNeemaHistory() {
        if(confirm("Clear our conversation and start fresh?")) {
            sessionStorage.removeItem('neemaHistory');
            loadHistory();
        }
    }

    function toggleNeema() {
        const window = document.getElementById('neemaWindow');
        window.style.display = window.style.display === 'flex' ? 'none' : 'flex';
        if(window.style.display === 'flex') chatArea.scrollTop = chatArea.scrollHeight;
    }

    function handleNeemaEnter(event) {
        if (event.key === 'Enter') sendNeemaMessage();
    }

    function appendBubble(role, text, isStreaming = false) {
        const div = document.createElement('div');
        div.className = `msg-bubble msg-${role}`;
        chatArea.insertBefore(div, document.getElementById('neemaTyping'));
        
        if (!isStreaming) {
            div.textContent = text;
            chatArea.scrollTop = chatArea.scrollHeight;
        }
        return div;
    }

    function typeWriterEffect(element, text, callback) {
        let i = 0;
        element.textContent = '';
        const speed = 15; 
        
        function type() {
            if (i < text.length) {
                element.textContent += text.charAt(i);
                chatArea.scrollTop = chatArea.scrollHeight;
                i++;
                setTimeout(type, speed);
            } else {
                if (callback) callback();
            }
        }
        type();
    }

    function sendNeemaMessage() {
        const inputField = document.getElementById('neemaInput');
        const message = inputField.value.trim();
        if (!message) return;

        let history = JSON.parse(sessionStorage.getItem('neemaHistory')) || [];
        history.push({ role: 'user', text: message });
        sessionStorage.setItem('neemaHistory', JSON.stringify(history));

        appendBubble('user', message, false);
        inputField.value = '';

        const typingIndicator = document.getElementById('neemaTyping');
        typingIndicator.style.display = 'block';
        chatArea.scrollTop = chatArea.scrollHeight;

        fetch("{{ route('chatbot.send') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ message: message, history: history }) 
        })
        .then(response => response.json())
        .then(data => {
            typingIndicator.style.display = 'none';
            const neemaBubble = appendBubble('neema', '', true);
            
            typeWriterEffect(neemaBubble, data.reply, function() {
                history.push({ role: 'neema', text: data.reply });
                sessionStorage.setItem('neemaHistory', JSON.stringify(history));
            });
        })
        .catch(error => {
            typingIndicator.style.display = 'none';
            appendBubble('neema', "Lost connection to the terminal. Let me try again.", false);
        });
    }
</script>