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