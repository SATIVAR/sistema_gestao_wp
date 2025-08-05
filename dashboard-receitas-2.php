<?php
/**
 * Template Name: Dashboard - Receitas 2
 */

get_header('zero');

if (!is_user_logged_in()) {
    get_template_part('login');
} else {
    get_template_part('header', 'user');
?>

<style>
    /* shadcn/ui CSS Variables */
    :root {
        --background: 0 0% 100%;
        --foreground: 222.2 84% 4.9%;
        --card: 0 0% 100%;
        --card-foreground: 222.2 84% 4.9%;
        --popover: 0 0% 100%;
        --popover-foreground: 222.2 84% 4.9%;
        --primary: 221.2 83.2% 53.3%;
        --primary-foreground: 210 40% 98%;
        --secondary: 210 40% 96%;
        --secondary-foreground: 222.2 84% 4.9%;
        --muted: 210 40% 96%;
        --muted-foreground: 215.4 16.3% 46.9%;
        --accent: 210 40% 96%;
        --accent-foreground: 222.2 84% 4.9%;
        --destructive: 0 84.2% 60.2%;
        --destructive-foreground: 210 40% 98%;
        --border: 214.3 31.8% 91.4%;
        --input: 214.3 31.8% 91.4%;
        --ring: 221.2 83.2% 53.3%;
        --radius: 0.5rem;
    }

    /* Apply CSS variables to elements */
    .bg-background { background-color: hsl(var(--background)); }
    .bg-foreground { background-color: hsl(var(--foreground)); }
    .bg-card { background-color: hsl(var(--card)); }
    .bg-card-foreground { background-color: hsl(var(--card-foreground)); }
    .bg-popover { background-color: hsl(var(--popover)); }
    .bg-popover-foreground { background-color: hsl(var(--popover-foreground)); }
    .bg-primary { background-color: hsl(var(--primary)); }
    .bg-primary-foreground { background-color: hsl(var(--primary-foreground)); }
    .bg-secondary { background-color: hsl(var(--secondary)); }
    .bg-secondary-foreground { background-color: hsl(var(--secondary-foreground)); }
    .bg-muted { background-color: hsl(var(--muted)); }
    .bg-muted-foreground { background-color: hsl(var(--muted-foreground)); }
    .bg-accent { background-color: hsl(var(--accent)); }
    .bg-accent-foreground { background-color: hsl(var(--accent-foreground)); }
    .bg-destructive { background-color: hsl(var(--destructive)); }
    .bg-destructive-foreground { background-color: hsl(var(--destructive-foreground)); }

    .text-background { color: hsl(var(--background)); }
    .text-foreground { color: hsl(var(--foreground)); }
    .text-card { color: hsl(var(--card)); }
    .text-card-foreground { color: hsl(var(--card-foreground)); }
    .text-popover { color: hsl(var(--popover)); }
    .text-popover-foreground { color: hsl(var(--popover-foreground)); }
    .text-primary { color: hsl(var(--primary)); }
    .text-primary-foreground { color: hsl(var(--primary-foreground)); }
    .text-secondary { color: hsl(var(--secondary)); }
    .text-secondary-foreground { color: hsl(var(--secondary-foreground)); }
    .text-muted { color: hsl(var(--muted)); }
    .text-muted-foreground { color: hsl(var(--muted-foreground)); }
    .text-accent { color: hsl(var(--accent)); }
    .text-accent-foreground { color: hsl(var(--accent-foreground)); }
    .text-destructive { color: hsl(var(--destructive)); }
    .text-destructive-foreground { color: hsl(var(--destructive-foreground)); }

    .border-border { border-color: hsl(var(--border)); }
    .border-input { border-color: hsl(var(--input)); }
    .border-ring { border-color: hsl(var(--ring)); }

    /* Modern card grid - Inspired by Slack, Notion, ClickUp */
    .receitas-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: 1.25rem;
        padding: 0.75rem 0;
    }
    
    /* Premium Card Component - Modern app style */
    .receita-card {
        background: hsl(0 0% 100%);
        border: 1px solid hsl(214.3 31.8% 91.4%);
        border-radius: 0.75rem;
        padding: 1.25rem;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        cursor: pointer;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        min-height: 160px;
        backdrop-filter: blur(8px);
    }
    
    .receita-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-color: hsl(221.2 83.2% 53.3% / 0.4);
        background: hsl(0 0% 99.5%);
    }
    
    .receita-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, hsl(221.2 83.2% 53.3%), hsl(142.1 76.2% 36.3%));
        opacity: 0;
        transition: opacity 0.25s ease;
    }
    
    .receita-card:hover::before {
        opacity: 1;
    }
    
    /* Modern header with enhanced spacing */
    .card-compact-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 0.75rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid hsl(214.3 31.8% 94%);
    }
    
    .card-main-content {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        flex: 1;
        margin-bottom: 0.5rem;
    }
    
    .card-actions-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-top: auto;
        padding-top: 1rem;
        border-top: 1px solid hsl(214.3 31.8% 94%);
        background: hsl(0 0% 99%);
        margin: 0 -1.25rem -1.25rem -1.25rem;
        padding: 1rem 1.25rem;
        border-radius: 0 0 0.75rem 0.75rem;
    }
    
    /* Modern Avatar - Enhanced Slack/Notion style */
    .patient-avatar {
        position: relative;
        display: flex;
        height: 2.75rem;
        width: 2.75rem;
        flex-shrink: 0;
        overflow: hidden;
        align-items: center;
        justify-content: center;
        border-radius: 0.5rem;
        background: linear-gradient(135deg, hsl(210 40% 96%), hsl(214.3 31.8% 94%));
        font-weight: 600;
        font-size: 0.875rem;
        border: 1px solid hsl(214.3 31.8% 88%);
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease;
    }
    
    .receita-card:hover .patient-avatar {
        transform: scale(1.05);
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.1);
    }
    
    /* Modern Badge - Enhanced Notion/ClickUp style */
    .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.375rem;
        border: 1px solid transparent;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        width: fit-content;
        white-space: nowrap;
        flex-shrink: 0;
        gap: 0.25rem;
        transition: all 0.2s ease;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        backdrop-filter: blur(4px);
    }
    
    .status-badge.complete {
        background: linear-gradient(135deg, hsl(142.1 76.2% 36.3% / 0.15), hsl(142.1 76.2% 36.3% / 0.1));
        color: hsl(142.1 76.2% 30%);
        border-color: hsl(142.1 76.2% 36.3% / 0.2);
        box-shadow: 0 1px 2px 0 hsl(142.1 76.2% 36.3% / 0.1);
    }
    
    .status-badge.pending {
        background: linear-gradient(135deg, hsl(47.9 95.8% 53.1% / 0.2), hsl(47.9 95.8% 53.1% / 0.15));
        color: hsl(38 92% 40%);
        border-color: hsl(47.9 95.8% 53.1% / 0.3);
        box-shadow: 0 1px 2px 0 hsl(47.9 95.8% 53.1% / 0.15);
    }
    
    .status-badge.type {
        background: linear-gradient(135deg, hsl(210 40% 96%), hsl(214.3 31.8% 94%));
        color: hsl(215.4 16.3% 40%);
        border-color: hsl(214.3 31.8% 88%);
        font-weight: 500;
        text-transform: none;
        letter-spacing: normal;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    
    /* Modern ID Badge */
    .id-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.375rem;
        background: linear-gradient(135deg, hsl(221.2 83.2% 53.3% / 0.1), hsl(221.2 83.2% 53.3% / 0.05));
        color: hsl(221.2 83.2% 45%);
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 700;
        font-family: ui-monospace, SFMono-Regular, "SF Mono", Consolas, "Liberation Mono", Menlo, monospace;
        border: 1px solid hsl(221.2 83.2% 53.3% / 0.2);
        box-shadow: 0 1px 2px 0 hsl(221.2 83.2% 53.3% / 0.1);
        transition: all 0.2s ease;
    }
    
    .receita-card:hover .id-badge {
        background: linear-gradient(135deg, hsl(221.2 83.2% 53.3% / 0.15), hsl(221.2 83.2% 53.3% / 0.1));
        border-color: hsl(221.2 83.2% 53.3% / 0.3);
        transform: scale(1.05);
    }
    
    /* Enhanced Input Component */
    .search-input {
        display: flex;
        height: 2.75rem;
        width: 100%;
        min-width: 0;
        border-radius: 0.75rem;
        border: 1px solid hsl(214.3 31.8% 88%);
        background: linear-gradient(135deg, hsl(0 0% 100%), hsl(210 40% 99%));
        padding: 0.5rem 1rem 0.5rem 3rem;
        font-size: 0.875rem;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.05), inset 0 1px 2px 0 rgba(0, 0, 0, 0.02);
        transition: all 0.25s ease;
        outline: none;
        color: hsl(222.2 84% 4.9%);
        backdrop-filter: blur(4px);
    }
    
    .search-input:focus-visible {
        border-color: hsl(221.2 83.2% 53.3%);
        box-shadow: 0 0 0 4px hsl(221.2 83.2% 53.3% / 0.15), 0 4px 8px 0 rgba(0, 0, 0, 0.1);
        background: hsl(0 0% 100%);
        transform: translateY(-1px);
    }
    
    .search-input::placeholder {
        color: hsl(215.4 16.3% 50%);
        font-weight: 500;
    }
    
    /* Enhanced Select Component */
    .filter-select {
        display: flex;
        height: 2.75rem;
        width: fit-content;
        align-items: center;
        justify-content: between;
        gap: 0.75rem;
        border-radius: 0.75rem;
        border: 1px solid hsl(214.3 31.8% 88%);
        background: linear-gradient(135deg, hsl(0 0% 100%), hsl(210 40% 99%));
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        white-space: nowrap;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.05), inset 0 1px 2px 0 rgba(0, 0, 0, 0.02);
        transition: all 0.25s ease;
        outline: none;
        color: hsl(222.2 84% 4.9%);
        cursor: pointer;
        backdrop-filter: blur(4px);
    }
    
    .filter-select:hover {
        border-color: hsl(214.3 31.8% 82%);
        background: hsl(0 0% 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.1);
    }
    
    .filter-select:focus-visible {
        border-color: hsl(221.2 83.2% 53.3%);
        box-shadow: 0 0 0 4px hsl(221.2 83.2% 53.3% / 0.15), 0 4px 8px 0 rgba(0, 0, 0, 0.1);
        background: hsl(0 0% 100%);
        transform: translateY(-1px);
    }
    
    /* Button Component - shadcn/ui style */
    .action-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        white-space: nowrap;
        border-radius: 0.375rem;
        text-sm: true;
        font-weight: 500;
        transition: all 0.2s ease;
        outline: none;
        height: 2.25rem;
        width: 2.25rem;
        flex-shrink: 0;
        text-decoration: none;
        border: 1px solid;
    }
    
    .action-button:focus-visible {
        border-color: hsl(221.2 83.2% 53.3%);
        box-shadow: 0 0 0 3px hsl(221.2 83.2% 53.3% / 0.2);
    }
    
    .action-button.destructive {
        background: hsl(0 84.2% 60.2%);
        color: hsl(210 40% 98%);
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        border-color: hsl(0 84.2% 60.2%);
    }
    
    .action-button.destructive:hover {
        background: hsl(0 84.2% 60.2% / 0.9);
    }
    
    .action-button.outline {
        border: 1px solid hsl(214.3 31.8% 91.4%);
        background: hsl(0 0% 100%);
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        color: hsl(222.2 84% 4.9%);
    }
    
    .action-button.outline:hover {
        background: hsl(210 40% 96%);
        color: hsl(222.2 84% 4.9%);
    }
    
    /* Enhanced content sections */
    .patient-info {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .patient-name {
        font-weight: 500;
        line-height: 1.3;
        margin: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        transition: color 0.2s ease;
    }
    
    .receita-card:hover .patient-name {
        color: hsl(221.2 83.2% 45%);
    }
    
    .patient-meta {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.8125rem;
        color: hsl(215.4 16.3% 46.9%);
        flex-wrap: wrap;
        line-height: 1.4;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        white-space: nowrap;
    }
    
    .meta-separator {
        width: 2px;
        height: 2px;
        background: hsl(215.4 16.3% 46.9%);
        border-radius: 50%;
        flex-shrink: 0;
    }
    
    /* Enhanced description */
    .description-text {
        font-size: 0.8125rem;
        color: hsl(215.4 16.3% 50%);
        line-height: 1.5;
        margin: 0.5rem 0 0 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        background: hsl(210 40% 98%);
        padding: 0.75rem;
        border-radius: 0.5rem;
        border: 1px solid hsl(214.3 31.8% 94%);
        transition: all 0.2s ease;
    }
    
    .receita-card:hover .description-text {
        background: hsl(210 40% 97%);
        border-color: hsl(214.3 31.8% 90%);
    }
    
    /* Modern file indicators - Enhanced Slack/ClickUp style */
    .file-indicator {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.25s ease;
        border: 1px solid transparent;
        backdrop-filter: blur(4px);
        position: relative;
        overflow: hidden;
    }
    
    .file-indicator::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        transition: left 0.5s ease;
    }
    
    .file-indicator:hover::before {
        left: 100%;
    }
    
    .file-indicator.available {
        background: linear-gradient(135deg, hsl(142.1 76.2% 36.3% / 0.15), hsl(142.1 76.2% 36.3% / 0.1));
        color: hsl(142.1 76.2% 30%);
        border-color: hsl(142.1 76.2% 36.3% / 0.25);
        box-shadow: 0 2px 4px 0 hsl(142.1 76.2% 36.3% / 0.1);
    }
    
    .file-indicator.available:hover {
        background: linear-gradient(135deg, hsl(142.1 76.2% 36.3% / 0.2), hsl(142.1 76.2% 36.3% / 0.15));
        border-color: hsl(142.1 76.2% 36.3% / 0.4);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px 0 hsl(142.1 76.2% 36.3% / 0.15);
    }
    
    .file-indicator.pending {
        background: linear-gradient(135deg, hsl(0 84.2% 60.2% / 0.15), hsl(0 84.2% 60.2% / 0.1));
        color: hsl(0 84.2% 50%);
        border-color: hsl(0 84.2% 60.2% / 0.25);
        cursor: not-allowed;
        box-shadow: 0 2px 4px 0 hsl(0 84.2% 60.2% / 0.1);
        opacity: 0.8;
    }
    
    /* Modern action buttons */
    .action-button-compact {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        border-radius: 0.5rem;
        border: 1px solid hsl(214.3 31.8% 88%);
        background: linear-gradient(135deg, hsl(0 0% 100%), hsl(210 40% 98%));
        color: hsl(215.4 16.3% 46.9%);
        transition: all 0.25s ease;
        text-decoration: none;
        cursor: pointer;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        position: relative;
        overflow: hidden;
    }
    
    .action-button-compact::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.6), transparent);
        transition: left 0.5s ease;
    }
    
    .action-button-compact:hover::before {
        left: 100%;
    }
    
    .action-button-compact:hover {
        background: linear-gradient(135deg, hsl(210 40% 96%), hsl(214.3 31.8% 94%));
        border-color: hsl(214.3 31.8% 82%);
        color: hsl(222.2 84% 4.9%);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.1);
    }
    
    .action-button-compact.destructive {
        color: hsl(0 84.2% 60.2%);
        border-color: hsl(0 84.2% 60.2% / 0.25);
        background: linear-gradient(135deg, hsl(0 84.2% 60.2% / 0.05), hsl(0 84.2% 60.2% / 0.02));
    }
    
    .action-button-compact.destructive:hover {
        background: linear-gradient(135deg, hsl(0 84.2% 60.2% / 0.1), hsl(0 84.2% 60.2% / 0.05));
        border-color: hsl(0 84.2% 60.2% / 0.4);
        color: hsl(0 84.2% 50%);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px 0 hsl(0 84.2% 60.2% / 0.15);
    }
    
    /* Enhanced search filters container */
    .search-filters {
        background: linear-gradient(135deg, hsl(0 0% 100%), hsl(210 40% 99%));
        border: 1px solid hsl(214.3 31.8% 88%);
        border-radius: 1rem;
        padding: 2rem;
        margin-bottom: 2.5rem;
        backdrop-filter: blur(8px);
        position: relative;
        overflow: hidden;
    }
    
    .search-filters::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, hsl(221.2 83.2% 53.3%), hsl(142.1 76.2% 36.3%), hsl(47.9 95.8% 53.1%));
    }
    
    /* Enhanced Pagination */
    .pagination-container {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.75rem;
        margin-top: 3rem;
        padding: 1.5rem;
        background: linear-gradient(135deg, hsl(0 0% 100%), hsl(210 40% 99%));
        border-radius: 1rem;
        border: 1px solid hsl(214.3 31.8% 88%);
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.05);
    }
    
    .pagination-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        white-space: nowrap;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        transition: all 0.25s ease;
        outline: none;
        height: 2.5rem;
        padding: 0.5rem 1rem;
        border: 1px solid hsl(214.3 31.8% 88%);
        background: linear-gradient(135deg, hsl(0 0% 100%), hsl(210 40% 99%));
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        color: hsl(222.2 84% 4.9%);
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    
    .pagination-button::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        transition: left 0.5s ease;
    }
    
    .pagination-button:hover::before {
        left: 100%;
    }
    
    .pagination-button:hover:not(:disabled) {
        background: linear-gradient(135deg, hsl(210 40% 96%), hsl(214.3 31.8% 94%));
        border-color: hsl(214.3 31.8% 82%);
        color: hsl(222.2 84% 4.9%);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.1);
    }
    
    .pagination-button.active {
        background: linear-gradient(135deg, hsl(142.1 76.2% 36.3%), hsl(142.1 76.2% 30%));
        color: hsl(210 40% 98%);
        border-color: hsl(142.1 76.2% 36.3%);
        box-shadow: 0 4px 8px 0 hsl(142.1 76.2% 36.3% / 0.3);
        transform: translateY(-1px);
    }
    
    .pagination-button.active:hover {
        background: linear-gradient(135deg, hsl(142.1 76.2% 40%), hsl(142.1 76.2% 34%));
        box-shadow: 0 6px 12px 0 hsl(142.1 76.2% 36.3% / 0.4);
    }
    
    .pagination-button:disabled {
        pointer-events: none;
        opacity: 0.4;
        background: hsl(210 40% 98%);
        color: hsl(215.4 16.3% 60%);
    }
    
    /* Enhanced empty state */
    .empty-state {
        text-align: center;
        padding: 5rem 3rem;
        background: linear-gradient(135deg, hsl(0 0% 100%), hsl(210 40% 99%));
        border: 1px solid hsl(214.3 31.8% 88%);
        border-radius: 1.5rem;
        margin: 3rem 0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        backdrop-filter: blur(8px);
        position: relative;
        overflow: hidden;
    }
    
    .empty-state::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, hsl(221.2 83.2% 53.3%), hsl(142.1 76.2% 36.3%), hsl(47.9 95.8% 53.1%));
    }
    
    /* Enhanced loading skeleton */
    .loading-skeleton {
        background: linear-gradient(90deg, 
            hsl(210 40% 96%) 25%, 
            hsl(214.3 31.8% 92%) 50%, 
            hsl(210 40% 96%) 75%);
        background-size: 200% 100%;
        animation: loading 2s infinite ease-in-out;
        border-radius: 0.75rem;
    }
    
    @keyframes loading {
        0% { 
            background-position: 200% 0; 
            opacity: 0.6;
        }
        50% { 
            opacity: 0.8;
        }
        100% { 
            background-position: -200% 0; 
            opacity: 0.6;
        }
    }
    
    /* Card entrance animation */
    @keyframes cardEntrance {
        0% {
            opacity: 0;
            transform: translateY(20px) scale(0.95);
        }
        100% {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    
    .receita-card {
        animation: cardEntrance 0.4s ease-out;
    }
    
    /* Staggered animation for multiple cards */
    .receita-card:nth-child(1) { animation-delay: 0.05s; }
    .receita-card:nth-child(2) { animation-delay: 0.1s; }
    .receita-card:nth-child(3) { animation-delay: 0.15s; }
    .receita-card:nth-child(4) { animation-delay: 0.2s; }
    .receita-card:nth-child(5) { animation-delay: 0.25s; }
    .receita-card:nth-child(6) { animation-delay: 0.3s; }
    
    /* Pulse animation for status badges */
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }
    
    .status-badge.pending {
        animation: pulse 2s infinite;
    }
    
    /* Subtle glow effect for interactive elements */
    @keyframes glow {
        0%, 100% { box-shadow: 0 0 5px rgba(34, 197, 94, 0.2); }
        50% { box-shadow: 0 0 20px rgba(34, 197, 94, 0.4); }
    }
    
    .file-indicator.available:hover {
        animation: glow 1.5s infinite;
    }
    
    /* Utility classes */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    /* Modern date indicators */
    .date-indicator {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.375rem;
        border: 1px solid transparent;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        width: fit-content;
        white-space: nowrap;
        flex-shrink: 0;
        gap: 0.25rem;
        transition: all 0.2s ease;
        backdrop-filter: blur(4px);
    }
    
    .date-indicator.emission {
        background: linear-gradient(135deg, hsl(142.1 70.6% 45.3% / 0.15), hsl(142.1 70.6% 45.3% / 0.1));
        color: hsl(142.1 70.6% 35%);
        border-color: hsl(142.1 70.6% 45.3% / 0.2);
        box-shadow: 0 1px 2px 0 hsl(142.1 70.6% 45.3% / 0.1);
    }
    
    .date-indicator.expiration {
        background: linear-gradient(135deg, hsl(0 84.2% 60.2% / 0.15), hsl(0 84.2% 60.2% / 0.1));
        color: hsl(0 84.2% 50%);
        border-color: hsl(0 84.2% 60.2% / 0.2);
        box-shadow: 0 1px 2px 0 hsl(0 84.2% 60.2% / 0.1);
    }
    
    .receita-card:hover .date-indicator {
        transform: scale(1.02);
    }
    
    /* Enhanced responsive design */
    @media (max-width: 768px) {
        .receitas-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .search-filters {
            padding: 1.5rem;
            border-radius: 0.75rem;
        }
        
        .receita-card {
            padding: 1rem;
            gap: 0.75rem;
            min-height: 140px;
            border-radius: 0.75rem;
        }
        
        .patient-avatar {
            width: 2.25rem;
            height: 2.25rem;
            font-size: 0.75rem;
        }
        
        .patient-name {
            font-size: 0.9375rem;
        }
        
        .patient-meta {
            font-size: 0.75rem;
            gap: 0.5rem;
        }
        
        .description-text {
            font-size: 0.75rem;
            -webkit-line-clamp: 1;
            padding: 0.5rem;
        }
        
        .file-indicator {
            padding: 0.25rem 0.5rem;
            font-size: 0.6875rem;
            gap: 0.25rem;
        }
        
        .action-button-compact {
            width: 1.75rem;
            height: 1.75rem;
        }
        
        .date-indicator {
            padding: 0.1875rem 0.375rem;
            font-size: 0.6875rem;
        }
        
        .id-badge {
            padding: 0.1875rem 0.375rem;
            font-size: 0.6875rem;
        }
        
        .status-badge {
            padding: 0.1875rem 0.375rem;
            font-size: 0.6875rem;
        }
        
        .card-actions-row {
            padding: 0.75rem 1rem;
            margin: 0 -1rem -1rem -1rem;
        }
    }
    
    @media (max-width: 480px) {
        .receitas-grid {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }
        
        .search-filters {
            padding: 1rem;
            border-radius: 0.75rem;
        }
        
        .receita-card {
            padding: 0.875rem;
            min-height: 120px;
            border-radius: 0.75rem;
        }
        
        .card-main-content {
            gap: 0.75rem;
        }
        
        .patient-avatar {
            width: 2rem;
            height: 2rem;
            font-size: 0.6875rem;
        }
        
        .card-actions-row {
            flex-direction: column;
            gap: 0.75rem;
            align-items: stretch;
            padding: 0.75rem 0.875rem;
            margin: 0 -0.875rem -0.875rem -0.875rem;
        }
        
        .card-actions-row > div {
            justify-content: center;
        }
        
        .patient-meta {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.375rem;
        }
        
        .meta-separator {
            display: none;
        }
        
        .search-input {
            height: 2.5rem;
            padding: 0.5rem 0.875rem 0.5rem 2.5rem;
        }
        
        .filter-select {
            height: 2.5rem;
            padding: 0.5rem 0.875rem;
        }
    }
</style>

<main class="mt-5 bg-transparent pb-[60px]">
    <div class="uk-container">
        <div class="bg-card text-card-foreground flex flex-col gap-6 rounded-xl border py-6 shadow-sm mb-6">
            <div class="px-6">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="space-y-1.5">
                        <h1 class="leading-none font-semibold text-xl">Todas as Receitas</h1>
                        <p class="text-muted-foreground text-sm">Visão geral de todas as receitas cadastradas no sistema.</p>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="<?php echo bloginfo("url"); ?>/nova-receita/" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all h-9 px-4 py-2 bg-green-600 text-primary-foreground shadow-xs hover:bg-green-700 hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Nova Receita
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="search-filters">
            <div class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1 relative">

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="absolute left-3 top-8 transform -translate-y-1/2 w-5 h-5 text-slate-600 z-[1]">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>

                    <input type="text" id="searchInput" class="search-input" placeholder="Buscar por paciente, prescritor ou detalhes da receita...">
                </div>
                <div class="flex flex-wrap gap-3">
                    <select id="statusFilter" class="filter-select">
                        <option value="">Todos os Status</option>
                        <option value="complete">Completas</option>
                        <option value="pending">Pendentes</option>
                    </select>
                    <select id="typeFilter" class="filter-select">
                        <option value="">Todos os Tipos</option>
                        <option value="assoc_paciente">Pacientes</option>
                        <option value="assoc_respon">Responsáveis</option>
                        <option value="assoc_tutor">Tutores</option>
                    </select>
                    <select id="sortFilter" class="filter-select">
                        <option value="date_desc">Mais Recentes</option>
                        <option value="date_asc">Mais Antigas</option>
                        <option value="patient_asc">Paciente A-Z</option>
                        <option value="patient_desc">Paciente Z-A</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Cards Grid Container -->
        <div id="receitasContainer">
            <div class="receitas-grid" id="receitasGrid">
                <?php
                $all_receitas_data = [];
                $args = [
                    'post_type'      => 'receitas',
                    'posts_per_page' => -1, // Get all for client-side pagination
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                ];
                $receitas_query = new WP_Query($args);

                if ($receitas_query->have_posts()) :
                    while ($receitas_query->have_posts()) : $receitas_query->the_post();
                        $id_receita = get_the_ID();
                        $all_receitas_data[$id_receita] = get_the_title();

                        $id_paciente_receita = get_field('id_paciente_receita');
                        $data_emissao = get_field('data_emissao');
                        $data_vencimento = get_field('data_vencimento');
                        $arquivo_receita = get_field('arquivo_receita');
                        $arquivo_laudo = get_field('arquivo_laudo');
                        $desc_curta = get_field('desc_curta');
                        $cid_patologia = get_field('cid_patologia');
                        $prescritor_amedis_check = get_field('prescritor_amedis_check');
                        $nome_prescritor = get_field('nome_prescritor');
                        $prescritor_amedis = get_field('prescritor_amedis');

                        $nome_completo = get_user_meta($id_paciente_receita, 'nome_completo', true);
                        $nome_completo_respon = get_user_meta($id_paciente_receita, 'nome_completo_respon', true);
                        $tipo_associacao = get_user_meta($id_paciente_receita, 'tipo_associacao', true);
                        $telefone = get_field('telefone', 'user_' . $id_paciente_receita);

                        $text_tipo_assoc = "";
                        $bg_badge = "bg-gray-50";
                        $txt_color = "text-gray-600";
                        $tipo_associacao_icon = '';

                        if ($tipo_associacao == 'assoc_paciente') {
                            $text_tipo_assoc = "Paciente";
                            $bg_badge = "bg-sky-50";
                            $txt_color = "text-sky-600";
                            $tipo_associacao_icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>';
                        } elseif ($tipo_associacao == 'assoc_respon') {
                            $text_tipo_assoc = "Responsável";
                            $bg_badge = "bg-purple-50";
                            $txt_color = "text-purple-600";
                            $tipo_associacao_icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.75 0 1 1-6.75 0 3.375 3.75 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>';
                        } elseif ($tipo_associacao == 'assoc_tutor') {
                            $text_tipo_assoc = "Tutor de Animal";
                            $bg_badge = "bg-pink-50";
                            $txt_color = "text-pink-600";
                            $tipo_associacao_icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" /></svg>';
                        }

                        // Get prescriber name
                        $prescritor_nome = '';
                        if($prescritor_amedis_check) {
                            $prescritor_nome = get_user_meta($prescritor_amedis, 'nome_completo_prescritor', true);
                        } else {
                            $prescritor_nome = $nome_prescritor;
                        }

                        // Determine completion status
                        $is_complete = !empty($arquivo_receita) && !empty($arquivo_laudo);
                ?>
                        <div class="receita-card" 
                             data-id="<?php echo $id_receita; ?>"
                             data-patient="<?php echo esc_attr(strtolower($nome_completo)); ?>"
                             data-prescriber="<?php echo esc_attr(strtolower($prescritor_nome)); ?>"
                             data-description="<?php echo esc_attr(strtolower($desc_curta)); ?>"
                             data-cid="<?php echo esc_attr(strtolower($cid_patologia)); ?>"
                             data-type="<?php echo esc_attr($tipo_associacao); ?>"
                             data-status="<?php echo $is_complete ? 'complete' : 'pending'; ?>"
                             data-date="<?php echo get_the_date('Y-m-d'); ?>">
                            
                            <!-- Compact Header -->
                            <div class="card-compact-header">
                                <span class="id-badge">#<?php echo $id_receita; ?></span>
                                <span class="status-badge <?php echo $is_complete ? 'complete' : 'pending'; ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12" fill="currentColor" class="w-2.5 h-2.5">
                                        <?php if($is_complete) : ?>
                                            <path fill-rule="evenodd" d="M9.312 2.532a.75.75 0 0 1 .156.78l-3.75 5.625a.75.75 0 0 1-.866.086l-2.25-2.25a.75.75 0 0 1 .795-.795l1.764 1.764 3.369-5.054a.75.75 0 0 1 .78-.156Z" clip-rule="evenodd" />
                                        <?php else : ?>
                                            <path fill-rule="evenodd" d="M6 11.25A5.25 5.25 0 1 0 6 .75a5.25 5.25 0 0 0 0 10.5ZM6.563 3.75a.563.563 0 0 0-1.125 0v3.52l-.54-.54a.563.563 0 0 0-.796.796l1.875 1.875a.563.563 0 0 0 .796 0l1.875-1.875a.563.563 0 1 0-.796-.796l-.54.54V3.75Z" clip-rule="evenodd" />
                                        <?php endif; ?>
                                    </svg>
                                    <?php echo $is_complete ? 'OK' : 'Pendente'; ?>
                                </span>
                            </div>
                            
                            <!-- Main Content -->
                            <div class="card-main-content">
                                <div class="patient-avatar <?php echo $bg_badge; ?> <?php echo $txt_color; ?>">
                                    <?php echo $tipo_associacao_icon; ?>
                                </div>
                                
                                <div class="patient-info">
                                    <h3 class="patient-name text-sm uppercase text-slate-700"><?php echo esc_html($nome_completo); ?></h3>
                                    
                                    <div class="patient-meta">
                                        <span class="status-badge type"><?php echo esc_html($text_tipo_assoc); ?></span>
                                        
                                        <?php if($prescritor_nome) : ?>
                                            <span class="meta-separator"></span>
                                            <span class="meta-item">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12" fill="currentColor" class="w-2.5 h-2.5">
                                                    <path d="M6 6a2.25 2.25 0 1 0 0-4.5A2.25 2.25 0 0 0 6 6ZM9.551 10.5c.463 0 .819-.42.654-.855a4.502 4.502 0 0 0-8.41 0c-.165.435.191.855.654.855h7.102Z" />
                                                </svg>
                                                Dr. <?php echo esc_html($prescritor_nome); ?>
                                            </span>
                                        <?php endif; ?>
                                        
                                        <?php if($telefone) : ?>
                                            <span class="meta-separator"></span>
                                            <span class="meta-item">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12" fill="currentColor" class="w-2.5 h-2.5">
                                                    <path fill-rule="evenodd" d="m2.891 5.464.8-.4a.75.75 0 0 0 .406-.784l-.33-2.144A.75.75 0 0 0 3.027 1.5H2.25a.75.75 0 0 0-.75.75v1.5c0 .532.062 1.05.178 1.547a6.759 6.759 0 0 0 5.025 5.025A6.768 6.768 0 0 0 8.25 10.5h1.5a.75.75 0 0 0 .75-.75v-.777a.75.75 0 0 0-.636-.741l-2.144-.33a.75.75 0 0 0-.784.406l-.4.8a5.64 5.64 0 0 1-3.645-3.644Z" clip-rule="evenodd" />
                                                </svg>
                                                <?php echo esc_html($telefone); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if($desc_curta) : ?>
                                        <p class="description-text"><?php echo esc_html($desc_curta); ?></p>
                                    <?php endif; ?>
                                    
                                    <!-- Dates -->
                                    <div class="flex items-center gap-2 mt-1">
                                        <div class="date-indicator emission">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12" fill="currentColor" class="w-2.5 h-2.5">
                                                <path d="M3 1.5a1.125 1.125 0 0 0-1.125 1.125v6.75A1.125 1.125 0 0 0 3 10.5h6a1.125 1.125 0 0 0 1.125-1.125V4.966a1.125 1.125 0 0 0-.33-.795L7.329 1.705A1.125 1.125 0 0 0 6.534 1.5H3Z" />
                                            </svg>
                                            <?php echo esc_html($data_emissao); ?>
                                        </div>
                                        <div class="date-indicator expiration">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12" fill="currentColor" class="w-2.5 h-2.5">
                                                <path fill-rule="evenodd" d="M6 11.25A5.25 5.25 0 1 0 6 .75a5.25 5.25 0 0 0 0 10.5ZM6.563 3.75a.563.563 0 0 0-1.125 0v2.017l-.405.405a.563.563 0 0 0 .795.795l1.125-1.125A.563.563 0 0 0 6.563 6V3.75Z" clip-rule="evenodd" />
                                            </svg>
                                            <?php echo esc_html($data_vencimento); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Compact Actions Row -->
                            <div class="card-actions-row">
                                <div class="flex items-center gap-1.5">
                                    <?php if($arquivo_receita) : ?>
                                        <a href="<?php echo esc_url($arquivo_receita); ?>" target="_blank" class="file-indicator available" title="Download Receita">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12" fill="currentColor" class="w-2.5 h-2.5">
                                                <path d="M6.563 2.063a.563.563 0 0 0-1.125 0v4.267L4.272 4.897a.563.563 0 0 0-.795.795l2.625 2.625a.563.563 0 0 0 .795 0l2.625-2.625a.563.563 0 1 0-.795-.795L7.563 6.33V2.063Z" />
                                                <path d="M2.625 7.313a.563.563 0 0 0-1.125 0v1.125A2.063 2.063 0 0 0 3.563 10.5h4.875A2.063 2.063 0 0 0 10.5 8.438V7.313a.563.563 0 0 0-1.125 0v1.125c0 .518-.42.937-.938.937H3.563a.938.938 0 0 1-.938-.937V7.313Z" />
                                            </svg>
                                            Receita
                                        </a>
                                    <?php else : ?>
                                        <span class="file-indicator pending" title="Receita Pendente">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12" fill="currentColor" class="w-2.5 h-2.5">
                                                <path fill-rule="evenodd" d="M6 11.25A5.25 5.25 0 1 0 6 .75a5.25 5.25 0 0 0 0 10.5ZM4.665 4.665a.563.563 0 0 1 .795 0L6 5.205l.54-.54a.563.563 0 1 1 .795.795L6.795 6l.54.54a.563.563 0 1 1-.795.795L6 6.795l-.54.54a.563.563 0 0 1-.795-.795L5.205 6l-.54-.54a.563.563 0 0 1 0-.795Z" clip-rule="evenodd" />
                                            </svg>
                                            Receita
                                        </span>
                                    <?php endif; ?>

                                    <?php if($arquivo_laudo) : ?>
                                        <a href="<?php echo esc_url($arquivo_laudo); ?>" target="_blank" class="file-indicator available" title="Download Laudo">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12" fill="currentColor" class="w-2.5 h-2.5">
                                                <path d="M6.563 2.063a.563.563 0 0 0-1.125 0v4.267L4.272 4.897a.563.563 0 0 0-.795.795l2.625 2.625a.563.563 0 0 0 .795 0l2.625-2.625a.563.563 0 1 0-.795-.795L7.563 6.33V2.063Z" />
                                                <path d="M2.625 7.313a.563.563 0 0 0-1.125 0v1.125A2.063 2.063 0 0 0 3.563 10.5h4.875A2.063 2.063 0 0 0 10.5 8.438V7.313a.563.563 0 0 0-1.125 0v1.125c0 .518-.42.937-.938.937H3.563a.938.938 0 0 1-.938-.937V7.313Z" />
                                            </svg>
                                            Laudo
                                        </a>
                                    <?php else : ?>
                                        <span class="file-indicator pending" title="Laudo Pendente">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12" fill="currentColor" class="w-2.5 h-2.5">
                                                <path fill-rule="evenodd" d="M6 11.25A5.25 5.25 0 1 0 6 .75a5.25 5.25 0 0 0 0 10.5ZM4.665 4.665a.563.563 0 0 1 .795 0L6 5.205l.54-.54a.563.563 0 1 1 .795.795L6.795 6l.54.54a.563.563 0 1 1-.795.795L6 6.795l-.54.54a.563.563 0 0 1-.795-.795L5.205 6l-.54-.54a.563.563 0 0 1 0-.795Z" clip-rule="evenodd" />
                                            </svg>
                                            Laudo
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if($cid_patologia) : ?>
                                        <span class="meta-separator"></span>
                                        <span class="meta-item text-xs">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12" fill="currentColor" class="w-2.5 h-2.5">
                                                <path fill-rule="evenodd" d="M6 11.25A5.25 5.25 0 1 0 6 .75a5.25 5.25 0 0 0 0 10.5Zm2.881-6.591a.563.563 0 0 0-.891-.689l-2.775 3.593-1.237-1.375a.563.563 0 1 0-.836.751l1.688 1.875a.563.563 0 0 0 .863-.032l3.188-4.125Z" clip-rule="evenodd" />
                                            </svg>
                                            <?php echo esc_html($cid_patologia); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="flex items-center gap-1">
                                    <button type="button" uk-toggle="target: #modal-excluir-<?php echo $id_receita; ?>" title="Excluir Receita" class="action-button-compact destructive">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 12 12" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m3.375 4.125.563 5.063A1.125 1.125 0 0 0 5.063 10.5h1.875a1.125 1.125 0 0 0 1.125-.938l.563-5.063M4.5 4.125V3a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 .75.75v1.125M3 4.125h6" />
                                        </svg>
                                    </button>
                                    <a href="<?php the_permalink(); ?>" title="Editar Receita" class="action-button-compact">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 12 12" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m10.124 1.127.75.75L2.625 10.125H1.5v-1.125l8.249-8.248ZM8.625 3l.75-.75" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                <?php
                    endwhile;
                else :
                ?>
                    <div class="empty-state">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 text-muted-foreground mx-auto mb-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <h3 class="text-xl font-semibold text-foreground mb-2">Nenhuma receita encontrada</h3>
                        <p class="text-muted-foreground mb-4">Não há receitas para exibir no momento.</p>
                        <a href="<?php echo bloginfo("url"); ?>/nova-receita/" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all h-9 px-4 py-2 bg-green-600 text-primary-foreground shadow-xs hover:bg-green-700/90">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Criar Nova Receita
                        </a>
                    </div>
                <?php
                endif;
                wp_reset_postdata();
                ?>
            </div>

            <!-- Pagination -->
            <div class="pagination-container" id="paginationContainer" style="display: none;">
                <button class="pagination-button" id="prevPage" disabled>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                    Anterior
                </button>
                <div id="pageNumbers"></div>
                <button class="pagination-button" id="nextPage">
                    Próximo
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 ml-1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</main>

<!-- Modals de Exclusão -->
<?php
if (!empty($all_receitas_data)) {
    foreach ($all_receitas_data as $id_receita => $receita_title) {
?>
<div id="modal-excluir-<?php echo $id_receita; ?>" class="uk-flex-top" uk-modal>
    <div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical rounded-lg shadow-lg">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <div class="text-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 text-red-500 mx-auto mb-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
            <h2 class="text-2xl font-bold mb-2">Confirmar Exclusão</h2>
            <p class="text-gray-600 mb-6">Você tem certeza que deseja excluir a receita <br><strong>"<?php echo esc_html($receita_title); ?>" (ID: <?php echo $id_receita; ?>)</strong>? <br>Esta ação não pode ser desfeita.</p>
            <div class="flex justify-center gap-4">
                <button class="uk-button uk-button-default rounded-md uk-modal-close" type="button">Cancelar</button>
                <a href="<?php echo get_delete_post_link($id_receita); ?>" class="uk-button bg-red-600 text-white rounded-md">Sim, Excluir</a>
            </div>
        </div>
    </div>
</div>
<?php
    }
}
?>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Modern Card Grid System
    class ReceitasManager {
        constructor() {
            this.allCards = $('.receita-card');
            this.filteredCards = this.allCards;
            this.currentPage = 1;
            this.itemsPerPage = 12;
            this.totalPages = 1;
            
            this.init();
        }
        
        init() {
            this.setupEventListeners();
            this.updateDisplay();
        }
        
        setupEventListeners() {
            // Search functionality
            $('#searchInput').on('input', debounce(() => {
                this.applyFilters();
            }, 300));
            
            // Filter functionality
            $('#statusFilter, #typeFilter, #sortFilter').on('change', () => {
                this.applyFilters();
            });
            
            // Pagination
            $('#prevPage').on('click', () => {
                if (this.currentPage > 1) {
                    this.currentPage--;
                    this.updateDisplay();
                }
            });
            
            $('#nextPage').on('click', () => {
                if (this.currentPage < this.totalPages) {
                    this.currentPage++;
                    this.updateDisplay();
                }
            });
        }
        
        applyFilters() {
            const searchTerm = $('#searchInput').val().toLowerCase();
            const statusFilter = $('#statusFilter').val();
            const typeFilter = $('#typeFilter').val();
            const sortFilter = $('#sortFilter').val();
            
            // Filter cards
            this.filteredCards = this.allCards.filter((index, card) => {
                const $card = $(card);
                const patient = $card.data('patient') || '';
                const prescriber = $card.data('prescriber') || '';
                const description = $card.data('description') || '';
                const cid = $card.data('cid') || '';
                const status = $card.data('status');
                const type = $card.data('type');
                
                // Search filter
                const searchMatch = !searchTerm || 
                    patient.includes(searchTerm) ||
                    prescriber.includes(searchTerm) ||
                    description.includes(searchTerm) ||
                    cid.includes(searchTerm);
                
                // Status filter
                const statusMatch = !statusFilter || status === statusFilter;
                
                // Type filter
                const typeMatch = !typeFilter || type === typeFilter;
                
                return searchMatch && statusMatch && typeMatch;
            });
            
            // Sort cards
            this.sortCards(sortFilter);
            
            // Reset to first page
            this.currentPage = 1;
            this.updateDisplay();
        }
        
        sortCards(sortType) {
            const cardsArray = this.filteredCards.toArray();
            
            cardsArray.sort((a, b) => {
                const $a = $(a);
                const $b = $(b);
                
                switch(sortType) {
                    case 'date_asc':
                        return new Date($a.data('date')) - new Date($b.data('date'));
                    case 'date_desc':
                        return new Date($b.data('date')) - new Date($a.data('date'));
                    case 'patient_asc':
                        return $a.data('patient').localeCompare($b.data('patient'));
                    case 'patient_desc':
                        return $b.data('patient').localeCompare($a.data('patient'));
                    default:
                        return new Date($b.data('date')) - new Date($a.data('date'));
                }
            });
            
            this.filteredCards = $(cardsArray);
        }
        
        updateDisplay() {
            // Hide all cards first
            this.allCards.hide();
            
            // Calculate pagination
            const totalItems = this.filteredCards.length;
            this.totalPages = Math.ceil(totalItems / this.itemsPerPage);
            
            if (totalItems === 0) {
                // Show empty state
                $('#receitasGrid').html(`
                    <div class="empty-state col-span-full">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 text-muted-foreground mx-auto mb-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <h3 class="text-xl font-semibold text-foreground mb-2">Nenhuma receita encontrada</h3>
                        <p class="text-muted-foreground mb-4">Tente ajustar os filtros ou termos de busca.</p>
                        <button onclick="location.reload()" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all h-9 px-4 py-2 bg-primary text-primary-foreground shadow-xs hover:bg-primary/90">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
                            </svg>
                            Limpar Filtros
                        </button>
                    </div>
                `);
                $('#paginationContainer').hide();
                return;
            }
            
            // Show current page items
            const startIndex = (this.currentPage - 1) * this.itemsPerPage;
            const endIndex = startIndex + this.itemsPerPage;
            const currentPageCards = this.filteredCards.slice(startIndex, endIndex);
            
            // Animate cards in
            currentPageCards.each((index, card) => {
                $(card).show().css({
                    opacity: 0,
                    transform: 'translateY(20px)'
                }).animate({
                    opacity: 1
                }, {
                    duration: 300,
                    step: function(now) {
                        $(this).css('transform', `translateY(${20 * (1 - now)}px)`);
                    }
                });
            });
            
            // Update pagination
            this.updatePagination();
        }
        
        updatePagination() {
            if (this.totalPages <= 1) {
                $('#paginationContainer').hide();
                return;
            }
            
            $('#paginationContainer').show();
            
            // Update prev/next buttons
            $('#prevPage').prop('disabled', this.currentPage === 1);
            $('#nextPage').prop('disabled', this.currentPage === this.totalPages);
            
            // Generate page numbers
            let pageNumbers = '';
            const maxVisiblePages = 5;
            let startPage = Math.max(1, this.currentPage - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(this.totalPages, startPage + maxVisiblePages - 1);
            
            if (endPage - startPage + 1 < maxVisiblePages) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }
            
            for (let i = startPage; i <= endPage; i++) {
                const isActive = i === this.currentPage ? 'active' : '';
                pageNumbers += `<button class="pagination-button ${isActive}" data-page="${i}">${i}</button>`;
            }
            
            $('#pageNumbers').html(pageNumbers);
            
            // Page number click handlers
            $('#pageNumbers .pagination-button').on('click', (e) => {
                const page = parseInt($(e.target).data('page'));
                if (page !== this.currentPage) {
                    this.currentPage = page;
                    this.updateDisplay();
                }
            });
        }
    }
    
    // Debounce function for search
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Initialize the system
    const receitasManager = new ReceitasManager();
    
    // Add loading states for better UX
    $(document).on('ajaxStart', function() {
        $('#receitasGrid').addClass('loading-skeleton');
    }).on('ajaxStop', function() {
        $('#receitasGrid').removeClass('loading-skeleton');
    });
    
    // Smooth scroll to top when changing pages
    $(document).on('click', '.pagination-button', function() {
        $('html, body').animate({
            scrollTop: $('#receitasContainer').offset().top - 100
        }, 300);
    });
    
    // Add keyboard shortcuts
    $(document).on('keydown', function(e) {
        if (e.target.tagName.toLowerCase() !== 'input' && e.target.tagName.toLowerCase() !== 'select') {
            switch(e.key) {
                case 'ArrowLeft':
                    $('#prevPage').click();
                    break;
                case 'ArrowRight':
                    $('#nextPage').click();
                    break;
                case '/':
                    e.preventDefault();
                    $('#searchInput').focus();
                    break;
            }
        }
    });
    
    // Add search shortcut hint
    $('#searchInput').attr('placeholder', $('#searchInput').attr('placeholder') + '');
});
</script>

<?php
} // Fim do else (is_user_logged_in)
get_footer();
?>
