@if(config('app.demo_login_enabled'))
    <style>
        .vortex-demo-box {
            background: #FAF5FF;
            border: 1.5px solid #E9D5FF;
            border-radius: 16px;
            padding: 16px 18px;
            margin-bottom: 22px;
            box-shadow: 0 4px 16px -2px rgba(103, 61, 230, 0.08);
            font-family: inherit;
        }
        .vortex-demo-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 6px;
        }
        .vortex-demo-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #581C87;
        }
        .vortex-demo-pulse {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #10B981;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: vortexPulse 2s infinite;
        }
        @keyframes vortexPulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }
        .vortex-env-badge {
            background: #EDE9FE;
            color: #673DE6;
            border: 1px solid #DDD6FE;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: 700;
            font-family: monospace;
        }
        .vortex-demo-desc {
            font-size: 12px;
            color: #64748B;
            margin: 0 0 12px 0;
            line-height: 1.4;
        }
        .vortex-demo-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            background: linear-gradient(135deg, #673DE6 0%, #5428D8 100%);
            color: #FFFFFF !important;
            font-weight: 700;
            font-size: 13px;
            padding: 12px 16px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            box-shadow: 0 4px 14px 0 rgba(103, 61, 230, 0.32);
            transition: all 0.2s ease;
            text-decoration: none;
            user-select: none;
        }
        .vortex-demo-btn:hover {
            background: linear-gradient(135deg, #5428D8 0%, #431EBD 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px 0 rgba(103, 61, 230, 0.42);
        }
        .vortex-demo-btn:active {
            transform: translateY(0);
        }
        .vortex-demo-btn[disabled] {
            opacity: 0.75;
            cursor: not-allowed;
            transform: none !important;
        }
        .vortex-btn-content {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .vortex-loading-state {
            display: none;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        @keyframes vortexSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .vortex-spinner {
            animation: vortexSpin 0.75s linear infinite;
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }
        .vortex-spinner-circle {
            opacity: 0.25;
        }
        .vortex-spinner-path {
            opacity: 0.85;
        }
        .vortex-demo-divider {
            display: flex;
            align-items: center;
            margin-top: 18px;
            margin-bottom: 6px;
        }
        .vortex-demo-line {
            flex: 1;
            height: 1px;
            background: #E2E8F0;
        }
        .vortex-demo-or {
            padding: 0 12px;
            font-size: 10px;
            font-weight: 700;
            color: #94A3B8;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
    </style>

    <div class="vortex-demo-box">
        <div class="vortex-demo-header">
            <div class="vortex-demo-pill">
                <span class="vortex-demo-pulse"></span>
                <span>Demo Environment</span>
            </div>
            <span class="vortex-env-badge">Dev Mode</span>
        </div>
        <p class="vortex-demo-desc">
            One-click instant authentication for testing and development:
        </p>
        <button type="button" 
                wire:click="quickDemoLogin" 
                wire:loading.attr="disabled"
                class="vortex-demo-btn">
            <span wire:loading.remove wire:target="quickDemoLogin" class="vortex-btn-content">
                <span>⚡</span>
                <span>1-Click Demo Login (test@example.com)</span>
            </span>
            <span wire:loading.inline-flex wire:target="quickDemoLogin" class="vortex-loading-state">
                <svg width="16" height="16" style="width: 16px; height: 16px;" class="vortex-spinner" fill="none" viewBox="0 0 24 24">
                    <circle class="vortex-spinner-circle" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="vortex-spinner-path" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Logging in...</span>
            </span>
        </button>
        <div class="vortex-demo-divider">
            <div class="vortex-demo-line"></div>
            <span class="vortex-demo-or">or sign in with credentials</span>
            <div class="vortex-demo-line"></div>
        </div>
    </div>
@endif
