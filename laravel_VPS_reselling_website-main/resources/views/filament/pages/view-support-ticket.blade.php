<x-filament-panels::page>
    @php
    $ticket = isset($this) ? $this->record : ($record ?? null);
    $replies = $ticket ? $ticket->replies()->with(['user', 'admin'])->oldest()->get() : collect();
    $user = $ticket?->user;
    $service = $ticket?->service;
    $isAdmin = request()->is('admin*') || auth('admin')->check();
    $isClosed = $ticket?->status === 'closed';
    @endphp

    <style>
        .vt-ticket-wrap {
            font-family: inherit;
            display: flex;
            flex-direction: column;
            gap: 20px;
            width: 100%;
        }

        .vt-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .vt-overview-card {
            padding: 24px;
        }

        .vt-overview-top {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding-bottom: 20px;
            margin-bottom: 20px;
            border-bottom: 1px solid #f1f5f9;
        }

        .vt-badges {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
        }

        .vt-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            line-height: 1.4;
        }

        .vt-pill-id {
            background: rgba(103, 61, 230, 0.08);
            color: #673DE6;
            border: 1px solid rgba(103, 61, 230, 0.2);
        }

        .vt-pill-open {
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .vt-pill-in_progress {
            background: #dbeafe;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }

        .vt-pill-answered {
            background: #d1fae5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }

        .vt-pill-closed {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .vt-pill-high {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .vt-pill-medium {
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .vt-pill-low {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .vt-meta-right {
            font-size: 12px;
            color: #64748b;
            line-height: 1.6;
        }

        .vt-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
        }

        .vt-grid-card {
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            padding: 14px 16px;
        }

        .vt-grid-label {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 4px;
            display: block;
        }

        .vt-grid-val {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .vt-grid-sub {
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* CONVERSATION THREAD CONTAINER */
        .vt-thread-wrapper {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .vt-thread-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 24px;
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
        }

        .vt-thread-title {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
        }

        .vt-thread-badge {
            font-size: 11px;
            font-weight: 700;
            background: rgba(103, 61, 230, 0.1);
            color: #673DE6;
            padding: 2px 8px;
            border-radius: 9999px;
        }

        /* CHAT STAGE CANVAS (WhatsApp / Intercom Layout) */
        .vt-chat-canvas {
            background: #f8fafc;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            min-height: 320px;
        }

        /* MESSAGE ROWS */
        .vt-chat-row {
            display: flex;
            width: 100%;
        }

        .vt-chat-row-left {
            justify-content: flex-start;
        }

        .vt-chat-row-right {
            justify-content: flex-end;
        }

        /* CHAT BUBBLES */
        .vt-chat-bubble {
            max-width: 82%;
            min-width: 280px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            position: relative;
            transition: transform 0.15s ease;
        }

        /* ADMIN / STAFF BUBBLE (LEFT) */
        .vt-bubble-staff {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #673DE6;
            border-radius: 4px 18px 18px 18px;
        }

        /* USER / CLIENT BUBBLE (RIGHT) */
        .vt-bubble-client {
            background: #FAF8FF;
            border: 1px solid #DDD6FE;
            border-right: 4px solid #673DE6;
            border-radius: 18px 4px 18px 18px;
            box-shadow: 0 2px 8px rgba(103, 61, 230, 0.06);
        }

        /* BUBBLE HEADER */
        .vt-bubble-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 16px;
            gap: 10px;
        }

        .vt-bubble-head-staff {
            background: rgba(103, 61, 230, 0.04);
            border-bottom: 1px solid rgba(103, 61, 230, 0.08);
            border-top-right-radius: 17px;
        }

        .vt-bubble-head-client {
            background: rgba(103, 61, 230, 0.03);
            border-bottom: 1px solid rgba(103, 61, 230, 0.08);
            border-top-left-radius: 17px;
        }

        .vt-bubble-author {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .vt-bubble-avatar {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 11px;
            color: #ffffff;
            flex-shrink: 0;
        }

        .vt-avatar-staff {
            background: #673DE6;
            box-shadow: 0 2px 4px rgba(103, 61, 230, 0.35);
        }

        .vt-avatar-client {
            background: #0f172a;
        }

        .vt-bubble-name {
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
        }

        .vt-tag-staff {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            padding: 2px 6px;
            border-radius: 6px;
            font-size: 9px;
            font-weight: 700;
            background: #673DE6;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .vt-tag-client {
            display: inline-flex;
            align-items: center;
            padding: 2px 6px;
            border-radius: 6px;
            font-size: 9px;
            font-weight: 700;
            background: #e2e8f0;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .vt-bubble-time {
            font-size: 11px;
            color: #64748b;
            white-space: nowrap;
        }

        /* BUBBLE BODY */
        .vt-bubble-body {
            padding: 14px 18px 16px 18px;
            color: #0f172a;
            font-size: 14px;
            line-height: 1.65;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .vt-bubble-body code,
        .vt-bubble-body pre {
            font-family: 'JetBrains Mono', ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 12px;
            background: #f1f5f9;
            padding: 2px 6px;
            border-radius: 4px;
            color: #0f172a;
        }

        /* INLINE CHAT COMPOSER */
        .vt-composer-box {
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            padding: 18px 24px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .vt-composer-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .vt-composer-title {
            font-size: 12px;
            font-weight: 700;
            color: #334155;
            display: flex;
            align-items: center;
            gap: 6px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .vt-composer-hint {
            font-size: 11px;
            color: #64748b;
        }

        .vt-composer-textarea {
            width: 100%;
            min-height: 80px;
            padding: 12px 14px;
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #0f172a;
            font-size: 13.5px;
            line-height: 1.55;
            outline: none;
            resize: vertical;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.03);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .vt-composer-textarea:focus {
            border-color: #673DE6;
            box-shadow: 0 0 0 3px rgba(103, 61, 230, 0.15);
        }

        .vt-composer-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .vt-composer-note {
            font-size: 11px;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .vt-btn-send {
            background: #673DE6;
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            padding: 9px 20px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 8px rgba(103, 61, 230, 0.28);
            transition: all 0.2s ease;
        }

        .vt-btn-send:hover {
            background: #5428D8;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(103, 61, 230, 0.35);
        }

        .vt-btn-send:active {
            transform: translateY(0);
        }

        .vt-btn-send:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .vt-btn-content {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .vt-loading-state {
            display: none;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        @keyframes vtSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .vt-spinner {
            animation: vtSpin 0.75s linear infinite;
            width: 14px;
            height: 14px;
            flex-shrink: 0;
        }

        /* CLOSED BANNER */
        .vt-closed-banner {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 20px 24px;
            text-align: center;
        }

        @media (max-width: 640px) {
            .vt-chat-bubble {
                max-width: 94%;
                min-width: 240px;
            }

            .vt-bubble-head {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }

            .vt-composer-box {
                padding: 14px 16px;
            }

            .vt-chat-canvas {
                padding: 16px 12px;
            }
        }
    </style>

    <div class="vt-ticket-wrap">
        <!-- 1. TICKET OVERVIEW CARD -->
        <div class="vt-card vt-overview-card">
            <div class="vt-overview-top">
                <div class="vt-badges">
                    <span class="vt-pill vt-pill-id">
                        {{ $ticket->formatted_id }}
                    </span>

                    @if($ticket->status === 'open')
                    <span class="vt-pill vt-pill-open">🟡 Open</span>
                    @elseif($ticket->status === 'in_progress')
                    <span class="vt-pill vt-pill-in_progress">🔵 In Progress</span>
                    @elseif($ticket->status === 'answered')
                    <span class="vt-pill vt-pill-answered">🟢 Answered</span>
                    @else
                    <span class="vt-pill vt-pill-closed">⚪ Closed</span>
                    @endif

                    <span class="vt-pill {{ $ticket->priority === 'high' ? 'vt-pill-high' : ($ticket->priority === 'medium' ? 'vt-pill-medium' : 'vt-pill-low') }}">
                        {{ strtoupper($ticket->priority) }} Priority
                    </span>
                </div>

                <div class="vt-meta-right">
                    <div><strong>Opened:</strong> {{ $ticket->created_at->format('M d, Y · H:i T') }}</div>
                    <div><strong>Last Activity:</strong> {{ $ticket->updated_at->diffForHumans() }}</div>
                </div>
            </div>

            <!-- Details Grid -->
            <div class="vt-grid">
                <div class="vt-grid-card">
                    <span class="vt-grid-label">Department</span>
                    <span class="vt-grid-val">
                        @if($ticket->department === 'technical')
                        🛠️ Technical Support
                        @elseif($ticket->department === 'billing')
                        💳 Billing & Payments
                        @else
                        💼 Sales & Upgrades
                        @endif
                    </span>
                </div>

                <div class="vt-grid-card">
                    <span class="vt-grid-label">Associated Server</span>
                    @if($service)
                    <span class="vt-grid-val" style="color: #673DE6;">
                        {{ $service->server_name ?: ('VPS #' . $service->id) }}
                    </span>
                    <span class="vt-grid-sub">{{ $service->ip_address ?: 'Pending IP' }}</span>
                    @else
                    <span class="vt-grid-val" style="color: #64748b;">General / None</span>
                    @endif
                </div>

                <div class="vt-grid-card">
                    <span class="vt-grid-label">Customer Account</span>
                    <span class="vt-grid-val">{{ $user?->name ?? 'Customer' }}</span>
                    <span class="vt-grid-sub">{{ $user?->email ?? '' }}</span>
                </div>

                <div class="vt-grid-card">
                    <span class="vt-grid-label">Messages</span>
                    <span class="vt-grid-val" style="font-size: 16px; color: #673DE6;">{{ $replies->count() }}</span>
                    <span class="vt-grid-sub">Replies in thread</span>
                </div>
            </div>
        </div>

        <!-- 2. WHATSAPP-STYLE CONVERSATION THREAD -->
        <div class="vt-thread-wrapper">
            <!-- Thread Title Bar -->
            <div class="vt-thread-header">
                <h2 class="vt-thread-title">
                    <span>💬 Ticket Conversation</span>
                    <span class="vt-thread-badge">{{ $replies->count() }} {{ \Illuminate\Support\Str::plural('message', $replies->count()) }}</span>
                </h2>

                <div style="font-size: 11px; color: #64748b; display: flex; align-items: center; gap: 12px;">
                    <span style="display: inline-flex; align-items: center; gap: 4px;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #673DE6; display: inline-block;"></span>
                        Staff (Left)
                    </span>
                    <span style="display: inline-flex; align-items: center; gap: 4px;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #0f172a; display: inline-block;"></span>
                        Client (Right)
                    </span>
                </div>
            </div>

            <!-- Chat Stage: User (Right), Admin (Left) -->
            <div class="vt-chat-canvas">
                @forelse($replies as $reply)
                @php
                $isStaff = $reply->is_staff_reply;
                @endphp

                <div class="vt-chat-row {{ $isStaff ? 'vt-chat-row-left' : 'vt-chat-row-right' }}">
                    <div class="vt-chat-bubble {{ $isStaff ? 'vt-bubble-staff' : 'vt-bubble-client' }}">
                        <!-- Bubble Header -->
                        <div class="vt-bubble-head {{ $isStaff ? 'vt-bubble-head-staff' : 'vt-bubble-head-client' }}">
                            <div class="vt-bubble-author">
                                <div class="vt-bubble-avatar {{ $isStaff ? 'vt-avatar-staff' : 'vt-avatar-client' }}">
                                    {{ strtoupper(substr($isStaff ? ($reply->admin?->name ?? 'S') : ($reply->user?->name ?? 'U'), 0, 1)) }}
                                </div>

                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <span class="vt-bubble-name">
                                        {{ $isStaff ? ($reply->admin?->name ?? 'VortexCloud Support') : ($reply->user?->name ?? 'Customer') }}
                                    </span>

                                    @if($isStaff)
                                    <span class="vt-tag-staff">
                                        <svg style="width: 9px; height: 9px;" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd" />
                                        </svg>
                                        Staff
                                    </span>
                                    @else
                                    <span class="vt-tag-client">Client</span>
                                    @endif
                                </div>
                            </div>

                            <div class="vt-bubble-time">
                                {{ $reply->created_at->format('h:i A · M d') }} ({{ $reply->created_at->diffForHumans() }})
                            </div>
                        </div>

                        <!-- Bubble Message Body -->
                        <div class="vt-bubble-body">
                            {!! nl2br(e($reply->message)) !!}
                        </div>
                    </div>
                </div>
                @empty
                <div style="padding: 40px 20px; text-align: center; color: #64748b;">
                    <div style="font-size: 28px; margin-bottom: 8px;">💬</div>
                    <p style="font-size: 14px; margin: 0; font-weight: 600;">No messages recorded in this conversation yet.</p>
                    <p style="font-size: 12px; margin: 4px 0 0 0; color: #94a3b8;">Use the composer below to post a reply.</p>
                </div>
                @endforelse
            </div>

            <!-- 3. INLINE CHAT COMPOSER (WhatsApp style at bottom) -->
            @if(!$isClosed)
            <div class="vt-composer-box">
                <div class="vt-composer-top">
                    <span class="vt-composer-title">
                        @if($isAdmin)
                        <span>🛡️ Official Staff Response</span>
                        @else
                        <span>✍️ Send Reply</span>
                        @endif
                    </span>
                    <span class="vt-composer-hint">
                        Press <strong>Ctrl + Enter</strong> to send
                    </span>
                </div>

                <textarea
                    wire:model="quickReplyMessage"
                    wire:keydown.ctrl.enter="sendQuickReply"
                    wire:keydown.cmd.enter="sendQuickReply"
                    class="vt-composer-textarea"
                    placeholder="{{ $isAdmin ? 'Type your official response to the customer here... (Press Ctrl + Enter to send)' : 'Type your reply or additional server details here... (Press Ctrl + Enter to send)' }}"></textarea>

                <div class="vt-composer-bottom">
                    <span class="vt-composer-note">
                        💡 Plain text, logs, and IPs are supported.
                    </span>

                    <button
                        type="button"
                        wire:click="sendQuickReply"
                        wire:loading.attr="disabled"
                        class="vt-btn-send">
                        <span wire:loading.remove wire:target="sendQuickReply" class="vt-btn-content">
                            <svg style="width: 13px; height: 13px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                            </svg>
                            <span>{{ $isAdmin ? 'Post Response' : 'Send Reply' }}</span>
                        </span>
                        <span wire:loading.inline-flex wire:target="sendQuickReply" class="vt-loading-state">
                            <svg class="vt-spinner" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity: 0.25;"></circle>
                                <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" style="opacity: 0.85;"></path>
                            </svg>
                            <span>Sending...</span>
                        </span>
                    </button>
                </div>
            </div>
            @else
            <div class="vt-closed-banner">
                <strong style="color: #0f172a; font-size: 13px;">🔒 This support ticket is currently Closed.</strong>
                <div style="font-size: 12px; margin-top: 4px; color: #64748b;">
                    If you require further assistance on this issue, you can reopen it using the "Reopen Ticket" button above.
                </div>
            </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>