import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem, SidebarMenuSub, SidebarMenuSubButton, SidebarMenuSubItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { ChevronDown } from 'lucide-react';
import { useState, useEffect } from 'react';

export function NavMain({ items = [] }: { items: NavItem[] }) {
    const page = usePage();
    const [openSubmenus, setOpenSubmenus] = useState<Record<string, boolean>>({});

    // Function to check if a URL matches a pattern (for nested routes)
    const isUrlActive = (url: string) => {
        // Remove query parameters and trailing slashes for comparison
        const currentPath = page.url.split('?')[0].replace(/\/$/, '');
        const targetPath = url.split('?')[0].replace(/\/$/, '');
        
        // Extract just the path from the current URL (remove domain)
        const currentPathOnly = currentPath.replace(/^https?:\/\/[^/]+/, '');
        
        // If the target path is just a hash (#), it's a parent menu item
        if (targetPath === '#') return false;
        
        // Split paths into segments
        const currentSegments = currentPathOnly.split('/').filter(Boolean);
        const targetSegments = targetPath.split('/').filter(Boolean);
        
        // For index routes, we want to match the base path
        if (targetSegments[targetSegments.length - 1] === 'index') {
            targetSegments.pop();
        }
        
        // Compare segments up to the length of the target path
        return targetSegments.every((segment, index) => currentSegments[index] === segment);
    };

    // Function to check if any child items are active
    const hasActiveChild = (items: NavItem[] | undefined) => {
        if (!items) return false;
        return items.some(item => isUrlActive(item.href));
    };

    // Initialize open submenus based on active routes
    useEffect(() => {
        const initialOpenState: Record<string, boolean> = {};
        items.forEach(item => {
            if (item.items && hasActiveChild(item.items)) {
                initialOpenState[item.title] = true;
            }
        });
        setOpenSubmenus(initialOpenState);
    }, [page.url, items]);

    const toggleSubmenu = (title: string) => {
        setOpenSubmenus(prev => ({
            ...prev,
            [title]: !prev[title]
        }));
    };

    return (
        <SidebarGroup className="px-2 py-0">
            <SidebarGroupLabel>Platform</SidebarGroupLabel>
            <SidebarMenu>
                {items.map((item) => {
                    const isActive = isUrlActive(item.href);
                    const hasActiveChildren = item.items && hasActiveChild(item.items);
                    
                    return (
                        <SidebarMenuItem key={item.title}>
                            {item.items ? (
                                <SidebarMenuButton 
                                    isActive={isActive || hasActiveChildren}
                                    onClick={() => toggleSubmenu(item.title)}
                                >
                                    {item.icon && <item.icon />}
                                    <span>{item.title}</span>
                                    <ChevronDown 
                                        className={`ml-auto h-4 w-4 transition-transform duration-200 ${
                                            openSubmenus[item.title] ? 'rotate-180' : ''
                                        }`}
                                    />
                                </SidebarMenuButton>
                            ) : (
                                <SidebarMenuButton 
                                    asChild 
                                    isActive={isActive}
                                >
                                    <Link href={item.href} prefetch>
                                        {item.icon && <item.icon />}
                                        <span>{item.title}</span>
                                    </Link>
                                </SidebarMenuButton>
                            )}
                            {item.items && openSubmenus[item.title] && (
                                <SidebarMenuSub>
                                    {item.items.map((subItem) => (
                                        <SidebarMenuSubItem key={subItem.title}>
                                            <SidebarMenuSubButton 
                                                asChild 
                                                isActive={isUrlActive(subItem.href)}
                                            >
                                                <Link href={subItem.href} prefetch>
                                                    {subItem.icon && <subItem.icon />}
                                                    <span>{subItem.title}</span>
                                                </Link>
                                            </SidebarMenuSubButton>
                                        </SidebarMenuSubItem>
                                    ))}
                                </SidebarMenuSub>
                            )}
                        </SidebarMenuItem>
                    );
                })}
            </SidebarMenu>
        </SidebarGroup>
    );
}
