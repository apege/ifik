<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Bantuan & Live Chat Lab - Panel Laboran') ?></title>
    
    <!-- Google Fonts & FontAwesome & SweetAlert2 -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        :root {
            --bg-color: #f8fafc;
            --surface-color: #ffffff;
            --text-color: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --primary: #ea580c;
            --primary-hover: #c2410c;
            --primary-light: #fff7ed;
            --accent-blue: #2563eb;
            --accent-green: #16a34a;
            --accent-purple: #7c3aed;
            --accent-red: #dc2626;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            height: 100vh;
            padding: 16px 24px 16px 76px; /* space for fixed left sidebar */
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .main-container {
            max-width: 1440px;
            width: 100%;
            margin: 0 auto;
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Top Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 12px;
            flex-shrink: 0;
        }

        .header-title-wrap h1 {
            font-size: 1.4rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px 12px;
        }

        .header-title-wrap h1 .role-badge {
            font-size: 0.72rem;
            font-weight: 700;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            padding: 3px 10px;
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .header-title-wrap p {
            color: var(--text-muted);
            font-size: 0.84rem;
            margin-top: 2px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            border: 1px solid var(--border-color);
            background: #ffffff;
            color: #334155;
        }

        .btn-action:hover {
            background: #f1f5f9;
            color: #0f172a;
            transform: translateY(-1px);
        }

        .btn-primary-action {
            background: linear-gradient(135deg, #ea580c, #f97316);
            color: #ffffff;
            border: none;
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.25);
        }

        .btn-primary-action:hover {
            background: linear-gradient(135deg, #c2410c, #ea580c);
            color: #ffffff;
            box-shadow: 0 6px 16px rgba(234, 88, 12, 0.35);
        }

        /* Main Chat Workspace Layout */
        .chat-workspace {
            display: grid;
            grid-template-columns: 360px 1fr;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius-xl);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
            flex: 1;
            min-height: 0;
            height: 100%;
            overflow: hidden;
            position: relative;
        }

        /* Left Pane: Conversation List */
        .chat-sidebar {
            background: #fafafa;
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            height: 100%;
            min-height: 0;
            overflow: hidden;
        }

        .sidebar-search-box {
            padding: 12px 14px 8px 14px;
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            flex-shrink: 0;
        }

        .search-input-wrapper {
            position: relative;
        }

        .search-input-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.88rem;
        }

        .search-input {
            width: 100%;
            padding: 9px 12px 9px 36px;
            font-size: 0.84rem;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            outline: none;
            background: #f8fafc;
            font-family: inherit;
            transition: all 0.2s;
        }

        .search-input:focus {
            border-color: #ea580c;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.12);
        }

        /* Filter Tabs */
        .sidebar-tabs {
            display: flex;
            padding: 8px 12px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            gap: 6px;
            flex-shrink: 0;
        }

        .filter-tab {
            flex: 1;
            padding: 6px 8px;
            border-radius: 8px;
            font-size: 0.78rem;
            font-weight: 700;
            border: none;
            background: #f1f5f9;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }

        .filter-tab.active {
            background: #ea580c;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(234, 88, 12, 0.25);
        }

        /* Conversation Thread List */
        .conversation-list {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            padding: 8px;
        }

        .conversation-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px;
            border-radius: 14px;
            cursor: pointer;
            margin-bottom: 6px;
            background: #ffffff;
            border: 1px solid transparent;
            transition: all 0.2s ease;
            position: relative;
        }

        .conversation-item:hover {
            background: #f8fafc;
            border-color: #e2e8f0;
            transform: translateX(2px);
        }

        .conversation-item.active {
            background: #fff7ed;
            border-color: #fed7aa;
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.08);
        }

        .conv-avatar {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.95rem;
            flex-shrink: 0;
            color: #ffffff;
            background: linear-gradient(135deg, #64748b, #475569);
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        }

        .conv-avatar.role-mahasiswa { background: linear-gradient(135deg, #0284c7, #0369a1); }
        .conv-avatar.role-dosen { background: linear-gradient(135deg, #7c3aed, #6d28d9); }
        .conv-avatar.role-laboran { background: linear-gradient(135deg, #ea580c, #c2410c); }

        .conv-info {
            flex: 1;
            min-width: 0;
        }

        .conv-header-line {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3px;
        }

        .conv-name {
            font-size: 0.88rem;
            font-weight: 700;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .conv-time {
            font-size: 0.72rem;
            font-weight: 600;
            color: #94a3b8;
            flex-shrink: 0;
        }

        .conv-subline {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 4px;
        }

        .role-pill {
            font-size: 0.65rem;
            font-weight: 700;
            padding: 1px 7px;
            border-radius: 999px;
            text-transform: uppercase;
        }

        .role-pill.mahasiswa { background: #e0f2fe; color: #0369a1; }
        .role-pill.dosen { background: #f3e8ff; color: #7e22ce; }

        .status-pill {
            font-size: 0.65rem;
            font-weight: 700;
            padding: 1px 7px;
            border-radius: 999px;
        }

        .status-pill.open { background: #fef3c7; color: #b45309; }
        .status-pill.resolved { background: #dcfce7; color: #15803d; }

        .conv-preview {
            font-size: 0.8rem;
            color: #64748b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.3;
        }

        .unread-badge {
            background: #ef4444;
            color: #ffffff;
            font-size: 0.7rem;
            font-weight: 800;
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-left: 6px;
            flex-shrink: 0;
            animation: bounce-badge 1s infinite alternate;
        }

        @keyframes bounce-badge {
            0% { transform: scale(1); }
            100% { transform: scale(1.1); }
        }

        /* Right Pane: Active Chat Area */
        .chat-main {
            display: flex;
            flex-direction: column;
            height: 100%;
            min-height: 0;
            background: #ffffff;
            position: relative;
            overflow: hidden;
        }

        /* Empty State */
        .chat-empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            padding: 32px;
            text-align: center;
            color: #64748b;
        }

        .empty-illustration {
            width: 120px;
            height: 120px;
            background: #fff7ed;
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: #ea580c;
            margin-bottom: 20px;
            box-shadow: 0 10px 25px rgba(234, 88, 12, 0.15);
        }

        .chat-empty-state h2 {
            font-size: 1.3rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .chat-empty-state p {
            max-width: 420px;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        /* Active Chat Header */
        .chat-header {
            padding: 12px 20px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-shrink: 0;
            z-index: 10;
        }

        .chat-header-user {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .btn-mobile-back {
            display: none;
            background: #f1f5f9;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            color: #0f172a;
            font-size: 1rem;
            cursor: pointer;
            align-items: center;
            justify-content: center;
        }

        .header-avatar {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.1rem;
            color: #ffffff;
            background: linear-gradient(135deg, #ea580c, #f97316);
        }

        .header-info h2 {
            font-size: 1.05rem;
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .header-info p {
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 2px;
        }

        .header-actions-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-status-toggle {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 0.82rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .btn-status-toggle.resolve-btn {
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }

        .btn-status-toggle.resolve-btn:hover {
            background: #16a34a;
            color: #ffffff;
        }

        .btn-status-toggle.reopen-btn {
            background: #fff7ed;
            color: #ea580c;
            border: 1px solid #fed7aa;
        }

        .btn-status-toggle.reopen-btn:hover {
            background: #ea580c;
            color: #ffffff;
        }

        /* Message Feed Area */
        .chat-messages {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            padding: 18px 20px;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .date-divider {
            text-align: center;
            position: relative;
            margin: 12px 0;
        }

        .date-divider span {
            background: #e2e8f0;
            color: #64748b;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 4px 14px;
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Message Bubbles */
        .message-row {
            display: flex;
            align-items: flex-end;
            gap: 10px;
            max-width: 80%;
        }

        .message-row.incoming {
            align-self: flex-start;
        }

        .message-row.outgoing {
            align-self: flex-end;
            flex-direction: row-reverse;
        }

        .bubble-avatar {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 800;
            color: #ffffff;
            flex-shrink: 0;
            margin-bottom: 2px;
        }

        .bubble-avatar.user-av { background: #0284c7; }
        .bubble-avatar.laboran-av { background: #ea580c; }

        .message-bubble {
            padding: 12px 16px;
            border-radius: 18px;
            font-size: 0.88rem;
            line-height: 1.45;
            position: relative;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
            word-break: break-word;
        }

        .message-row.incoming .message-bubble {
            background: #ffffff;
            color: #0f172a;
            border: 1px solid #e2e8f0;
            border-bottom-left-radius: 4px;
        }

        .message-row.outgoing .message-bubble {
            background: linear-gradient(135deg, #ea580c, #f97316);
            color: #ffffff;
            border-bottom-right-radius: 4px;
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.2);
        }

        .sender-tag {
            font-size: 0.72rem;
            font-weight: 700;
            margin-bottom: 4px;
            display: block;
        }

        .message-row.incoming .sender-tag { color: #0284c7; }
        .message-row.outgoing .sender-tag { color: #fed7aa; }

        .message-meta {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 4px;
            font-size: 0.68rem;
            margin-top: 4px;
            font-weight: 600;
        }

        .message-row.incoming .message-meta { color: #94a3b8; }
        .message-row.outgoing .message-meta { color: #fed7aa; }

        /* Input Bar */
        .chat-input-area {
            padding: 12px 18px;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: flex-end;
            gap: 10px;
            flex-shrink: 0;
        }

        .chat-textarea {
            flex: 1;
            min-height: 42px;
            max-height: 100px;
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            outline: none;
            font-family: inherit;
            font-size: 0.86rem;
            resize: none;
            background: #f8fafc;
            transition: all 0.2s;
            line-height: 1.4;
        }

        .chat-textarea:focus {
            border-color: #ea580c;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.12);
        }

        .btn-send {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, #ea580c, #f97316);
            color: #ffffff;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3);
            flex-shrink: 0;
        }

        .btn-send:hover {
            transform: scale(1.05);
            background: linear-gradient(135deg, #c2410c, #ea580c);
        }

        .btn-send:active {
            transform: scale(0.95);
        }

        /* Responsive Styles */
        @media (max-width: 1024px) {
            body {
                padding: 12px 16px 12px 16px;
            }
            .chat-workspace {
                grid-template-columns: 320px 1fr;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 10px 10px 10px 10px;
            }
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
                margin-bottom: 8px;
            }

            .chat-workspace {
                display: block;
                height: 100%;
                position: relative;
            }
            .chat-sidebar {
                width: 100%;
                height: 100%;
            }
            .chat-main {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 20;
                transform: translateX(100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .chat-workspace.mobile-active .chat-main {
                transform: translateX(0);
            }
            .btn-mobile-back {
                display: inline-flex;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar Integration -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <div class="main-container">
        <!-- Top Header -->
        <div class="page-header">
            <div class="header-title-wrap">
                <h1>
                    <span>Bantuan & Live Chat Lab</span>
                    <span class="role-badge">Panel Laboran</span>
                </h1>
                <p>Pusat help desk interaktif untuk menjawab pertanyaan, kendala praktikum, dan izin lab secara langsung.</p>
            </div>
            <div class="header-actions">
                <button type="button" class="btn-action" onclick="createSampleChat()" title="Simulasikan pesan chat masuk baru">
                    <i class="fa-solid fa-plus-circle text-orange-500"></i>
                    <span>Simulasi Chat Masuk</span>
                </button>
                <button type="button" class="btn-action" onclick="fetchConversations()" title="Segarkan data">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </button>
            </div>
        </div>

        <!-- Chat Workspace Area -->
        <div class="chat-workspace" id="chatWorkspace">
            <!-- Left Sidebar Pane -->
            <div class="chat-sidebar">
                <div class="sidebar-search-box">
                    <div class="search-input-wrapper">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="searchInput" class="search-input" placeholder="Cari nama, topik, atau pesan..." oninput="handleSearch(this.value)">
                    </div>
                </div>

                <div class="sidebar-tabs">
                    <button type="button" class="filter-tab active" data-status="all" onclick="setFilterStatus('all', this)">Semua</button>
                    <button type="button" class="filter-tab" data-status="open" onclick="setFilterStatus('open', this)">Menunggu</button>
                    <button type="button" class="filter-tab" data-status="resolved" onclick="setFilterStatus('resolved', this)">Selesai</button>
                </div>

                <div class="conversation-list" id="conversationList">
                    <!-- Dynamic conversation cards loaded via AJAX -->
                    <div style="padding: 24px; text-align: center; color: #94a3b8;">
                        <i class="fa-solid fa-spinner fa-spin fa-2x"></i>
                        <p style="margin-top: 8px; font-size: 0.84rem;">Memuat daftar percakapan...</p>
                    </div>
                </div>
            </div>

            <!-- Right Main Chat Pane -->
            <div class="chat-main" id="chatMain">
                <!-- Empty Placeholder State -->
                <div class="chat-empty-state" id="emptyChatState">
                    <div class="empty-illustration">
                        <i class="fa-solid fa-headset"></i>
                    </div>
                    <h2>Pilih Percakapan Help Desk</h2>
                    <p>Klik salah satu percakapan di sebelah kiri untuk membaca pesan dan memberikan tanggapan cepat kepada mahasiswa atau dosen.</p>
                </div>

                <!-- Active Chat Content (Hidden initially) -->
                <div id="activeChatWrap" style="display: none; height: 100%; flex-direction: column; min-height: 0; overflow: hidden;">
                    <!-- Active Chat Header -->
                    <div class="chat-header">
                        <div class="chat-header-user">
                            <button type="button" class="btn-mobile-back" onclick="closeMobileChat()">
                                <i class="fa-solid fa-arrow-left"></i>
                            </button>
                            <div class="header-avatar" id="headerAvatar">RF</div>
                            <div class="header-info">
                                <h2 id="headerUserName">
                                    <span>Rian Firmansyah</span>
                                    <span class="role-pill mahasiswa" id="headerUserRole">Mahasiswa</span>
                                </h2>
                                <p id="headerUserSub">1301213001 &bull; Topik: Peminjaman Ruangan Lab</p>
                            </div>
                        </div>
                        <div class="header-actions-wrap">
                            <button type="button" id="btnToggleStatus" class="btn-status-toggle resolve-btn" onclick="toggleActiveStatus()">
                                <i class="fa-solid fa-check"></i>
                                <span>Tandai Selesai</span>
                            </button>
                        </div>
                    </div>

                    <!-- Message Feed -->
                    <div class="chat-messages" id="chatMessages">
                        <!-- Messages dynamically injected here -->
                    </div>

                    <!-- Input Area -->
                    <div class="chat-input-area">
                        <textarea id="chatInput" class="chat-textarea" placeholder="Tulis balasan untuk pengguna... (Tekan Enter untuk kirim, Shift+Enter baris baru)" rows="1" onkeydown="handleInputKeydown(event)"></textarea>
                        <button type="button" class="btn-send" onclick="sendMessage()" title="Kirim Balasan">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Chat Engine -->
    <script>
        let currentStatusFilter = 'all';
        let searchQuery = '';
        let activeConversationId = null;
        let activeConversationStatus = 'open';
        let pollingTimer = null;
        let isSending = false;

        let lastConversationsJson = '';
        let renderedConversationId = null;
        let renderedMessageIds = new Set();

        $(document).ready(function() {
            fetchConversations();

            // Auto polling setiap 3.5 detik
            pollingTimer = setInterval(function() {
                pollUpdates();
            }, 3500);

            // Auto resize textarea
            const textarea = document.getElementById('chatInput');
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        });

        function setFilterStatus(status, btn) {
            $('.filter-tab').removeClass('active');
            $(btn).addClass('active');
            currentStatusFilter = status;
            lastConversationsJson = '';
            fetchConversations();
        }

        let searchDebounce = null;
        function handleSearch(val) {
            clearTimeout(searchDebounce);
            searchDebounce = setTimeout(() => {
                searchQuery = val.trim();
                lastConversationsJson = '';
                fetchConversations();
            }, 300);
        }

        function fetchConversations() {
            $.ajax({
                url: '<?= site_url("laboran/help/conversations") ?>',
                type: 'GET',
                data: {
                    status: currentStatusFilter,
                    q: searchQuery
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        renderConversationList(res.data);
                    }
                }
            });
        }

        function renderConversationList(conversations) {
            const newJson = JSON.stringify(conversations);
            if (newJson === lastConversationsJson) {
                // Periksa apakah item aktif masih memiliki class active
                if (activeConversationId) {
                    $(`.conversation-item`).removeClass('active');
                    $(`.conversation-item[onclick="selectConversation(${activeConversationId})"]`).addClass('active');
                }
                return;
            }
            lastConversationsJson = newJson;

            const list = $('#conversationList');
            list.empty();

            if (!conversations || conversations.length === 0) {
                list.html(`
                    <div style="padding: 36px 16px; text-align: center; color: #94a3b8;">
                        <i class="fa-regular fa-comment-dots fa-3x" style="color: #cbd5e1; margin-bottom: 10px;"></i>
                        <p style="font-size: 0.88rem; font-weight: 600;">Tidak ada percakapan ditemukan</p>
                    </div>
                `);
                return;
            }

            conversations.forEach(c => {
                const isActive = (c.id === activeConversationId) ? 'active' : '';
                const initials = getInitials(c.user_nama);
                const roleClass = c.user_role.toLowerCase() === 'dosen' ? 'role-dosen' : 'role-mahasiswa';
                const rolePillClass = c.user_role.toLowerCase() === 'dosen' ? 'dosen' : 'mahasiswa';
                const statusPill = (c.status === 'resolved') 
                    ? `<span class="status-pill resolved"><i class="fa-solid fa-check"></i> Selesai</span>` 
                    : `<span class="status-pill open"><i class="fa-solid fa-clock"></i> Menunggu</span>`;
                
                const unread = (c.unread_laboran > 0) ? `<span class="unread-badge">${c.unread_laboran}</span>` : '';

                const html = `
                    <div class="conversation-item ${isActive}" onclick="selectConversation(${c.id})">
                        <div class="conv-avatar ${roleClass}">${initials}</div>
                        <div class="conv-info">
                            <div class="conv-header-line">
                                <div class="conv-name">${escapeHtml(c.user_nama)}</div>
                                <div class="conv-time">${c.last_message_time}</div>
                            </div>
                            <div class="conv-subline">
                                <span class="role-pill ${rolePillClass}">${escapeHtml(c.user_role)}</span>
                                ${statusPill}
                                ${unread}
                            </div>
                            <div class="conv-preview">${escapeHtml(c.last_message || c.topik)}</div>
                        </div>
                    </div>
                `;
                list.append(html);
            });
        }

        function selectConversation(id) {
            if (activeConversationId !== id) {
                activeConversationId = id;
                renderedConversationId = null;
                renderedMessageIds.clear();
            }
            $('#chatWorkspace').addClass('mobile-active');
            $('#emptyChatState').hide();
            $('#activeChatWrap').css('display', 'flex');

            // Update active style in list
            $('.conversation-item').removeClass('active');
            $(`.conversation-item[onclick="selectConversation(${id})"]`).addClass('active');

            loadMessages(id, true);
        }

        function loadMessages(id, scrollToBottom = true) {
            $.ajax({
                url: '<?= site_url("laboran/help/messages") ?>/' + id,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        renderActiveHeader(res.conversation);
                        renderMessages(res.messages, scrollToBottom);
                    }
                }
            });
        }

        function renderActiveHeader(conv) {
            activeConversationStatus = conv.status;
            $('#headerAvatar').text(getInitials(conv.user_nama));
            $('#headerUserName span:first').text(conv.user_nama);
            
            const roleEl = $('#headerUserRole');
            roleEl.text(conv.user_role);
            roleEl.attr('class', 'role-pill ' + (conv.user_role.toLowerCase() === 'dosen' ? 'dosen' : 'mahasiswa'));

            $('#headerUserSub').html(`${conv.user_nim_nip} &bull; <strong>Topik:</strong> ${escapeHtml(conv.topik)}`);

            const btn = $('#btnToggleStatus');
            if (conv.status === 'resolved') {
                btn.attr('class', 'btn-status-toggle reopen-btn')
                   .html('<i class="fa-solid fa-rotate-left"></i><span>Buka Kembali Tiket</span>');
            } else {
                btn.attr('class', 'btn-status-toggle resolve-btn')
                   .html('<i class="fa-solid fa-check"></i><span>Tandai Selesai</span>');
            }
        }

        function renderMessages(messages, scrollToBottom = false) {
            const feed = $('#chatMessages');
            const isDifferentConv = (renderedConversationId !== activeConversationId);

            if (isDifferentConv) {
                renderedConversationId = activeConversationId;
                renderedMessageIds.clear();
                feed.empty();
            }

            if (!messages || messages.length === 0) {
                if (isDifferentConv) {
                    feed.html(`
                        <div style="text-align: center; color: #94a3b8; padding: 40px;">
                            <i class="fa-solid fa-comments fa-2x" style="color: #cbd5e1; margin-bottom: 8px;"></i>
                            <p style="font-size: 0.84rem;">Belum ada pesan dalam sesi bantuan ini.</p>
                        </div>
                    `);
                }
                return;
            }

            const wasAtBottom = isFeedAtBottom();
            let hasNew = false;

            messages.forEach(m => {
                if (renderedMessageIds.has(m.id)) {
                    return; // Already rendered in DOM, do not touch or re-render
                }

                hasNew = true;
                renderedMessageIds.add(m.id);

                const isLaboran = (m.sender_role === 'laboran');
                const rowClass = isLaboran ? 'outgoing' : 'incoming';
                const avatarClass = isLaboran ? 'laboran-av' : 'user-av';
                const avatarInit = isLaboran ? 'LB' : getInitials(m.sender_name);

                const checkMark = isLaboran ? '<i class="fa-solid fa-check-double text-orange-200" style="font-size: 0.65rem;"></i>' : '';

                const html = `
                    <div class="message-row ${rowClass}" data-msg-id="${m.id}">
                        <div class="bubble-avatar ${avatarClass}">${avatarInit}</div>
                        <div class="message-bubble">
                            <span class="sender-tag">${escapeHtml(m.sender_name)}</span>
                            <div>${m.message}</div>
                            <div class="message-meta">
                                <span>${m.time}</span>
                                ${checkMark}
                            </div>
                        </div>
                    </div>
                `;
                feed.append(html);
            });

            if (hasNew && (scrollToBottom || wasAtBottom || isDifferentConv)) {
                feed.scrollTop(feed[0].scrollHeight);
            }
        }

        function isFeedAtBottom() {
            const feed = document.getElementById('chatMessages');
            if (!feed) return true;
            return (feed.scrollHeight - feed.scrollTop - feed.clientHeight) < 80;
        }

        function handleInputKeydown(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        }

        function sendMessage() {
            if (!activeConversationId || isSending) return;

            const input = $('#chatInput');
            const message = input.val().trim();
            if (!message) return;

            isSending = true;
            input.prop('disabled', true);

            $.ajax({
                url: '<?= site_url("laboran/help/send") ?>',
                type: 'POST',
                data: {
                    conversation_id: activeConversationId,
                    message: message
                },
                dataType: 'json',
                success: function(res) {
                    isSending = false;
                    input.prop('disabled', false).val('');
                    input.css('height', 'auto');
                    input.focus();

                    if (res.status === 'success') {
                        loadMessages(activeConversationId, true);
                        fetchConversations();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Mengirim',
                            text: res.message || 'Terjadi kesalahan saat mengirim pesan.'
                        });
                    }
                },
                error: function() {
                    isSending = false;
                    input.prop('disabled', false);
                    Swal.fire({
                        icon: 'error',
                        title: 'Koneksi Terputus',
                        text: 'Gagal terhubung ke server.'
                    });
                }
            });
        }

        function toggleActiveStatus() {
            if (!activeConversationId) return;

            const targetStatus = (activeConversationStatus === 'resolved') ? 'open' : 'resolved';
            const actionText = (targetStatus === 'resolved') ? 'Tandai Selesai' : 'Buka Kembali';

            Swal.fire({
                title: `${actionText}?`,
                text: (targetStatus === 'resolved') 
                    ? 'Percakapan akan ditandai terselesaikan dan notifikasi penyelesaian akan dikirimkan.'
                    : 'Percakapan akan dibuka kembali untuk tindak lanjut bantuan.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ea580c',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: `Ya, ${actionText}`,
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= site_url("laboran/help/toggle-status") ?>',
                        type: 'POST',
                        data: {
                            conversation_id: activeConversationId,
                            status: targetStatus
                        },
                        dataType: 'json',
                        success: function(res) {
                            if (res.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Status Diperbarui',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                loadMessages(activeConversationId, true);
                                fetchConversations();
                            }
                        }
                    });
                }
            });
        }

        function createSampleChat() {
            $.ajax({
                url: '<?= site_url("laboran/help/sample") ?>',
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Simulasi Berhasil',
                            text: 'Pesan chat simulasi baru dari pengguna telah masuk.',
                            timer: 1800,
                            showConfirmButton: false
                        });
                        fetchConversations();
                        if (res.conversation_id) {
                            selectConversation(res.conversation_id);
                        }
                    }
                }
            });
        }

        function closeMobileChat() {
            $('#chatWorkspace').removeClass('mobile-active');
        }

        function pollUpdates() {
            fetchConversations();
            if (activeConversationId) {
                loadMessages(activeConversationId, false);
            }
        }

        function getInitials(name) {
            if (!name) return '??';
            const parts = name.trim().split(' ');
            if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();
            return (parts[0][0] + parts[1][0]).toUpperCase();
        }

        function escapeHtml(text) {
            if (!text) return '';
            return $('<div>').text(text).html();
        }
    </script>
</body>
</html>
