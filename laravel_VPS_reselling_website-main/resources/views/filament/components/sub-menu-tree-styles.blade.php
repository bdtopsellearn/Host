<style>
    /* ==========================================================================
       Modern UI Minimalism: Tight Edges & Compact Spacing
       ========================================================================== */
    .fi-section,
    .fi-wi-widget .fi-section,
    .fi-ta-ctn {
        border-radius: 8px !important;
    }

    /* Tighten dashboard layout gaps & margins */
    .fi-page-content {
        gap: 0.75rem !important;
    }

    .fi-page-header {
        margin-bottom: 0.625rem !important;
        padding-bottom: 0 !important;
    }

    /* Sleek minimal scrollbar for sidebar (eliminates bulky native scrollbar) */
    .fi-sidebar-nav {
        scrollbar-width: thin !important;
        scrollbar-color: rgba(148, 163, 184, 0.3) transparent !important;
    }

    .fi-sidebar-nav::-webkit-scrollbar {
        width: 4px !important;
    }

    .fi-sidebar-nav::-webkit-scrollbar-track {
        background: transparent !important;
    }

    .fi-sidebar-nav::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.35) !important;
        border-radius: 4px !important;
    }

    .fi-sidebar-nav::-webkit-scrollbar-thumb:hover {
        background: rgba(103, 61, 230, 0.5) !important;
    }

    /* ==========================================================================
       VortexCloud Sub-Menu Timeline / Tree Connector Styles
       Renders connected vertical lines and bullet node dots for sub-menu items
       matching modern enterprise design specs.
       ========================================================================== */

    /* Navigation Group Parent Button - Enhanced Clickable Styling */
    .fi-sidebar-group-btn {
        cursor: pointer !important;
        border-radius: 0.5rem !important;
        padding: 0.5rem 0.625rem !important;
        transition: all 0.15s ease-in-out !important;
        user-select: none;
    }

    .fi-sidebar-group-btn:hover {
        background-color: rgba(241, 245, 249, 0.9) !important; /* Slate-100 */
    }

    .fi-sidebar-group-btn:hover .fi-icon {
        color: #673DE6 !important; /* Royal Purple */
    }

    .fi-sidebar-group-btn:hover .fi-sidebar-group-label {
        color: #0F172A !important; /* Deep Slate Navy */
    }

    .fi-sidebar-group-label {
        font-weight: 600 !important;
        letter-spacing: -0.01em !important;
    }

    /* Chevron collapse indicator */
    .fi-sidebar-group-collapse-btn {
        transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }

    /* Sub-menu group items container */
    .fi-sidebar-group .fi-sidebar-group-items {
        position: relative;
        margin-top: 2px;
        margin-bottom: 6px;
    }

    /* Ensure parent button doesn't clip the connector lines */
    .fi-sidebar-group-items .fi-sidebar-item-btn {
        overflow: visible !important;
        padding-top: 0.4rem;
        padding-bottom: 0.4rem;
        border-radius: 0.5rem;
        transition: background-color 0.15s ease, color 0.15s ease;
    }

    /* Sub-menu tree connector container */
    .fi-sidebar-item-grouped-border {
        position: relative !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 1.5rem !important; /* 24px width matching parent group icon */
        min-width: 1.5rem !important;
        height: 1.5rem !important; /* 24px height */
        min-height: 1.5rem !important;
        flex-shrink: 0 !important;
    }

    /* Upper connector line: connects from top of row spacing down to the dot */
    .fi-sidebar-item-grouped-border-part-not-first {
        position: absolute !important;
        top: -1rem !important; /* extends 16px up through row gap to join previous item */
        bottom: 50% !important; /* terminates at exact center of dot */
        left: 50% !important;
        width: 2px !important;
        transform: translateX(-50%) !important;
        background-color: #CBD5E1 !important; /* Slate-300 */
        border-radius: 1px !important;
        z-index: 1 !important;
    }

    /* Lower connector line: connects from the dot down through row spacing */
    .fi-sidebar-item-grouped-border-part-not-last {
        position: absolute !important;
        top: 50% !important; /* starts at exact center of dot */
        bottom: -1rem !important; /* extends 16px down through row gap to join next item */
        left: 50% !important;
        width: 2px !important;
        transform: translateX(-50%) !important;
        background-color: #CBD5E1 !important; /* Slate-300 */
        border-radius: 1px !important;
        z-index: 1 !important;
    }

    /* Circular Node Dot */
    .fi-sidebar-item-grouped-border-part {
        position: relative !important;
        z-index: 2 !important;
        width: 7px !important;
        height: 7px !important;
        min-width: 7px !important;
        min-height: 7px !important;
        max-width: 7px !important;
        max-height: 7px !important;
        border-radius: 9999px !important;
        background-color: #94A3B8 !important; /* Slate-400 */
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        box-sizing: border-box !important;
    }

    /* Label Typography & Hierarchy */
    .fi-sidebar-group-items .fi-sidebar-item-label {
        font-size: 0.875rem !important; /* 14px */
        line-height: 1.25rem !important;
        font-weight: 500 !important;
        letter-spacing: -0.01em !important;
        color: #475569 !important; /* Slate-600 */
        transition: color 0.15s ease-in-out !important;
    }

    /* Hover State on Sub-Menu Item */
    .fi-sidebar-item-btn:hover .fi-sidebar-item-grouped-border-part {
        background-color: #673DE6 !important; /* Royal Purple */
        transform: scale(1.35) !important;
        box-shadow: 0 0 0 3px rgba(103, 61, 230, 0.25) !important;
    }

    .fi-sidebar-item-btn:hover .fi-sidebar-item-label {
        color: #0F172A !important; /* Deep Slate Navy */
    }

    /* Active State on Sub-Menu Item */
    .fi-sidebar-item.fi-active .fi-sidebar-item-btn {
        background-color: rgba(103, 61, 230, 0.07) !important;
    }

    .fi-sidebar-item.fi-active .fi-sidebar-item-grouped-border-part {
        background-color: #673DE6 !important; /* Royal Purple */
        transform: scale(1.3) !important;
        box-shadow: 0 0 0 3px rgba(103, 61, 230, 0.3) !important;
    }

    .fi-sidebar-item.fi-active .fi-sidebar-item-label {
        color: #673DE6 !important; /* Royal Purple */
        font-weight: 600 !important;
    }

    /* --------------------------------------------------------------------------
       Dark Mode Palette
       -------------------------------------------------------------------------- */
    .dark .fi-sidebar-group-btn:hover {
        background-color: rgba(255, 255, 255, 0.06) !important;
    }

    .dark .fi-sidebar-group-btn:hover .fi-icon {
        color: #A78BFA !important;
    }

    .dark .fi-sidebar-group-btn:hover .fi-sidebar-group-label {
        color: #F8FAFC !important;
    }

    .dark .fi-sidebar-item-grouped-border-part-not-first,
    .dark .fi-sidebar-item-grouped-border-part-not-last {
        background-color: #475569 !important; /* Slate-600 */
    }

    .dark .fi-sidebar-item-grouped-border-part {
        background-color: #64748B !important; /* Slate-500 */
    }

    .dark .fi-sidebar-group-items .fi-sidebar-item-label {
        color: #94A3B8 !important; /* Slate-400 */
    }

    .dark .fi-sidebar-item-btn:hover .fi-sidebar-item-label {
        color: #F8FAFC !important; /* Light slate */
    }

    .dark .fi-sidebar-item-btn:hover .fi-sidebar-item-grouped-border-part {
        background-color: #8B5CF6 !important;
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.3) !important;
    }

    .dark .fi-sidebar-item.fi-active .fi-sidebar-item-btn {
        background-color: rgba(139, 92, 246, 0.12) !important;
    }

    .dark .fi-sidebar-item.fi-active .fi-sidebar-item-grouped-border-part {
        background-color: #8B5CF6 !important;
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.35) !important;
    }

    .dark .fi-sidebar-item.fi-active .fi-sidebar-item-label {
        color: #A78BFA !important;
        font-weight: 600 !important;
    }
</style>

<script>
    /* Ensure navigation groups default to expanded state so timeline sub-menus are immediately visible */
    (function() {
        try {
            var stored = localStorage.getItem('collapsedGroups');
            if (stored && stored !== '[]') {
                var parsed = JSON.parse(stored);
                if (Array.isArray(parsed) && parsed.length > 0) {
                    localStorage.setItem('collapsedGroups', JSON.stringify([]));
                }
            }
        } catch (e) {}
    })();
</script>
