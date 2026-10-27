<style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: #f7f8fa;
            color: #292930;
            font-family: Arial, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 13px;
            line-height: 1.5;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: 220px;
            padding: 24px 16px;
            background: #91283b;
            color: white;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            z-index: 100;
        }

        .brand {
            padding: 0 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-icon {
            width: 32px;
            height: 32px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .brand-text {
            font-size: 20px;
            font-weight: 700;
            line-height: 1.1;
        }

        .brand-text small {
            display: block;
            margin-top: 3px;
            font-size: 7.5px;
            font-weight: 600;
            letter-spacing: 1.5px;
            color: #fce7eb;
        }

        .menu-label {
            margin: 28px 10px 10px;
            font-size: 10px;
            color: #f0c7cf;
            letter-spacing: 0.5px;
            font-weight: 700;
        }

        .sidebar-menu {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .sidebar-menu a,
        .sidebar-menu span {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 8px;
            color: #fce7eb;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.15s ease;
        }

        .sidebar-menu a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .sidebar-menu a.menu-active {
            background: rgba(255, 255, 255, 0.16);
            color: white;
            font-weight: 600;
        }

        .sidebar-menu .menu-disabled {
            opacity: 0.85;
            cursor: pointer;
        }

        .menu-group-active {
            background: rgba(255, 255, 255, 0.16);
            border-radius: 8px;
            padding-bottom: 6px;
        }

        .menu-parent-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px 4px;
            color: #ffffff;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
        }

        .menu-sub-link {
            display: block;
            padding: 2px 12px 4px 38px;
            font-size: 12px;
            color: #fce7eb;
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .menu-sub-link:hover,
        .menu-sub-link.active {
            color: #ffffff;
            font-weight: 600;
        }

        .sidebar-spacer {
            flex: 1;
            min-height: 24px;
        }

        /* Sidebar Sync Info Box */
        .sync-info-box {
            background: rgba(0, 0, 0, 0.18);
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 16px;
        }

        .sync-info-status {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: #f8d5dc;
            font-weight: 500;
            margin-bottom: 6px;
        }

        .sync-dot {
            width: 7px;
            height: 7px;
            background: #22c55e;
            border-radius: 50%;
            display: inline-block;
        }

        .sync-info-label {
            font-size: 10px;
            color: #e8a2af;
        }

        .sync-info-time {
            font-size: 11px;
            color: #ffffff;
            font-weight: 600;
            margin-top: 1px;
        }

        /* Sidebar User Profile */
        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 6px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
        }

        .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.22);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            color: white;
            flex-shrink: 0;
        }

        .sidebar-user-info {
            flex: 1;
            min-width: 0;
        }

        .user-name {
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #ffffff;
        }

        .user-role {
            font-size: 10.5px;
            color: #f0c7cf;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .logout-icon-btn {
            background: none;
            border: none;
            color: #fce7eb;
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
        }

        .logout-icon-btn:hover {
            color: white;
            background: rgba(255, 255, 255, 0.15);
        }

        /* Main Content */
</style>