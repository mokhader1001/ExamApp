<?php
$role = session()->get('role');
$userId = session()->get('user_id');
$prefix = ($role === 'teacher') ? 'teacher' : 'student';
?>

<!-- Chat Widget -->
<div id="chat-widget">
    <button type="button" class="btn btn-primary rounded-circle shadow-lg p-0"
        style="width: 60px; height: 60px; background: <?= ($role === 'teacher') ? 'var(--primary-gradient)' : 'var(--student-primary)' ?>;"
        onclick="toggleChat()">
        <i class="bi bi-chat-dots fs-3 text-white"></i>
        <span id="chat-badge"
            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">
            0
        </span>
    </button>

    <div id="chat-box" class="card border-0 rounded-4 shadow-lg mt-3 bg-white"
        style="display: none; width: 350px; height: 500px; flex-direction: column;">
        <div class="card-header bg-expert text-white p-3 d-flex justify-content-between align-items-center rounded-top-4"
            style="background: <?= ($role === 'teacher') ? '#1e293b' : '#064e3b' ?>;">
            <h6 class="mb-0 fw-bold">
                <i class="bi bi-chat-left-text me-2"></i> Messages
                <span id="ws-status" class="badge rounded-pill bg-danger ms-2"
                    style="font-size: 0.5rem; vertical-align: middle;">Offline</span>
            </h6>
            <div class="d-flex align-items-center">
                <button type="button" class="btn btn-sm btn-outline-light border-0 me-2 d-none" id="back-to-contacts"
                    onclick="showContacts()">
                    <i class="bi bi-arrow-left"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-light border-0 me-2" title="Reconnect Chat"
                    onclick="initGlobalChat()">
                    <i class="bi bi-arrow-repeat"></i>
                </button>
                <button type="button" class="btn-close btn-close-white" onclick="toggleChat()"></button>
            </div>
        </div>

        <!-- Contact List View -->
        <div id="contacts-view" class="flex-grow-1 overflow-y-auto p-2">
            <div class="text-muted small px-3 py-2 fw-bold text-uppercase">Contacts</div>
            <div id="contacts-list" class="list-group list-group-flush">
                <!-- Contacts injected here -->
            </div>
        </div>

        <!-- Individual Chat View -->
        <div id="individual-chat-view" class="flex-grow-1 flex-column d-none" style="min-height: 0;">
            <div id="chat-messages" class="flex-grow-1 overflow-y-auto p-3 d-flex flex-column bg-light">
                <!-- Messages injected here -->
            </div>
            <div class="card-footer bg-white p-3 border-top flex-shrink-0">
                <div class="input-group">
                    <input type="text" id="chat-input" class="form-control border-0 bg-light rounded-pill px-4"
                        placeholder="Type a message...">
                    <button type="button"
                        class="btn btn-primary rounded-circle ms-2 p-0 d-flex align-items-center justify-content-center"
                        style="width: 40px; height: 40px;" onclick="sendMessage()">
                        <i class="bi bi-send-fill text-white"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #chat-widget {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 2000;
    }

    #chat-messages::-webkit-scrollbar {
        width: 4px;
    }

    #chat-messages::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .msg {
        max-width: 85%;
        margin-bottom: 12px;
        padding: 10px 15px;
        border-radius: 18px;
        font-size: 0.88rem;
        line-height: 1.4;
        position: relative;
    }

    .msg-sent {
        align-self: flex-end;
        background: #6366f1;
        color: white;
        border-bottom-right-radius: 4px;
    }

    .msg-received {
        align-self: flex-start;
        background: #e2e8f0;
        color: #1e293b;
        border-bottom-left-radius: 4px;
    }

    .contact-item {
        cursor: pointer;
        transition: all 0.2s;
        border-radius: 12px !important;
        margin: 2px 8px;
        border: none !important;
    }

    .contact-item:hover {
        background-color: #f1f5f9;
    }

    .contact-item.active {
        background-color: #eef2ff !important;
        color: #4338ca !important;
    }
</style>

<script>
    const currentUserId = <?= (int) $userId ?>;
    const userRole = '<?= $role ?>';
    const chatPrefix = '<?= $prefix ?>';
    let activeReceiverId = null;
    let wsConn;
    let unreadCount = 0;
    // Map to store unread counts per user locally for updates
    let contactUnreadMap = {};
    let typingTimeout;
    let isTyping = false;
    let lastTypingTime = 0;

    function updateBadge() {
        if (unreadCount > 0) {
            $('#chat-badge').removeClass('d-none').text(unreadCount);
        } else {
            $('#chat-badge').addClass('d-none');
        }
    }

    $(document).on('input', '#chat-input', function () {
        if (!activeReceiverId) return;

        const now = Date.now();
        if (now - lastTypingTime > 2000) {
            wsConn.send(JSON.stringify({
                type: 'typing',
                senderId: currentUserId,
                receiverId: activeReceiverId
            }));
            lastTypingTime = now;
        }
    });

    function checkUnreadMessages() {
        $.get('<?= site_url($prefix . "/chat/contacts") ?>', function (res) {
            if (res && res.length > 0) {
                unreadCount = 0;
                contactUnreadMap = {};
                res.forEach(c => {
                    if (c.unread_count > 0) {
                        unreadCount += parseInt(c.unread_count);
                        contactUnreadMap[c.id] = parseInt(c.unread_count);
                    }
                });
                updateBadge();
            }
        });
    }

    function initGlobalChat() {
        const host = window.location.hostname || '127.0.0.1';
        const wsUrl = 'ws://' + host + ':8282';
        console.log("Connecting to WebSocket: " + wsUrl);

        if (wsConn) {
            wsConn.close();
        }

        wsConn = new WebSocket(wsUrl);

        wsConn.onopen = function () {
            console.log("Global Chat WebSocket Connected");
            $('#ws-status').removeClass('bg-danger').addClass('bg-success').text('Online');
            wsConn.send(JSON.stringify({ type: 'auth', userId: currentUserId }));
        };

        wsConn.onmessage = function (e) {
            console.log("WS Data Received:", e.data);
            const data = JSON.parse(e.data);
            if (data.type === 'message') {
                // Ensure robust comparison by converting both to strings
                if (activeReceiverId && String(activeReceiverId) === String(data.senderId)) {
                    console.log("Appending received message...");
                    appendMessage('received', data.message);
                } else {
                    unreadCount++;
                    updateBadge();

                    // distinct handling for contact list badge
                    if (contactUnreadMap[data.senderId]) {
                        contactUnreadMap[data.senderId]++;
                    } else {
                        contactUnreadMap[data.senderId] = 1;
                    }

                    // Update UI if contact list is visible
                    const badge = $(`.contact-item[data-user-id="${data.senderId}"] .unread-badge`);
                    if (badge.length) {
                        badge.text(contactUnreadMap[data.senderId]);
                        badge.removeClass('d-none');
                    }
                }
            } else if (data.type === 'status') {
                updateContactStatus(data.userId, data.status, data.lastSeen);
            } else if (data.type === 'typing') {
                if (activeReceiverId && activeReceiverId == data.senderId) {
                    $('#chat-box h6 small').text('Typing...').css('color', '#10b981').css('font-weight', 'bold');
                    clearTimeout(typingTimeout);
                    typingTimeout = setTimeout(() => {
                        // Revert to status
                        const item = $(`.contact-item[data-user-id="${data.senderId}"]`);
                        const statusText = item.find('.last-seen-text').text() === 'Online' ? 'Online' : 'Offline';
                        $('#chat-box h6 small').text(statusText).css('color', '').css('font-weight', 'normal');
                    }, 3000);
                }
            }
        };

        wsConn.onerror = function (err) {
            console.error("WebSocket Error:", err);
            $('#ws-status').removeClass('bg-success').addClass('bg-danger').text('Error');
        };

        wsConn.onclose = () => {
            console.log("WebSocket Closed. Retrying in 5s...");
            $('#ws-status').removeClass('bg-success').addClass('bg-danger').text('Offline');
            setTimeout(initGlobalChat, 5000);
        };
    }

    function toggleChat() {
        $('#chat-box').toggle();
        // Do NOT reset unread count here. Only when opening specific chat.
        if ($('#chat-box').is(':visible') && !activeReceiverId) {
            loadContacts();
        }
    }

    function loadContacts() {
        const list = $('#contacts-list');
        list.html('<div class="text-center p-4"><div class="spinner-border spinner-border-sm text-primary"></div></div>');

        $.get('<?= site_url($prefix . "/chat/contacts") ?>', function (res) {
            list.empty();
            if (!res || res.length === 0) {
                list.append('<div class="text-center p-4 text-muted small">No contacts found in your classes.</div>');
                return;
            }
            res.forEach(c => {
                const isOnline = false; // Initial state...
                const uCount = parseInt(c.unread_count) || 0;
                contactUnreadMap[c.id] = uCount; // Sync map

                const html = `
                    <div class="list-group-item contact-item d-flex align-items-center p-3" data-user-id="${c.id}" onclick="openChat(${c.id}, '${c.full_name}')">
                        <div class="position-relative me-3">
                            <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(c.full_name)}&background=random&size=40" class="rounded-circle">
                            <span class="status-dot position-absolute bottom-0 end-0 border border-2 border-white rounded-circle bg-secondary" style="width: 12px; height: 12px;"></span>
                        </div>
                        <div class="overflow-hidden flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold small text-truncate">${c.full_name}</span>
                                <span class="badge rounded-pill bg-danger unread-badge ${uCount > 0 ? '' : 'd-none'}">${uCount}</span>
                            </div>
                            <div class="x-small text-muted text-uppercase d-flex justify-content-between" style="font-size: 0.65rem;">
                                <span>${c.role}</span>
                                <span class="last-seen-text">${formatLastSeen(c.last_seen)}</span>
                            </div>
                        </div>
                    </div>`;
                list.append(html);
            });
        }).fail(function (err) {
            console.error("Chat Error:", err);
            list.html('<div class="text-center p-4 text-danger small">Failed to load contacts.</div>');
        });
    }

    function updateContactStatus(userId, status, lastSeen) {
        const item = $(`.contact-item[data-user-id="${userId}"]`);
        const dot = item.find('.status-dot');
        const text = item.find('.last-seen-text');

        if (status === 'online') {
            dot.removeClass('bg-secondary').addClass('bg-success');
            text.text('Online');
            if (activeReceiverId == userId) {
                $('#chat-box h6 small').text('Online');
            }
        } else {
            dot.removeClass('bg-success').addClass('bg-secondary');
            text.text(formatLastSeen(lastSeen));
            if (activeReceiverId == userId) {
                $('#chat-box h6 small').text('Offline');
            }
        }
    }

    function formatLastSeen(dateStr) {
        if (!dateStr) return '';
        const date = new Date(dateStr.replace(' ', 'T'));
        const now = new Date();
        const diff = Math.floor((now - date) / 1000);

        if (diff < 60) return 'Just now';
        if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
        if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
        return date.toLocaleDateString();
    }

    function openChat(receiverId, name) {
        activeReceiverId = parseInt(receiverId);

        // Decrement global unread count
        const count = contactUnreadMap[receiverId] || 0;
        if (count > 0) {
            unreadCount = Math.max(0, unreadCount - count);
            contactUnreadMap[receiverId] = 0;
            updateBadge();
        }

        $('#contacts-view').addClass('d-none');
        $('#individual-chat-view').removeClass('d-none').addClass('d-flex');
        $('#back-to-contacts').removeClass('d-none');

        const item = $(`.contact-item[data-user-id="${receiverId}"]`);
        // clear badge on item
        item.find('.unread-badge').addClass('d-none').text(0);

        const statusText = item.find('.last-seen-text').text() === 'Online' ? 'Online' : 'Offline';

        $('#chat-box h6').html(`<i class="bi bi-person me-2"></i>${name} <br><small style="font-size: 0.65rem; font-weight: normal; opacity: 0.8;">${statusText}</small>`);
        loadHistory(receiverId);
    }

    function showContacts() {
        activeReceiverId = null;
        $('#individual-chat-view').addClass('d-none').removeClass('d-flex');
        $('#contacts-view').removeClass('d-none');
        $('#back-to-contacts').addClass('d-none');
        $('#chat-box h6').html('<i class="bi bi-chat-left-text me-2"></i> Messages');
        // Do NOT reset unread count here, as we might have other unread messages
        loadContacts();
    }

    function loadHistory(receiverId) {
        $.get('<?= site_url($prefix . "/chat/history/") ?>' + receiverId, function (res) {
            const container = $('#chat-messages');
            container.empty();
            res.forEach(msg => {
                const type = msg.sender_id == currentUserId ? 'sent' : 'received';
                appendMessage(type, msg.message, false);
            });
            scrollToBottom();
        });
    }

    function sendMessage() {
        const text = $('#chat-input').val().trim();
        if (!text || !activeReceiverId) return;

        // Display immediately for sender
        appendMessage('sent', text);
        $('#chat-input').val('');
        scrollToBottom();

        $.post('<?= site_url($prefix . "/chat/save") ?>', {
            receiverId: activeReceiverId,
            message: text,
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        }, function (res) {
            if (res.status === 'success') {
                wsConn.send(JSON.stringify({
                    type: 'message',
                    senderId: currentUserId,
                    receiverId: activeReceiverId,
                    message: text
                }));
            }
        });
    }

    function appendMessage(type, text, scroll = true) {
        const html = `<div class="msg msg-${type}">${text}</div>`;
        $('#chat-messages').append(html);
        if (scroll) scrollToBottom();
    }

    function scrollToBottom() {
        const container = $('#chat-messages');
        container.scrollTop(container[0].scrollHeight);
    }

    $(document).ready(function () {
        initGlobalChat();
        checkUnreadMessages();
    });
</script>