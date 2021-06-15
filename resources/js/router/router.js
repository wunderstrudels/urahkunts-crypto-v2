import VueRouter from 'vue-router';


const routes = [
    // Default routes.
    {
        path: "/",
        name: "dashboard",
        component: () => import('../components/pages/Dashboard.vue'),
        meta: {
            requiresAuth: true
        }
    },
    {
        path: "/wallets/:name?",
        name: "wallets",
        component: () => import('../components/pages/Wallets.vue'),
        meta: {
            requiresAuth: true
        }
    },
    {
        path: "/scenarios/:scenario?",
        name: "scenarios",
        component: () => import('../components/pages/Scenarios.vue'),
        meta: {
            requiresAuth: true
        }
    },
    {
        path: "/graphs",
        name: "graphs",
        component: () => import('../components/pages/Graphs.vue'),
        meta: {
            requiresAuth: true
        }
    },

    // Public routes.
    {
        path: "/login",
        name: "login",
        component: () => import('../components/pages/Login.vue'),
        meta: {
            requiresAuth: false
        }
    }
];

export const router = new VueRouter({ 
	mode: 'history',
	routes: routes
});