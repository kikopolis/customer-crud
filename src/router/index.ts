import { createRouter, createWebHistory } from 'vue-router';
import CustomersView from '../views/CustomersView.vue';

const router = createRouter(
    {
        history: createWebHistory(import.meta.env.BASE_URL),
        routes:  [
            {
                path:      '/',
                name:      'customers',
                component: CustomersView,
                meta:      {
                    title: 'Customers',
                }
            },
        ],
    });
export default router;
