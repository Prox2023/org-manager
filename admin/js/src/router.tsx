import { Router, Route, RootRoute } from '@tanstack/react-router';
import { MainLayout } from './components/layout/MainLayout';
import { FeatureSettings } from './components/features/FeatureSettings';
import { Dashboard } from './components/pages/Dashboard';

const rootRoute = new RootRoute({
    component: MainLayout,
});

const indexRoute = new Route({
    getParentRoute: () => rootRoute,
    path: '/',
    component: Dashboard,
});

const featureRoute = new Route({
    getParentRoute: () => rootRoute,
    path: 'features/$featureId',
    component: FeatureSettings,
});

const routeTree = rootRoute.addChildren([indexRoute, featureRoute]);

export const router = new Router({ routeTree });

declare module '@tanstack/react-router' {
    interface Register {
        router: typeof router;
    }
} 